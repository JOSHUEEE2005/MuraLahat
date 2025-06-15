<?php
session_start();
require_once 'classes/database.php';

try {
    $con = new database();
    $products = $con->getProductsWithPrices();
} catch (PDOException $e) {
    error_log('Database Error: ' . $e->getMessage());
    $products = [];
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Product List - Mura Lahat Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
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
        .main-content {
            margin-left: 250px;
            padding: 20px;
            transition: all 0.3s;
            min-width: calc(100vw - 250px);
        }
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
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
        .btn-edit, .btn-delete {
            border-radius: 50px;
            padding: 5px 15px;
            font-weight: 500;
            transition: background-color 0.3s ease;
        }
        .btn-edit {
            background-color: #007bff;
            border: none;
        }
        .btn-edit:hover {
            background-color: #0056b3;
        }
        .btn-delete {
            background-color: #dc3545;
            border: none;
        }
        .btn-delete:hover {
            background-color: #c82333;
        }
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
    </style>
</head>
<body>
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
                <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'product_list.php' ? 'active' : '' ?>" href="product_list.php">
                    <i class="bi bi-box me-2"></i> Product List
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
    <div class="main-content">
        <div class="container my-5">
            <h2 class="section-title">Product List</h2>
            <div class="table-container">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Product ID</th>
                            <th>Name</th>
                            <th>Stock</th>
                            <th>Price</th>
                            <th>Categories</th>
                            <th>Image</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products as $product): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($product['Product_ID']); ?></td>
                                <td><?php echo htmlspecialchars($product['Product_Name']); ?></td>
                                <td><?php echo htmlspecialchars($product['Product_Stock']); ?></td>
                                <td>₱<?php echo number_format($product['Price'] ?? 0, 2); ?></td>
                                <td><?php echo htmlspecialchars($product['Category_Names'] ?? 'None'); ?></td>
                                <td>
                                    <img src="<?php echo !empty($product['Product_Image']) && file_exists($product['Product_Image']) ? htmlspecialchars($product['Product_Image']) : 'https://via.placeholder.com/50x50?text=' . urlencode($product['Product_Name']); ?>" alt="<?php echo htmlspecialchars($product['Product_Name']); ?>" style="width: 50px; height: 50px; object-fit: cover;">
                                </td>
                                <td>
                                    <button class="btn btn-edit btn-sm text-white" onclick="showUpdateModal(<?php echo $product['Product_ID']; ?>)">
                                        <i class="bi bi-pencil"></i> Edit
                                    </button>
                                    <button class="btn btn-delete btn-sm text-white" onclick="deleteProduct(<?php echo $product['Product_ID']; ?>, '<?php echo htmlspecialchars($product['Product_Name'], ENT_QUOTES); ?>')">
                                        <i class="bi bi-trash"></i> Delete
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    <script>
        async function fetchCategories() {
            try {
                const response = await fetch('get_categories.php');
                const data = await response.json();
                return data.categories || [];
            } catch (error) {
                console.error('Error fetching categories:', error);
                return [];
            }
        }

        async function showUpdateModal(productId) {
            try {
                // Fetch product details
                const response = await fetch('get_product_details.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ productId })
                });
                const product = await response.json();
                if (!product.success) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: product.error || 'Failed to fetch product details.',
                        customClass: { confirmButton: 'btn btn-secondary' }
                    });
                    return;
                }

                // Fetch categories
                const categories = await fetchCategories();
                const categoryOptions = categories.map(c => `
                    <option value="${c.Category_ID}" ${product.data.Category_IDs && product.data.Category_IDs.split(',').includes(c.Category_ID.toString()) ? 'selected' : ''}>
                        ${c.Category_Name}
                    </option>
                `).join('');

                Swal.fire({
                    title: 'Update Product',
                    html: `
                        <input type="hidden" id="productId" value="${productId}">
                        <label for="prodName" class="form-label">Product Name</label>
                        <input type="text" id="prodName" class="swal2-input" value="${product.data.Product_Name || ''}" required>
                        <label for="prodStock" class="form-label">Stock</label>
                        <input type="number" id="prodStock" class="swal2-input" min="0" value="${product.data.Product_Stock || 0}" required>
                        <label for="stockMode" class="form-label">Stock Update Mode</label>
                        <select id="stockMode" class="swal2-select">
                            <option value="set" ${product.data.stockMode === 'set' ? 'selected' : ''}>Set Stock</option>
                            <option value="add" ${product.data.stockMode === 'add' ? 'selected' : ''}>Add to Stock</option>
                        </select>
                        <label for="prodPrice" class="form-label">Price</label>
                        <input type="number" id="prodPrice" class="swal2-input" min="0" step="0.01" value="${product.data.Price || 0}" required>
                        <label for="effectiveFrom" class="form-label">Price Effective From</label>
                        <input type="date" id="effectiveFrom" class="swal2-input" value="${product.data.Effective_From || ''}" required>
                        <label for="effectiveTo" class="form-label">Price Effective To (Optional)</label>
                        <input type="date" id="effectiveTo" class="swal2-input" value="${product.data.Effective_To || ''}">
                        <label for="categoryIds" class="form-label">Categories</label>
                        <select id="categoryIds" class="swal2-select" multiple required>
                            ${categoryOptions}
                        </select>
                        <label for="productImage" class="form-label">Product Image (Optional)</label>
                        <input type="file" id="productImage" class="swal2-file" accept="image/jpeg,image/png,image/gif">
                    `,
                    showCancelButton: true,
                    confirmButtonText: 'Update Product',
                    cancelButtonText: 'Cancel',
                    customClass: {
                        confirmButton: 'btn btn-primary',
                        cancelButton: 'btn btn-secondary'
                    },
                    preConfirm: () => {
                        const productId = document.getElementById('productId').value;
                        const prodName = document.getElementById('prodName').value.trim();
                        const prodStock = parseInt(document.getElementById('prodStock').value);
                        const stockMode = document.getElementById('stockMode').value;
                        const prodPrice = parseFloat(document.getElementById('prodPrice').value);
                        const effectiveFrom = document.getElementById('effectiveFrom').value;
                        const effectiveTo = document.getElementById('effectiveTo').value;
                        const categoryIds = Array.from(document.getElementById('categoryIds').selectedOptions).map(option => parseInt(option.value));
                        const productImage = document.getElementById('productImage').files[0];

                        if (!prodName) {
                            Swal.showValidationMessage('Product name is required');
                            return false;
                        }
                        if (isNaN(prodStock) || prodStock < 0) {
                            Swal.showValidationMessage('Stock must be a non-negative number');
                            return false;
                        }
                        if (!stockMode || !['set', 'add'].includes(stockMode)) {
                            Swal.showValidationMessage('Invalid stock update mode');
                            return false;
                        }
                        if (isNaN(prodPrice) || prodPrice < 0) {
                            Swal.showValidationMessage('Price must be a non-negative number');
                            return false;
                        }
                        if (!effectiveFrom) {
                            Swal.showValidationMessage('Effective from date is required');
                            return false;
                        }
                        if (categoryIds.length === 0) {
                            Swal.showValidationMessage('At least one category is required');
                            return false;
                        }

                        return {
                            productId,
                            prodName,
                            prodStock,
                            stockMode,
                            prodPrice,
                            effectiveFrom,
                            effectiveTo,
                            categoryIds,
                            productImage
                        };
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        updateProduct(result.value);
                    }
                });
            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: `Failed to load product details: ${error.message}`,
                    customClass: { confirmButton: 'btn btn-secondary' }
                });
            }
        }

        function updateProduct(data) {
            const formData = new FormData();
            formData.append('productId', data.productId);
            formData.append('prodName', data.prodName);
            formData.append('prodStock', data.prodStock);
            formData.append('stockMode', data.stockMode);
            formData.append('prodPrice', data.prodPrice);
            formData.append('effectiveFrom', data.effectiveFrom);
            if (data.effectiveTo) {
                formData.append('effectiveTo', data.effectiveTo);
            }
            formData.append('categoryIds', JSON.stringify(data.categoryIds));
            if (data.productImage) {
                formData.append('productImage', data.productImage);
            }

            fetch('process_update_product.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(result => {
                Swal.fire({
                    icon: result.success ? 'success' : 'error',
                    title: result.success ? 'Success' : 'Error',
                    text: result.success ? 'Product updated successfully.' : result.error || 'Failed to update product.',
                    customClass: { confirmButton: 'btn btn-primary' }
                }).then(() => {
                    if (result.success) {
                        window.location.reload();
                    }
                });
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

        function deleteProduct(productId, productName) {
            Swal.fire({
                title: 'Delete Product',
                text: `Are you sure you want to delete ${productName}?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Delete',
                cancelButtonText: 'Cancel',
                customClass: {
                    confirmButton: 'btn btn-danger',
                    cancelButton: 'btn btn-secondary'
                }
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
                            title: data.success ? 'Success' : 'Error',
                            text: data.success ? 'Product deleted successfully.' : data.error || 'Failed to delete product.',
                            customClass: { confirmButton: 'btn btn-primary' }
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
                            customClass: { confirmButton: 'btn btn-secondary' }
                        });
                    });
                }
            });
        }

        function updatePersonalInfo() {
            Swal.fire({
                icon: 'info',
                title: 'Info',
                text: 'Update personal info functionality not implemented yet.',
                customClass: { confirmButton: 'btn btn-primary' }
            });
        }

        function updatePassword() {
            Swal.fire({
                icon: 'info',
                title: 'Info',
                text: 'Update password functionality not implemented yet.',
                customClass: { confirmButton: 'btn btn-primary' }
            });
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
    </script>
</body>
</html>