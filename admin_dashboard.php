<?php
session_start();
require_once('classes/database.php');

// Check if user is logged in and has Admin position
if (!isset($_SESSION['user_id']) || $_SESSION['position'] !== 'Admin') {
    header('Location: index.php');
    exit();
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Dashboard - Mura Lahat Store</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Custom CSS -->
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .navbar {
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .container {
            max-width: 1200px;
            margin-top: 50px;
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
            border-radius: 12px;
            overflow: hidden;
            background: #ffffff;
            text-align: center;
            cursor: pointer;
        }
        .dashboard-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
        }
        .dashboard-img {
            width: 100%;
            height: 150px;
            object-fit: cover;
        }
        .card-title {
            font-size: 1.2rem;
            font-weight: 600;
            color: #343a40;
            margin-top: 10px;
        }
        /* Center the cards in the row */
        .row-centered {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Mura Lahat Store</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <button class="nav-link text-danger" onclick="logout()">Logout</button>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Dashboard -->
    <div class="container">
        <h2 class="section-title">Admin Dashboard</h2>
        <div class="row row-cols-1 row-cols-md-3 g-4 row-centered">
            <div class="col">
                <a href="manage_users.php" class="text-decoration-none">
                    <div class="card dashboard-card">
                        <img src="https://via.placeholder.com/300x150?text=Employee+Position" class="dashboard-img" alt="Employee Position">
                        <div class="card-body">
                            <h5 class="card-title">Employee Position</h5>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col">
                <a href="Register.php" class="text-decoration-none">
                    <div class="card dashboard-card">
                        <img src="https://via.placeholder.com/300x150?text=Register" class="dashboard-img" alt="Register">
                        <div class="card-body">
                            <h5 class="card-title">Register</h5>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col">
                <div class="card dashboard-card" onclick="alert('Income Financing functionality not implemented yet.')">
                    <img src="https://via.placeholder.com/300x150?text=Income+Financing" class="dashboard-img" alt="Income Financing">
                    <div class="card-body">
                        <h5 class="card-title">Income Financing</h5>
                    </div>
                </div>
            </div>
            <div class="col">
                <a href="add_product.php" class="text-decoration-none">
                    <div class="card dashboard-card">
                        <img src="https://via.placeholder.com/300x150?text=Product" class="dashboard-img" alt="Product">
                        <div class="card-body">
                            <h5 class="card-title">Product</h5>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col">
                <a href="product_list.php" class="text-decoration-none">
                    <div class="card dashboard-card">
                        <img src="https://via.placeholder.com/300x150?text=Product+List" class="dashboard-img" alt="Product List">
                        <div class="card-body">
                            <h5 class="card-title">Product List</h5>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS and Popper.js -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    <script>
        function logout() {
            Swal.fire({
                icon: 'success',
                title: 'Logged Out',
                text: 'You have been logged out successfully.',
                confirmButtonText: 'OK',
                customClass: { confirmButton: 'btn btn-primary' },
                buttonsStyling: false
            }).then(() => {
                window.location.href = 'index.php';
            });
        }
    </script>
</body>
</html>