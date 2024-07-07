<?php

include "header.php";

?>

<div class="container-fluid mt-1">

    <div class="form-wrapper container">
        <h4>Detailed System Checkup</h4>
        <!-- Date Range Picker Form -->
        <form id="dateRangeForm" class="mb-4 form-inline" method="POST" action="">
            <label for="dateRange" class="mr-2">Select Date Range:</label>
            <input type="text" id="dateRange" name="dateRange" class="form-control mr-2">
            <button type="submit" id="applyDateRange" class="btn btn-primary">Apply</button>
        </form>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered">
            <thead class="th-bg">
                <tr>
                    <th rowspan="2">Date & Time</th>
                    <th rowspan="2">Incoming calls</th>
                    <!-- PBX headers -->
                    <th colspan="3">PBX1</th>
                    <th colspan="3">PBX2</th>
                    <th colspan="3">PBX3</th>
                    <th colspan="3">PBX4</th>
                    <th colspan="3">PBX5</th>
                    <th colspan="3">PBX6</th>
                    <th colspan="3">PBX7</th>
                    <!-- Synology headers -->
                    <th colspan="4">Synology1 PKF</th>
                    <th colspan="4">Synology2</th>
                    <th colspan="4">XIVODRIVE</th>
                </tr>
                <tr>
                    <!-- PBX columns -->
                    <th>Calls</th>
                    <th>Used storage</th>
                    <th>Web</th>

                    <th>Calls</th>
                    <th>Used storage</th>
                    <th>Web</th>

                    <th>Calls</th>
                    <th>Used storage</th>
                    <th>Web</th>

                    <th>Calls</th>
                    <th>Used storage</th>
                    <th>Web</th>

                    <th>Calls</th>
                    <th>Used storage</th>
                    <th>Web</th>

                    <th>Calls</th>
                    <th>Used storage</th>
                    <th>Web</th>

                    <th>Calls</th>
                    <th>Used storage</th>
                    <th>Web</th>


                    <!-- Synology columns -->
                    <th>Backup</th>
                    <th>Storage</th>
                    <th>Recordings</th>
                    <th>Web</th>

                    <!-- Synology columns -->
                    <th>Backup</th>
                    <th>Available Storage</th>
                    <th>Recordings</th>
                    <th>Web</th>

                    <!-- Synology columns -->
                    <th>Backup</th>
                    <th>Storage</th>
                    <th>Recordings</th>
                    <th>Web</th>

                </tr>
            </thead>
            <tbody id="dataRows">
                <?php
                require_once './Classes/Config.php';
                require_once './Classes/Details.php';

                $database = new Config();
                $db = $database->getConnection();
                $checkup = new Details($db);

                // Default date range (last 7 days)
                $endDate = date('Y-m-d');
                $startDate = date('Y-m-d', strtotime('-7 days'));
                $displayStartDate = date('F d, Y', strtotime('-7 days'));
                $displayEndDate = date('F d, Y');

                if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['dateRange'])) {
                    $dateRange = $_POST['dateRange'];
                    $dates = explode(' - ', $dateRange);
                    $startDate = date('Y-m-d', strtotime($dates[0]));
                    $endDate = date('Y-m-d', strtotime($dates[1]));
                    $displayStartDate = date('F d, Y', strtotime($dates[0]));
                    $displayEndDate = date('F d, Y', strtotime($dates[1]));
                }

                // Calculate the number of days
                $start = new DateTime($startDate);
                $end = new DateTime($endDate);
                $interval = $start->diff($end);
                $days = $interval->days;

                // Display the date range and the number of days
                echo "<p class='text-center'>Displaying entries for the period between <strong>{$displayStartDate}</strong> and <strong>{$displayEndDate}</strong> ({$days} days)</p>";

                $pbxData = $checkup->getPBXCheckups($startDate, $endDate);
                $synologyData = $checkup->getSynologyCheckups($startDate, $endDate);

                // Group data by date
                $groupedData = [];
                //var_dump($pbxData);

                /* 
                    loop through each entry in $pbxData, 
                    extract date value for each
                    checks if $date is already a key in the $groupedData array
                    If $date does not exist in $groupedData, it initializes it as a key with an empty array for both 'pbx' and 'synology'
                    append the current element ($pbx) to the 'pbx' array under the $date key in $groupedData

                    PURPOSE OF THE LOOP
                    group elements from $pbxData based on their 'date' value into $groupedData. Each unique 'date' will have an entry in $groupedData, and under each 'date', there will be an array containing all elements from $pbxData that share that 'date'.
                    */
                foreach ($pbxData as $pbx) {
                    $date = $pbx['date'];
                    if (!isset($groupedData[$date])) {
                        $groupedData[$date] = [
                            'pbx' => [],
                            'synology' => []
                        ];
                    }
                    $groupedData[$date]['pbx'][] = $pbx;
                }

                foreach ($synologyData as $synology) {
                    $date = $synology['date'];
                    if (!isset($groupedData[$date])) {
                        $groupedData[$date] = [
                            'pbx' => [],
                            'synology' => []
                        ];
                    }
                    $groupedData[$date]['synology'][] = $synology;
                }

                foreach ($groupedData as $date => $data) {
                    echo "<tr>";
                    echo "<td>{$date}</td>";

                    // Display PBX data
                    echo "<td>Working</td>";
                    foreach ($data['pbx'] as $pbx) {
                        echo "<td>{$pbx['outgoing_calls_status']}</td>";
                        echo "<td>{$pbx['total_storage']}</td>";
                        echo "<td>{$pbx['web_interface_status']}</td>";
                    }

                    // Display Synology data
                    foreach ($data['synology'] as $synology) {
                        echo "<td>{$synology['hyperbackup_status']}</td>";
                        echo "<td>{$synology['storage_remaining']}</td>";
                        echo "<td>{$synology['yesterdays_recordings_status']}</td>";
                        echo "<td>{$synology['web_interface_status']}</td>";
                    }

                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    $(function() {
        var start = moment().subtract(7, 'days');
        var end = moment();

        // Check if the user has selected a date range
        <?php if (isset($_POST['dateRange'])) : ?>
            var selectedStart = moment("<?php echo $dates[0]; ?>", "MMMM D, YYYY");
            var selectedEnd = moment("<?php echo $dates[1]; ?>", "MMMM D, YYYY");
            start = selectedStart;
            end = selectedEnd;
        <?php endif; ?>

        function cb(start, end) {
            $('#dateRange').val(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
        }

        $('#dateRange').daterangepicker({
            startDate: start,
            endDate: end,
            opens: 'left',
            locale: {
                format: 'MMMM D, YYYY'
            }
        }, cb);

        cb(start, end);
    });
</script>

<!-- jQuery, Popper.js, Bootstrap JS, and Date Range Picker JS -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

</body>

</html>