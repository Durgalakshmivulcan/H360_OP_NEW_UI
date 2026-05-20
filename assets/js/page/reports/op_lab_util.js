/* /assets/js/page/reports/op_lab_util.js */
$(function () {
  // --- Setup: Date range
  const $drp = $("#f_daterange");
  const supportsDRP = !!$.fn.daterangepicker;

  if (supportsDRP) {
    $drp.daterangepicker({
      autoUpdateInput: true,
      startDate: moment().subtract(6, "days"),
      endDate: moment(),
      locale: { format: "YYYY-MM-DD" },
    });
  } else {
    $drp.val(
      moment().subtract(6, "days").format("YYYY-MM-DD") +
      " - " +
      moment().format("YYYY-MM-DD")
    );
  }

  function parseRange(s) {
    const p = (s || "").split(" - ");
    return { from: p[0] || "", to: p[1] || "" };
  }

  // --- DataTable
  const $table = $("#utilTable");
  const dt = $table.DataTable({
    serverSide: false,
    paging: true,
    searching: true,
    ordering: true,
    columns: [
      { data: "test_name" },
      { data: "orders", className: "text-end" },
    ],
    order: [[1, "desc"]],
    data: [],
    language: { emptyTable: "Use filters and click Apply to see results" },
  });

  // --- Column visibility toggles
  $(".col-toggle").on("change", function () {
    const idx = parseInt(this.value, 10);
    dt.column(idx).visible(this.checked);
  });

  // --- Quick ranges
  $(".quick-range").on("click", function () {
    const key = $(this).data("range");
    let from = moment(),
      to = moment();
    if (key === "7d") from = moment().subtract(6, "days");
    else if (key === "30d") from = moment().subtract(29, "days");
    else if (key === "qtd") from = moment().startOf("quarter");
    else if (key === "ytd") from = moment().startOf("year");

    if (supportsDRP) $drp.data("daterangepicker").setStartDate(from);
    if (supportsDRP) $drp.data("daterangepicker").setEndDate(to);

    $drp.val(from.format("YYYY-MM-DD") + " - " + to.format("YYYY-MM-DD"));
  });

  // --- Populate doctor dropdown
  function populateDoctorDropdown() {
    $.ajax({
      url: "./ajax/opReports/op_lab_util_list.php",
      type: "POST",
      dataType: "json",
      data: { action: "doctors" },

      success: function (resp) {
        // console.log("Doctors response:", resp);
        var $doctorSelect = $("#f_doctor");
        // remove old options except default
        $doctorSelect.find("option:not([value=''])").remove();

        // Handle both array or object format
        var doctors = resp.doctors || resp;

        if (doctors && doctors.length) {
          doctors.forEach(function (doctor) {
            $doctorSelect.append(
              $("<option>", {
                value: doctor.doc_id,       // store ID
                text: doctor.doctor_name    // show name
              })
            );
          });
        }
      },
      error: function (xhr, status, error) {
        // console.error("Failed to load doctors:", error);
      },
    });
  }

  function populateTestDropdown() {
    $.ajax({
      url: "./ajax/opReports/op_lab_util_list.php",
      type: "POST",
      dataType: "json",
      data: { action: "tests" },

      success: function (resp) {
        // console.log("Tests response:", resp);
        var $testSelect = $("#f_test_search");
        // remove old options except default
        $testSelect.find("option:not([value=''])").remove();

        // Handle both array or object format
        var tests = resp.tests || resp;

        if (tests && tests.length) {
          tests.forEach(function (test) {
            $testSelect.append(
              $("<option>", {
                value: test.test_id || test.test_name, // use ID if available
                text: test.test_name + ""
              })
            );
          });
        }
      },
      error: function (xhr, status, error) {
        // console.error("Failed to load tests:", error);
      },
    });
  }

  $(document).ready(function () {
    // Initialize select2
    $("#f_doctor").select2({
      placeholder: "Select a doctor",
      allowClear: true,
      width: "100%"
    });

    $("#f_test_search").select2({
      placeholder: "Select a test",
      allowClear: true,
      width: "100%"
    });

    // Load dropdown data
    populateDoctorDropdown();
    populateTestDropdown();
  });






  // --- AJAX function to get table data
  function getLabTests() {
    const { from, to } = parseRange($drp.val());
    $.ajax({
      url: "./ajax/opReports/op_lab_util_list.php",
      type: "POST",
      dataType: "json",
      data: {
        action: "data", // action to get table data
        from: from,
        to: to,
        doctor: $("#f_doctor").val(),
        test_search: $("#f_test_search").val(),
      },
      success: function (data) {
        dt.clear().rows.add(data).draw();
        toggleEmpty(!data || data.length === 0);
      },
      error: function (err) {
        // console.error(err);
        dt.clear().draw();
        toggleEmpty(true);
      },
    });
  }

  // --- Apply
  $("#btnApply").on("click", function () {
    getLabTests();
  });

  // --- Export (placeholder)
  // --- Export CSV
  $("#btnExport").on("click", function () {
    const data = dt.rows({ search: "applied" }).data().toArray();
    if (!data.length) {
      // alert("No data available to export.");
      return;
    }

    let csv = "Test Name,Orders\n";
    data.forEach(row => {
      csv += `"${row.test_name}","${row.orders}"\n`;
    });

    const blob = new Blob([csv], { type: "text/csv;charset=utf-8;" });
    const url = URL.createObjectURL(blob);
    const link = document.createElement("a");
    link.href = url;
    link.download = "lab_utilization.csv";
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
  });




  // --- Reset
  $("#btnReset").on("click", function () {
    // Reset date range
    const defaultFrom = moment().subtract(6, "days");
    const defaultTo = moment();

    if (supportsDRP) {
      $drp.data("daterangepicker").setStartDate(defaultFrom);
      $drp.data("daterangepicker").setEndDate(defaultTo);
    }
    $drp.val(defaultFrom.format("YYYY-MM-DD") + " - " + defaultTo.format("YYYY-MM-DD"));

    // Reset select2 dropdowns (must trigger change for UI refresh)
    $("#f_doctor").val(null).trigger("change");
    $("#f_test_search").val(null).trigger("change");

    // Clear datatable
    dt.clear().draw();

    // Show empty state
    toggleEmpty(true);
  });

  // --- Empty state helper
  function toggleEmpty(isEmpty) {
    $("#emptyState").toggleClass("d-none", !isEmpty);
    $(".table-responsive").toggleClass("d-none", isEmpty);
  }
  
  getLabTests();
});


// --- Save View
$("#btnSaveView").on("click", function () {
  const drpVal = $("#f_daterange").val().split(" - ");
  const from = drpVal[0] || "";
  const to = drpVal[1] || "";

  const view = {
    from: from,
    to: to,
    doctor: $("#f_doctor").val(),
    test: $("#f_test_search").val(),
  };

  localStorage.setItem("lab_util_saved_view", JSON.stringify(view));
  alert("Current view has been saved!");
});



// --- Load saved view (optional, auto-apply on page load)
function loadSavedView() {
  const saved = localStorage.getItem("lab_util_saved_view");
  if (!saved) return;

  try {
    const view = JSON.parse(saved);

    if (view.from && view.to) {
      $("#f_daterange").val(view.from + " - " + view.to);
      if (supportsDRP) {
        $drp.data("daterangepicker").setStartDate(view.from);
        $drp.data("daterangepicker").setEndDate(view.to);
      }
    }

    if (view.doctor) {
      $("#f_doctor").val(view.doctor).trigger("change");
    }

    if (view.test) {
      $("#f_test_search").val(view.test).trigger("change");
    }

    // Auto-apply data
    getLabTests();
  } catch (e) {
    console.error("Failed to load saved view:", e);
  }
}

// Call on page load
$(document).ready(function () {
  
  loadSavedView();
});

