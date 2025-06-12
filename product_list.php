<?php
session_start();
require_once('classes/database.php');
$con = new database();
$products = $con->getProductsWithPrices();

// Check if user is admin
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
    <title>Product List - Mura Lahat Store</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <!-- SweetAlert2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
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
        .table-container {
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-top: 30px;
        }
        .section-title {
            font-size: 2rem;
            font-weight: 700;
            color: #343a40;
            margin-bottom: 30px;
        }
        .btn-update {
            background-color: #007bff;
            border: none;
            border-radius: 50px;
            padding: 5px 15px;
            font-weight: 500;
            transition: background-color 0.3s ease;
        }
        .btn-update:hover {
            background-color: #0056b3;
        }
        .btn-delete {
            background-color: #dc3545;
            border: none;
            border-radius: 50px;
            padding: 5px 15px;
            font-weight: 500;
            transition: background-color 0.3s ease;
        }
        .btn-delete:hover {
            background-color: #c82333;
        }
        .container {
            max-width: 1200px;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="admin_dashboard.php">Mura Lahat Store</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="admin_dashboard.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link text-danger" onclick="logout()">Logout</button>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Product List -->
    <div class="container my-5">
        <h2 class="section-title">Product List</h2>
        <div class="table-container">
            <?php if (!empty($products)): ?>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th scope="col">Product ID</th>
                            <th scope="col">Name</th>
                            <th scope="col">Stock</th>
                            <th scope="col">Price</th>
                            <th scope="col">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products as $product): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($product['Product_ID']); ?></td>
                                <td><?php echo htmlspecialchars($product['Product_Name']); ?></td>
                                <td><?php echo htmlspecialchars($product['Product_Stock']); ?></td>
                                <td>₱<?php echo number_format($product['Price'], 2); ?></td>
                                <td>
                                    <button class="btn btn-update me-2" onclick="updateProduct(<?php echo $product['Product_ID']; ?>)">
                                        <i class="bi bi-pencil me-2"></i>Update
                                    </button>
                                    <button class="btn btn-delete" onclick="deleteProduct(<?php echo $product['Product_ID']; ?>)">
                                        <i class="bi bi-trash me-2"></i>Delete
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="text-muted text-center">No products found.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Bootstrap JS and Popper.js -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    <!-- Custom JavaScript -->
    <script>
        function updateProduct(productId) {
            fetch('get_product.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ productId })
            })
            .then(response => response.json())
            .then(data => {
                if (!data.success) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.error,
                        confirmButtonText: 'OK',
                        customClass: { confirmButton: 'btn btn-primary' },
                        buttonsStyling: false
                    });
                    return;
                }
                const product = data.product;
                Swal.fire({
                    title: 'Update Product',
                    html: `
                        <label for="prodName" class="form-label">Product Name:</label>
                        <input type="text" id="prodName" class="swal2-input" value="${product.Product_Name}" required>
                        <label for="stockMode" class="form-label">Stock Action:</label>
                        <select id="stockMode" class="swal2-select">
                            <option value="set">Set Stock</option>
                            <option value="add">Add Stock</option>
                        </select>
                        <label for="prodStock" class="form-label">Stock (Current: ${product.Product_Stock}):</label>
                        <input type="number" id="prodStock" class="swal2-input" value="${product.Product_Stock}" min="0" required>
                        <label for="prodPrice" class="form-label">Price:</label>
                        <input type="number" id="prodPrice" class="swal2-input" value="${product.Price}" min="0" step="0.01" required>
                        <label for="effectiveFrom" class="form-label">Effective From:</label>
                        <input type="date" id="effectiveFrom" class="swal2-input" value="${product.Effective_From}" required>
                        <label for="effectiveTo" class="form-label">Effective To:</label>
                        <input type="date" id="effectiveTo" class="swal2-input" value="${product.Effective_To || ''}">
                    `,
                    showCancelButton: true,
                    confirmButtonText: 'Update',
                    cancelButtonText: 'Cancel',
                    customClass: {
                        confirmButton: 'btn btn-primary',
                        cancelButton: 'btn btn-secondary'
                    },
                    buttonsStyling: false,
                    preConfirm: () => {
                        const prodName = document.getElementById('prodName').value.trim();
                        const stockMode = document.getElementById('stockMode').value;
                        const prodStock = parseInt(document.getElementById('prodStock').value);
                        const prodPrice = parseFloat(document.getElementById('prodPrice').value);
                        const effectiveFrom = document.getElementById('effectiveFrom').value;
                        const effectiveTo = document.getElementById('effectiveTo').value || null;
                        if (!prodName || prodStock < 0 || prodPrice < 0 || !effectiveFrom || !stockMode) {
                            Swal.showValidationMessage('Please fill in all required fields with valid values');
                            return false;
                        }
                        return { productId, prodName, stockMode, prodStock, prodPrice, effectiveFrom, effectiveTo };
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        const data = result.value;
                        fetch('process_update_product.php', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify(data)
                        })
                        .then(response => response.json())
                        .then(data => {
                            Swal.fire({
                                icon: data.success ? 'success' : 'error',
                                title: data.success ? 'Product Updated' : 'Error',
                                text: data.success ? 'Product has been updated successfully.' : data.error,
                                confirmButtonText: 'OK',
                                customClass: { confirmButton: 'btn btn-primary' },
                                buttonsStyling: false
                            }).then(() => {
                                if (data.success) {
                                    window.location.reload();
                                }
                            });
                        });
                    }
                });
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Connection Error',
                    text: `Failed to connect to server: ${error.message}`,
                    confirmButtonText: 'OK',
                    customClass: { confirmButton: 'btn btn-primary' },
                    buttonsStyling: false
                });
            });
        }

        function deleteProduct(productId) {
            Swal.fire({
                title: 'Are you sure?',
                text: 'This will permanently delete the product.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel',
                customClass: {
                    confirmButton: 'btn btn-danger',
                    cancelButton: 'btn btn-secondary'
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch('process_delete_product.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ productId })
                    })
                    .then(response => response.json())
                    .then(data => {
                        Swal.fire({
                            icon: data.success ? 'success' : 'error',
                            title: data.success ? 'Product Deleted' : 'Error',
                            text: data.success ? 'Product has been deleted successfully.' : data.error,
                            confirmButtonText: 'OK',
                            customClass: { confirmButton: 'btn btn-primary' },
                            buttonsStyling: false
                        }).then(() => {
                            if (data.success) {
                                window.location.reload();
                            }
                        });
                    })
                    .catch(error => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Connection Error',
                            text: `Failed to connect to server: ${error.message}`,
                            confirmButtonText: 'OK',
                            customClass: { confirmButton: 'btn btn-primary' },
                            buttonsStyling: false
                        });
                    });
                }
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