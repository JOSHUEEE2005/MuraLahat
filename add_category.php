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
                <a class="nav-link" href="add_product.php"><i class="bi bi-plus-circle me-2"></i><span>Add Product</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="view_products.php"><i class="bi bi-list-ul me-2"></i><span>View Products</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="manage_members.php"><i class="bi bi-people me-2"></i><span>Manage Members</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="add_category.php"><i class="bi bi-tag me-2"></i><span>Add Category</span></a>
            </li>
           
            </ul>
        <div class="sidebar-footer">
            <a class="nav-link" href="#" onclick="logout()"><i class="bi bi-box-arrow-right me-2"></i><span>Logout</span></a>
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