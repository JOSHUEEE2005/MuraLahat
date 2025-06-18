<?php
session_start();
require_once('classes/database.php');
$con = new database();
 
// Fetch data for both income and expenses
$incomeData = $con->getIncomeData();
$employees = $con->getEmployeesWithSalary();
$expenseData = $con->getExpenseData();
$totalExpenses = $con->getTotalExpenses();
 
// Calculate total salary per employee
$employeeSalaries = [];
foreach ($employees as $employee) {
    $id = $employee['User_Account_ID'];
    if (!isset($employeeSalaries[$id])) {
        $employeeSalaries[$id] = [
            'name' => $employee['employee_name'] ?? 'Unknown',
            'position' => $employee['Position'] ?? 'No Position',
            'total_salary' => 0
        ];
    }
    $employeeSalaries[$id]['total_salary'] += $employee['Salary_Amount'] ?? 0;
}
 
// Handle expense form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['employee_id'])) {
    $employeeId = $_POST['employee_id'] ?? null;
    $salaryAmount = $_POST['salary_amount'] ?? 0;
    $supplyFees = $_POST['supply_fees'] ?? 0;
    $utilities = $_POST['utilities'] ?? 0;
   
    if ($con->addExpenseWithSalary($employeeId, $salaryAmount, $supplyFees, $utilities)) {
        $_SESSION['success'] = "Expense recorded successfully!";
    } else {
        $_SESSION['error'] = "Failed to record expense.";
    }
    header("Location: financial.php");
    exit();
}
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Financial Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- SweetAlert CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
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
    margin-top: 20px; /* Reduced from 50px to bring container higher */
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

.dashboard-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    border: none;
    border-radius: 10px;
    overflow: hidden;
    background: #ffffff;
    text-align: center;
    cursor: pointer;
    width: 80%;
    height: auto;
}

.dashboard-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
}

.dashboard-img {
    width: 100%;
    height: 150px;
    object-fit: contain;
    display: block;
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
    height: 140px;
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
    padding: 20px 20px 20px 20px; /* Added top padding to prevent content from touching top edge */
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
        margin-top: 10px; /* Further reduced for smaller screens */
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
                <a class="nav-link text-white active" href="financial.php"><i class="bi bi-cash-stack me-2"></i> Financials</a>
            </li>
           
        </ul>
        <div class="sidebar-footer">
            <a class="nav-link" href="#" onclick="logout()"><i class="bi bi-box-arrow-right me-2"></i><span>Logout</span></a>
        </div>
    </div>




    <!-- Main Content -->
    <div class="main-content">
        <div class="container-fluid">
            <h2 class="mb-4">Financial Management</h2>
           
            <!-- Income Table -->
            <div class="card">
                <div class="card-header">
                    <h4>Income Records</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Income ID</th>
                                    <th>Payment ID</th>
                                    <th>Amount</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($incomeData as $income): ?>
                                <tr>
                                    <td><?= $income['Income_ID'] ?></td>
                                    <td><?= $income['Payment_ID'] ?></td>
                                    <td>₱<?= number_format($income['Income_Amount'], 2) ?></td>
                                    <td><?= date('M d, Y', strtotime($income['Income_Date'])) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
 
            <!-- Expense Form -->
            <div class="card form-card">
                <div class="card-header">
                    <h4>Add New Expense</h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="">
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label">Employee</label>
                                <select class="form-select" id="employee_id" name="employee_id" required>
                                    <option value="">Select Employee</option>
                                    <?php foreach ($employeeSalaries as $id => $employee): ?>
                                        <option value="<?= $id ?>">
                                            <?= $employee['name'] ?> (<?= $employee['position'] ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="mt-2">
                                    <strong>Total Salary:</strong>
                                    <span id="salary_display">₱0.00</span>
                                </div>
                                <input type="hidden" id="salary_amount" name="salary_amount" value="0">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Supply Fees</label>
                                <input type="number" class="form-control" name="supply_fees" step="0.01" min="0" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Utilities</label>
                                <input type="number" class="form-control" name="utilities" step="0.01" min="0" required>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Record Expense</button>
                    </form>
                </div>
            </div>
 
            <!-- Expense Records -->
            <div class="card mt-4">
                <div class="card-header">
                    <h4>Expense Records</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Employee ID</th>
                                    <th>Employee</th>
                                    <th>Total Salary</th>
                                    <th>Supply Fees</th>
                                    <th>Utilities</th>
                                    <th>Total</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // Group expenses by employee and calculate totals
                                $groupedExpenses = [];
                                $grandTotalSalary = 0;
                                $grandTotalSupply = 0;
                                $grandTotalUtilities = 0;
                                $grandTotalAll = 0;
                               
                                foreach ($expenseData as $expense) {
                                    $key = $expense['User_Account_ID'] ?? 'none';
                                    if (!isset($groupedExpenses[$key])) {
                                        $groupedExpenses[$key] = [
                                            'employee_name' => $expense['employee_name'] ?? 'N/A',
                                            'Salary_Amount' => 0,
                                            'Supply_Fees' => 0,
                                            'Utilities' => 0,
                                            'Payout_Date' => $expense['Payout_Date'] ?? date('Y-m-d')
                                        ];
                                    }
                                    $groupedExpenses[$key]['Salary_Amount'] += $expense['Salary_Amount'] ?? 0;
                                    $groupedExpenses[$key]['Supply_Fees'] += $expense['Supply_Fees'] ?? 0;
                                    $groupedExpenses[$key]['Utilities'] += $expense['Utilities'] ?? 0;
                                   
                                    // Add to grand totals
                                    $grandTotalSalary += $expense['Salary_Amount'] ?? 0;
                                    $grandTotalSupply += $expense['Supply_Fees'] ?? 0;
                                    $grandTotalUtilities += $expense['Utilities'] ?? 0;
                                    $grandTotalAll += ($expense['Salary_Amount'] ?? 0) + ($expense['Supply_Fees'] ?? 0) + ($expense['Utilities'] ?? 0);
                                }
                               
                                // Display grouped data
                                foreach ($groupedExpenses as $employeeId => $expense):
                                    $rowTotal = ($expense['Salary_Amount'] ?? 0) +
                                               ($expense['Supply_Fees'] ?? 0) +
                                               ($expense['Utilities'] ?? 0);
                                ?>
                                <tr>
                                    <td><?= $employeeId == 'none' ? 'N/A' : $employeeId ?></td>
                                    <td><?= $expense['employee_name'] ?></td>
                                    <td>₱<?= number_format($expense['Salary_Amount'], 2) ?></td>
                                    <td>₱<?= number_format($expense['Supply_Fees'], 2) ?></td>
                                    <td>₱<?= number_format($expense['Utilities'], 2) ?></td>
                                    <td>₱<?= number_format($rowTotal, 2) ?></td>
                                    <td><?= date('M d, Y', strtotime($expense['Payout_Date'])) ?></td>
                                </tr>
                                <?php endforeach; ?>
                               
                                <!-- Grand Total Row -->
                                <tr class="table-primary fw-bold">
                                    <td colspan="2">Grand Total</td>
                                    <td>₱<?= number_format($grandTotalSalary, 2) ?></td>
                                    <td>₱<?= number_format($grandTotalSupply, 2) ?></td>
                                    <td>₱<?= number_format($grandTotalUtilities, 2) ?></td>
                                    <td>₱<?= number_format($grandTotalAll, 2) ?></td>
                                    <td></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
 
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- SweetAlert JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.getElementById('employee_id').addEventListener('change', function() {
            const employeeId = this.value;
            if (employeeId) {
                // Show loading for minimum 5 seconds
                let timerInterval;
                Swal.fire({
                    title: 'Calculating Salary',
                    html: 'Please wait while we calculate the total salary...',
                    timer: 5000,
                    timerProgressBar: true,
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                        // Start fetching immediately but keep loading for at least 5 seconds
                        const fetchPromise = fetch('get_salary.php?employee_id=' + employeeId)
                            .then(response => response.json());
                       
                        // Create a minimum 5 second delay promise
                        const delayPromise = new Promise(resolve => setTimeout(resolve, 3000));
                       
                        Promise.all([fetchPromise, delayPromise])
                            .then(([data]) => {
                                Swal.close();
                                // Update the salary amount field
                                document.getElementById('salary_amount').value = data.total;
                               
                                // Display the formatted salary
                                document.getElementById('salary_display').textContent =
                                    '₱' + parseFloat(data.total).toLocaleString('en-PH', {
                                        minimumFractionDigits: 2,
                                        maximumFractionDigits: 2
                                    });
                            })
                            .catch(error => {
                                Swal.fire('Error', 'Failed to get salary data', 'error');
                                document.getElementById('salary_amount').value = 0;
                                document.getElementById('salary_display').textContent = '₱0.00';
                            });
                    },
                    willClose: () => {
                        clearInterval(timerInterval);
                    }
                });
            } else {
                document.getElementById('salary_amount').value = 0;
                document.getElementById('salary_display').textContent = '₱0.00';
            }
        });
 
        // Handle form submission with SweetAlert
        document.querySelector('form').addEventListener('submit', function(e) {
            e.preventDefault();
            const form = this;
           
            Swal.fire({
                title: 'Recording Expense',
                text: 'Please wait while we process your expense...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                    // Submit the form after showing the loading alert
                    fetch(form.action, {
                        method: form.method,
                        body: new FormData(form)
                    })
                    .then(response => response.text())
                    .then(() => {
                        // After successful submission, show success message
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: 'Expense recorded successfully!',
                            confirmButtonText: 'OK'
                        }).then(() => {
                            // Refresh the page to show updated records
                            window.location.reload();
                        });
                    })
                    .catch(error => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to record expense: ' + error,
                            confirmButtonText: 'OK'
                        });
                    });
                }
            });
        });
 
        // Show existing success/error messages with SweetAlert
        <?php if (isset($_SESSION['success'])): ?>
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: '<?= $_SESSION['success'] ?>',
                confirmButtonText: 'OK'
            });
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>
       
        <?php if (isset($_SESSION['error'])): ?>
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: '<?= $_SESSION['error'] ?>',
                confirmButtonText: 'OK'
            });
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>
    </script>
</body>
</html>