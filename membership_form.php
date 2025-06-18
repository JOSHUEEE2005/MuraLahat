<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    error_log('Form generated CSRF token: ' . $_SESSION['csrf_token'] . ' | Session ID: ' . session_id());
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Add New Member - Mura Lahat Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        * { box-sizing: border-box; }
        .sidebar { position: fixed; top: 0; left: 0; height: 100vh; width: 250px; background-color: #343a40; color: white; z-index: 1000; transition: all 0.3s; box-shadow: 2px 0 5px rgba(0,0,0,0.1); }
        .sidebar-header { border-bottom: 1px solid rgba(255,255,255,0.1); padding: 20px; text-align: center; }
        .sidebar .nav-link { color: rgba(255,255,255,0.8); padding: 12px 20px; margin: 5px 10px; border-radius: 5px; transition: all 0.3s; }
        .sidebar .nav-link:hover { color: white; background-color: rgba(255,255,255,0.1); }
        .sidebar .nav-link.active { color: white; background-color: rgba(0,123,255,0.2); border-left: 3px solid #0d6efd; }
        .sidebar-footer { position: absolute; bottom: 0; width: 100%; border-top: 1px solid rgba(255,255,255,0.1); padding: 20px; }
        .main-content { margin-left: 250px; padding: 20px; transition: all 0.3s; min-width: calc(100vw - 250px); background-color: #f8f9fa; }
        .form-container { background: white; border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px; margin-top: 30px; max-width: 600px; }
        .section-title { font-size: 2rem; font-weight: 700; color: #343a40; margin-bottom: 30px; }
        .btn-submit { background-color: #28a745; border: none; border-radius: 50px; padding: 8px 20px; color: white; }
        .btn-submit:hover { background-color: #218838; }
        @media (max-width: 768px) {
            .sidebar { width: 70px; overflow: hidden; }
            .sidebar .nav-link span, .sidebar-header h3, .sidebar .dropdown-toggle span { display: none; }
            .sidebar .nav-link { text-align: center; padding: 12px 5px; }
            .sidebar .nav-link i { margin-right: 0; font-size: 1.2rem; }
            .main-content { margin-left: 70px; min-width: calc(100vw - 70px); }
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="sidebar-header">
            <h3>Mura Lahat Store</h3>
        </div>
        <ul class="nav flex-column">
            <li class="nav-item"><a class="nav-link" href="add_product.php"><i class="bi bi-plus-circle me-2"></i> Add Product</a></li>
            <li class="nav-item"><a class="nav-link" href="view_products.php"><i class="bi bi-list-ul me-2"></i> View Products</a></li>
            <li class="nav-item"><a class="nav-link active" href="manage_members.php"><i class="bi bi-people me-2"></i> Manage Members</a></li>
            <li class="nav-item"><a class="nav-link" href="add_category.php"><i class="bi bi-tag me-2"></i> Add Category</a></li>
        </ul>
        <div class="sidebar-footer">
            <div class="dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="profileDropdown" role="button" data-bs-toggle="dropdown">
                    <i class="bi bi-person-circle me-2"></i> Profile
                </a>
                <ul class="dropdown-menu">
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
            <h2 class="section-title">Add New Member</h2>
            <?php if (isset($_SESSION['success'])) { echo "<div class='alert alert-success'>{$_SESSION['success']}</div>"; unset($_SESSION['success']); } ?>
            <?php if (isset($_SESSION['error'])) { echo "<div class='alert alert-danger'>{$_SESSION['error']}</div>"; unset($_SESSION['error']); } ?>
            <div class="form-container">
                <form id="membershipForm" action="process_membership.php" method="POST">
                    <input type="hidden" name="csrf_token" id="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                    <div class="mb-3">
                        <label for="firstName" class="form-label">First Name</label>
                        <input type="text" class="form-control" id="firstName" name="firstName" required>
                    </div>
                    <div class="mb-3">
                        <label for="lastName" class="form-label">Last Name</label>
                        <input type="text" class="form-control" id="lastName" name="lastName" required>
                    </div>
                    <div class="mb-3">
                        <label for="phoneNumber" class="form-label">Phone Number (10 digits)</label>
                        <input type="text" class="form-control" id="phoneNumber" name="phoneNumber" required pattern="[0-9]{10}">
                    </div>
                    <div class="mb-3">
                        <label for="street" class="form-label">Street</label>
                        <input type="text" class="form-control" id="street" name="street" required>
                    </div>
                    <div class="mb-3">
                        <label for="barangay" class="form-label">Barangay</label>
                        <input type="text" class="form-control" id="barangay" name="barangay" required>
                    </div>
                    <div class="mb-3">
                        <label for="city" class="form-label">City</label>
                        <input type="text" class="form-control" id="city" name="city" required>
                    </div>
                    <button type="submit" class="btn btn-submit">Add Member</button>
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    <script>
        document.getElementById('membershipForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const form = this;
            const submitButton = form.querySelector('button[type="submit"]');
            submitButton.disabled = true;

            const formData = new FormData(form);
            const formDataObj = Object.fromEntries(formData);
            console.log('Form Data Sent:', formDataObj);
            console.log('CSRF Token in Form:', formDataObj.csrf_token);

            fetch('process_membership.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams(formData)
            })
            .then(response => {
                console.log('Response Status:', response.status);
                return response.text().then(text => ({ status: response.status, text }));
            })
            .then(({ status, text }) => {
                console.log('Raw Response:', text);
                let data;
                try {
                    data = JSON.parse(text);
                } catch (e) {
                    throw new Error('Invalid JSON response: ' + text);
                }
                console.log('Parsed Response:', data);
                Swal.fire({
                    icon: data.success ? 'success' : 'error',
                    title: data.success ? 'Success' : 'Error',
                    text: data.message || data.error || 'Unknown error',
                    customClass: { confirmButton: 'btn btn-primary' }
                }).then(() => {
                    if (data.success) {
                        window.location = 'manage_members.php';
                    } else {
                        submitButton.disabled = false;
                        if (data.csrf_token) {
                            document.getElementById('csrf_token').value = data.csrf_token;
                            console.log('Updated CSRF Token:', data.csrf_token);
                        }
                    }
                });
            })
            .catch(error => {
                console.error('Fetch Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: `Failed to add new member: ${error.message}`,
                    customClass: { confirmButton: 'btn btn-secondary' }
                });
                submitButton.disabled = false;
            });
        });

        function updatePersonalInfo() { Swal.fire({ icon: 'info', title: 'Info', text: 'Update personal info not implemented.', customClass: { confirmButton: 'btn btn-primary' } }); }
        function updatePassword() { Swal.fire({ icon: 'info', title: 'Info', text: 'Update password not implemented.', customClass: { confirmButton: 'btn btn-primary' } }); }
        function logout() {
            fetch('logout.php', { method: 'POST', headers: { 'Content-Type': 'application/json' } })
            .then(response => response.json())
            .then(data => {
                Swal.fire({
                    icon: data.success ? 'success' : 'error',
                    title: data.success ? 'Success' : 'Error',
                    text: data.success ? 'Successfully logged out.' : data.error,
                    customClass: { confirmButton: 'btn btn-primary' }
                }).then(() => { if (data.success) window.location = 'index.php'; });
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Connection Error',
                    text: `Failed to connect: ${error.message}`,
                    customClass: { confirmButton: 'btn btn-secondary' }
                });
            });
        }
    </script>
</body>
</html>