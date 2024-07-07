<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="img/logo.jpeg">
    <title>Register</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <?php
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            include_once './Classes/Config.php';
            include_once './Classes/User.php';

            $database = new Config();
            $db = $database->getConnection();

            $user = new User($db);

            $user->first_name = $_POST['first_name'];
            $user->last_name = $_POST['last_name'];
            $user->email = $_POST['email'];
            $user->password = $_POST['password'];
            $repeat_password = $_POST['repeat_password'];
            $user->created_at = date('Y-m-d H:i:s');

            if ($user->emailExists()) {
                echo '<div class="alert alert-danger">Email already exists.</div>';
            } elseif ($user->password !== $repeat_password) {
                echo '<div class="alert alert-danger">Passwords do not match.</div>';
            } else {
                if ($user->register()) {
                    echo '<div class="alert alert-success">Registration successful.</div>';
                    header('Location: login.php');
                    exit;
                } else {
                    echo '<div class="alert alert-danger">Unable to register. Please try again.</div>';
                }
            }
        }
        ?>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header th-bg text-center">
                        Register
                    </div>
                    <div class="card-body">
                        <form method="POST" action="register.php">
                            <div class="form-group">
                                <label for="first_name">First Name</label>
                                <input type="text" name="first_name" class="form-control" id="first_name" required>
                            </div>
                            <div class="form-group">
                                <label for="last_name">Last Name</label>
                                <input type="text" name="last_name" class="form-control" id="last_name" required>
                            </div>
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" name="email" class="form-control" id="email" required>
                            </div>
                            <div class="form-group">
                                <label for="password">Password</label>
                                <input type="password" name="password" class="form-control" id="password" required>
                            </div>

                            <div class="form-group">
                                <label for="repeat_password">Repeat Password</label>
                                <input type="password" name="repeat_password" class="form-control" id="repeat_password"
                                    required>
                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn btn-secondary btn-block">Signup</button>
                                <p class="text-center pt-2"> Already have an account? <a href="login.php">Signin</a> </p>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>



</body>

</html>