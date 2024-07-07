<?php
session_start();
include_once './Classes/Config.php';
include_once './Classes/Checkup.php';

// Check if user is not logged in, redirect to login page
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Logout logic
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: login.php");
    exit();
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Server Insights</title>
    <link rel="icon" type="image/x-icon" href="logo.jpeg">
    <!-- Include Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="css/style.css">
    <style>
        /* Flexbox container */
        .chart-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-around;
        }

        /* Adjust canvas size */
        canvas {
            max-width: 500px;
            /* Adjust as per your layout */
            margin: 20px;
            display: block;
        }
    </style>
</head>

<body>

    <header class="text-white py-2">
        <div class="container d-flex align-items-center justify-content-between">
            <!-- Logo Section -->
            <div class="d-flex align-items-center">
                <img src="logo.jpeg" alt="Company Logo" class="mr-3" style="height: 50px;">
                <h2 class="mb-0">Apollo <br> Agriculture</h2>
            </div>

            <!-- Navigation Links Section -->
            <div class="d-flex align-items-center">
                <a href="dashboard.php" class="text-white mx-3">Home</a>
                <a href="insights.php" class="text-white mx-3">Insights</a>

            </div>

            <!-- Buttons Section -->
            <div>
                <?php if (isset($_SESSION['user_id'])) : ?>
                    <span class="text-white"><?= htmlspecialchars($_SESSION['first_name']); ?></span>
                    <a href="dashboard.php?logout=true" class="btn btn-warning ml-2">Logout</a>
                <?php else : ?>
                    <button class="btn btn-outline-light mr-2" onclick="location.href='register.php'">Signup</button>
                    <button class="btn btn-warning" onclick="location.href='login.php'">Login</button>
                <?php endif; ?>
            </div>
        </div>
    </header>

    
    <h3 class="text-center mb-2">Server Insights</h3>

    <?php
    // Database connection (example using PDO)
    $host = 'localhost'; // Your MySQL host
    $db = 'morning_checkup'; // Your database name
    $user = 'benson'; // Your database username
    $password = 'kogi254'; // Your database password

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Query to fetch Synology data for the last 1 month (adjust as needed)
        $synologyQuery = "SELECT synology_name, hyperbackup_status, yesterdays_recordings_status, web_interface_status 
        FROM synology_checkups 
        WHERE date BETWEEN DATE_SUB(CURDATE(), INTERVAL 1 MONTH) AND CURDATE()";

        // Prepare Synology query
        $synologyStmt = $pdo->prepare($synologyQuery);
        $synologyStmt->execute();
        $synologyData = $synologyStmt->fetchAll(PDO::FETCH_ASSOC);

        // Prepare data for Chart.js for Synology
        $synologyNames = [];
        $hyperBackup = [];
        $yesterdayRecordings = [];
        $synologyWebInterface = [];

        foreach ($synologyData as $synology) {
            $synologyNames[] = $synology['synology_name'];
            $hyperBackup[] = $synology['hyperbackup_status'] == 'Running' ? 1 : 0;
            $yesterdayRecordings[] = $synology['yesterdays_recordings_status'] == 'Available' ? 1 : 0;
            $synologyWebInterface[] = $synology['web_interface_status'] == 'Accessible' ? 1 : 0;
        }

        // Query to count PBX statuses for the last 1 month
        $pbxQuery = "
            SELECT pbx_name, 
                   SUM(incoming_calls_status = 'Working') AS incoming_working,
                   SUM(incoming_calls_status != 'Working') AS incoming_not_working,
                   SUM(outgoing_calls_status = 'Working') AS outgoing_working,
                   SUM(outgoing_calls_status != 'Working') AS outgoing_not_working,
                   SUM(web_interface_status = 'Accessible') AS web_accessible,
                   SUM(web_interface_status != 'Accessible') AS web_not_accessible
            FROM pbx_checkups 
            WHERE date BETWEEN DATE_SUB(CURDATE(), INTERVAL 1 MONTH) AND CURDATE()
            GROUP BY pbx_name
            LIMIT 7"; // Limit to 7 PBX entries

        // Prepare PBX query
        $pbxStmt = $pdo->prepare($pbxQuery);
        $pbxStmt->execute();
        $pbxData = $pbxStmt->fetchAll(PDO::FETCH_ASSOC);

        // Close connection
        $pdo = null;

        // Prepare data for Chart.js for PBX
        $pbxNames = [];
        $incomingWorking = [];
        $incomingNotWorking = [];
        $outgoingWorking = [];
        $outgoingNotWorking = [];
        $webAccessible = [];
        $webNotAccessible = [];

        foreach ($pbxData as $pbx) {
            $pbxNames[] = $pbx['pbx_name'];
            $incomingWorking[] = (int)$pbx['incoming_working'];
            $incomingNotWorking[] = (int)$pbx['incoming_not_working'];
            $outgoingWorking[] = (int)$pbx['outgoing_working'];
            $outgoingNotWorking[] = (int)$pbx['outgoing_not_working'];
            $webAccessible[] = (int)$pbx['web_accessible'];
            $webNotAccessible[] = (int)$pbx['web_not_accessible'];
        }
    ?>

        <!-- Display PBX Insights -->
        <div class="chart-container">
            <div>
                <p>PBX Incoming Calls Status (Last Month)</p>
                <canvas id="incomingCallsChart" width="400" height="200"></canvas>
            </div>

            <div>
                <p>PBX Outgoing Calls Status (Last Month)</p>
                <canvas id="outgoingCallsChart" width="400" height="200"></canvas>
            </div>

            <div>
                <p>PBX Web Interface Status (Last Month)</p>
                <canvas id="webInterfaceChart" width="400" height="200"></canvas>
            </div>
        </div>

        <script>
            var ctx1 = document.getElementById('incomingCallsChart').getContext('2d');
            var incomingCallsChart = new Chart(ctx1, {
                type: 'bar',
                data: {
                    labels: <?php echo json_encode($pbxNames); ?>,
                    datasets: [{
                        label: 'Working',
                        backgroundColor: 'rgba(75, 192, 192, 0.6)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1,
                        data: <?php echo json_encode($incomingWorking); ?>
                    }, {
                        label: 'Not Working',
                        backgroundColor: 'rgba(255, 99, 132, 0.6)',
                        borderColor: 'rgba(255, 99, 132, 1)',
                        borderWidth: 1,
                        data: <?php echo json_encode($incomingNotWorking); ?>
                    }]
                },
                options: {
                    scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero: true,
                                stepSize: 1 // Force integer ticks
                            }
                        }]
                    }
                }
            });

            var ctx2 = document.getElementById('outgoingCallsChart').getContext('2d');
            var outgoingCallsChart = new Chart(ctx2, {
                type: 'bar',
                data: {
                    labels: <?php echo json_encode($pbxNames); ?>,
                    datasets: [{
                        label: 'Working',
                        backgroundColor: 'rgba(54, 162, 235, 0.6)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1,
                        data: <?php echo json_encode($outgoingWorking); ?>
                    }, {
                        label: 'Not Working',
                        backgroundColor: 'rgba(255, 206, 86, 0.6)',
                        borderColor: 'rgba(255, 206, 86, 1)',
                        borderWidth: 1,
                        data: <?php echo json_encode($outgoingNotWorking); ?>
                    }]
                },
                options: {
                    scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero: true,
                                stepSize: 1 // Force integer ticks
                            }
                        }]
                    }
                }
            });

            var ctx3 = document.getElementById('webInterfaceChart').getContext('2d');
            var webInterfaceChart = new Chart(ctx3, {
                type: 'bar',
                data: {
                    labels: <?php echo json_encode($pbxNames); ?>,
                    datasets: [{
                        label: 'Accessible',
                        backgroundColor: 'rgba(153, 102, 255, 0.6)',
                        borderColor: 'rgba(153, 102, 255, 1)',
                        borderWidth: 1,
                        data: <?php echo json_encode($webAccessible); ?>
                    }, {
                        label: 'Not Accessible',
                        backgroundColor: 'rgba(255, 159, 64, 0.6)',
                        borderColor: 'rgba(255, 159, 64, 1)',
                        borderWidth: 1,
                        data: <?php echo json_encode($webNotAccessible); ?>
                    }]
                },
                options: {
                    scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero: true,
                                stepSize: 1 // Force integer ticks
                            }
                        }]
                    }
                }
            });
        </script>

        <!-- Display Synology Insights -->



    <?php
    } catch (PDOException $e) {
        echo "Connection failed: " . $e->getMessage();
    }
    ?>
</body>

</html>