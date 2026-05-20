// audit_feature/assets/js/page/audit-log.js
// Client-side code for Audit Log page using Gati components and DataTables.

$(function() {
  const $table = $('#auditTable');
  const $modal = $('#detailModal');
  const $before = $('#json_before');
  const $after  = $('#json_after');

  // Date range picker initialisation (using daterangepicker if available)
  const $range = $('#f_daterange');
  if ($.fn.daterangepicker) {
    $range.daterangepicker({
      autoUpdateInput: true,
      startDate: moment().subtract(6, 'days'),
      endDate: moment(),
      locale: { format: 'YYYY-MM-DD' }
    });
  } else {
    // Fallback: last 7 days in a simple string format
    $range.val(
      moment().subtract(6, 'days').format('YYYY-MM-DD') + ' - ' +
      moment().format('YYYY-MM-DD')
    );
  }

  // Quick range chips
  $('.quick-range').on('click', function() {
    const key = $(this).data('range');
    let from = moment();
    let to   = moment();
    if (key === 'today') {
      // keep defaults
    } else if (key === '7d') {
      from = moment().subtract(6, 'days');
    } else if (key === '30d') {
      from = moment().subtract(29, 'days');
    } else if (key === 'qtd') {
      from = moment().startOf('quarter');
    }
    $range.val(from.format('YYYY-MM-DD') + ' - ' + to.format('YYYY-MM-DD'));
    loadStats();
    dt.ajax.reload();
  });

  // DataTable setup
  const dt = $table.DataTable({
    serverSide: true,
    processing: true,
    searching: true,
    ajax: function(data, callback) {
      const { from, to } = parseRange($range.val());
      const params = {
        start: data.start,
        length: data.length,
        search: { value: data.search.value },
        from: from,
        to: to,
        module_filter: $('#f_module').val(),
        action_filter: $('#f_action').val(),
        user_filter: $('#f_user').val()
      };
      $.getJSON('./ajax/audit/log_list.php', params, function(resp) {
        callback({
          recordsTotal: resp.recordsTotal,
          recordsFiltered: resp.recordsFiltered,
          data: resp.data
        });
      });
    },
    columns: [
      {
        data: null,
        render: function (data, type, row, meta) {
          return meta.row + meta.settings._iDisplayStart + 1;
        }
      },

      { data: 'ts' },
      { data: 'admin_name' },
      {
        data: 'module',
        render: function(val) {
          return '<span class="badge bg-light text-dark">' + escapeHtml(val) + '</span>';
        }
      },
      {
        data: 'action',
        render: function(val) {
          const map = {
            'create': 'success',
            'update': 'warning',
            'delete': 'danger',
            'login':  'info',
            'logout': 'secondary'
          };
          const cls = map[val] || 'secondary';
          return '<span class="badge bg-' + cls + ' text-uppercase">' + escapeHtml(val) + '</span>';
        }
      },
      { data: 'entity' },
      // {
      //   data: 'entity_id',
      //   render: function(val) {
      //     return val == null ? '—' : val;
      //   }
      // },
      // {
      //   data: 'ip',
      //   render: function(val) {
      //     return val ? val : '—';
      //   }
      // },
      {
        data: null,
        orderable: false,
        searchable: false,
        render: function(row) {
          return '<button class="btn btn-sm btn-outline-primary btn-detail" data-id="' + row.id + '"><i class="fas fa-eye"></i></button>';
        }
      }
    ],
    order: [[1, 'desc']]
  });

  $('#btnApply').on('click', function() {
    loadStats();
    dt.ajax.reload();
  });

  $('#btnExport').on('click', function() {
    const { from, to } = parseRange($range.val());
    const qs = $.param({ from: from, to: to });
    window.location = './ajax/audit/log_export_csv.php?' + qs;
  });

  // Row details

  $table.on('click', '.btn-detail', function() {
    const id = $(this).data('id');
    $.getJSON('./ajax/audit/log_detail.php', { id }, function(res) {
      console.log(res);

      // Replace modal body with dynamic table
      $('#auditDetailContainer').remove(); // remove old table if exists
      const tableHtml = `<div id="auditDetailContainer" class="table-responsive mt-3">
                          ${buildAuditTable(res.before, res.after)}
                         </div>`;
                         console.log(tableHtml);
      $modal.find('.modal-body').html(tableHtml);
      $modal.modal('show');
    });
  });

  // Build dynamic audit table
  function buildAuditTable(before, after) {
    // console.log(after);
    let html = `<table class="table table-bordered table-sm w-100">
                  <thead><tr><th>Field</th><th>Before</th><th>After</th></tr></thead>
                  <tbody>`;

    const allKeys = new Set([...Object.keys(before||{}), ...Object.keys(after||{})]);

    allKeys.forEach(key => {
      html += `<tr>
                 <td>${escapeHtml(key)}</td>
                 <td><pre style="white-space: pre-wrap; word-break: break-word; margin:0;">${escapeHtml(before?.[key]??'')}</pre></td>
                 <td><pre style="white-space: pre-wrap; word-break: break-word; margin:0;">${escapeHtml(after?.[key]??'')}</pre></td>
               </tr>`;
    });

    html += `</tbody></table>`;
    return html;
  }


  // KPI fetch
  function loadStats() {
    const { from, to } = parseRange($range.val());
    const params = {
      from: from,
      to: to,
      module_filter: $('#f_module').val(),
      action_filter: $('#f_action').val(),
      user_filter: $('#f_user').val()
    };
    $.getJSON('./ajax/audit/log_stats.php', params, function(resp) {
      $('#kpi_total').text(resp.total || 0);
      $('#kpi_create').text(resp.create || 0);
      $('#kpi_update').text(resp.update || 0);
      $('#kpi_delete').text(resp.delete || 0);
    });
  }

  // Utility: parse date range string into {from,to}
  function parseRange(s) {
    const parts = (s || '').split(' - ');
    return { from: parts[0] || '', to: parts[1] || '' };
  }
  // Escape HTML special chars
  function escapeHtml(str) {
    return String(str).replace(/[&<>"']/g, function(c) {
      return {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#39;'
      }[c] || c;
    });
  }

  // Initial KPI load
  loadStats();
});