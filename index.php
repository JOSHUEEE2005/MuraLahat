<?php 
session_start();
require_once('classes/database.php');

$sweetAlertConfig = "";
$con = new database();

if (isset($_POST['login'])) {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    $result = $con->loginUser($username, $password);

    if ($result) {
        $_SESSION['user_id'] = $result['user_id'];
        $_SESSION['position'] = $result['position'];
        $redirectUrl = $result['position'] === 'Admin' ? 'admin_dashboard.php' : 'view_products.php';
        $sweetAlertConfig = "<script>
            Swal.fire({
                icon: 'success',
                title: 'Login Successful',
                text: 'Welcome!',
                confirmButtonText: 'Continue',
                customClass: { confirmButton: 'btn btn-primary' },
                buttonsStyling: false
            }).then(() => {
                window.location.href = '$redirectUrl';
            });
        </script>";
    } else {
        $sweetAlertConfig = "<script>
            Swal.fire({
                icon: 'error',
                title: 'Login Failed',
                text: 'Invalid username or password.',
                confirmButtonText: 'OK',
                customClass: { confirmButton: 'btn btn-primary' },
                buttonsStyling: false
            });
        </script>";
    }
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Inventory Management System</title>
    <!-- Bootstrap CSS (CDN) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <!-- SweetAlert2 CSS (CDN) -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <style>
        .custom-container {
            max-width: 500px;
            margin: auto;
            padding: 20px;
        }
    </style>
</head>
<body>
    <div class="container custom-container rounded-3 shadow p-4 bg-light mt-5">
        <h3 class="text-center mb-4">Login</h3>
        <form method="post" action="" novalidate>
            <div class="form-group mb-3">
                <label for="username">Username:</label>
                <input type="text" class="form-control" name="username" placeholder="Enter username" required>
                <div class="valid-feedback">Looks good!</div>
                <div class="invalid-feedback">Please enter a valid username.</div>
            </div>
            <div class="form-group mb-3">
                <label for="password">Password:</label>
                <input type="password" class="form-control" name="password" placeholder="Enter password" required>
                <div class="valid-feedback">Looks good!</div>
                <div class="invalid-feedback">Please enter your password.</div>
            </div>
            <button type="submit" name="login" class="btn btn-primary w-100 py-2">Login</button>
            <div class="text-center mt-4">
                <a href="Registration.php" class="text-decoration-none">Don't have an account? Register here</a>
            </div>
        </form>
    </div>
    <!-- Bootstrap JS (CDN) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <!-- SweetAlert2 JS (CDN) -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    <?php echo $sweetAlertConfig; ?>
</body>
</html>