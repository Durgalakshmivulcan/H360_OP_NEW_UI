<?php

require_once("../../config/functions.php");

?>

<?php
$currentYear = date("Y");
$yearsToShow = 5;
$startYear = $currentYear - $yearsToShow;
$endYear = $currentYear + $yearsToShow;
?>

<select class="form-select" name="year" id="year" onchange="renderChart(this.value)">
    <?php
    for ($year = $startYear; $year <= $endYear; $year++) {
        $selected = ($year == $currentYear) ? 'selected' : '';
        echo "<option value='$year' $selected>$year</option>";
    }
    ?>
</select>

<?php

$getappoint = mysqli_query($conn, "SELECT appoint_date FROM appointment_online WHERE appoint_id");
$monthYearCountArray = array();

while ($resappoint = mysqli_fetch_object($getappoint)) {

    $shortMonth = date("M", strtotime($resappoint->appoint_date));
    
    if (isset($monthYearCountArray[$shortMonth])) {
        $monthYearCountArray[$shortMonth]++;
    } else {
        $monthYearCountArray[$shortMonth] = 1;
    }
}

$allShortMonths = array('Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec');

foreach ($allShortMonths as $shortMonth) {
    if (!isset($monthYearCountArray[$shortMonth])) {
        $monthYearCountArray[$shortMonth] = 0;
    }
}

$sortedMonthYearCountArray = array();
foreach ($allShortMonths as $shortMonth) {
    $sortedMonthYearCountArray[$shortMonth] = $monthYearCountArray[$shortMonth];
}

$shortMonths = array_keys($sortedMonthYearCountArray);
$countData = array_values($sortedMonthYearCountArray);

$jsonShortMonths = json_encode($shortMonths);
$jsonCountData = json_encode($countData);
?>


<script>
      var jsonMonthsYears = <?php echo $jsonShortMonths; ?>;
var jsonCountData = <?php echo $jsonCountData; ?>;

// Function to render the chart with provided data
function renderChart(selectedYear) {
    // Fetch data for the selected year from the server using AJAX
    $.ajax({
        url: 'ajax/GetChart.php', // Replace with the actual URL to fetch data
        method: 'POST',
        data: { year: selectedYear },
        dataType: 'json',
        success: function(response) {
            var options = {
                series: [{
                    name: "Appointments",
                    data: response.countData
                }],
                chart: {
                    height: 270,
                    type: 'line',
                    zoom: {
                        enabled: false
                    }
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    curve: 'straight'
                },
                title: {
                    text: 'Product Trends by Month for ' + selectedYear,
                    align: 'left'
                },
                grid: {
                    row: {
                        colors: ['#f3f3f3', 'transparent'],
                        opacity: 0.5
                    },
                },
                xaxis: {
                    categories: response.shortMonths,
                    labels: {
                        show: true 
                    }
                }
            };

            var chart = new ApexCharts(document.querySelector("#chart"), options);
            chart.render();
        }
    });
}

// Initialize the chart with the current year's data
renderChart(<?php echo $currentYear; ?>);
</script>