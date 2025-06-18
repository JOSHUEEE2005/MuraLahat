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
        /* Background image with corrected syntax */
        background-image: url('button_images/tralalerotropalang.png');
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
    .navbar {
        background-color: #e90e00; /* Fully transparent navbar */
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); /* Subtle shadow for depth */
    }
    .navbar-brand {
        padding: 0; /* Remove default padding for image alignment */
    }
    .navbar-brand img {
        height: 60px; /* Increased logo height for better fit */
        width: auto; /* Maintain aspect ratio */
        transition: transform 0.3s ease; /* Smooth hover effect */
    }
    .navbar-brand img:hover {
        transform: scale(1.1); /* Slight zoom on hover */
    }
    .navbar-toggler-icon {
        filter: invert(1); /* White toggler icon for contrast */
    }
    .nav-link {
        color: #ffd700 !important; /* Gold/yellow for links to match palette */
        transition: color 0.3s ease;
    }
    .nav-link:hover, .nav-link:focus {
        color: #ff4500 !important; /* Red hover to match palette */
    }
    .nav-link.text-danger {
        color:rgb(255, 204, 0) !important; /* Red for logout button */
    }
    .nav-link.text-danger:hover {
        color: #ffd700 !important; /* Gold/yellow hover for consistency */
    }
    .container {
        max-width: 1200px;
        margin-top: 50px;
        position: relative; /* Ensure container is above the overlay */
        z-index: 1;
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
    /* Ensure the card has a defined size */
    .dashboard-card {
        width: 80%; /* Adjust as needed */
        height: auto; /* Allow height to adjust based on content */
        overflow: hidden; /* Prevent content overflow */
    }
    /* Style the image to fit inside the card */
    .dashboard-img {
        width: 100%; /* Make image take full width of the card */
        height: auto; /* Maintain aspect ratio */
        object-fit: contain; /* Ensure the entire image is visible */
        display: block; /* Remove any inline spacing issues */
    }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#"><img src="button_images/pusongligaw.png" alt="Mura Lahat Store Logo"></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <button class="nav-link text-danger" onclick="logout()"><svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="currentColor" class="bi bi-box-arrow-right" viewBox="0 0 16 16">
  <path fill-rule="evenodd" d="M10 12.5a.5.5 0 0 1-.5.5h-8a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 .5.5v2a.5.5 0 0 0 1 0v-2A1.5 1.5 0 0 0 9.5 2h-8A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h8a1.5 1.5 0 0 0 1.5-1.5v-2a.5.5 0 0 0-1 0z"/>
  <path fill-rule="evenodd" d="M15.854 8.354a.5.5 0 0 0 0-.708l-3-3a.5.5 0 0 0-.708.708L14.293 7.5H5.5a.5.5 0 0 0 0 1h8.793l-2.147 2.146a.5.5 0 0 0 .708.708z"/>
</svg></button>
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
                        <img src="button_images/employeeposition.png" class="dashboard-img" alt="Employee Position">
                        <div class="card-body">
                            <h5 class="card-title">Employee Position</h5>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col">
                <a href="Register.php" class="text-decoration-none">
                    <div class="card dashboard-card">
                        <img src="button_images/register.png" class="dashboard-img" alt="Register">
                        <div class="card-body">
                            <h5 class="card-title">Register</h5>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col">
                <a href="financial.php" class="text-decoration-none">
                <div class="card dashboard-card">
                    <img src="button_images/incomefinancing.png" class="dashboard-img" alt="Income Financing">
                    <div class="card-body">
                        <h5 class="card-title">Income Financing</h5>
                    </div>
                </div>
                </a>
            </div>
            <div class="col">
                <a href="add_product.php" class="text-decoration-none">
                    <div class="card dashboard-card">
                        <img src="button_images/products.png" class="dashboard-img" alt="Product">
                        <div class="card-body">
                            <h5 class="card-title">Product</h5>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col">
                <a href="product_list.php" class="text-decoration-none">
                    <div class="card dashboard-card">
                        <img src="button_images/productlist.png" class="dashboard-img" alt="Product List">
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