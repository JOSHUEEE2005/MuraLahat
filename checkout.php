<?php
session_start();
require_once 'classes/database.php';

$con = new database();
$customerId = $_SESSION['customer_id'] ?? 'guest_' . uniqid();
$cartItems = $con->getCart($customerId);
$total = array_sum(array_map(function($item) {
    return floatval($item['Quantity']) * floatval($item['Price']);
}, $cartItems));
$points = 0;
$customerType = strpos($customerId, 'guest_') === 0 ? 'guest' : 'member';
if ($customerType === 'member') {
    $pointsResult = $con->getCustomerPoints($customerId);
    $points = $pointsResult['success'] ? (int)$pointsResult['points'] : 0;
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Checkout - Mura Lahat Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { background-color: #f8f9fa; }
        .checkout-container {
            max-width: 800px;
            margin: 50px auto;
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .section-title {
            font-size: 2rem;
            font-weight: 700;
            color: #343a40;
            margin-bottom: 20px;
        }
        .cart-table th, .cart-table td { vertical-align: middle; }
        .points-info { margin: 20px 0; font-size: 1.1rem; }
        .btn-checkout {
            background-color: #28a745;
            border: none;
            padding: 10px 20px;
            font-size: 1.1rem;
        }
        .btn-checkout:hover { background-color: #218838; }
    </style>
</head>
<body>
    <div class="checkout-container">
        <h2 class="section-title">Checkout</h2>
        <?php if (empty($cartItems)): ?>
            <div class="alert alert-warning">Your cart is empty.</div>
        <?php else: ?>
            <table class="table cart-table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Quantity</th>
                        <th>Price</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cartItems as $item): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['Product_Name']); ?></td>
                            <td><?php echo htmlspecialchars($item['Quantity']); ?></td>
                            <td>₱<?php echo number_format($item['Price'], 2); ?></td>
                            <td>₱<?php echo number_format($item['Quantity'] * $item['Price'], 2); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="3" class="text-end">Total:</th>
                        <th>₱<?php echo number_format($total, 2); ?></th>
                    </tr>
                </tfoot>
            </table>
            <?php if ($customerType === 'member'): ?>
                <div class="points-info">
                    <strong>Points Available:</strong> <span id="pointsBalance"><?php echo $points; ?></span>
                    <div class="form-check mt-2">
                        <input type="checkbox" class="form-check-input" id="usePoints" <?php echo $points < 1 ? 'disabled' : ''; ?>>
                        <label class="form-check-label" for="usePoints">Use Points for Discount (1 point = ₱1)</label>
                    </div>
                </div>
            <?php endif; ?>
            <form id="checkoutForm">
                <div class="mb-3">
                    <label for="cashPaid" class="form-label">Cash Paid (₱)</label>
                    <input type="number" class="form-control" id="cashPaid" step="0.01" min="0">
                </div>
                <button type="submit" class="btn btn-checkout">Complete Purchase</button>
            </form>
        <?php endif; ?>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    <script>
        function refreshPointsBalance(customerId) {
            fetch('./get_points.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ customerId: customerId })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('pointsBalance').textContent = data.points;
                    document.getElementById('usePoints').disabled = data.points < 1;
                }
            })
            .catch(error => console.error('Error refreshing points:', error));
        }

        document.getElementById('checkoutForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const cashPaidInput = document.getElementById('cashPaid');
            const cashPaid = cashPaidInput.value ? parseFloat(cashPaidInput.value) : 0;
            const usePoints = <?php echo $customerType === 'member' ? 'document.getElementById("usePoints").checked' : 'false'; ?>;
            const total = <?php echo $total; ?>;
            const points = parseInt(document.getElementById('pointsBalance').textContent);
            const customerType = '<?php echo $customerType; ?>';
            const customerId = '<?php echo $customerId; ?>';

            if (usePoints && points >= total && cashPaidInput.value && cashPaid > 0) {
                Swal.fire({
                    icon: 'info',
                    title: 'Points Sufficient',
                    text: 'Your points cover the full amount. No cash is needed.',
                    customClass: { confirmButton: 'btn btn-primary' }
                });
                cashPaidInput.value = '';
                return;
            }

            if (!usePoints && (isNaN(cashPaid) || cashPaid < total)) {
                Swal.fire({
                    icon: 'error',
                    title: 'Invalid Input',
                    text: 'Please enter a cash amount at least equal to the total.',
                    customClass: { confirmButton: 'btn btn-secondary' }
                });
                return;
            }

            const payload = {
                cashPaid: cashPaid,
                total: total,
                customerType: customerType,
                customerId: customerId,
                usePoints: usePoints
            };
            console.log('Checkout Payload:', payload);

            fetch('./process_transaction.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            })
            .then(response => {
                console.log('Fetch response status:', response.status);
                if (!response.ok) {
                    return response.text().then(text => {
                        throw new Error(`HTTP error! status: ${response.status}, response: ${text}`);
                    });
                }
                return response.json();
            })
            .then(data => {
                console.log('Fetch response data:', data);
                if (data.success) {
                    if (customerType === 'member' && document.getElementById('pointsBalance')) {
                        document.getElementById('pointsBalance').textContent = data.pointsBalance;
                        document.getElementById('usePoints').disabled = data.pointsBalance < 1;
                        refreshPointsBalance(customerId);
                    }
                    Swal.fire({
                        icon: 'success',
                        title: 'Purchase Complete',
                        html: `Total: ₱${data.total}<br>Change: ₱${data.change}<br>Points Used: ${data.pointsUsed}<br>Points Earned: ${data.pointsEarned}<br>Points Balance: ${data.pointsBalance}`,
                        customClass: { confirmButton: 'btn btn-primary' }
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.error,
                        customClass: { confirmButton: 'btn btn-secondary' }
                    });
                }
            })
            .catch(error => {
                console.error('Fetch error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: `Failed to process transaction: ${error.message}`,
                    customClass: { confirmButton: 'btn btn-secondary' }
                });
            });
        });
    </script>
</body>
</html>