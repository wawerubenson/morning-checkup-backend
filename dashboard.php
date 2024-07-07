<?php

include("header.php");

// Instantiate database and get connection
$database = new Config();
$db = $database->getConnection();

// Initialize Checkup object
$checkup = new Checkup($db);

// Get selected date
$date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');

// Fetch PBX checkups
$pbx_checkups_stmt = $checkup->getPBXCheckupsByDate($date);
$pbx_checkups = $pbx_checkups_stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch PBX checkups
$pbx_checkups_stmt = $checkup->getPBXCheckupsByDate($date);
$pbx_checkups = $pbx_checkups_stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch PBX storage remaining for previous day
$pbx_previous_day_stmt = $checkup->getPBXStorageRemainingPreviousDay($date);
$pbx_previous_day = $pbx_previous_day_stmt->fetchAll(PDO::FETCH_ASSOC);
$pbx_previous_day_map = [];
foreach ($pbx_previous_day as $row) {
    $pbx_previous_day_map[$row['pbx_name']] = $row['storage_remaining'];
}

// Fetch Synology checkups
$synology_checkups_stmt = $checkup->getSynologyCheckupsByDate($date);
$synology_checkups = $synology_checkups_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

    <div class="container mt-1">
        <div class="d-flex justify-content-between align-items-center">
            <h4 class="mb-0">Today's System Checkup</h4>
            <div>
                <label for="report-date" class="mr-2">Select Date:</label>
                <input type="date" id="report-date" class="form-control d-inline-block w-auto" value="<?= htmlspecialchars($date) ?>" onchange="updateReportDate()">
            </div>
        </div>

        <p class="text-center text-muted">Currently displaying report for: <span id="current-report-date"><?= htmlspecialchars($date) ?></span></p>

        <!-- Combined Checkup Table -->
        <div class="card mb-4">
            <div class="card-body p-0">
                <table class="table table-bordered">
                    <thead class="">
                        <tr>
                            <th scope="col" class="th-bg" colspan="8">PBX Checkup</th>
                        </tr>
                        <tr>
                            <th scope="col">PBX</th>
                            <th scope="col">Incoming Calls</th>
                            <th scope="col">Outgoing Calls</th>
                            <th scope="col">Total Storage</th>
                            <th scope="col">Storage Usage</th>
                            <th scope="col">Storage Remaining</th>
                            <th scope="col">Previous Day Storage Remaining</th>
                            <th scope="col">Web Interface</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($pbx_checkups) > 0) : ?>
                            <?php foreach ($pbx_checkups as $row) : ?>
                                <tr>
                                    <td> <?= htmlspecialchars($row['pbx_name']); ?> </td>
                                    <td><span class="badge badge-<?= $row['incoming_calls_status'] === 'working' ? 'success' : 'danger'; ?>"><?= htmlspecialchars($row['incoming_calls_status']); ?></span></td>
                                    <td><span class="badge badge-<?= $row['outgoing_calls_status'] === 'working' ? 'success' : 'danger'; ?>"><?= htmlspecialchars($row['outgoing_calls_status']); ?></span></td>
                                    <td><?= htmlspecialchars($row['total_storage']); ?></td>
                                    <td><?= htmlspecialchars($row['storage_used']); ?></td>
                                    <td><?= htmlspecialchars($row['storage_remaining']); ?></td>
                                    <td>
                                        <?php if (isset($pbx_previous_day_map[$row['pbx_name']])) : ?>
                                            <?= htmlspecialchars($pbx_previous_day_map[$row['pbx_name']]); ?>
                                        <?php else : ?>
                                            <span class="text-danger">No record</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><span class="badge badge-<?= $row['web_interface_status'] === 'accessible' ? 'success' : 'danger'; ?>"><?= htmlspecialchars($row['web_interface_status']); ?></span></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <tr>
                                <td colspan="8" class="text-center text-danger">No PBX records found for this date.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>


                <table class="table table-bordered mt-4">
                    <thead class="">
                        <tr>
                            <th class="th-bg" scope="col" colspan="6">Synology Checkup</th>
                        </tr>
                        <tr>
                            <th scope="col">Server</th>
                            <th scope="col">Hyper Backup</th>
                            <th scope="col">Yesterday's Recordings</th>
                            <th scope="col">Storage Usage</th>
                            <th scope="col">Storage Remaining</th>
                            <th scope="col">Web interface</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($synology_checkups) > 0) : ?>
                            <?php foreach ($synology_checkups as $row) : ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['synology_name']); ?></td>
                                    <td><span class="badge badge-<?= $row['hyperbackup_status'] === 'Running' ? 'success' : 'warning'; ?>"><?= htmlspecialchars($row['hyperbackup_status']); ?></span></td>
                                    <td><span class="badge badge-<?= $row['yesterdays_recordings_status'] === 'Available' ? 'success' : 'danger'; ?>"><?= htmlspecialchars($row['yesterdays_recordings_status']); ?></span></td>
                                    <td><?= htmlspecialchars($row['storage_usage']); ?></td>
                                    <td><?= htmlspecialchars($row['storage_remaining']); ?></td>

                                    <td><span class="badge badge-<?= $row['web_interface_status'] === 'Accessible' ? 'success' : 'danger'; ?>"><?= htmlspecialchars($row['web_interface_status']); ?></span></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <tr>
                                <td colspan="5" class="text-center text-danger">No Synology records found for this date.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        function updateReportDate() {
            const selectedDate = document.getElementById('report-date').value;
            window.location.href = 'dashboard.php?date=' + selectedDate;
        }
    </script>
</body>

</html>