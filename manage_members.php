    <?php
    session_start();
    require_once 'classes/database.php';

    $con = new database();
    try {
        $members = $con->getMembers();
    } catch (Exception $e) {
        $members = [];
        echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: '" . addslashes($e->getMessage()) . "',
                confirmButtonText: 'OK',
                customClass: { confirmButton: 'btn btn-primary' },
                buttonsStyling: false
            });
        </script>";
    }
    ?>
    <!doctype html>
    <html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Manage Members - Mura Lahat Store</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
        <style>
            body {
                background-image: url('button_images/otsootso.png');
                background-size: cover;
                background-position: center;
                background-attachment: scroll;
                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                position: relative;
                margin: 0;
                min-height: 100vh;
            }
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
            .container {
                max-width: 1200px;
                margin-top: 50px;
                position: relative;
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
                background-color: #e90e00;
            }
            .sidebar-header {
                border-bottom: 1px solid rgba(255,255,255,0.1);
                padding: 15px;
            }
            .sidebar img {
                height: 200px;
                width: auto;
                transition: transform 0.3s ease;
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
            .main-content {
                margin-left: 250px;
                padding: 20px;
                transition: all 0.3s;
                min-width: calc(100vw - 250px);
                box-sizing: border-box;
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
            }
            .table-container {
                background: #ffffff;
                border-radius: 12px;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                padding: 20px;
                margin-top: 30px;
            }
            .btn-delete {
                background-color: #dc3545;
                border: none;
                border-radius: 50px;
                padding: 5px 15px;
                font-weight: 500;
                color: #fff;
                transition: background-color 0.3s ease;
            }
            .btn-delete:hover {
                background-color: #c82333;
            }
            .btn-delete:disabled {
                background-color: #6c757d;
                cursor: not-allowed;
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
                <a class="nav-link" href="#" onclick="logout(); return false;"><i class="bi bi-box-arrow-right me-2"></i><span>Logout</span></a>
            </div>
        </div>
        <div class="main-content">
            <div class="container my-5">
                <h2 class="section-title">Manage Members</h2>
                <div class="table-container">
                    <?php if (!empty($members)): ?>
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Customer ID</th>
                                    <th>Name</th>
                                    <th>Phone</th>
                                    <th>Address</th>
                                    <th>Points</th>
                                    <th>Total Spent</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($members as $member): ?>
                                    <?php
                                    $customerId = isset($member['Customer_ID']) && is_numeric($member['Customer_ID']) && $member['Customer_ID'] > 0 ? (int)$member['Customer_ID'] : null;
                                    $name = (isset($member['Customer_FirstName']) && isset($member['Customer_LastName'])) 
                                        ? $member['Customer_FirstName'] . ' ' . $member['Customer_LastName'] 
                                        : ($member['Customer_FirstName'] ?? ($member['Customer_LastName'] ?? 'Unknown'));
                                    ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($customerId ?? 'N/A'); ?></td>
                                        <td><?php echo htmlspecialchars($name); ?></td>
                                        <td><?php echo htmlspecialchars($member['Customer_Phone'] ?? 'N/A'); ?></td>
                                        <td><?php 
                                            $address = '';
                                            if (isset($member['CA_Street'])) $address .= $member['CA_Street'];
                                            if (isset($member['CA_Barangay'])) $address .= ($address ? ', ' : '') . $member['CA_Barangay'];
                                            if (isset($member['CA_City'])) $address .= ($address ? ', ' : '') . $member['CA_City'];
                                            echo htmlspecialchars($address ?: 'N/A');
                                        ?></td>
                                        <td><?php echo htmlspecialchars($member['Points_Balance'] ?? '0'); ?></td>
                                        <td>₱<?php echo number_format($member['Total_Spent'] ?? 0, 2); ?></td>
                                        <td>
                                            <button class="btn btn-delete" 
                                                    <?php echo $customerId === null ? 'disabled' : ''; ?>
                                                    onclick="<?php echo $customerId !== null ? "deleteMember($customerId, '" . htmlspecialchars($name, ENT_QUOTES) . "')" : ''; ?>">
                                                <i class="bi bi-trash"></i> Delete
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p class="text-muted text-center">No members found.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
        <script>
            function deleteMember(customerId, name) {
                if (!customerId || customerId <= 0) {
                    console.error('Invalid customerId:', customerId);
                    Swal.fire({
                        icon: 'error',
                        title: 'Invalid ID',
                        text: 'Cannot delete member with invalid ID.',
                        customClass: { confirmButton: 'btn btn-primary' }
                    });
                    return;
                }
                console.log(`Attempting to delete member: customerId=${customerId}, name=${name}`);
                Swal.fire({
                    title: 'Delete Member',
                    text: `Are you sure you want to delete ${name}'s membership?`,
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
                        console.log(`Confirmed deletion for customerId=${customerId}`);
                        fetch('./delete_member.php', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({ customerId: customerId })
                        })
                        .then(response => {
                            console.log(`Fetch response: status=${response.status}, url=${response.url}`);
                            if (!response.ok) {
                                throw new Error(`HTTP error! status: ${response.status}`);
                            }
                            return response.json();
                        })
                        .then(data => {
                            console.log('Server response:', data);
                            Swal.fire({
                                icon: data.success ? 'success' : 'error',
                                title: data.success ? 'Deleted' : 'Error',
                                text: data.success ? 'Member has been deleted.' : (data.error || 'Unknown error occurred.'),
                                customClass: { confirmButton: 'btn btn-primary' }
                            }).then(() => {
                                if (data.success) {
                                    console.log('Reloading page after successful deletion');
                                    window.location.reload();
                                }
                            });
                        })
                        .catch(error => {
                            console.error('Fetch error:', error);
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: `Failed to delete member: ${error.message}`,
                                customClass: { confirmButton: 'btn btn-secondary' }
                            });
                        });
                    } else {
                        console.log('Deletion cancelled');
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
                Swal.fire({
                    title: 'Logout',
                    text: 'Are you sure you want to log out?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, log out',
                    cancelButtonText: 'Cancel',
                    customClass: {
                        confirmButton: 'btn btn-danger',
                        cancelButton: 'btn btn-secondary'
                    },
                    buttonsStyling: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch('./logout.php', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' }
                        })
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Network response was not ok');
                            }
                            return response.json();
                        })
                        .then(data => {
                            if (data.success) {
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
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Logout Failed',
                                    text: data.error || 'Failed to log out. Please try again.',
                                    confirmButtonText: 'OK',
                                    customClass: { confirmButton: 'btn btn-primary' },
                                    buttonsStyling: false
                                });
                            }
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
        </script>
    </body>
    </html>