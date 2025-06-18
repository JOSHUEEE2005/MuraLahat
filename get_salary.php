    <?php
require_once('classes/database.php');
header('Content-Type: application/json');
 
if (isset($_GET['employee_id'])) {
    $con = new database();
    $total = $con->getEmployeeTotalSalary($_GET['employee_id']);
    echo json_encode(['total' => $total]);
    exit;
}
 
echo json_encode(['total' => 0]);
?>