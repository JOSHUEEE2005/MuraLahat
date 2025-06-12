<?php
session_start();
require_once('classes/database.php');
$con = new database();

if (!isset($_SESSION['user_id']) || !isset($_SESSION['position'])) {
    header('Location: index.php');
    exit;
}

// Fetch all users with their positions
$users = $con->getAllUsersWithPositions();

// Define valid positions for dropdown
$valid_positions = ['Admin', 'Cashier', 'Staff', 'Sales Lady', 'Manager'];

$sweetAlertConfig = "";
if (isset($_POST['update_position'])) {
    $user_id = filter_input(INPUT_POST, 'user_id', FILTER_VALIDATE_INT);
    $new_position = filter_input(INPUT_POST, 'position', FILTER_SANITIZE_STRING);

    if ($user_id && $new_position && in_array($new_position, $valid_positions)) {
        $result = $con->updateUserPosition($user_id, $new_position);
        if ($result['success']) {
            $sweetAlertConfig = "
            <script>
            Swal.fire({
                icon: 'success',
                title: 'Position Updated',
                text: 'User position has been updated successfully!',
                confirmButtonText: 'OK',
                customClass: { confirmButton: 'btn btn-primary' },
                buttonsStyling: false
            }).then(() => {
                window.location.href = 'manage_users.php';
            });
            </script>";
        } else {
            $sweetAlertConfig = "
            <script>
            Swal.fire({
                icon: 'error',
                title: 'Update Failed',
                text: '" . addslashes($result['error']) . "',
                confirmButtonText: 'OK',
                customClass: { confirmButton: 'btn btn-primary' },
                buttonsStyling: false
            });
            </script>";
        }
    } else {
        $sweetAlertConfig = "
        <script>
        Swal.fire({
            icon: 'error',
            title: 'Invalid Input',
            text: 'Please select a valid user and position.',
            confirmButtonText: 'OK',
            customClass: { confirmButton: 'btn btn-primary' },
            buttonsStyling: false
        });
        </script>";
    }
}

if (isset($_GET['delete_user'])) {
    $user_id = filter_input(INPUT_GET, 'delete_user', FILTER_VALIDATE_INT);
    if ($user_id) {
        $result = $con->deleteUser($user_id);
        if ($result['success']) {
            $sweetAlertConfig = "
            <script>
            Swal.fire({
                icon: 'success',
                title: 'User Deleted',
                text: 'User has been deleted successfully!',
                confirmButtonText: 'OK',
                customClass: { confirmButton: 'btn btn-primary' },
                buttonsStyling: false
            }).then(() => {
                window.location.href = 'manage_users.php';
            });
            </script>";
        } else {
            $sweetAlertConfig = "
            <script>
            Swal.fire({
                icon: 'error',
                title: 'Deletion Failed',
                text: '" . addslashes($result['error']) . "',
                confirmButtonText: 'OK',
                customClass: { confirmButton: 'btn btn-primary' },
                buttonsStyling: false
            });
            </script>";
        }
    }
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Mura Lahat | Manage Users</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.min.js"></script>
    <style>
        .custom-container {
            max-width: 900px;
            margin: auto;
            padding: 20px;
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            margin-top: 30px;
        }
        .table th, .table td {
            vertical-align: middle;
        }
        .btn-primary {
            background-color: #ff6b6b;
            border-color: #ff6b6b;
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            background-color: #ff8787;
            border-color: #ff8787;
        }
        .btn-danger {
            background-color: #dc3545;
            border-color: #dc3545;
        }
        .btn-danger:hover {
            background-color: #c82333;
            border-color: #c82333;
        }
        .navbar-brand, .nav-link {
            color: #fff !important;
        }
        .navbar {
            background: rgb(8, 8, 8);
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Mura Lahat</a>
            <div class="navbar-nav">
                <a class="nav-link" href="<?php echo $_SESSION['position'] === 'Admin' ? 'admin_dashboard.php' : 'view_products.php'; ?>">Back to Dashboard</a>
            </div>
        </div>
    </nav>

    <div class="container custom-container">
        <h3 class="text-center mb-4">Manage Users</h3>
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>Username</th>
                    <th>Position</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($user['Username']); ?></td>
                        <td>
                            <form method="post" action="" class="d-inline">
                                <input type="hidden" name="user_id" value="<?php echo $user['User_Account_ID']; ?>">
                                <select name="position" class="form-select form-select-sm d-inline w-auto">
                                    <?php foreach ($valid_positions as $position): ?>
                                        <option value="<?php echo htmlspecialchars($position); ?>" <?php echo $user['POSITION'] === $position ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($position); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <button type="submit" name="update_position" class="btn btn-primary btn-sm">Update</button>
                            </form>
                        </td>
                        <td>
                            <button class="btn btn-danger btn-sm delete-btn" data-user-id="<?php echo $user['User_Account_ID']; ?>">Delete</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.delete-btn').on('click', function() {
                const userId = $(this).data('user-id');
                Swal.fire({
                    icon: 'warning',
                    title: 'Are you sure?',
                    text: 'This will permanently delete user!',
                    showCancelButton: true,
                    confirmButtonText: 'OK',
                    cancelButtonText: 'Cancel',
                    customClass: { confirmButton: 'btn btn-danger', cancelButton: 'btn btn-secondary' },
                    buttonsStyling: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = 'manage_users.php?delete_user=' + userId;
                    }
                });
            });
        });
    </script>
    <?php echo $sweetAlertConfig; ?>
</body>
</html>