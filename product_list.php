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
                <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'product_list.php' ? 'active' : '' ?>" href="product_list.php">
                    <i class="bi bi-box me-2"></i> Product List
                </a>
            </li>
           
        </ul>
        <div class="sidebar-footer">
            <a class="nav-link" href="#" onclick="logout()"><i class="bi bi-box-arrow-right me-2"></i><span>Logout</span></a>
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
                                    <button class="btn btn-edit btn-sm text-warning" onclick="showUpdateModal(<?php echo $product['Product_ID']; ?>)">
                                        <i class="bi bi-pencil text-warning"></i> Edit
                                    </button>
                                    <button class="btn btn-delete btn-sm text-danger" onclick="deleteProduct(<?php echo $product['Product_ID']; ?>, '<?php echo htmlspecialchars($product['Product_Name'], ENT_QUOTES); ?>')">
                                        <i class="bi bi-trash text-danger"></i> Delete
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