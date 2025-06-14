<?php
session_start();
require_once('classes/database.php');
$con = new database();
 
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $prod_name = $_POST['prod_name'] ?? '';
    $prod_quantity = $_POST['prod_quantity'] ?? 0;
    $prod_price = $_POST['prod_price'] ?? 0.00;
    $price_effective_from = $_POST['price_effective_from'] ?? date('Y-m-d');
    $price_effective_to = $_POST['price_effective_to'] ?? null;
    $category_ids = $_POST['category_ids'] ?? [];
    $date_added = date('Y-m-d H:i:s');
    $user_account_id = $_SESSION['user_account_id'] ?? 1; // Default to 1 for testing
 
    // Server-side validation for dates
    if (strtotime($price_effective_from) > time()) {
        echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Invalid Date',
                text: 'Effective From date cannot be in the future. Please select today\\'s date or earlier.',
                confirmButtonText: 'OK',
                customClass: { confirmButton: 'btn btn-primary' },
                buttonsStyling: false
            }).then(() => {
                window.location.href='add_product.php';
            });
        </script>";
        exit;
    }
    if ($price_effective_to && strtotime($price_effective_to) < time()) {
        echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Invalid Date',
                text: 'Effective To date cannot be in the past. Please select a future date or leave it blank.',
                confirmButtonText: 'OK',
                customClass: { confirmButton: 'btn btn-primary' },
                buttonsStyling: false
            }).then(() => {
                window.location.href='add_product.php';
            });
        </script>";
        exit;
    }
 
    $image_path = null;
    if (!empty($_FILES['prod_image']['name'])) {
        $target_dir = 'images/';
        // Create directory if it doesn't exist
        if (!is_dir($target_dir)) {
            if (!mkdir($target_dir, 0755, true)) {
                die('Failed to create images directory.');
            }
        }
        $image_name = 'product_' . uniqid() . '_' . basename($_FILES['prod_image']['name']);
        $target_file = $target_dir . $image_name;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $allowed_types = ['jpg', 'jpeg', 'png'];
 
        // Validate image
        if (!in_array($imageFileType, $allowed_types)) {
            echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'Invalid File Type',
                    text: 'Only JPG, JPEG, and PNG files are allowed.',
                    confirmButtonText: 'OK',
                    customClass: { confirmButton: 'btn btn-primary' },
                    buttonsStyling: false
                }).then(() => {
                    window.location.href='add_product.php';
                });
            </script>";
        exit;
    }
    if ($_FILES['prod_image']['size'] > 2 * 1024 * 1024) {
        echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'File Too Large',
                text: 'File size must be less than 2MB.',
                confirmButtonText: 'OK',
                customClass: { confirmButton: 'btn btn-primary' },
                buttonsStyling: false
            }).then(() => {
                window.location.href='add_product.php';
            });
        </script>";
        exit;
    }
 
    // Move uploaded file
    if (move_uploaded_file($_FILES['prod_image']['tmp_name'], $target_file)) {
        $image_path = $target_file;
    } else {
        echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Upload Failed',
                text: 'Failed to upload image. Please check directory permissions.',
                confirmButtonText: 'OK',
                customClass: { confirmButton: 'btn btn-primary' },
                buttonsStyling: false
            }).then(() => {
                window.location.href='add_product.php';
            });
        </script>";
        exit;
    }
}
 
    // Add product to database
    $product_id = $con->addNewProduct(
        $user_account_id,
        $prod_name,
        $prod_quantity,
        $prod_price,
        $date_added,
        $price_effective_from,
        $price_effective_to,
        $category_ids,
        $image_path
    );
 
    if ($product_id) {
        echo "<script>
            function showSuccessAlert() {
                if (typeof Swal === 'undefined') {
                    setTimeout(showSuccessAlert, 100);
                    return;
                }
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: 'Product added successfully!',
                    confirmButtonText: 'OK',
                    customClass: { confirmButton: 'btn btn-primary' },
                    buttonsStyling: false
                }).then(() => {
                    window.location.href='view_products.php';
                });
            }
            showSuccessAlert();
        </script>";
    } else {
        echo "<script>
            function showErrorAlert() {
                if (typeof Swal === 'undefined') {
                    setTimeout(showErrorAlert, 100);
                    return;
                }
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to add product. Please try again.',
                    confirmButtonText: 'OK',
                    customClass: { confirmButton: 'btn btn-primary' },
                    buttonsStyling: false
                }).then(() => {
                    window.location.href='add_product.php';
                });
            }
            showErrorAlert();
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
    <script defer src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
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
                        <a class="nav-link active" aria-current="page" href="add_product.php">Add Product</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="view_products.php">View Products</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="manage_members.php">Manage Members</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="add_category.php">Add Category</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="add_discount.php">Add Discount</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="profileDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-person-circle"></i> Profile
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdown">
                            <li><a class="dropdown-item" href="profile.html"><i class="bi bi-person-circle me-2"></i> See Profile</a></li>
                            <li><button class="dropdown-item" onclick="updatePersonalInfo()"><i class="bi bi-pencil-square me-2"></i> Update Info</button></li>
                            <li><button class="dropdown-item" onclick="updatePassword()"><i class="bi bi-key me-2"></i> Update Password</button></li>
                            <li><button class="dropdown-item text-danger" onclick="logout()"><i class="bi bi-box-arrow-right me-2"></i> Logout</button></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
 
    <!-- Add Product Form -->
    <div class="container">
        <h2 class="section-title text-center">Add New Product</h2>
        <form action="add_product.php" method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="prod_name" class="form-label">Product Name</label>
                <input type="text" class="form-control" id="prod_name" name="prod_name" required>
            </div>
            <div class="mb-3">
                <label for="prod_quantity" class="form-label">Quantity</label>
                <input type="number" class="form-control" id="prod_quantity" name="prod_quantity" min="0" required>
            </div>
            <div class="mb-3">
                <label for="prod_price" class="form-label">Price</label>
                <input type="number" step="0.01" class="form-control" id="prod_price" name="prod_price" min="0" required>
            </div>
            <div class="mb-3">
                <label for="price_effective_from" class="form-label">Price Effective From</label>
                <input type="date" class="form-control" id="price_effective_from" name="price_effective_from" value="<?php echo date('Y-m-d'); ?>" required>
            </div>
            <div class="mb-3">
                <label for="price_effective_to" class="form-label">Price Effective To (Optional)</label>
                <input type="date" class="form-control" id="price_effective_to" name="price_effective_to">
            </div>
            <div class="mb-3">
                <label for="prod_image" class="form-label">Product Image (Optional, max 2MB)</label>
                <input type="file" class="form-control" id="prod_image" name="prod_image" accept="image/jpeg,image/png">
            </div>
            <div class="mb-3">
                <label for="category_ids" class="form-label">Categories (Optional)</label>
                <select class="form-select" id="category_ids" name="category_ids[]" multiple>
                    <?php
                    $categories = $con->viewCategory();
                    foreach ($categories as $category) {
                        echo "<option value='{$category['Category_ID']}'>{$category['Category_Name']}</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="text-center">
                <button type="submit" class="btn btn-primary">Add Product</button>
            </div>
        </form>
    </div>
 
    <!-- Bootstrap JS and Popper.js -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>
    <!-- Custom JavaScript -->
    <script>
        // Client-side validation for Effective From date
        document.getElementById('price_effective_from').addEventListener('change', function() {
            const selectedDate = new Date(this.value);
            const today = new Date();
            today.setHours(0, 0, 0, 0); // Normalize to midnight
            if (selectedDate > today) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Future Date Selected',
                    text: 'The product will not be visible until the selected effective date. Proceed?',
                    showCancelButton: true,
                    confirmButtonText: 'Yes',
                    cancelButtonText: 'No',
                    customClass: { confirmButton: 'btn btn-primary', cancelButton: 'btn btn-secondary' },
                    buttonsStyling: false
                }).then((result) => {
                    if (!result.isConfirmed) {
                        this.value = '<?php echo date('Y-m-d'); ?>';
                    }
                });
            }
        });
 
        // Client-side validation for Effective To date
        document.getElementById('price_effective_to').addEventListener('change', function() {
            if (this.value) {
                const selectedDate = new Date(this.value);
                const today = new Date();
                today.setHours(0, 0, 0, 0);
                if (selectedDate < today) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Past Date Selected',
                        text: 'The price effective to date cannot be in the past. Please select a future date or leave it blank.',
                        confirmButtonText: 'OK',
                        customClass: { confirmButton: 'btn btn-primary' },
                        buttonsStyling: false
                    });
                    this.value = '';
                }
            }
        });
 
        function updatePersonalInfo() {
            Swal.fire({
                icon: 'info',
                title: 'Info',
                text: 'Update personal info functionality not implemented yet.',
                confirmButtonText: 'OK',
                customClass: { confirmButton: 'btn btn-primary' },
                buttonsStyling: false
            });
        }
 
        function updatePassword() {
            Swal.fire({
                icon: 'info',
                title: 'Info',
                text: 'Update password functionality not implemented yet.',
                confirmButtonText: 'OK',
                customClass: { confirmButton: 'btn btn-primary' },
                buttonsStyling: false
            });
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
</body>
</html>
 