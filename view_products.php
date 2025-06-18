<?php
session_start();
require_once 'classes/database.php';

try {
    $con = new database();
    $products = [];
    if ($con) {
        $products = $con->getProductsWithPrices();
    }
    $cartItems = [];
    $cartTotal = 0.00;
    $cartCount = 0;
    if (isset($_SESSION['customer_id'])) {
        $customerId = $_SESSION['customer_id'];
        $cartItems = $con->getCart($customerId);
        $cartTotal = array_sum(array_map(function($item) {
            return floatval($item['Quantity'] ?? 0) * floatval($item['Price'] ?? 0);
        }, $cartItems));
        $cartCount = count($cartItems);
    }
} catch (PDOException $e) {
    error_log('Database Error: ' . $e->getMessage());
    $products = [];
    $cartItems = [];
    $cartTotal = 0.00;
    $cartCount = 0;
} catch (Exception $e) {
    error_log('General Error: ' . $e->getMessage());
    $products = [];
    $cartItems = [];
    $cartTotal = 0.00;
    $cartCount = 0;
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>View Products - Mura Lahat Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        /* Ensure box-sizing is applied globally */
        * {
            box-sizing: border-box;
        }

        /* Body Styles */
        body {
            background-image: url('button_images/otsootso.png');
            background-size: cover;
            background-position: center;
            background-attachment: scroll;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            position: relative;
            margin: 0;
            min-height: 100vh;
            overflow-x: auto;
        }

        /* Add a semi-transparent overlay to improve text readability */
        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(255, 255, 255, 0.7);
            z-index: -1;
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
            background-color: #e90e00;
        }

        .sidebar-header {
            border-bottom: 1px solid rgba(255,255,255,0.1);
            padding: 15px;
            text-align: center;
        }

        .sidebar img {
            height: 200px;
            width: auto;
            transition: transform 0.3s ease;
        }

        .sidebar img:hover {
            transform: scale(1.1);
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
            min-width: calc(100vw - 250px);
        }

        .container {
            max-width: 1200px;
            background: transparent;
            border-radius: 12px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 30px;
            margin-top: 50px;
            position: relative;
            z-index: 1;
        }

        .section-title {
            font-size: 2.5rem;
            font-weight: 700;
            color: #343a40;
            text-align: center;
            margin-bottom: 40px;
        }

        .product-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: none;
            border-radius: 10px;
            overflow: hidden;
            background: #ffffff;
            text-align: center;
            cursor: pointer;
            width: 100%;
            height: auto;
        }

        .product-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
        }

        .product-img {
            width: 100%;
            height: 150px;
            object-fit: contain;
            display: block;
        }

        .product-title {
            font-size: 1.2rem;
            font-weight: 600;
            color: #343a40;
            margin-top: 10px;
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

        .cart-table {
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
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
            transition: background-color 0.3s ease;
        }

        .btn-remove-item:hover {
            background-color: #c82333;
        }

        .category-section {
            margin-bottom: 40px;
        }

        .category-title {
            font-size: 1.8rem;
            font-weight: 600;
            color: #343a40;
            position: relative;
            margin-bottom: 20px;
        }

        .category-title::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 60px;
            height: 3px;
            background-color: #007bff;
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

            .container {
                max-width: 100%;
                padding: 20px;
            }
        }

        @media (max-width: 576px) or (max-device-width: 576px) {
            .sidebar {
                width: 60px;
            }

            .main-content {
                margin-left: 60px;
                min-width: calc(100vw - 60px);
            }

            .container {
                margin-top: 20px;
                padding: 15px;
            }

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
    <!-- Sidebar -->
    <div class="sidebar text-white">
        <div class="sidebar-header p-3">
            <h3 class="text-center"><img src="button_images/jobart.png" alt="logo"></h3>
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
            <a class="nav-link " href="#" onclick="logout()"><i class="bi bi-box-arrow-right me-2"></i><span>Logout</span></a>
        </div>
    </div>
    <div class="main-content">
        <div class="container my-5">
            <h2 class="section-title">Our Products</h2>
            <div class="cart-total">Cart Total: ₱<span id="cartTotal"><?php echo number_format($cartTotal, 2); ?></span> (<span id="cartCount"><?php echo $cartCount; ?></span> items)</div>
            <?php
            $groupedProducts = [];
            foreach ($products as $product) {
                $categoryNames = !empty($product['Category_Names']) ? explode(',', $product['Category_Names']) : ['Uncategorized'];
                foreach ($categoryNames as $categoryName) {
                    $categoryName = trim($categoryName);
                    if (!isset($groupedProducts[$categoryName])) {
                        $groupedProducts[$categoryName] = [];
                    }
                    if (!in_array($product['Product_ID'], array_column($groupedProducts[$categoryName], 'Product_ID'))) {
                        $groupedProducts[$categoryName][] = $product;
                    }
                }
            }
            ksort($groupedProducts);
            foreach ($groupedProducts as $categoryName => $categoryProducts): ?>
                <div class="category-section mb-5">
                    <h3 class="category-title mb-4"><?php echo htmlspecialchars($categoryName); ?></h3>
                    <div class="row row-cols-1 row-cols-md-3 g-4">
                        <?php foreach ($categoryProducts as $product): ?>
                            <div class="col">
                                <div class="card product-card">
                                    <img src="<?php echo !empty($product['Product_Image']) && file_exists($product['Product_Image']) ? htmlspecialchars($product['Product_Image']) : 'https://via.placeholder.com/300x220?text=' . urlencode($product['Product_Name']); ?>" class="product-img" alt="<?php echo htmlspecialchars($product['Product_Name']); ?>">
                                    <div class="card-body">
                                        <h5 class="card-title product-title"><?php echo htmlspecialchars($product['Product_Name']); ?></h5>
                                        <p class="card-text product-price">₱<?php echo number_format($product['Price'] ?? 0, 2); ?></p>
                                        <p class="card-text stock-info" data-product-id="<?php echo $product['Product_ID']; ?>">In Stock: <?php echo htmlspecialchars($product['Product_Stock']); ?></p>
                                        <button class="btn btn-add-to-cart" onclick="showAddToCartModal(<?php echo $product['Product_ID']; ?>, '<?php echo htmlspecialchars($product['Product_Name'], ENT_QUOTES); ?>', <?php echo floatval($product['Price'] ?? 0); ?>, <?php echo intval($product['Product_Stock']); ?>)">
                                            <i class="bi bi-cart-plus me-2"></i>Add to Cart
                                        </button>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
            <button class="btn btn-view-cart" id="viewCartButton">
                <i class="bi bi-cart me-2"></i>View Cart
            </button>
            <button class="btn btn-membership" id="membershipButton">
                <i class="bi bi-person-plus me-2"></i>Membership
            </button>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    <script>
        function showAddToCartModal(productId, productName, price, stock) {
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
                customClass: { confirmButton: 'btn btn-primary', cancelButton: 'btn btn-secondary' },
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
        function addToCart(productId, productName, quantity, price) {
            fetch('add_to_cart.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    customerId: <?php echo isset($_SESSION['customer_id']) ? json_encode($_SESSION['customer_id']) : 'null'; ?>,
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
        function removeFromCart(cartId, productName) {
            Swal.fire({
                title: 'Remove Item',
                text: `Are you sure you want to remove ${productName} from your cart?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Remove',
                cancelButtonText: 'Cancel',
                customClass: { confirmButton: 'btn btn-danger', cancelButton: 'btn btn-secondary' }
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch('remove_from_cart.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ cartId })
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
                                const cartRow = document.querySelector(`tr[data-cart-id="${cartId}"]`);
                                if (cartRow) cartRow.remove();
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
        document.getElementById('viewCartButton').addEventListener('click', function() {
            fetch('get_cart.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    customerId: <?php echo isset($_SESSION['customer_id']) ? json_encode($_SESSION['customer_id']) : 'null'; ?>
                })
            })
            .then(response => response.json())
            .then(data => {
                console.log('Cart Data:', data);
                const cartItems = data.items;
                if (!data.success || cartItems.length === 0) {
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
                                    <tr data-cart-id="${item.Cart_ID}">
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
                                            <button class="btn btn-remove-item" onclick="removeFromCart('${item.Cart_ID}', '${item.Product_Name.replace(/'/g, "\\'")}')">
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
                    customClass: { confirmButton: 'btn btn-primary', cancelButton: 'btn btn-secondary' },
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
                console.error('Cart Fetch Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Connection Error',
                    text: `Failed to connect to server: ${error.message}`,
                    customClass: { confirmButton: 'btn btn-secondary' }
                });
            });
        });
        function showPaymentModal(total) {
            Swal.fire({
                title: 'Customer Information',
                html: `
                    <p>Total Amount: ₱${parseFloat(total).toFixed(2)}</p>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="radio" name="customerType" id="guestCustomer" value="guest" checked>
                        <label class="form-check-label" for="guestCustomer">Guest Checkout</label>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="radio" name="customerType" id="memberCustomer" value="member">
                        <label class="form-check-label" for="memberCustomer">Member Checkout</label>
                    </div>
                    <div id="memberFields" style="display:none;">
                        <label for="customerId" class="form-label">Customer ID</label>
                        <input type="text" id="customerId" class="swal2-input" placeholder="Enter your member number">
                        <div id="pointsFields" style="display:none;">
                            <p id="pointsBalance"></p>
                            <p id="pointsToUse"></p>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="usePoints" name="usePoints">
                                <label class="form-check-label" for="usePoints">Use Points for Discount</label>
                            </div>
                        </div>
                    </div>
                    <p id="discountedTotal" class="mt-3">Amount to Pay: ₱${parseFloat(total).toFixed(2)}</p>
                    <label for="cashPaid" class="form-label">Cash Paid</label>
                    <input type="number" id="cashPaid" class="swal2-input" min="0" step="0.01" required>
                    <p id="change" class="mt-3">Change: ₱0.00</p>
                `,
                showCancelButton: true,
                confirmButtonText: 'Complete Purchase',
                cancelButtonText: 'Cancel',
                customClass: { confirmButton: 'btn btn-primary', cancelButton: 'btn btn-secondary' },
                preConfirm: () => {
                    const customerType = document.querySelector('input[name="customerType"]:checked').value;
                    const cashPaid = parseFloat(document.getElementById('cashPaid').value);
                    let customerId = null;
                    let usePoints = false;
                    let discountedTotal = total;

                    if (customerType === 'member') {
                        customerId = document.getElementById('customerId').value.trim();
                        if (!customerId || isNaN(customerId)) {
                            Swal.showValidationMessage('Please enter a valid Customer ID');
                            return false;
                        }
                        usePoints = document.getElementById('usePoints').checked;
                        if (usePoints) {
                            const pointsToUse = parseInt(document.getElementById('pointsToUse').dataset.points || 0);
                            discountedTotal = Math.max(0, total - pointsToUse);
                        }
                    }

                    if (isNaN(cashPaid) || cashPaid < discountedTotal) {
                        Swal.showValidationMessage(`Cash paid must be at least ₱${discountedTotal.toFixed(2)}`);
                        return false;
                    }

                    return { customerType, customerId, cashPaid, total, usePoints };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    const { customerType, customerId, cashPaid, total, usePoints } = result.value;
                    processTransaction(customerType, customerId, cashPaid, total, usePoints);
                }
            });

            const customerTypeRadios = document.querySelectorAll('input[name="customerType"]');
            const memberFields = document.getElementById('memberFields');
            const pointsFields = document.getElementById('pointsFields');
            const customerIdInput = document.getElementById('customerId');
            const pointsBalance = document.getElementById('pointsBalance');
            const pointsToUse = document.getElementById('pointsToUse');
            const usePointsCheckbox = document.getElementById('usePoints');
            const cashPaidInput = document.getElementById('cashPaid');
            const discountedTotalElement = document.getElementById('discountedTotal');
            const changeElement = document.getElementById('change');

            customerTypeRadios.forEach(radio => {
                radio.addEventListener('change', function() {
                    memberFields.style.display = this.value === 'member' ? 'block' : 'none';
                    pointsFields.style.display = 'none';
                    pointsBalance.textContent = '';
                    pointsToUse.textContent = '';
                    usePointsCheckbox.checked = false;
                    cashPaidInput.value = '';
                    discountedTotalElement.textContent = `Amount to Pay: ₱${total.toFixed(2)}`;
                    changeElement.textContent = 'Change: ₱0.00';
                });
            });

            customerIdInput.addEventListener('blur', function() {
                const customerId = this.value.trim();
                if (customerId && !isNaN(customerId)) {
                    fetch('get_customer_points.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ customerId })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            pointsFields.style.display = 'block';
                            pointsBalance.textContent = `Available Points: ${data.points}`;
                            const pointsUsable = Math.min(data.points, Math.floor(total));
                            pointsToUse.textContent = `Points to Use: ${pointsUsable}`;
                            pointsToUse.dataset.points = pointsUsable;
                            updatePaymentDetails();
                        } else {
                            pointsFields.style.display = 'none';
                            Swal.showValidationMessage(data.error || 'Invalid Customer ID');
                        }
                    })
                    .catch(error => {
                        pointsFields.style.display = 'none';
                        Swal.showValidationMessage(`Failed to fetch points: ${error.message}`);
                    });
                }
            });

            function updatePaymentDetails() {
                let discountedTotal = total;
                if (usePointsCheckbox.checked && pointsToUse.dataset.points) {
                    const pointsUsable = parseInt(pointsToUse.dataset.points);
                    discountedTotal = Math.max(0, total - pointsUsable);
                }
                discountedTotalElement.textContent = `Amount to Pay: ₱${discountedTotal.toFixed(2)}`;
                cashPaidInput.min = discountedTotal;

                const cashPaid = parseFloat(cashPaidInput.value) || 0;
                const change = cashPaid >= discountedTotal ? cashPaid - discountedTotal : 0;
                changeElement.textContent = `Change: ₱${change.toFixed(2)}`;
            }

            usePointsCheckbox.addEventListener('change', updatePaymentDetails);
            cashPaidInput.addEventListener('input', updatePaymentDetails);
        }
        function processTransaction(customerType, customerId, cashPaid, total, usePoints) {
            fetch('process_transaction.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    customerType,
                    customerId,
                    cashPaid,
                    total,
                    usePoints
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Purchase Complete',
                        html: `
                            <p>Total: ₱${parseFloat(data.total).toFixed(2)}</p>
                            <p>Change: ₱${parseFloat(data.change).toFixed(2)}</p>
                            ${data.pointsUsed > 0 ? `<p>Points Used: ${data.pointsUsed}</p>` : ''}
                            ${data.pointsEarned > 0 ? `<p>Points Earned: ${data.pointsEarned}</p>` : ''}
                        `,
                        customClass: { confirmButton: 'btn btn-primary' }
                    }).then(() => {
                        document.getElementById('cartTotal').textContent = '0.00';
                        document.getElementById('cartCount').textContent = '0';
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.error || 'Transaction failed.',
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
        document.getElementById('membershipButton').addEventListener('click', function() {
            Swal.fire({
                title: 'Become a Member',
                html: `
                    <input type="text" id="firstName" class="swal2-input" placeholder="First Name" required>
                    <input type="text" id="lastName" class="swal2-input" placeholder="Last Name" required>
                    <input type="text" id="phoneNumber" class="swal2-input" placeholder="Phone Number (10 digits)" required>
                    <input type="text" id="street" class="swal2-input" placeholder="Street" required>
                    <input type="text" id="barangay" class="swal2-input" placeholder="Barangay" required>
                    <input type="text" id="city" class="swal2-input" placeholder="City" required>
                `,
                showCancelButton: true,
                confirmButtonText: 'Submit',
                cancelButtonText: 'Cancel',
                customClass: { confirmButton: 'btn btn-primary', cancelButton: 'btn btn-secondary' },
                preConfirm: () => {
                    const firstName = document.getElementById('firstName').value.trim();
                    const lastName = document.getElementById('lastName').value.trim();
                    const phoneNumber = document.getElementById('phoneNumber').value.trim();
                    const street = document.getElementById('street').value.trim();
                    const barangay = document.getElementById('barangay').value.trim();
                    const city = document.getElementById('city').value.trim();
                    if (!firstName || !lastName || !phoneNumber || !street || !barangay || !city) {
                        Swal.showValidationMessage('All fields are required');
                        return false;
                    }
                    if (!/^\d{10}$/.test(phoneNumber)) {
                        Swal.showValidationMessage('Phone number must be 10 digits');
                        return false;
                    }
                    return { firstName, lastName, phone: phoneNumber, street, barangay, city };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    const { firstName, lastName, phone, street, barangay, city } = result.value;
                    const formData = new FormData();
                    formData.append('firstName', firstName);
                    formData.append('lastName', lastName);
                    formData.append('phone', phone);
                    formData.append('street', street);
                    formData.append('barangay', barangay);
                    formData.append('city', city);
                    console.log('FormData:', [...formData.entries()]); // Debug
                    fetch('add_member.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.text()) // Debug: Capture raw response
                    .then(text => {
                        console.log('Raw Response:', text); // Debug
                        try {
                            const data = JSON.parse(text);
                            Swal.fire({
                                icon: data.success ? 'success' : 'error',
                                title: data.success ? 'Membership Created' : 'Error',
                                text: data.success ? `Membership created! Your Customer ID is ${data.customerId}.` : data.error,
                                customClass: { confirmButton: 'btn btn-primary' }
                            });
                        } catch (e) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Invalid Response',
                                text: `Server returned invalid JSON: ${e.message}`,
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
        });
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
                Swal.fire({
                    icon: data.success ? 'success' : 'error',
                    title: 'Success',
                    text: data.success ? 'Successfully logged out.' : data.error,
                    customClass: { confirmButton: 'btn btn-primary' }
                }).then(() => {
                    if (data.success) {
                        window.location = 'index.php';
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
    </script>
</body>
</html>