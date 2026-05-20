
$(function(){
  // Helpers for date range parsing and default values
  const $drp = $('#f_daterange');
  const supportsDRP = !!$.fn.daterangepicker;
  if (supportsDRP) {
    $drp.daterangepicker({
      autoUpdateInput: true,
      startDate: moment().subtract(29,'days'),
      endDate: moment(),
      locale: { format: 'YYYY-MM-DD' }
    });
  } else {
    $drp.val(
      moment().subtract(29,'days').format('YYYY-MM-DD') +
      ' - ' +
      moment().format('YYYY-MM-DD')
    );
  }
  function parseRange(str) {
    const parts = (str || '').split(' - ');
    return { from: parts[0] || '', to: parts[1] || '' };
  }

  // Populate service select via AJAX and enhance with Choices/Select2
  const $service = $('#f_service');
  $.getJSON('./ajax/oprevenue/op_revenue_services.php', function(list){
    if (Array.isArray(list)) {
      list.forEach(function(name){
        $service.append($('<option/>',{ value: name, text: name }));
      });
    }
    // Enhance dropdown after data appended
    if (window.Choices) new Choices('#f_service', { allowHTML:false, removeItemButton:true, searchPlaceholderValue:'Search service' });
    if ($.fn.select2 && !$service.data('choices')) $service.select2({ placeholder:'All services', allowClear:true, width:'100%' });
  });

  // Populate payer select via AJAX and enhance
  // const $payer = $('#f_payer');
  // $.getJSON('./ajax/oprevenue/op_revenue_payers.php', function(list){
  //   if (Array.isArray(list)) {
  //     list.forEach(function(p){
  //       $payer.append($('<option/>',{ value: p, text: p }));
  //     });
  //   }
  //   if (window.Choices) new Choices('#f_payer', { allowHTML:false, removeItemButton:true });
  //   if ($.fn.select2 && !$payer.data('choices')) $payer.select2({ placeholder:'All payers', allowClear:true, width:'100%' });
  // });

  // Initialize charts
  const trendCtx = document.getElementById('chartTrend').getContext('2d');
  const compCtx  = document.getElementById('chartComp').getContext('2d');
  const trendChart = new Chart(trendCtx, {
    type: 'line',
    data: { labels: [], datasets: [{ label:'Net Revenue', data: [], borderWidth:2, fill:false, borderColor:'#6777ef' }] },
    options: {
      responsive:true,
      maintainAspectRatio:false,
      plugins:{ legend:{ display:false } },
      scales:{ x:{ grid:{ display:false } }, y:{ beginAtZero:true } }
    }
  });
  const compChart = new Chart(compCtx, {
    type: 'doughnut',
    data: { labels: [], datasets: [{ data: [], backgroundColor:['#47c363','#6777ef','#ffa426','#fc544b','#c70039'] }] },
    options: {
      responsive:true,
      maintainAspectRatio:false,
      plugins:{ legend:{ position:'bottom' } }
    }
  });

  // DataTable setup
  const $table = $('#revTable');
  const dt = $table.DataTable({
    serverSide: true,
    processing: true,
    searching: true,
    ordering: true,
    ajax: function(data, callback) {
      const range = parseRange($drp.val());
      const params = {
        start: data.start,
        length: data.length,
        search: { value: data.search.value },
        from: range.from,
        to: range.to,
        service: $('#f_service').val() || '',
        doctor: $('#f_doctor').val() || '',
        payer: $('#f_payer').val() || '',
        search_text: $('#f_search').val() || ''
      };
      $.getJSON('./ajax/oprevenue/op_revenue_list.php', params, function(resp){
        // FIX_B_700: guard undefined fields to prevent DataTables `.length` crash.
        resp = resp || {};
        if (!Array.isArray(resp.data)) resp.data = [];
        callback({
          recordsTotal: resp.recordsTotal || 0,
          recordsFiltered: resp.recordsFiltered || 0,
          data: resp.data
        });
      }).fail(function(){
        // FIX_B_700: ensure DataTables always gets a valid shape, never undefined.
        callback({ recordsTotal: 0, recordsFiltered: 0, data: [] });
      });
    },
    columns: [
      { data:'date' },
      { data:'service' },
      { data:'doctor' },
      { data:'payer' },
      { data:'invoices', className:'text-end' },
      { data:'gross', className:'text-end', render: v => formatCurrency(v) },
      { data:'discount', className:'text-end', render: v => formatCurrency(v) },
      { data:'tax', className:'text-end', render: v => formatCurrency(v) },
      { data:'net', className:'text-end', render: v => formatCurrency(v) }
    ],
    order: [[0,'desc']],
    language: { emptyTable:'No data for the selected filters' }
  });

  // Currency formatter
  function formatCurrency(val) {
    if (val == null) return '—';
    return new Intl.NumberFormat('en-IN',{ style:'currency', currency:'INR', minimumFractionDigits:2 }).format(val);
  }

  // Column toggle events
  $('.col-toggle').on('change', function(){
    dt.column(parseInt(this.value,10)).visible(this.checked);
  });

  // Quick range chips
  $('.quick-range').on('click', function(){
    const key = $(this).data('range');
    let from = moment();
    let to   = moment();
    if (key === '7d')      from = moment().subtract(6,'days');
    else if (key === '30d') from = moment().subtract(29,'days');
    else if (key === 'qtd') from = moment().startOf('quarter');
    else if (key === 'ytd') from = moment().startOf('year');
    // else 'today' or default
    if (supportsDRP) {
      $drp.data('daterangepicker').setStartDate(from);
      $drp.data('daterangepicker').setEndDate(to);
    }
    $drp.val(from.format('YYYY-MM-DD') + ' - ' + to.format('YYYY-MM-DD'));
  });

  // Toggle empty state visibility
  function toggleEmpty(isEmpty) {
    $('#emptyState').toggleClass('d-none', !isEmpty);
    $('.table-responsive').toggleClass('d-none', isEmpty);
  }
  toggleEmpty(true);

  // Apply button: fetch KPIs, charts and reload table
  $('#btnApply').on('click', function(){
    loadKpis();
    loadCharts();
    dt.ajax.reload();
  });

  // Export CSV: call server endpoint with current filters
  $('#btnExport').on('click', function(){
    const range = parseRange($drp.val());
    const params = {
      from: range.from,
      to: range.to,
      service: $('#f_service').val() || '',
      doctor: $('#f_doctor').val() || '',
      payer: $('#f_payer').val() || ''
    };
    const qs = $.param(params);
    window.location = './ajax/oprevenue/op_revenue_export.php?' + qs;
  });

  // Load KPIs from server
  function loadKpis() {
    const range = parseRange($drp.val());
    const params = {
      from: range.from,
      to: range.to,
      service: $('#f_service').val() || '',
      doctor: $('#f_doctor').val() || '',
      payer: $('#f_payer').val() || ''
    };
    $.getJSON('./ajax/oprevenue/op_revenue_kpis.php', params, function(resp){
      $('#kpi_gross').text(formatCurrency(resp.gross || 0));
      $('#kpi_discount').text(formatCurrency(resp.discount || 0));
      $('#kpi_tax').text(formatCurrency(resp.tax || 0));
      $('#kpi_net').text(formatCurrency(resp.net || 0));
    });
  }

  // Load charts from server
  function loadCharts() {
    const range = parseRange($drp.val());
    const params = {
      from: range.from,
      to: range.to,
      service: $('#f_service').val() || '',
      doctor: $('#f_doctor').val() || '',
      payer: $('#f_payer').val() || ''
    };
    $.getJSON('./ajax/oprevenue/op_revenue_charts.php', params, function(resp){
      // Trend
      const t = resp.trend || { labels: [], data: [] };
      trendChart.data.labels = t.labels;
      trendChart.data.datasets[0].data = t.data;
      trendChart.update();
      // Composition
      const c = resp.composition || { labels: [], data: [] };
      compChart.data.labels = c.labels;
      compChart.data.datasets[0].data = c.data;
      compChart.update();
    });
  }

  // Reset filters and charts
  $('#btnReset').on('click', function(){
    if (supportsDRP) {
      $drp.data('daterangepicker').setStartDate(moment().subtract(29,'days'));
      $drp.data('daterangepicker').setEndDate(moment());
    }
    $drp.val(
      moment().subtract(29,'days').format('YYYY-MM-DD') + ' - ' + moment().format('YYYY-MM-DD')
    );
    $('#f_service').val('');
    if ($service.data('select2')) $service.val(null).trigger('change');
    if ($service.data('choices')) {
      const inst = $service.data('choices');
      inst.setChoiceByValue('');
    }
    $('#f_doctor').val('');
    $('#f_payer').val('');
    if ($payer.data('select2')) $payer.val(null).trigger('change');
    if ($payer.data('choices')) {
      const inst = $payer.data('choices');
      inst.setChoiceByValue('');
    }
    $('#f_search').val('');
    dt.clear().draw();
    toggleEmpty(true);
    // Reset KPIs and charts
    $('#kpi_gross,#kpi_discount,#kpi_tax,#kpi_net').text('0');
    trendChart.data.labels=[];
    trendChart.data.datasets[0].data=[];
    trendChart.update();
    compChart.data.labels=[];
    compChart.data.datasets[0].data=[];
    compChart.update();
  });
});