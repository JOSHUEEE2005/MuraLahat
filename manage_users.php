<?php
session_start();
require_once('classes/database.php');
$con = new database();

if (!isset($_SESSION['user_id']) || !isset($_SESSION['position']) || $_SESSION['position'] !== 'Admin') {
    header('Location: index.php');
    exit;
}

$users = $con->getAllUsersWithPositions();
$positions = $con->viewPositions();

// Fetch active sessions for each user
$active_sessions = [];
foreach ($users as $user) {
    $stmt = $con->opencon()->prepare("SELECT Session_ID, Login_Time FROM user_sessions WHERE User_Account_ID = ? AND Logout_Time IS NULL ORDER BY Login_Time DESC LIMIT 1");
    $stmt->execute([$user['User_Account_ID']]);
    $session = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($session) {
        $active_sessions[$user['User_Account_ID']] = [
            'Session_ID' => $session['Session_ID'],
            'Login_Time' => $session['Login_Time']
        ];
    }
}

$sweetAlertConfig = "";
if (isset($_POST['update_position'])) {
    $user_id = filter_input(INPUT_POST, 'user_id', FILTER_VALIDATE_INT);
    $new_position = filter_input(INPUT_POST, 'position', FILTER_SANITIZE_STRING);
    $valid_position_names = array_column($positions, 'Position');

    if ($user_id === false || $user_id <= 0) {
        $error_message = "Invalid user ID.";
    } elseif (empty($new_position)) {
        $error_message = "No position selected.";
    } elseif (!in_array($new_position, $valid_position_names)) {
        $error_message = "Selected position is not valid.";
    } else {
        $error_message = null;
    }

    if ($error_message === null) {
        $result = $con->updateUserPosition($user_id, $new_position);
        if ($result['success']) {
            $sweetAlertConfig = "<script>
                Swal.fire({
                    icon: 'success',
                    title: 'Position Updated',
                    text: 'User position has been updated successfully!',
                    confirmButtonText: 'OK',
                    customClass: { confirmButton: 'btn btn-primary' }
                }).then(() => {
                    window.location.href = 'manage_users.php';
                });
            </script>";
        } else {
            $sweetAlertConfig = "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to update position: " . addslashes($result['error']) . "',
                    confirmButtonText: 'OK',
                    customClass: { confirmButton: 'btn btn-primary' }
                });
            </script>";
        }
    } else {
        $sweetAlertConfig = "<script>
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: '$error_message',
                confirmButtonText: 'OK',
                customClass: { confirmButton: 'btn btn-primary' }
            });
        </script>";
    }
}

if (isset($_POST['update_hourly_rate'])) {
    $user_id = filter_input(INPUT_POST, 'user_id', FILTER_VALIDATE_INT);
    $hourly_rate = filter_input(INPUT_POST, 'hourly_rate', FILTER_VALIDATE_FLOAT);

    if ($user_id === false || $user_id <= 0) {
        $error_message = "Invalid user ID.";
    } elseif ($hourly_rate === false || $hourly_rate < 0) {
        $error_message = "Invalid rate.";
    } else {
        $error_message = null;
    }

    if ($error_message === null) {
        $result = $con->updateHourlyRate($user_id, $hourly_rate);
        if ($result['success']) {
            $sweetAlertConfig = "<script>
                Swal.fire({
                    icon: 'success',
                    title: 'Rate Updated',
                    text: 'User rate has been updated successfully!',
                    confirmButtonText: 'OK',
                    customClass: { confirmButton: 'btn btn-primary' }
                }).then(() => {
                    window.location.href = 'manage_users.php';
                });
            </script>";
        } else {
            $sweetAlertConfig = "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to update rate: " . addslashes($result['error']) . "',
                    confirmButtonText: 'OK',
                    customClass: { confirmButton: 'btn btn-primary' }
                });
            </script>";
        }
    } else {
        $sweetAlertConfig = "<script>
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: '$error_message',
                confirmButtonText: 'OK',
                customClass: { confirmButton: 'btn btn-primary' }
            });
        </script>";
    }
}

if (isset($_POST['delete_user'])) {
    $user_id = filter_input(INPUT_POST, 'user_id', FILTER_VALIDATE_INT);

    if ($user_id === false || $user_id <= 0) {
        $sweetAlertConfig = "<script>
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Invalid user ID.',
                confirmButtonText: 'OK',
                customClass: { confirmButton: 'btn btn-primary' }
            });
        </script>";
    } else {
        $result = $con->deleteUser($user_id);
        if ($result['success']) {
            $sweetAlertConfig = "<script>
                Swal.fire({
                    icon: 'success',
                    title: 'User Deleted',
                    text: 'User has been deleted successfully!',
                    confirmButtonText: 'OK',
                    customClass: { confirmButton: 'btn btn-primary' }
                }).then(() => {
                    window.location.href = 'manage_users.php';
                });
            </script>";
        } else {
            $sweetAlertConfig = "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to delete user: " . addslashes($result['error']) . "',
                    confirmButtonText: 'OK',
                    customClass: { confirmButton: 'btn btn-primary' }
                });
            </script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - Mura Lahat Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body {
    /* Background image with corrected syntax */
    background-image: url('button_images/otsootso.png');
    background-size: cover;
    background-position: center;
    background-attachment: scroll; /* Image scrolls with content */
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    position: relative;
    margin: 0;
    min-height: 100vh;
}

/* Add a semi-transparent overlay to improve text readability */
body::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(255, 255, 255, 0.7); /* Light overlay for contrast */
    z-index: -1;
}

.container {
    max-width: 1200px;
    margin-top: 50px;
    position: relative; /* Ensure container is above the overlay */
    z-index: 1;
    background: #ffffff;
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.section-title {
    font-size: 2.5rem;
    font-weight: 700;
    color: #343a40;
    text-align: center;
    margin-bottom: 40px;
}

.dashboard-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    border: none;
    border-radius: 10px;
    overflow: hidden;
    background: #ffffff;
    text-align: center;
    cursor: pointer;
    width: 80%; /* Adjust as needed */
    height: auto; /* Allow height to adjust based on content */
}

.dashboard-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
}

.dashboard-img {
    width: 100%; /* Make image take full width of the card */
    height: 150px;
    object-fit: contain; /* Ensure the entire image is visible */
    display: block; /* Remove any inline spacing issues */
}

.card-title {
    font-size: 1.2rem;
    font-weight: 600;
    color: #343a40;
    margin-top: 10px;
}

.row-centered {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
}

.form-label {
    font-weight: 500;
    color: #343a40;
}

.btn-primary {
    background-color: #007bff;
    border: none;
    border-radius: 50px;
    padding: 10px 25px;
    font-weight: 500;
    transition: background-color 0.3s ease;
}

.btn-primary:hover {
    background-color: #0056b3;
}

.form-control, .form-select {
    border-radius: 8px;
    border: 1px solid #ced4da;
}

/* Sidebar Styles */
.sidebar {
    position: fixed;
    top: 0;
    left: 0;
    height: 100vh;
    width: 250px;
    z-index: 1000;
    transition: all 0.3s;
    box-shadow: 2px 0 5px rgba(0,0,0,0.1);
    box-sizing: border-box;
    background-color: #e90e00; /* Red background to match original design */
}

.sidebar-header {
    border-bottom: 1px solid rgba(255,255,255,0.1);
    padding: 15px;
}

.sidebar img {
        height: 200px; /* Increased logo height for better fit */
        width: auto; /* Maintain aspect ratio */
        transition: transform 0.3s ease; /* Smooth hover effect */
    }

.sidebar .nav-link {
    color: rgba(255,255,255,0.8);
    padding: 12px 20px;
    margin: 5px 10px;
    border-radius: 5px;
    transition: all 0.3s;
}

.sidebar .nav-link:hover {
    color: white;
    background-color: rgba(255,255,255,0.1);
}

.sidebar .nav-link.active {
    color: white;
    background-color: rgba(0,123,255,0.2);
    border-left: 3px solid #0d6efd;
}

.sidebar-footer {
    position: absolute;
    bottom: 0;
    width: 100%;
    border-top: 1px solid rgba(255,255,255,0.1);
    padding: 15px;
}

/* Main Content Styles */
.main-content {
    margin-left: 250px;
    padding: 20px;
    transition: all 0.3s;
    min-width: calc(100vw - 250px); /* Ensure content doesn't collapse */
    box-sizing: border-box;
}

/* Responsive Styles */
@media (max-width: 768px) {
    .sidebar {
        width: 70px;
        overflow: hidden;
    }

    .sidebar .nav-link span,
    .sidebar-header h3,
    .sidebar .dropdown-toggle span {
        display: none;
    }

    .sidebar .nav-link {
        text-align: center;
        padding: 12px 5px;
    }

    .sidebar .nav-link i {
        margin-right: 0;
        font-size: 1.2rem;
    }

    .main-content {
        margin-left: 70px;
        min-width: calc(100vw - 70px); /* Adjust for smaller sidebar */
    }

    .container {
        max-width: 100%;
        padding: 20px;
    }
}

@media (max-width: 576px) or (max-device-width: 576px) {
    .sidebar {
        width: 60px; /* Slightly smaller for very small screens or high zoom */
    }

    .main-content {
        margin-left: 60px;
        min-width: calc(100vw - 60px);
    }

    .container {
        margin-top: 20px;
        padding: 15px;
    }
}
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar text-white"> <!-- Removed bg-dark class -->
        <div class="sidebar-header p-3">
            <h3 class="text-center"><img src="button_images/jobart.png" alt="logo" width=></h3>
        </div>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link" href="admin_dashboard.php"><i class="bi bi-house me-2"></i><span>Dashboard</span></a>
            </li>
           
            <li class="nav-item">
                <a class="nav-link active" href="manage_users.php"><i class="bi bi-person-gear me-2"></i><span>Manage Users</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" href="add_position.php"><i class="bi bi-person-gear me-2"></i><span>Add Position</span></a>
            </li>
           
        </ul>
        <div class="sidebar-footer">
            <a class="nav-link" href="#" onclick="logout()"><i class="bi bi-box-arrow-right me-2"></i><span>Logout</span></a>
        </div>
    </div>



    <div class="main-content">
        <div class="container">
            <h2 class="section-title">Manage Users</h2>
            <div class="table-container">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>User ID</th>
                            <th>Username</th>
                            <th>Position</th>
                            <th>Rate (₱)</th>
                            <th>Current Salary (₱)</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($users)): ?>
                            <tr>
                                <td colspan="6" class="text-center">No users found.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($users as $user): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($user['User_Account_ID']); ?></td>
                                    <td><?php echo htmlspecialchars($user['Username']); ?></td>
                                    <td>
                                        <form method="post" action="">
                                            <input type="hidden" name="user_id" value="<?php echo $user['User_Account_ID']; ?>">
                                            <select name="position" class="form-select d-inline-block w-auto">
                                                <?php foreach ($positions as $position): ?>
                                                    <option value="<?php echo htmlspecialchars($position['Position']); ?>" <?php echo $user['Position'] === $position['Position'] ? 'selected' : ''; ?>>
                                                        <?php echo htmlspecialchars($position['Position']); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                            <button type="submit" name="update_position" class="btn btn-update mt-2"><i class="bi bi-pencil"></i> Update</button>
                                        </form>
                                    </td>
                                    <td>
                                        <form method="post" action="">
                                            <input type="hidden" name="user_id" value="<?php echo $user['User_Account_ID']; ?>">
                                            <input type="number" name="hourly_rate" value="<?php echo htmlspecialchars($user['Hourly_Rate']); ?>" step="0.01" min="0" class="form-control d-inline-block w-auto">
                                            <button type="submit" name="update_hourly_rate" class="btn btn-rate mt-2"><i class="bi bi-currency-exchange"></i> Update</button>
                                        </form>
                                    </td>
                                    <td>
                                        <span class="current-salary" 
                                              data-user-id="<?php echo $user['User_Account_ID']; ?>" 
                                              data-rate="<?php echo $user['Hourly_Rate']; ?>" 
                                              data-login-time="<?php echo isset($active_sessions[$user['User_Account_ID']]) ? $active_sessions[$user['User_Account_ID']]['Login_Time'] : ''; ?>">
                                            <?php echo isset($active_sessions[$user['User_Account_ID']]) ? '0.00' : number_format($user['Total_Salary'], 2); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <form method="post" action="" style="display:inline;">
                                            <input type="hidden" name="user_id" value="<?php echo $user['User_Account_ID']; ?>">
                                            <button type="submit" name="delete_user" class="btn btn-delete" onclick="return confirmDelete('<?php echo htmlspecialchars($user['Username']); ?>')">
                                                <i class="bi bi-trash"></i> Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    <script>
        function confirmDelete(username) {
            return confirm(`Are you sure you want to delete user ${username}?`);
        }
        function logout() {
            fetch('logout.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: 'Successfully logged out.',
                        customClass: { confirmButton: 'btn btn-primary' }
                    }).then(() => {
                        window.location = 'index.php';
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.error || 'Logout failed.',
                        customClass: { confirmButton: 'btn btn-secondary' }
                    });
                }
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Connection Error',
                    text: `Failed to connect to server: ${error.message}`,
                    customClass: { confirmButton: 'btn btn-secondary' }
                });
            });
        }
        // Real-time salary calculation
        document.addEventListener('DOMContentLoaded', function() {
            const salaryElements = document.querySelectorAll('.current-salary');
            salaryElements.forEach(element => {
                const userId = element.dataset.userId;
                const rate = parseFloat(element.dataset.rate);
                const loginTime = element.dataset.loginTime;

                if (loginTime && !isNaN(rate)) {
                    const loginDate = new Date(loginTime);

                    function updateSalary() {
                        const now = new Date();
                        const elapsedSeconds = Math.floor((now - loginDate) / 1000);
                        const increments = Math.floor(elapsedSeconds / 5); // Number of 5-second increments
                        const salary = increments * rate; // Full rate per 5-second increment
                        element.textContent = salary.toFixed(2);
                    }

                    updateSalary(); // Initial update
                    setInterval(updateSalary, 5000); // Update every 5 seconds
                }
            });
        });
    </script>
    <?php echo $sweetAlertConfig; ?>
</body>
</html>