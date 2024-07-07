
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