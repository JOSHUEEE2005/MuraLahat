<?php
session_start();

require_once('classes/database.php');
$con = new database();
$sweetAlertConfig = "";

if (isset($_POST['addCategory'])) {

  $categName = $_POST['categoryName'];
  $result = $con->addCategory($categName);

  if ($result) {
    $sweetAlertConfig = "
      <script>
        Swal.fire({
          icon: 'success',
          title: 'Category Added',
          text: 'The category has been added successfully!',
          confirmButtonText: 'OK'
        }).then((result) => {
          if (result.isConfirmed) {
            window.location.href = 'add_product.php';
          }
        })
      </script>";
  } else {
    
    $sweetAlertConfig = "
      <script>
        Swal.fire({
          icon: 'error',
          title: 'Something went wrong',
          text: 'There was an error adding the category. Please try again.',
          confirmButtonText: 'OK'
        })
      </script>";
  }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product - Mura Lahat Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
    /* Sidebar Styles */
    /* Ensure box-sizing is applied globally */
* {
    box-sizing: border-box;
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
}

.sidebar-header {
    border-bottom: 1px solid rgba(255,255,255,0.1);
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
}

/* Main Content Styles */
.main-content {
    margin-left: 250px;
    padding: 20px;
    transition: all 0.3s;
    min-width: calc(100vw - 250px); /* Ensure content doesn't collapse */
}

/* Body Styles */
body {
    background-color: #f8f9fa;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    overflow-x: auto; /* Prevent content clipping when zoomed */
}

.navbar {
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.container {
    max-width: 600px;
    margin-top: 50px;
    background: #ffffff;
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
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

.section-title {
    font-size: 1.8rem;
    font-weight: 700;
    color: #343a40;
    margin-bottom: 20px;
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
        min-width: calc(100vw - 70px);
    }
}

/* Handle high zoom levels and very small screens */
@media (max-width: 576px) or (max-device-width: 576px) {
    .sidebar {
        width: 60px;
    }

    .main-content {
        margin-left: 60px;
        min-width: calc(100vw - 60px);
    }

    .container {
        padding: 15px; /* Reduce padding for smaller screens */
    }
}
</style>
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .navbar {
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .container {
            max-width: 600px;
            margin-top: 50px;
            background: #ffffff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
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
        .section-title {
            font-size: 1.8rem;
            font-weight: 700;
            color: #343a40;
            margin-bottom: 20px;
        }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="./package/dist/sweetalert2.css">

</head>
<body>
    <!-- Sidebar (unchanged) -->
    <div class="sidebar bg-dark text-white">
        <div class="sidebar-header p-3">
            <h3 class="text-center">Mura Lahat Store</h3>
        </div>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'add_product.php' ? 'active' : '' ?>" href="add_product.php">
                    <i class="bi bi-plus-circle me-2"></i> Add Product
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'view_products.php' ? 'active' : '' ?>" href="view_products.php">
                    <i class="bi bi-list-ul me-2"></i> View Products
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'manage_members.php' ? 'active' : '' ?>" href="manage_members.php">
                    <i class="bi bi-people me-2"></i> Manage Members
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'add_category.php' ? 'active' : '' ?>" href="add_category.php">
                    <i class="bi bi-tag me-2"></i> Add Category
                </a>
            </li>
        </ul>
        <div class="sidebar-footer p-3">
            <div class="dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="profileDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-person-circle me-2"></i> Profile
                </a>
                <ul class="dropdown-menu" aria-labelledby="profileDropdown">
                    <li><a class="dropdown-item" href="profile.html"><i class="bi bi-person-circle me-2"></i> See Profile</a></li>
                    <li><button class="dropdown-item" onclick="updatePersonalInfo()"><i class="bi bi-pencil-square me-2"></i> Update Info</button></li>
                    <li><button class="dropdown-item" onclick="updatePassword()"><i class="bi bi-key me-2"></i> Update Password</button></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><button class="dropdown-item text-danger" onclick="logout()"><i class="bi bi-box-arrow-right me-2"></i> Logout</button></li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Add Category Form -->
    <div class="main-content">
        <div class="container">
            <h2 class="section-title text-center">Add New Category</h2>
            <form action="" method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="categoryName" class="form-label">Category Name</label>
                    <input type="text" name="categoryName" class="form-control" id="categoryName" required>
                </div>
                <div class="text-center">
                    <button type="submit" name="addCategory" class="btn btn-primary">Add Category</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Bootstrap JS and Popper.js -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    <!-- Custom JavaScript -->
    <script>
        // Client-side validation for Effective From date
        function updatePersonalInfo() {
            Swal.fire('Info', 'Update personal info functionality not implemented yet.', 'info');
        }

        function updatePassword() {
            Swal.fire('Info', 'Update password functionality not implemented yet.', 'info');
        }

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
    <?php echo $sweetAlertConfig; ?>
</body>
</html>