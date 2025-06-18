<?php
session_start();

require_once('classes/database.php');
$con = new database();
$sweetAlertConfig = "";

if (isset($_POST['addPosition'])) {

  $posiName = $_POST['positionName'];
  $result = $con->addPosition($posiName);

  if ($result) {
    $sweetAlertConfig = "
      <script>
        Swal.fire({
          icon: 'success',
          title: 'Position Added',
          text: 'The position   has been added successfully!',
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
          text: 'There was an error adding the position. Please try again.',
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
    </style>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="./package/dist/sweetalert2.css">

</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
            <a class="navbar-brand" href="#"><img src="button_images/pusongligaw.png" alt="Mura Lahat Store Logo"></a>
            <div class="navbar-nav">
                <a class="nav-link" href="<?php echo $_SESSION['position'] === 'Admin' ? 'admin_dashboard.php' : 'view_products.php'; ?>">Back to Dashboard</a>
            </div>
        </div>
    </nav>

    <!-- Add Product Form -->
    <div class="container">
        <h2 class="section-title text-center">Add New Position</h2>
        <form action="" method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="positionName" class="form-label">Position Name</label>
                <input type="text" name="positionName" class="form-control" id="positionName" required>
            </div>
            
            <div class="text-center">
                <button type="submit" name="addPosition" class="btn btn-primary">Add Position</button>
            </div>
        </form>
    </div>

    <!-- Bootstrap JS and Popper.js -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>
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
    <script src="./package/dist/sweetalert2.js"></script>
    <?php echo $sweetAlertConfig; ?>
</body>
</html>