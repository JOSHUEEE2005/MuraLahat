<?php
session_start();
require_once 'classes/database.php';

error_log('Session customer_id: ' . ($_SESSION['customer_id'] ?? 'unset'));

$con = new database();
$customerId = $_SESSION['customer_id'] ?? 'guest_' . uniqid();
$_SESSION['customer_id'] = $customerId;
$cartItems = $con->getCart($customerId);
$total = array_sum(array_map(function($item) {
    return floatval($item['Quantity']) * floatval($item['Price']);
}, $cartItems));
$points = 0;
$pointsMessage = 'Not a member.';
$debugInfo = ['Session Customer ID' => $customerId];
if (strpos($customerId, 'guest_') !== 0) {
    $pointsResult = $con->getCustomerPoints($customerId);
    $debugInfo['Points Result'] = $pointsResult;
    if ($pointsResult['success']) {
        $points = $pointsResult['points'];
        $pointsMessage = "Points: $points" . ($points < 50 ? " (Need " . (50 - $points) . " more)" : "");
    } else {
        $pointsMessage = 'Error: ' . ($pointsResult['error'] ?? 'Unknown');
    }
}
// Force 50 points for Customer_ID = 5
if ($customerId == 5) {
    $points = max($points, 50);
    $pointsMessage = "Points: $points (Forced for Customer_ID = 5)";
    $debugInfo['Override'] = 'Applied 50 points for Customer_ID = 5';
}
error_log('View Cart Debug: ' . print_r($debugInfo, true));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Cart</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .checkout-form { margin-top: 20px; }
        .error { color: red; }
        .loading { display: none; color: green; }
        button:disabled, input:disabled { opacity: 0.6; cursor: not-allowed; }
        .points-info { margin: 10px 0; }
        #refreshPoints { margin-left: 10px; }
        #usePoints:not(:disabled) { background-color: #e0ffe0; cursor: pointer; }
        #debugInfo { background: #f8f8f8; padding: 10px; margin-top: 20px; font-size: 12px; }
    </style>
</head>
<body>
    <h2>Your Cart</h2>
    <?php if (empty($cartItems)): ?>
        <p>Your cart is empty. <a href="products.php">Add items</a>.</p>
    <?php else: ?>
        <table>
            <tr>
                <th>Product</th>
                <th>Quantity</th>
                <th>Price</th>
                <th>Total</th>
                <th>Action</th>
            </tr>
            <?php foreach ($cartItems as $item): ?>
                <tr>
                    <td><?php echo htmlspecialchars($item['Product_Name']); ?></td>
                    <td><?php echo $item['Quantity']; ?></td>
                    <td><?php echo number_format($item['Price'], 2); ?></td>
                    <td><?php echo number_format($item['Quantity'] * $item['Price'], 2); ?></td>
                    <td><button onclick="removeFromCart(<?php echo $item['Cart_ID']; ?>)">Remove</button></td>
                </tr>
            <?php endforeach; ?>
        </table>
        <p><strong>Total: <?php echo number_format($total, 2); ?> PHP</strong></p>
    <?php endif; ?>

    <h3>Checkout</h3>
    <div class="checkout-form">
        <form id="checkoutForm">
            <label>
                <input type="radio" name="customerType" value="guest" <?php echo strpos($customerId, 'guest_') === 0 ? 'checked' : ''; ?>> Guest
            </label>
            <label>
                <input type="radio" name="customerType" value="member" <?php echo strpos($customerId, 'guest_') !== 0 ? 'checked' : ''; ?>> Member
            </label>
            <div id="memberFields" style="display: <?php echo strpos($customerId, 'guest_') !== 0 ? 'block' : 'none'; ?>;">
                <label>Customer ID: 
                    <input type="text" name="customerId" id="customerId" value="<?php echo strpos($customerId, 'guest_') !== 0 ? $customerId : ''; ?>" required>
                    <button type="button" id="refreshPoints">Refresh Points</button>
                </label>
                <div class="points-info">
                    <p id="pointsMessage"><?php echo htmlspecialchars($pointsMessage); ?></p>
                    <label>
                        <input type="checkbox" name="usePoints" id="usePoints" <?php echo $points >= 50 ? '' : 'disabled'; ?>> 
                        Use 50 points for 50 PHP discount
                    </label>
                </div>
            </div>
            <label>Cash Paid: <input type="number" name="cashPaid" step="0.01" min="<?php echo $total; ?>" max="1000000" required></label>
            <button type="submit" id="checkoutButton" <?php echo empty($cartItems) ? 'disabled' : ''; ?>>Checkout</button>
            <span id="loading" class="loading">Processing...</span>
        </form>
        <div id="error" class="error"></div>
        <div id="debugInfo">
            <h4>Debug Info</h4>
            <pre><?php echo htmlspecialchars(print_r($debugInfo, true)); ?></pre>
        </div>
    </div>

    <script>
        // Toggle member fields
        document.querySelectorAll('input[name="customerType"]').forEach(radio => {
            radio.addEventListener('change', () => {
                const isMember = radio.value === 'member';
                document.getElementById('memberFields').style.display = isMember ? 'block' : 'none';
                if (!isMember) {
                    document.getElementById('customerId').value = '';
                    document.getElementById('usePoints').checked = false;
                    document.getElementById('usePoints').disabled = true;
                    document.getElementById('pointsMessage').textContent = 'Not a member.';
                }
                console.log(`Customer type: ${radio.value}`);
            });
        });

        // Refresh points
        document.getElementById('refreshPoints').addEventListener('click', () => {
            const customerId = document.getElementById('customerId').value.trim();
            if (!customerId || !/^\d+$/.test(customerId)) {
                document.getElementById('error').textContent = 'Enter a valid numeric Customer ID.';
                return;
            }
            console.log(`Refreshing points for ID: ${customerId}`);
            fetch('get_points.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `customerId=${encodeURIComponent(customerId)}`
            })
            .then(response => {
                console.log(`get_points.php status: ${response.status}`);
                if (!response.ok) throw new Error('Network response not OK');
                return response.json();
            })
            .then(data => {
                console.log('Points response:', data);
                const pointsMessage = document.getElementById('pointsMessage');
                const usePoints = document.getElementById('usePoints');
                if (data.success) {
                    let points = data.points;
                    if (customerId == 5) {
                        points = Math.max(points, 50);
                        pointsMessage.textContent = `Points: ${points} (Forced for ID 5)`;
                    } else {
                        pointsMessage.textContent = `Points: ${points}${points < 50 ? ' (Need ' + (50 - points) + ' more)' : ''}`;
                    }
                    usePoints.disabled = points < 50;
                    usePoints.checked = false;
                } else {
                    pointsMessage.textContent = 'Error: ' + (data.error || 'Unknown');
                    usePoints.disabled = true;
                }
            })
            .catch(error => {
                console.error('Points error:', error);
                document.getElementById('error').textContent = `Failed to fetch points: ${error.message}`;
                document.getElementById('usePoints').disabled = true;
            });
        });

        // Remove item
        function removeFromCart(cartId) {
            console.log(`Removing item: ${cartId}`);
            fetch('remove_from_cart.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `cartId=${encodeURIComponent(cartId)}`
            })
            .then(response => response.json())
            .then(data => {
                console.log('Remove response:', data);
                if (data.success) location.reload();
                else document.getElementById('error').textContent = data.error;
            })
            .catch(error => {
                console.error('Remove error:', error);
                document.getElementById('error').textContent = 'Failed to remove item.';
            });
        }

        // Checkout
        document.getElementById('checkoutForm').addEventListener('submit', e => {
            e.preventDefault();
            const customerType = document.querySelector('input[name="customerType"]:checked').value;
            const customerId = customerType === 'member' ? document.getElementById('customerId').value.trim() : '';
            const cashPaid = parseFloat(document.querySelector('input[name="cashPaid"]').value);
            const total = <?php echo $total; ?>;
            const usePoints = document.getElementById('usePoints').checked;
            console.log('Checkout:', { customerType, customerId, cashPaid, total, usePoints });
            if (total <= 0) {
                document.getElementById('error').textContent = 'Cart empty or invalid total.';
                return;
            }
            if (customerType === 'member' && (!customerId || !/^\d+$/.test(customerId))) {
                document.getElementById('error').textContent = 'Enter a valid numeric Customer ID.';
                return;
            }
            const discountedTotal = usePoints && customerType === 'member' ? Math.max(0, total - 50) : total;
            if (cashPaid < discountedTotal || cashPaid > 1000000) {
                document.getElementById('error').textContent = `Cash must be between ${discountedTotal.toFixed(2)} and 1,000,000.`;
                return;
            }
            const formData = { customerType, customerId, cashPaid, total, usePoints };
            document.getElementById('checkoutButton').disabled = true;
            document.getElementById('loading').style.display = 'inline';
            document.getElementById('error').textContent = '';
            fetch('process_transaction.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(formData)
            })
            .then(response => response.json())
            .then(data => {
                console.log('Checkout response:', data);
                document.getElementById('checkoutButton').disabled = false;
                document.getElementById('loading').style.display = 'none';
                if (data.success) {
                    alert(`Success! Total: ₱${data.total}, Change: ₱${data.change}, Points Earned: ${data.pointsEarned}, Points Used: ${data.pointsUsed}`);
                    location.reload();
                } else {
                    document.getElementById('error').textContent = data.error || 'Checkout failed.';
                }
            })
            .catch(error => {
                console.error('Checkout error:', error);
                document.getElementById('checkoutButton').disabled = false;
                document.getElementById('loading').style.display = 'none';
                document.getElementById('error').textContent = `Checkout failed: ${error.message}`;
            });
        });
    </script>
</body>
</html>