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

// Instantiate database and get connection
$database = new Config();
$db = $database->getConnection();

// Initialize Checkup object
$checkup = new Checkup($db);

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daily System Status</title>
    <link rel="icon" type="image/x-icon" href="logo.jpeg">
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- jQuery and Date Range Picker CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <link rel="stylesheet" href="/css/style.css">
    <style>
        body {
            box-sizing: border-box;
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            margin: 0;
            padding: 0;
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
                <a href="dashboard.php" class="text-white mx-3">Today</a>
                <a href="detailed.php" class="text-white mx-3">Detailed</a>
                <a href="overview.php" class="text-white mx-3">Overview</a>
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
    <div class="container">

        <div class="form-wrapper mt-3">
            <h4 class="mb-4">PBX System Status</h4>

            <!-- Date range selection form -->
            <form id="dateRangeForm">
                <div class="form-group">
                    <label for="startDate">Select Date Range:</label>
                    <input type="date" id="startDate" name="startDate" value="2024-07-01">
                    <span>to</span>
                    <input type="date" id="endDate" name="endDate" value="2024-07-07">
                    <button type="submit" class="btn btn-primary">View Stats</button>
                </div>
            </form>
        </div>

        <!-- Date range message -->
        <h3 id="dateRangeMessage">Viewing for the last 7 days</h3>

        <!-- Table to display PBX system status -->
        <table id="pbxStatusTable" class="table table-bordered">
            <thead class="th-bg">
                <tr>
                    <th>System</th>
                    <th>Incoming Calls</th>
                    <th>Outgoing Calls</th>
                    <th>Web Interface</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <!-- Data rows will be dynamically populated -->
            </tbody>
        </table>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <!-- jQuery (optional, needed for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#dateRangeForm').submit(function(event) {
                event.preventDefault();
                updateTable();
            });

            function updateTable() {
                var startDate = $('#startDate').val();
                var endDate = $('#endDate').val();

                // Example: Calculate status based on selected dates (dummy data)
                var pbxData = [{
                        system: 'PBX1',
                        incomingCalls: '3 issues',
                        outgoingCalls: '4 issues',
                        webInterface: '5 issues',
                        status: 'warning'
                    },
                    {
                        system: 'PBX2',
                        incomingCalls: 'No issues',
                        outgoingCalls: '1 issue',
                        webInterface: 'No issues',
                        status: 'good'
                    }
                    // Add more PBX systems as needed
                ];

                // Clear existing table rows
                $('#pbxStatusTable tbody').empty();

                // Populate table with updated data
                pbxData.forEach(function(item) {
                    var statusClass = '';
                    if (item.status === 'good') {
                        statusClass = 'status-good';
                    } else if (item.status === 'warning') {
                        statusClass = 'status-warning';
                    } else if (item.status === 'bad') {
                        statusClass = 'status-bad';
                    }

                    var newRow = '<tr>' +
                        '<td>' + item.system + '</td>' +
                        '<td>' + item.incomingCalls + '</td>' +
                        '<td>' + item.outgoingCalls + '</td>' +
                        '<td>' + item.webInterface + '</td>' +
                        '<td><span class="badge badge-' + statusClass + '">' + item.status + '</span></td>' +
                        '</tr>';

                    $('#pbxStatusTable tbody').append(newRow);
                });

                // Update the date range message above the table
                var dateRangeMessage = 'Viewing for the last ' + calculateDaysDifference(startDate, endDate) + ' days';
                $('#dateRangeMessage').text(dateRangeMessage);
            }

            // Function to calculate days difference
            function calculateDaysDifference(startDate, endDate) {
                var start = new Date(startDate);
                var end = new Date(endDate);
                var difference = Math.floor((end - start) / (1000 * 60 * 60 * 24)) + 1; // +1 to include the end date
                return difference;
            }

            // Initial table update on page load
            updateTable();
        });
    </script>
</body>

</html>