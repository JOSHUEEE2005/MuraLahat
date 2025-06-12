<?php
session_start();
require_once 'classes/database.php';

try {
    $con = new database();
    $products = $con->getProductsWithPrices();
    $customerId = $_SESSION['customer_id'] ?? 1; // Fallback customer ID
    $cartItems = $con->getCart($customerId);
    $cartTotal = array_sum(array_map(function($item) {
        return floatval($item['Quantity']) * floatval($item['Price']);
    }, $cartItems));
    $cartCount = count($cartItems);
} catch (Exception $e) {
    error_log('Error in view_products.php: ' . $e->getMessage() . ' in ' . $e->getFile() . ' on line ' . $e->getLine(), 3, 'php_errors.log');
    die('Error loading page: ' . htmlspecialchars($e->getMessage()));
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>View Products - Mura Lahat Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .navbar {
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .product-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: none;
            border-radius: 12px;
            overflow: hidden;
            background: #ffffff;
            margin-bottom: 20px;
        }
        .product-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
        }
        .product-img {
            width: 100%;
            height: 220px;
            object-fit: cover;
            border-bottom: 1px solid #dee2e6;
        }
        .product-title {
            font-size: 1.3rem;
            font-weight: 600;
            color: #343a40;
            margin-bottom: 10px;
        }
        .product-price {
            font-size: 1.15rem;
            font-weight: 500;
            color: #28a745;
        }
        .stock-info {
            font-size: 0.95rem;
            color: #6c757d;
            margin-bottom: 15px;
        }
        .btn-add-to-cart {
            background-color: #007bff;
            border: none;
            border-radius: 50px;
            padding: 10px 25px;
            font-weight: 500;
            transition: background-color 0.3s ease;
        }
        .btn-add-to-cart:hover {
            background-color: #0056b3;
        }
        .btn-view-cart {
            background-color: #17a2b8;
            border: none;
            border-radius: 50px;
            padding: 10px 25px;
            font-weight: 500;
            transition: background-color 0.3s ease;
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 1000;
        }
        .btn-view-cart:hover {
            background-color: #138496;
        }
        .btn-membership {
            background-color: #ffc107;
            border: none;
            border-radius: 50px;
            padding: 10px 25px;
            font-weight: 500;
            transition: background-color 0.3s ease;
            position: fixed;
            bottom: 70px;
            right: 20px;
            z-index: 1000;
        }
        .btn-membership:hover {
            background-color: #e0a800;
        }
        .cart-total {
            font-size: 1.1rem;
            font-weight: 600;
            color: #343a40;
            margin-bottom: 20px;
        }
        .container {
            max-width: 1200px;
        }
        .section-title {
            font-size: 2rem;
            font-weight: 700;
            color: #343a40;
            margin-bottom: 30px;
        }
        .cart-table img {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 4px;
        }
        .cart-table th, .cart-table td {
            vertical-align: middle;
        }
        .btn-remove-item {
            background-color: #dc3545;
            border: none;
            border-radius: 50px;
            padding: 5px 15px;
            font-size: 0.9rem;
        }
        .btn-remove-item:hover {
            background-color: #c82333;
        }
        @media (max-width: 576px) {
            .btn-view-cart, .btn-membership {
                width: 150px;
                right: 10px;
            }
            .btn-membership {
                bottom: 80px;
            }
            .cart-table {
                font-size: 0.9rem;
            }
            .cart-table img {
                width: 40px;
                height: 40px;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Mura Lahat Store</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="add_product.php">Add Product</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="view_products.php">View Products</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="manage_members.php">Manage Members</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="profileDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-person-circle"></i> Profile
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdown">
                            <li><a class="dropdown-item" href="profile.php"><i class="bi bi-person-circle me-2"></i> See Profile</a></li>
                            <li><button class="dropdown-item" onclick="updatePersonalInfo()"><i class="bi bi-pencil-square me-2"></i> Update Info</button></li>
                            <li><button class="dropdown-item" onclick="updatePassword()"><i class="bi bi-key me-2"></i> Update Password</button></li>
                            <li><button class="dropdown-item text-danger" onclick="logout()"><i class="bi bi-box-arrow-right me-2"></i> Logout</button></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container my-5">
        <h2 class="section-title">Our Products</h2>
        <div class="cart-total">Cart Total: ₱<span id="cartTotal"><?php echo number_format($cartTotal, 2); ?></span> (<span id="cartCount"><?php echo $cartCount; ?></span> items)</div>
        <div class="row row-cols-1 row-cols-md-3 g-4">
            <?php if (!empty($products)): ?>
                <?php foreach ($products as $product): ?>
                    <div class="col">
                        <div class="card product-card">
                            <img src="<?php echo !empty($product['Product_Image']) && file_exists($product['Product_Image']) ? htmlspecialchars($product['Product_Image']) : 'https://via.placeholder.com/300x220?text=' . urlencode($product['Product_Name']); ?>" class="product-img" alt="<?php echo htmlspecialchars($product['Product_Name']); ?>">
                            <div class="card-body">
                                <h5 class="card-title product-title"><?php echo htmlspecialchars($product['Product_Name']); ?></h5>
                                <p class="card-text product-price">₱<?php echo number_format($product['Price'], 2); ?></p>
                                <p class="card-text stock-info" data-product-id="<?php echo $product['Product_ID']; ?>">In Stock: <?php echo htmlspecialchars($product['Product_Stock']); ?></p>
                                <button class="btn btn-add-to-cart" onclick="showAddToCartModal(<?php echo $product['Product_ID']; ?>, '<?php echo htmlspecialchars($product['Product_Name'], ENT_QUOTES); ?>', <?php echo floatval($product['Price']); ?>, <?php echo intval($product['Product_Stock']); ?>)">
                                    <i class="bi bi-cart-plus me-2"></i>Add to Cart
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center">
                    <p class="text-muted">No products available at the moment.</p>
                </div>
            <?php endif; ?>
        </div>
        <button class="btn btn-view-cart" id="viewCartButton">
            <i class="bi bi-cart me-2"></i>View Cart
        </button>
        <button class="btn btn-membership" id="membershipButton">
            <i class="bi bi-person-plus me-2"></i>Membership
        </button>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    <script>
        // Show Add to Cart modal
        function showAddToCartModal(productId, productName, price, stock) {
            if (!productId || !productName || isNaN(price) || isNaN(stock)) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Invalid product data.',
                    customClass: { confirmButton: 'btn btn-secondary' }
                });
                return;
            }
            Swal.fire({
                title: `Add ${productName} to Cart`,
                html: `
                    <p>Price: ₱${parseFloat(price).toFixed(2)}</p>
                    <p>Stock: ${stock}</p>
                    <label for="quantity" class="form-label">Quantity</label>
                    <input type="number" id="quantity" class="swal2-input" min="1" max="${stock}" value="1" required>
                    <p id="totalPrice" class="mt-3">Total: ₱${parseFloat(price).toFixed(2)}</p>
                `,
                showCancelButton: true,
                confirmButtonText: 'Add to Cart',
                cancelButtonText: 'Cancel',
                customClass: {
                    confirmButton: 'btn btn-primary',
                    cancelButton: 'btn btn-secondary'
                },
                preConfirm: () => {
                    const quantity = parseInt(document.getElementById('quantity').value);
                    if (!quantity || isNaN(quantity) || quantity < 1 || quantity > stock) {
                        Swal.showValidationMessage('Please enter a valid quantity');
                        return false;
                    }
                    return { productId, productName, quantity, price, total: quantity * price };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    const { productId, quantity, price } = result.value;
                    addToCart(productId, productName, quantity, price);
                }
            });

            const quantityInput = document.getElementById('quantity');
            if (quantityInput) {
                quantityInput.addEventListener('input', function () {
                    const quantity = parseInt(this.value);
                    if (quantity && quantity > 0 && quantity <= stock) {
                        document.getElementById('totalPrice').textContent = `Total: ₱${(quantity * parseFloat(price)).toFixed(2)}`;
                    }
                });
            }
        }

        // Add item to cart via fetch
        function addToCart(productId, productName, quantity, price) {
            fetch('add_to_cart.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    customerId: <?php echo json_encode($customerId); ?>,
                    productId,
                    quantity,
                    price
                })
            })
            .then(response => response.json())
            .then(data => {
                Swal.fire({
                    icon: data.success ? 'success' : 'error',
                    title: data.success ? 'Added to Cart' : 'Error',
                    text: data.success ? `${quantity} ${productName}(s) added to cart.` : data.error,
                    customClass: { confirmButton: 'btn btn-primary' }
                });
                if (data.success) {
                    document.getElementById('cartTotal').textContent = parseFloat(data.cartTotal).toFixed(2);
                    document.getElementById('cartCount').textContent = data.cartCount;
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

        // Remove item from cart
        function removeFromCart(cartId, productName) {
            Swal.fire({
                title: 'Remove Item',
                text: `Are you sure you want to remove ${productName} from your cart?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Remove',
                cancelButtonText: 'Cancel',
                customClass: {
                    confirmButton: 'btn btn-danger',
                    cancelButton: 'btn btn-secondary'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch('remove_from_cart.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({
                            customerId: <?php echo json_encode($customerId); ?>,
                            cartId
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Removed',
                                text: `${productName} has been removed from your cart.`,
                                customClass: { confirmButton: 'btn btn-primary' }
                            }).then(() => {
                                document.getElementById('cartTotal').textContent = parseFloat(data.cartTotal).toFixed(2);
                                document.getElementById('cartCount').textContent = data.cartCount;
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: data.error || 'Failed to remove item.',
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
            });
        }

        // Show View Cart modal
        document.getElementById('viewCartButton').addEventListener('click', function () {
            fetch('get_cart.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ customerId: <?php echo json_encode($customerId); ?> })
            })
            .then(response => response.json())
            .then(data => {
                if (!data.success) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.error || 'Failed to fetch cart.',
                        customClass: { confirmButton: 'btn btn-secondary' }
                    });
                    return;
                }

                const cartItems = data.items;
                if (cartItems.length === 0) {
                    Swal.fire({
                        icon: 'info',
                        title: 'Cart is Empty',
                        text: 'No items in your cart.',
                        customClass: { confirmButton: 'btn btn-primary' }
                    });
                    return;
                }

                let total = 0;
                const html = `
                    <table class="table cart-table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Total</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${cartItems.map(item => {
                                const price = parseFloat(item.Price) || 0;
                                const quantity = parseInt(item.Quantity) || 0;
                                const itemTotal = price * quantity;
                                total += itemTotal;
                                const imageSrc = item.Product_Image && item.Product_Image !== 'NULL' ? item.Product_Image : 'https://via.placeholder.com/50x50?text=' + encodeURIComponent(item.Product_Name);
                                return `
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="${imageSrc}" alt="${item.Product_Name || 'Unknown Product'}">
                                                <span class="ms-2">${item.Product_Name || 'Unknown Product'}</span>
                                            </div>
                                        </td>
                                        <td>₱${price.toFixed(2)}</td>
                                        <td>${quantity}</td>
                                        <td>₱${itemTotal.toFixed(2)}</td>
                                        <td>
                                            <button class="btn btn-remove-item" onclick="removeFromCart(${item.Cart_ID}, '${item.Product_Name.replace(/'/g, "\\'")}')">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                `;
                            }).join('')}
                        </tbody>
                    </table>
                    <hr>
                    <p class="text-end"><strong>Grand Total: ₱${total.toFixed(2)}</strong></p>
                `;

                Swal.fire({
                    title: 'Your Cart',
                    html: html,
                    showCancelButton: true,
                    confirmButtonText: 'Proceed to Payment',
                    cancelButtonText: 'Close',
                    customClass: {
                        confirmButton: 'btn btn-primary',
                        cancelButton: 'btn btn-secondary'
                    },
                    width: '800px'
                }).then((result) => {
                    if (result.isConfirmed) {
                        if (isNaN(total) || total <= 0) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Invalid cart total.',
                                customClass: { confirmButton: 'btn btn-secondary' }
                            });
                            return;
                        }
                        showPaymentModal(total);
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
        });

        // Show Payment modal
        function showPaymentModal(total) {
            Swal.fire({
                title: 'Payment',
                html: `
                    <p>Total Amount: ₱${parseFloat(total).toFixed(2)}</p>
                    <label for="cashPaid" class="form-label">Cash Paid</label>
                    <input type="number" id="cashPaid" class="swal2-input" min="0" step="0.01" required>
                    <p id="change" class="mt-3">Change: ₱0.00</p>
                `,
                showCancelButton: true,
                confirmButtonText: 'Finish Transaction',
                cancelButtonText: 'Cancel',
                customClass: {
                    confirmButton: 'btn btn-primary',
                    cancelButton: 'btn btn-secondary'
                },
                preConfirm: () => {
                    const cashPaid = parseFloat(document.getElementById('cashPaid').value);
                    if (isNaN(cashPaid) || cashPaid < total) {
                        Swal.showValidationMessage('Please enter a valid cash amount greater than or equal to the total');
                        return false;
                    }
                    return { cashPaid };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    const { cashPaid } = result.value;
                    processTransaction(cashPaid, total);
                }
            });

            const cashPaidInput = document.getElementById('cashPaid');
            if (cashPaidInput) {
                cashPaidInput.addEventListener('input', function() {
                    const cashPaid = parseFloat(this.value);
                    if (!isNaN(cashPaid) && cashPaid >= total) {
                        document.getElementById('change').textContent = `Change: ₱${(cashPaid - total).toFixed(2)}`;
                    } else {
                        document.getElementById('change').textContent = 'Change: ₱0.00';
                    }
                });
            }
        }

        // Process transaction
        function processTransaction(cashPaid, total) {
            fetch('process_transaction.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    customerId: <?php echo json_encode($customerId); ?>,
                    cashPaid
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: 'Transaction Completed',
                        icon: 'success',
                        html: `Transaction completed successfully!<br>Total: ₱${data.total}<br>Change: ₱${data.change}`,
                        customClass: { confirmButton: 'btn btn-primary' }
                    }).then(() => {
                        document.getElementById('cartTotal').textContent = '0.00';
                        document.getElementById('cartCount').textContent = '0';
                        if (data.items && Array.isArray(data.items)) {
                            data.items.forEach(item => {
                                const stockDisplay = document.querySelector(`.stock-info[data-product-id="${item.Product_ID}"]`);
                                if (stockDisplay) {
                                    const newStock = parseInt(item.Product_Stock) - parseInt(item.Quantity);
                                    stockDisplay.textContent = `In Stock: ${newStock}`;
                                    const addButton = document.querySelector(`button[onclick*="showAddToCartModal(${item.Product_ID})"]`);
                                    if (addButton) {
                                        addButton.setAttribute('onclick', `showAddToCartModal(${item.Product_ID}, '${item.Product_Name}', ${parseFloat(item.Price)}, ${newStock})`);
                                    }
                                }
                            });
                        }
                    });
                } else {
                    Swal.fire({
                        title: 'Error',
                        icon: 'error',
                        text: `Failed to process transaction: ${data.error || 'Unknown error occurred'}`,
                        customClass: { confirmButton: 'btn btn-secondary' }
                    });
                }
            })
            .catch(error => {
                Swal.fire({
                    title: 'Error',
                    icon: 'error',
                    text: `Failed to connect to server: ${error.message}`,
                    customClass: { confirmButton: 'btn btn-secondary' }
                });
            });
        }

        // Show membership modal
        document.getElementById('membershipButton').addEventListener('click', function() {
            Swal.fire({
                title: 'Join Premium Membership',
                html: `
                    <label for="firstName" class="form-label">First Name</label>
                    <input type="text" id="firstName" class="swal2-input" required>
                    <label for="lastName" class="form-label">Last Name</label>
                    <input type="text" id="lastName" class="swal2-input" required>
                    <label for="phoneNumber" class="form-label">Phone Number</label>
                    <input type="text" id="phoneNumber" class="swal2-input" required>
                `,
                showCancelButton: true,
                confirmButtonText: 'Save Membership',
                cancelButtonText: 'Cancel',
                customClass: {
                    confirmButton: 'btn btn-primary',
                    cancelButton: 'btn btn-secondary'
                },
                preConfirm: () => {
                    const firstName = document.getElementById('firstName').value.trim();
                    const lastName = document.getElementById('lastName').value.trim();
                    const phoneNumber = document.getElementById('phoneNumber').value.trim();
                    if (!firstName || !lastName || !phoneNumber) {
                        Swal.showValidationMessage('Please fill in all fields');
                        return false;
                    }
                    if (!/^\d{10,}$/.test(phoneNumber)) {
                        Swal.showValidationMessage('Please enter a valid phone number (at least 10 digits)');
                        return false;
                    }
                    return { firstName, lastName, phoneNumber };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    const { firstName, lastName, phoneNumber } = result.value;
                    fetch('process_membership.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ firstName, lastName, phoneNumber })
                    })
                    .then(response => response.json())
                    .then(data => {
                        Swal.fire({
                            icon: data.success ? 'success' : 'error',
                            title: data.success ? 'Success' : 'Error',
                            text: data.success ? 'Customer has been registered as a premium member.' : data.error,
                            customClass: { confirmButton: 'btn btn-primary' }
                        });
                    })
                    .catch(error => {
                        Swal.fire({
                            title: 'Error',
                            icon: 'error',
                            text: `Failed to connect to server: ${error.message}`,
                            customClass: { confirmButton: 'btn btn-secondary' }
                        });
                    });
                }
            });
        });

        // Helper functions
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
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: 'Successfully logged out.',
                customClass: { confirmButton: 'btn btn-primary' }
            }).then(() => {
                window.location = 'index.php';
            });
        }
    </script>
</body>
</html>