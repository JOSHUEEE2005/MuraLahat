<?php

require_once('classes/database.php');
$con = new database();
session_start();
$sweetAlertConfig = "";

if(empty($id = $_POST['id'])) {

    header('location:update_product.php');

} else {
    $id = $_POST['id'];
    $data = $con->viewProductID($id);
}

if(isset($_POST['updateProducts'])) {

   $user_account_id = 1;
   $prod_name = $_POST['productName'];
   $prod_quantity = $_POST['productQuantity'];
   $prod_price = $_POST['productPrice'];
   $date_added = $_POST['productDateAdded'];
   $price_effective_from = $_POST['priceEffectiveFrom'];
   $price_effective_to = $_POST['priceEffectiveTo'];
   $category_ids = isset($_POST['productCategory']) ? $_POST['productCategory'] : [];

   $result = $con->addNewProduct($user_account_id, $prod_name, $prod_quantity, $prod_price, $date_added, $price_effective_from, $price_effective_to, $category_ids);

      if($result) {
        $sweetAlertConfig = "
            	<script>
                Swal.fire({
                  icon: 'success',
                  title: 'Author Updated',
                  text: 'An existing author has been updated successfully!',
                  confirmButtonText: 'OK'
                }).then((result) => {
                  if (result.isConfirmed) {
                    window.location.href = 'admin_homepage.php';
                  }
                })
              </script>";
      } else {
        $_SESSION['error'] = "Sorry, there was an error signing up.";
      }
  }

?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="./bootstrap-5.3.3-dist/css/bootstrap.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css"> <!-- Correct Bootstrap Icons CSS -->
  <title>Update Product Information</title>

  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <link rel="stylesheet" href="./package/dist/sweetalert2.css">
    
</head>
<body>
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
      <a class="navbar-brand" href="admin_homepage.php">Mura Lahat Store Sample Page (ADMIN)</a>
      <a class="btn btn-outline-light ms-auto active" href="add_products.php">Add Products</a>
      <a class="btn btn-outline-light ms-2" href="add_category.php">Add Category</a>
      <a class="btn btn-outline-light ms-2" href="add_books.php">Add</a>
      <div class="dropdown ms-2">
        <button class="btn btn-outline-light dropdown-toggle" type="button" id="profileDropdown" data-bs-toggle="dropdown" aria-expanded="false">
          <i class="bi bi-person-circle"></i> <!-- Bootstrap icon -->
        </button>
        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdown">
          <li>
              <a class="dropdown-item" href="profile.html">
                  <i class="bi bi-person-circle me-2"></i> See Profile Information
              </a>
            </li>
          <li>
            <button class="dropdown-item" onclick="updatePersonalInfo()">
              <i class="bi bi-pencil-square me-2"></i> Update Personal Information
            </button>
          </li>
          <li>
            <button class="dropdown-item" onclick="updatePassword()">
              <i class="bi bi-key me-2"></i> Update Password
            </button>
          </li>
          <li>
            <button class="dropdown-item text-danger" onclick="logout()">
              <i class="bi bi-box-arrow-right me-2"></i> Logout
            </button>
          </li>
        </ul>
      </div>
    </div>
  </nav>
<div class="container my-5 border border-2 rounded-3 shadow p-4 bg-light">

  <h4 class="mt-5">Update Product Information</h4>
  <form method="POST" action="" novalidate>
     <input type="hidden" class="form-control" id="id" name="id" value="<?php echo $data['Product_ID']?>" required>
    <div class="mb-3">
      <label for="productName" class="form-label">Product Name</label>
      <input type="text" name="productName" class="form-control" id="productName" value="<?php echo $data['Product_Name']?>" required>
    </div>
 
    <div class="mb-3">
      <label for="productPrice" class="form-label">Product Price</label>
      <input type="number" name="productPrice" class="form-control" id="" value="<?php echo $data['Price']?>" required>
    </div>

    <div class="mb-3">
      <label for="priceEffectiveFrom">Price Effective From</label>
        <input type="date" class="form-control" name="priceEffectiveFrom" id="priceEffectiveFrom" value="<?php echo $data['Effective_From']?>" required>
        <div class="valid-feedback">Great!</div>
        <div class="invalid-feedback">Please enter a valid date.</div>
    </div>

    <div class="mb-3">
      <label for="priceEffectiveTo">Price Effective To</label>
        <input type="date" class="form-control" name="priceEffectiveTo" id="priceEffectiveTo" value="<?php echo $data['Effective_To']?>" required>
        <div class="valid-feedback">Great!</div>
        <div class="invalid-feedback">Please enter a valid date.</div>
    </div>

    <div class="mb-3">
      <label for="productCategory" class="form-label">Product Category</label>
      <select class="form-select" id="productCategory" name="productCategory[]"  value="<?php echo $data['Category_Name']?>" multiple required>
        <?php foreach($categories as $category): ?>
        <option value="<?php echo $category['Category_ID']; ?>"> <?php echo htmlspecialchars($category['Category_Name']); ?></option>
        <?php endforeach; ?>

        <!-- Add more categories as needed -->
      </select>
      <small class="form-text text-muted">Hold down the Ctrl (Windows) or Command (Mac) key to select multiple categories.</small>
    </div>

    <div class="mb-3">
      <label for="productQuantity" class="form-label">Quantity Available</label>
      <input type="number" name="productQuantity" class="form-control" id="productQuantity" value="<?php echo $data['Product_Stock']?>" required>
    </div>

    <div class="mb-3">
      <label for="productDateAdded">Date Added</label>
      <input type="datetime-local" class="form-control" name="productDateAdded" id="productDateAdded" value="<?php echo $data['Created_At']?>" required>
      <div class="valid-feedback">Great!</div>
      <div class="invalid-feedback">Please enter a valid date.</div>
    </div>

    <button type="submit" name="addProducts" class="btn btn-primary">Add Product</button>

    
  </form>
</div>
<script src="./bootstrap-5.3.3-dist/js/bootstrap.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script> <!-- Add Popper.js -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script> <!-- Correct Bootstrap JS -->


    <script src="./package/dist/sweetalert2.js"></script>
    <?php echo $sweetAlertConfig; ?>



</body>
</html>