<?php
class database {
    private static $validPositions = ['Admin', 'Cashier', 'Staff', 'Sales Lady', 'Manager'];

    function opencon(): PDO {
        $con = new PDO('mysql:host=127.0.0.1;dbname=help_muralahat;charset=utf8mb4', 'root', '');
        $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $con;
    }

    function signupUser($username, $password, $position, $profile_picture_path) {
        $con = $this->opencon();
        try {
            $con->beginTransaction();
            $stmt = $con->prepare("INSERT INTO user_account (Username, Pass, POSITION, User_Photo) VALUES (?, ?, ?, ?)");
            $stmt->execute([$username, $password, $position, $profile_picture_path]);
            $userID = $con->lastInsertId();
            $stmt = $con->prepare("INSERT INTO user_position (User_Account_ID, User_Status) VALUES (?, 1)");
            $stmt->execute([$userID]);
            $positionID = $con->lastInsertId();
            $stmt = $con->prepare("INSERT INTO position_details (Position_ID, Position, Position_Status) VALUES (?, ?, 'Active')");
            $stmt->execute([$positionID, $position]);
            $con->commit();
            return $userID;
        } catch (PDOException $e) {
            $con->rollback();
            error_log('Signup Error: ' . $e->getMessage() . ' in ' . $e->getFile() . ' on line ' . $e->getLine());
            return false;
        }
    }

    function loginUser($username, $password) {
        $con = $this->opencon();
        try {
            $stmt = $con->prepare("SELECT User_Account_ID, Pass, POSITION FROM user_account WHERE Username = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($user && password_verify($password, $user['Pass'])) {
                return ['user_id' => $user['User_Account_ID'], 'position' => $user['POSITION']];
            }
            return false;
        } catch (PDOException $e) {
            error_log('Login Error: ' . $e->getMessage() . ' in ' . $e->getFile() . ' on line ' . $e->getLine());
            return false;
        }
    }

    function addNewProduct($user_account_id, $prod_name, $prod_quantity, $prod_price, $date_added, $price_effective_from, $price_effective_to, $category_ids = [], $image_path = null) {
        $con = $this->opencon();
        try {
            $con->beginTransaction();
            $stmt = $con->prepare("INSERT INTO product (User_Account_ID, Product_Name, Product_Stock, Product_Image) VALUES (?, ?, ?, ?)");
            $stmt->execute([$user_account_id, $prod_name, $prod_quantity, $image_path]);
            $product_id = $con->lastInsertId();
            $stmt = $con->prepare("INSERT INTO product_price (Product_ID, Price, Effective_From, Effective_To, Created_At) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$product_id, $prod_price, $price_effective_from, $price_effective_to, $date_added]);
            foreach ($category_ids as $category_id) {
                $stmt = $con->prepare("INSERT INTO product_category (Product_ID, Category_ID) VALUES (?, ?)");
                $stmt->execute([$product_id, $category_id]);
            }
            $con->commit();
            return $product_id;
        } catch (PDOException $e) {
            $con->rollBack();
            error_log('Add Product Error: ' . $e->getMessage() . ' in ' . $e->getFile() . ' on line ' . $e->getLine());
            return false;
        }
    }

    function updateProduct($product_id, $prod_name, $prod_quantity, $prod_price, $price_effective_from, $price_effective_to, $category_ids = []) {
        $con = $this->opencon();
        try {
            $con->beginTransaction();
            $stmt = $con->prepare("UPDATE product SET Product_Name = ?, Product_Stock = ? WHERE Product_ID = ?");
            $stmt->execute([$prod_name, $prod_quantity, $product_id]);
            $stmt = $con->prepare("UPDATE product_price SET Effective_To = CURDATE() WHERE Product_ID = ? AND Effective_To IS NULL");
            $stmt->execute([$product_id]);
            $stmt = $con->prepare("INSERT INTO product_price (Product_ID, Price, Effective_From, Effective_To, Created_At) VALUES (?, ?, ?, ?, NOW())");
            $stmt->execute([$product_id, $prod_price, $price_effective_from, $price_effective_to]);
            $stmt = $con->prepare("DELETE FROM product_category WHERE Product_ID = ?");
            $stmt->execute([$product_id]);
            foreach ($category_ids as $category_id) {
                $stmt = $con->prepare("INSERT INTO product_category (Product_ID, Category_ID) VALUES (?, ?)");
                $stmt->execute([$product_id, $category_id]);
            }
            $con->commit();
            return ['success' => true];
        } catch (PDOException $e) {
            $con->rollBack();
            error_log('Update Product Error: ' . $e->getMessage() . ' in ' . $e->getFile() . ' on line ' . $e->getLine());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    function deleteProduct($product_id) {
        $con = $this->opencon();
        try {
            $con->beginTransaction();
            $stmt = $con->prepare("SELECT Product_ID FROM product WHERE Product_ID = ?");
            $stmt->execute([$product_id]);
            if (!$stmt->fetch()) {
                $con->rollBack();
                error_log("Delete Product Error: Product ID $product_id not found");
                return ['success' => false, 'error' => 'Product not found'];
            }
            $stmt = $con->prepare("DELETE FROM product_price WHERE Product_ID = ?");
            $stmt->execute([$product_id]);
            $stmt = $con->prepare("DELETE FROM product_category WHERE Product_ID = ?");
            $stmt->execute([$product_id]);
            $stmt = $con->prepare("DELETE FROM product WHERE Product_ID = ?");
            $stmt->execute([$product_id]);
            $rowCount = $stmt->rowCount();
            if ($rowCount === 0) {
                $con->rollBack();
                error_log("Delete Product Error: No rows deleted for Product ID $product_id");
                return ['success' => false, 'error' => 'No rows deleted'];
            }
            $con->commit();
            error_log("Delete Product Success: Product ID $product_id deleted");
            return ['success' => true];
        } catch (PDOException $e) {
            $con->rollBack();
            error_log('Delete Product Error: ' . $e->getMessage() . ' in ' . $e->getFile() . ' on line ' . $e->getLine());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    function viewCategory() {
        $con = $this->opencon();
        return $con->query("SELECT * FROM category")->fetchAll();
    }

    function viewCategoryID($id) {
        $con = $this->opencon();
        $stmt = $con->prepare("SELECT * FROM category WHERE Category_ID = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    function getProductsWithPrices() {
        $con = $this->opencon();
        $query = "SELECT p.Product_ID, p.Product_Name, p.Product_Stock, pp.Price, p.Product_Image
                  FROM product p
                  JOIN product_price pp ON p.Product_ID = pp.Product_ID
                  WHERE pp.Effective_From <= CURDATE()
                  AND (pp.Effective_To >= CURDATE() OR pp.Effective_To IS NULL)";
        return $con->query($query)->fetchAll(PDO::FETCH_ASSOC);
    }

    function getProductDetails($product_id) {
        $con = $this->opencon();
        $query = "SELECT p.Product_ID, p.Product_Name, p.Product_Stock, pp.Price, pp.Effective_From, pp.Effective_To, p.Product_Image,
                         GROUP_CONCAT(pc.Category_ID) as Category_IDs
                  FROM product p
                  LEFT JOIN product_price pp ON p.Product_ID = pp.Product_ID
                  LEFT JOIN product_category pc ON p.Product_ID = pc.Product_ID
                  WHERE p.Product_ID = ?
                  AND (pp.Effective_From <= CURDATE() AND (pp.Effective_To >= CURDATE() OR pp.Effective_To IS NULL))
                  GROUP BY p.Product_ID, pp.Price_ID";
        $stmt = $con->prepare($query);
        $stmt->execute([$product_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    function addCustomerMembership($firstName, $lastName, $phoneNumber) {
        $con = $this->opencon();
        try {
            $con->beginTransaction();
            $stmt = $con->prepare("SELECT Customer_ID FROM customer WHERE Customer_Phone = ?");
            $stmt->execute([$phoneNumber]);
            if ($stmt->fetch()) {
                $con->rollBack();
                return ['success' => false, 'error' => 'Phone number already registered'];
            }
            $stmt = $con->prepare("INSERT INTO customer (Customer_FirstName, Customer_LastName, Customer_Phone, Membership_Status) VALUES (?, ?, ?, 1)");
            $stmt->execute([$firstName, $lastName, $phoneNumber]);
            $customerId = $con->lastInsertId();
            $con->commit();
            return ['success' => true, 'customerId' => $customerId];
        } catch (PDOException $e) {
            $con->rollBack();
            error_log('Add Membership Error: ' . $e->getMessage() . ' in ' . $e->getFile() . ' on line ' . $e->getLine());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    function getMembers() {
        $con = $this->opencon();
        $query = "SELECT Customer_ID, Customer_FirstName, Customer_LastName, Customer_Phone
                  FROM customer
                  WHERE Membership_Status = 1";
        return $con->query($query)->fetchAll(PDO::FETCH_ASSOC);
    }

    function deleteMember($customerId) {
        $con = $this->opencon();
        try {
            $con->beginTransaction();
            $stmt = $con->prepare("SELECT Customer_ID FROM customer WHERE Customer_ID = ?");
            $stmt->execute([$customerId]);
            if (!$stmt->fetch()) {
                $con->rollBack();
                error_log("Delete Member Error: Customer ID $customerId not found");
                return ['success' => false, 'error' => 'Customer not found'];
            }
            $stmt = $con->prepare("UPDATE customer SET Membership_Status = 0 WHERE Customer_ID = ?");
            $stmt->execute([$customerId]);
            $rowCount = $stmt->rowCount();
            if ($rowCount === 0) {
                $con->rollBack();
                error_log("Delete Member Error: No rows updated for Customer ID $customerId");
                return ['success' => false, 'error' => 'No rows updated'];
            }
            $con->commit();
            error_log("Delete Member Success: Membership_Status set to 0 for Customer ID $customerId");
            return ['success' => true];
        } catch (PDOException $e) {
            $con->rollBack();
            error_log('Delete Member Error: ' . $e->getMessage() . ' in ' . $e->getFile() . ' on line ' . $e->getLine());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    function getAllUsersWithPositions() {
        $con = $this->opencon();
        $query = "SELECT ua.User_Account_ID, ua.Username, ua.POSITION
                  FROM user_account ua
                  JOIN user_position up ON ua.User_Account_ID = up.User_Account_ID
                  JOIN position_details pd ON up.Position_ID = pd.Position_ID
                  WHERE up.User_Status = 1 AND pd.Position_Status = 'Active'";
        return $con->query($query)->fetchAll(PDO::FETCH_ASSOC);
    }

    function updateUserPosition($user_id, $new_position) {
        $con = $this->opencon();
        try {
            if (!in_array($new_position, self::$validPositions)) {
                error_log("Update Position Error: Invalid position $new_position for User ID $user_id");
                return ['success' => false, 'error' => 'Invalid position selected'];
            }

            $con->beginTransaction();
            $stmt = $con->prepare("SELECT ua.User_Account_ID, up.Position_ID
                                   FROM user_account ua
                                   JOIN user_position up ON ua.User_Account_ID = up.User_Account_ID
                                   WHERE ua.User_Account_ID = ? AND up.User_Status = 1");
            $stmt->execute([$user_id]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$user) {
                $con->rollBack();
                error_log("Update Position Error: User ID $user_id not found or inactive");
                return ['success' => false, 'error' => 'User not found or inactive'];
            }
            $stmt = $con->prepare("UPDATE user_account SET POSITION = ? WHERE User_Account_ID = ?");
            $stmt->execute([$new_position, $user_id]);
            $stmt = $con->prepare("UPDATE position_details SET Position = ? WHERE Position_ID = ?");
            $stmt->execute([$new_position, $user['Position_ID']]);
            $con->commit();
            error_log("Update Position Success: User ID $user_id position changed to $new_position");
            return ['success' => true];
        } catch (PDOException $e) {
            $con->rollBack();
            error_log('Update Position Error: ' . $e->getMessage() . ' in ' . $e->getFile() . ' on line ' . $e->getLine());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    function deleteUser($user_id) {
        $con = $this->opencon();
        try {
            $con->beginTransaction();
            $stmt = $con->prepare("SELECT ua.User_Account_ID, up.Position_ID
                                   FROM user_account ua
                                   JOIN user_position up ON ua.User_Account_ID = up.User_Account_ID
                                   WHERE ua.User_Account_ID = ?");
            $stmt->execute([$user_id]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$user) {
                $con->rollBack();
                error_log("Delete User Error: User ID $user_id not found");
                return ['success' => false, 'error' => 'User not found'];
            }
            $stmt = $con->prepare("DELETE FROM position_details WHERE Position_ID = ?");
            $stmt->execute([$user['Position_ID']]);
            $stmt = $con->prepare("DELETE FROM user_position WHERE User_Account_ID = ?");
            $stmt->execute([$user_id]);
            $stmt = $con->prepare("DELETE FROM employee_salary WHERE User_Account_ID = ?");
            $stmt->execute([$user_id]);
            $stmt = $con->prepare("DELETE FROM product WHERE User_Account_ID = ?");
            $stmt->execute([$user_id]);
            $stmt = $con->prepare("DELETE FROM user_account WHERE User_Account_ID = ?");
            $stmt->execute([$user_id]);
            $rowCount = $stmt->rowCount();
            if ($rowCount === 0) {
                $con->rollBack();
                error_log("Delete User Error: No rows deleted for User ID $user_id");
                return ['success' => false, 'error' => 'No rows deleted'];
            }
            $con->commit();
            error_log("Delete User Success: User ID $user_id deleted");
            return ['success' => true];
        } catch (PDOException $e) {
            $con->rollBack();
            error_log('Delete User Error: ' . $e->getMessage() . ' in ' . $e->getFile() . ' on line ' . $e->getLine());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    function addToCart($customerId, $productId, $quantity, $price) {
        $con = $this->opencon();
        try {
            $stmt = $con->prepare("SELECT Product_Stock FROM product WHERE Product_ID = ?");
            $stmt->execute([$productId]);
            $product = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$product || $product['Product_Stock'] < $quantity) {
                return ['success' => false, 'error' => 'Insufficient stock for product ID ' . $productId];
            }

            $stmt = $con->prepare("INSERT INTO cart (Customer_ID, Product_ID, Quantity, Price) VALUES (?, ?, ?, ?)");
            $stmt->execute([$customerId, $productId, $quantity, $price]);
            return ['success' => true];
        } catch (PDOException $e) {
            error_log('Add to Cart Error: ' . $e->getMessage() . ' in ' . $e->getFile() . ' on line ' . $e->getLine());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    function getCart($customerId) {
        $con = $this->opencon();
        try {
            $stmt = $con->prepare("SELECT c.Cart_ID, c.Product_ID, p.Product_Name, c.Quantity, c.Price, p.Product_Stock
                                    FROM cart c
                                    JOIN product p ON c.Product_ID = p.Product_ID
                                    WHERE c.Customer_ID = ?");
            $stmt->execute([$customerId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('Get Cart Error: ' . $e->getMessage() . ' in ' . $e->getFile() . ' on line ' . $e->getLine());
            return [];
        }
    }

    function clearCart($customerId) {
        $con = $this->opencon();
        try {
            $stmt = $con->prepare("DELETE FROM cart WHERE Customer_ID = ?");
            $stmt->execute([$customerId]);
            return true;
        } catch (PDOException $e) {
            error_log('Clear Cart Error: ' . $e->getMessage() . ' in ' . $e->getFile() . ' on line ' . $e->getLine());
            return false;
        }
    }
}
?>