/*
 * op_rx_patterns.js
 *
 * Front‑end script for the OP Diagnosis & Prescription Patterns report. This
 * script creates a feature‑rich UI using DataTables for tabular
 * presentation, Chart.js for trends and compositions, and jQuery for
 * event handling. It supports date range selection (with or without
 * daterangepicker), quick range chips, drug class selection via
 * Choices or Select2, column visibility toggling, KPI updates, and
 * simple persistence of filters (via resets). At present, all data
 * displayed is static sample data. To connect to your back‑end
 * endpoints, replace the blocks marked TODO with AJAX calls and update
 * the charts and KPIs accordingly.
 */

$(function() {
  // Parse a range string ("YYYY-MM-DD - YYYY-MM-DD") into {from,to}
  function parseRange(range) {
    const parts = (range || '').split(' - ');
    return { from: parts[0] || '', to: parts[1] || '' };
  }

  // Date range picker setup
  const $drp = $('#f_daterange');
  const drpEnabled = !!$.fn.daterangepicker;
  if (drpEnabled) {
    $drp.daterangepicker({
      autoUpdateInput: true,
      startDate: moment().subtract(29, 'days'),
      endDate: moment(),
      locale: { format: 'YYYY-MM-DD' }
    });
  } else {
    $drp.val(moment().subtract(29, 'days').format('YYYY-MM-DD') + ' - ' + moment().format('YYYY-MM-DD'));
  }

  // Populate drug class select with some typical classes
  const $cls = $('#f_class');
  const defaultClasses = ['Antibiotics', 'Antihypertensives', 'Antidiabetics', 'NSAIDs', 'PPIs', 'Statins'];
  defaultClasses.forEach(cls => $cls.append($('<option/>', { value: cls, text: cls })));
  // Enhance select with Choices.js or Select2 if available
  if (window.Choices) new Choices('#f_class', { allowHTML: false, removeItemButton: true, searchPlaceholderValue: 'Search class' });
  if ($.fn.select2 && !$cls.data('choices')) $cls.select2({ placeholder: 'All classes', allowClear: true, width: '100%' });

  // Quick date range chips
  $('.quick-range').on('click', function() {
    const key = $(this).data('range');
    let from = moment();
    const to = moment();
    if (key === '7d') from = moment().subtract(6, 'days');
    else if (key === '30d') from = moment().subtract(29, 'days');
    else if (key === 'qtd') from = moment().startOf('quarter');
    else if (key === 'ytd') from = moment().startOf('year');
    if (drpEnabled) {
      $drp.data('daterangepicker').setStartDate(from);
      $drp.data('daterangepicker').setEndDate(to);
    }
    $drp.val(from.format('YYYY-MM-DD') + ' - ' + to.format('YYYY-MM-DD'));
  });

  // Initialise charts
  const ctxTrend = document.getElementById('chartTrend').getContext('2d');
  const ctxClass = document.getElementById('chartClass').getContext('2d');
  const chartTrend = new Chart(ctxTrend, {
    type: 'line',
    data: { labels: [], datasets: [{ label: 'Scripts', data: [], borderWidth: 2, fill: false }] },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: { legend: { display: false } },
      scales: { x: { grid: { display: false } }, y: { beginAtZero: true } }
    }
  });
  const chartClass = new Chart(ctxClass, {
    type: 'doughnut',
    data: { labels: [], datasets: [{ data: [], borderWidth: 0 }] },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: { legend: { position: 'bottom' } }
    }
  });

  // DataTable setup
  const $table = $('#rxTable');
  const dt = $table.DataTable({
    serverSide: false,
    paging: true,
    searching: true,
    ordering: true,
    columns: [
      { data: 'dx_name' },
      { data: 'icd_code' },
      { data: 'drug_name' },
      { data: 'drug_class' },
      { data: 'scripts', className: 'text-end' },
      { data: 'patients', className: 'text-end' },
      { data: 'share_pct', className: 'text-end', render: v => (v != null ? v.toFixed(1) + '%' : '—') },
      { data: 'flag', render: f => f ? '<span class="badge bg-danger">Overuse?</span>' : '—' }
    ],
    order: [[4, 'desc']],
    data: []
  });

  // Column visibility toggler
  $('.col-toggle').on('change', function() {
    dt.column(parseInt(this.value, 10)).visible(this.checked);
  });

  // Empty state toggling helper
  function toggleEmpty(isEmpty) {
    $('#emptyState').toggleClass('d-none', !isEmpty);
    $('.table-responsive').toggleClass('d-none', isEmpty);
  }
  toggleEmpty(true);

  // Apply button: use sample data for demonstration
  $('#btnApply').on('click', function() {
    // Get filters (unused in mock, but ready for real data)
    const { from, to } = parseRange($drp.val());
    const dxFilter = ($('#f_dx').val() || '').toLowerCase();
    const classFilter = $('#f_class').val();
    // const docFilter = $('#f_doctor').val(); // ignored in mock

    // Sample dataset representing aggregated patterns
    const sample = [
      { dx_name:'Type 2 Diabetes Mellitus', icd_code:'E11', drug_name:'Metformin',   drug_class:'Antidiabetics', scripts:320, patients:260, share_pct:46.2, flag: false },
      { dx_name:'Type 2 Diabetes Mellitus', icd_code:'E11', drug_name:'Glimepiride',  drug_class:'Antidiabetics', scripts:180, patients:150, share_pct:26.0, flag: false },
      { dx_name:'Hypertension',             icd_code:'I10', drug_name:'Amlodipine',   drug_class:'Antihypertensives', scripts:290, patients:240, share_pct:37.4, flag: false },
      { dx_name:'Hypertension',             icd_code:'I10', drug_name:'Atenolol',     drug_class:'Antihypertensives', scripts:210, patients:175, share_pct:27.1, flag: true  },
      { dx_name:'Hyperlipidemia',           icd_code:'E78', drug_name:'Atorvastatin', drug_class:'Statins', scripts:205, patients:180, share_pct:58.3, flag: false }
    ].filter(r => (
      (!dxFilter || r.dx_name.toLowerCase().includes(dxFilter) || r.icd_code.toLowerCase().includes(dxFilter)) &&
      (!classFilter || r.drug_class === classFilter)
    ));

    // Update DataTable & empty state
    dt.clear().rows.add(sample).draw();
    toggleEmpty(sample.length === 0);

    // Mock charts data
    chartTrend.data.labels = ['W1','W2','W3','W4','W5'];
    chartTrend.data.datasets[0].data = [50, 80, 120, 160, 140];
    chartTrend.update();
    chartClass.data.labels = ['Antidiabetics','Antihypertensives','Statins','NSAIDs'];
    chartClass.data.datasets[0].data = [42, 32, 16, 10];
    chartClass.update();

    // Update KPIs with mock values
    $('#kpi_scripts').text(1205);
    $('#kpi_drugs').text(48);
    $('#kpi_topdx').text('E11');
    $('#kpi_flags').text(1);
  });

  // Export button: display message until API is implemented
  $('#btnExport').on('click', function() {
    if (window.toastr && toastr.info) toastr.info('Export endpoint not connected yet.');
    else alert('Export endpoint not connected yet.');
  });

  // Reset filters and clear data
  $('#btnReset').on('click', function() {
    if (drpEnabled) {
      $drp.data('daterangepicker').setStartDate(moment().subtract(29, 'days'));
      $drp.data('daterangepicker').setEndDate(moment());
    }
    $drp.val(moment().subtract(29, 'days').format('YYYY-MM-DD') + ' - ' + moment().format('YYYY-MM-DD'));
    $('#f_dx').val('');
    $('#f_class').val('');
    if ($cls.data('select2')) $cls.val(null).trigger('change');
    $('#f_doctor').val('');
    dt.clear().draw();
    toggleEmpty(true);
    $('#kpi_scripts, #kpi_drugs, #kpi_topdx, #kpi_flags').text('0');
  });
});