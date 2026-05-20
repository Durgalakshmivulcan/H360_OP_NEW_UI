
$(function(){
  // Date range picker setup
  const $drp = $('#f_daterange');
  const supportsDRP = !!$.fn.daterangepicker;
  if (supportsDRP) {
    $drp.daterangepicker({
      autoUpdateInput: true,
      startDate: moment().subtract(29,'days'),
      endDate: moment(),
      locale: { format:'YYYY-MM-DD' }
    });
  } else {
    $drp.val(
      moment().subtract(29,'days').format('YYYY-MM-DD') + ' - ' +
      moment().format('YYYY-MM-DD')
    );
  }
  function parseRange(str) {
    const parts = (str || '').split(' - ');
    return { from: parts[0] || '', to: parts[1] || '' };
  }

  // Populate doctor select via AJAX and enhance with Choices/Select2
  const $docSelect = $('#f_doctor');
  $.getJSON('./ajax/PatientsOutcomeReport/op_patient_outcome_doctors.php', function(list){
    if (Array.isArray(list)) {
      list.forEach(function(item){
        // Expect objects {id:..., name:...}
        if (item && item.doc_id !== undefined) {
          $docSelect.append($('<option/>',{ value:item.doc_id, text:item.doctor_name }));
        }
      });
    }
    if (window.Choices) {
      new Choices('#f_doctor',{ allowHTML:false, removeItemButton:true, searchPlaceholderValue:'Search doctor' });
    }
    if ($.fn.select2 && !$docSelect.data('choices')) {
      $docSelect.select2({ placeholder:'All doctors', allowClear:true, width:'100%' });
    }
  });

  // Initialise charts
  const trendCtx = document.getElementById('chartTrend').getContext('2d');
  const compCtx  = document.getElementById('chartComp').getContext('2d');
  const trendChart = new Chart(trendCtx, {
    type: 'line',
    data: { labels: [], datasets: [
      { label:'Improved', data: [], borderWidth:5, fill:false, borderColor:'#47c363' },
      { label:'Declined', data: [], borderWidth:5, fill:false, borderColor:'#fc544b' },
      { label:'Same', data: [], borderWidth:5, fill:false, borderColor:'#6777ef' },
      { label:'No Data', data: [], borderWidth:5, fill:false, borderColor:'#ffa426' }
    ] },
    options: {
      responsive:true,
      maintainAspectRatio:false,
      plugins:{ legend:{ position:'bottom' } },
      scales:{ x:{ grid:{ display:false } }, y:{ beginAtZero:true } }
    }
  });
  const compChart = new Chart(compCtx, {
    type: 'doughnut',
    data: { labels: [], datasets: [ { data: [], backgroundColor:['#47c363','#fc544b','#6777ef','#ffa426'] } ] },
    options: { responsive:true, maintainAspectRatio:false, plugins:{ legend:{ position:'bottom' } } }
  });

  // DataTable configuration
  const $tbl = $('#outcomeTable');
  const dt = $tbl.DataTable({
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
        doctor: $('#f_doctor').val() || '',
        search_text: $('#f_search').val() || ''
      };
      $.getJSON('./ajax/PatientsOutcomeReport/op_patient_outcome_export.php', params, function(resp){
        callback({
          recordsTotal: resp.recordsTotal,
          recordsFiltered: resp.recordsFiltered,
          data: resp.data
        });
        toggleEmpty(resp.data.length === 0);
      });
    },
    columns: [
      { data:'patient' },
      { data:'baseline_date' },
      { data:'baseline_bp', className:'text-end' },
      { data:'last_date' },
      { data:'last_bp', className:'text-end' },
      { data:'next_follow', render: function(v){ return v || '—'; } },
     { 
        data:'missed', 
        className:'text-center', 
        render: function(v){ 
          if (v === 'Yes') {
            return '<i class="fas fa-times-circle text-danger"></i>'; // missed
          } else {
            return '<i class="fas fa-check-circle text-success"></i>'; // not missed
          }
        } 
  }

    ],
    order: [[3,'desc']],
    language: { emptyTable:'No data for the selected filters' }
  });

  // Column toggles
  $('.col-toggle').on('change', function(){
    dt.column(parseInt(this.value, 10)).visible(this.checked);
  });

  // Toggle empty state visibility
  function toggleEmpty(isEmpty) {
    $('#emptyState').toggleClass('d-none', !isEmpty);
    $('.table-responsive').toggleClass('d-none', isEmpty);
  }
  toggleEmpty(true);

  // Quick range chips
  $('.quick-range').on('click', function(){
    const key = $(this).data('range');
    let from = moment();
    let to   = moment();
    if (key === '7d') from = moment().subtract(6,'days');
    else if (key === '30d') from = moment().subtract(29,'days');
    else if (key === 'qtd') from = moment().startOf('quarter');
    else if (key === 'ytd') from = moment().startOf('year');
    // 'today' means from = to = today
    if (supportsDRP) {
      $drp.data('daterangepicker').setStartDate(from);
      $drp.data('daterangepicker').setEndDate(to);
    }
    $drp.val(from.format('YYYY-MM-DD') + ' - ' + to.format('YYYY-MM-DD'));
  });

  // Apply button: load KPIs, charts and refresh table
  $('#btnApply').on('click', function(){
    loadKpis();
    loadCharts();
    dt.ajax.reload();
  });

  // Export CSV
    $('#btnExport').on('click', function(){
      const range = parseRange($drp.val());
      const params = {
        from: range.from,
        to: range.to,
        doctor: $('#f_doctor').val() || '',
        search_text: $('#f_search').val() || '',
        export: 'csv' // 👈 force CSV
      };
      const qs = $.param(params);
      window.location = './ajax/PatientsOutcomeReport/op_patient_outcomes.php?' + qs;
    });


  // Reset filters
  $('#btnReset').on('click', function(){
    if (supportsDRP) {
      $drp.data('daterangepicker').setStartDate(moment().subtract(29,'days'));
      $drp.data('daterangepicker').setEndDate(moment());
    }
    $drp.val(
      moment().subtract(29,'days').format('YYYY-MM-DD') + ' - ' +
      moment().format('YYYY-MM-DD')
    );
    $('#f_doctor').val('');
    if ($docSelect.data('select2')) $docSelect.val(null).trigger('change');
    if ($docSelect.data('choices')) $docSelect[0].tom.selectNone();
    $('#f_search').val('');
    dt.clear().draw();
    toggleEmpty(true);
    // Reset KPIs and charts
    $('#kpi_total').text('0');
    $('#kpi_improved').text('0');
    $('#kpi_declined').text('0');
    $('#kpi_missed').text('0');
    trendChart.data.labels = [];
    trendChart.data.datasets.forEach(ds => ds.data = []);
    trendChart.update();
    compChart.data.labels = [];
    compChart.data.datasets[0].data = [];
    compChart.update();
  });

  // Load KPIs via AJAX
      function loadKpis() {
        const range = parseRange($drp.val());
        const params = {
          from: range.from,
          to: range.to,
          doctor: $('#f_doctor').val() || '',
          search_text: $('#f_search').val() || ''
        };
          $.getJSON('./ajax/PatientsOutcomeReport/op_patient_outcome_kpis.php', params, function(resp){
        console.log('Response:', resp);
        console.log('Elements:', $('#kpi_total'), $('#kpi_same'));
        
        $('#kpi_total').text(resp.total || 0);
        $('#kpi_improved').text(resp.improved || 0);
        $('#kpi_declined').text(resp.declined || 0);
        $('#kpi_missed').text(resp.missed || 0);
        $('#kpi_same').text(resp.same || 0);
    });
  }
  loadKpis(); 
  // Load charts via AJAX
  function loadCharts() {
    const range = parseRange($drp.val());
    const params = {
      from: range.from,
      to: range.to,
      doctor: $('#f_doctor').val() || '',
      search_text: $('#f_search').val() || ''
    };
    $.getJSON('./ajax/PatientsOutcomeReport/op_patient_outcome_charts.php', params, function(resp){
      // Expect resp.trend.labels and .datasets with keys improved, declined, same, nodata
      if (resp && resp.trend) {
        trendChart.data.labels = resp.trend.labels || [];
        trendChart.data.datasets[0].data = resp.trend.improved || [];
        trendChart.data.datasets[1].data = resp.trend.declined || [];
        trendChart.data.datasets[2].data = resp.trend.same || [];
        trendChart.data.datasets[3].data = resp.trend.nodata || [];
        trendChart.update();
      }
      if (resp && resp.comp) {
        compChart.data.labels = resp.comp.labels || [];
        compChart.data.datasets[0].data = resp.comp.data || [];
        compChart.update();
      }
    });
  }
  loadCharts();
});