<?php
class database {
    private static $validPositions = ['Admin', 'Cashier', 'Staff', 'Sales Lady', 'Manager'];

    function opencon(): PDO {
        $con = new PDO('mysql:host=127.0.0.1;dbname=testml2;charset=utf8mb4', 'root', '');
        $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $con;
    }

    function signupUser($username, $password, $position, $hourly_rate, $profile_picture_path) {
        $con = $this->opencon();
        try {
            $con->beginTransaction();
            $stmt = $con->prepare("INSERT INTO user_account (Username, Pass, Hourly_Rate, User_Photo) VALUES (?, ?, ?, ?)");
            $stmt->execute([$username, $password, $hourly_rate, $profile_picture_path]);
            $userID = $con->lastInsertId();
            $stmt = $con->prepare("INSERT INTO user_position (User_Account_ID, Position_Details_ID, User_Status, Start_Date) VALUES (?, ?, 1, NOW())");
            $stmt->execute([$userID, $position]);
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
            $stmt = $con->prepare("
                SELECT ua.User_Account_ID, ua.Pass, pd.Position
                FROM user_account ua
                LEFT JOIN user_position up ON ua.User_Account_ID = up.User_Account_ID
                LEFT JOIN position_details pd ON up.Position_Details_ID = pd.Position_Details_ID
                WHERE ua.Username = ? AND up.User_Status = 1
            ");
            $stmt->execute([$username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['Pass'])) {
                $stmt = $con->prepare("INSERT INTO user_sessions (User_Account_ID, Login_Time) VALUES (?, NOW())");
                $stmt->execute([$user['User_Account_ID']]);
                $_SESSION['session_id'] = $con->lastInsertId();
                return ['user_id' => $user['User_Account_ID'], 'position' => $user['Position']];
            }
            return false;
        } catch (PDOException $e) {
            error_log('Login Error: ' . $e->getMessage() . ' in ' . $e->getFile() . ' on line ' . $e->getLine());
            return false;
        }
    }

    function logoutUser($user_id, $session_id) {
        $con = $this->opencon();
        try {
            $con->beginTransaction();
            $stmt = $con->prepare("UPDATE user_sessions SET Logout_Time = NOW() WHERE User_Account_ID = ? AND Session_ID = ? AND Logout_Time IS NULL");
            $stmt->execute([$user_id, $session_id]);
            $stmt = $con->prepare("
                SELECT Login_Time, Logout_Time, ua.Hourly_Rate
                FROM user_sessions us
                JOIN user_account ua ON us.User_Account_ID = ua.User_Account_ID
                WHERE us.Session_ID = ?
            ");
            $stmt->execute([$session_id]);
            $session = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($session) {
                $login_time = new DateTime($session['Login_Time']);
                $logout_time = new DateTime($session['Logout_Time']);
                $seconds = $logout_time->getTimestamp() - $login_time->getTimestamp();
                $increments = floor($seconds / 5);
                $rate = $session['Hourly_Rate'];
                $salary = $increments * $rate;
                $stmt = $con->prepare("INSERT INTO employee_salary (User_Account_ID, Salary_Amount, Payout_Date) VALUES (?, ?, NOW())");
                $stmt->execute([$user_id, $salary]);
            }
            $con->commit();
            return ['success' => true];
        } catch (PDOException $e) {
            $con->rollback();
            error_log('Logout Error: ' . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    function updateHourlyRate($user_id, $hourly_rate) {
        $con = $this->opencon();
        try {
            $stmt = $con->prepare("UPDATE user_account SET Hourly_Rate = ? WHERE User_Account_ID = ?");
            $stmt->execute([$hourly_rate, $user_id]);
            return ['success' => true];
        } catch (PDOException $e) {
            error_log('Update Hourly Rate Error: ' . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    function getUserDetails($user_id) {
        $con = $this->opencon();
        try {
            $stmt = $con->prepare("
                SELECT ua.User_Account_ID, ua.Username, ua.Hourly_Rate, pd.Position
                FROM user_account ua
                JOIN user_position up ON ua.User_Account_ID = up.User_Account_ID
                JOIN position_details pd ON up.Position_Details_ID = pd.Position_Details_ID
                WHERE ua.User_Account_ID = ? AND up.User_Status = 1
            ");
            $stmt->execute([$user_id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('Get User Details Error: ' . $e->getMessage());
            return false;
        }
    }

    function getAllUsersWithPositions() {
        $con = $this->opencon();
        try {
            $query = $con->prepare("
                SELECT u.User_Account_ID, u.Username, u.Hourly_Rate, up.Position_Details_ID, pd.Position,
                       COALESCE(SUM(es.Salary_Amount), 0) AS Total_Salary
                FROM user_account u
                JOIN user_position up ON u.User_Account_ID = up.User_Account_ID
                JOIN position_details pd ON up.Position_Details_ID = pd.Position_Details_ID
                LEFT JOIN employee_salary es ON u.User_Account_ID = es.User_Account_ID
                WHERE up.User_Status = 1
                GROUP BY u.User_Account_ID, u.Username, u.Hourly_Rate, up.Position_Details_ID, pd.Position
            ");
            $query->execute();
            return $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('Get All Users Error: ' . $e->getMessage());
            return [];
        }
    }

    function viewPositions() {
        $con = $this->opencon();
        try {
            $stmt = $con->prepare("SELECT Position_Details_ID, Position FROM position_details WHERE Position_Status = 'Active'");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('View Positions Error: ' . $e->getMessage() . ' in ' . $e->getFile() . ' on line ' . $e->getLine());
            return [];
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

    function updateProduct($product_id, $prod_name, $prod_quantity, $prod_price, $price_effective_from, $price_effective_to, $category_ids = [], $stock_mode = 'set', $image_path = null) {
        $con = $this->opencon();
        try {
            $con->beginTransaction();
            // Check if product exists
            $stmt = $con->prepare("SELECT Product_Stock, Product_Image FROM product WHERE Product_ID = ?");
            $stmt->execute([$product_id]);
            $current_product = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$current_product) {
                $con->rollBack();
                error_log("Update Product Error: Product ID $product_id not found");
                return ['success' => false, 'error' => 'Product not found'];
            }
            // Calculate new stock based on stock_mode
            $new_stock = ($stock_mode === 'add') ? $current_product['Product_Stock'] + $prod_quantity : $prod_quantity;
            if ($new_stock < 0) {
                $con->rollBack();
                error_log("Update Product Error: Resulting stock for Product ID $product_id is negative");
                return ['success' => false, 'error' => 'Resulting stock cannot be negative'];
            }
            // Update product details
            $query = "UPDATE product SET Product_Name = ?, Product_Stock = ?";
            $params = [$prod_name, $new_stock];
            if ($image_path) {
                $query .= ", Product_Image = ?";
                $params[] = $image_path;
            }
            $query .= " WHERE Product_ID = ?";
            $params[] = $product_id;
            $stmt = $con->prepare($query);
            $stmt->execute($params);
            // Update product price
            $stmt = $con->prepare("UPDATE product_price SET Effective_To = CURDATE() WHERE Product_ID = ? AND Effective_To IS NULL");
            $stmt->execute([$product_id]);
            $stmt = $con->prepare("INSERT INTO product_price (Product_ID, Price, Effective_From, Effective_To, Created_At) VALUES (?, ?, ?, ?, NOW())");
            $stmt->execute([$product_id, $prod_price, $price_effective_from, $price_effective_to]);
            // Update categories
            $stmt = $con->prepare("DELETE FROM product_category WHERE Product_ID = ?");
            $stmt->execute([$product_id]);
            foreach ($category_ids as $category_id) {
                $stmt = $con->prepare("INSERT INTO product_category (Product_ID, Category_ID) VALUES (?, ?)");
                $stmt->execute([$product_id, $category_id]);
            }
            $con->commit();
            error_log("Update Product Success: Product ID $product_id updated");
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
            // Check if product exists
            $stmt = $con->prepare("SELECT Product_ID FROM product WHERE Product_ID = ?");
            $stmt->execute([$product_id]);
            if (!$stmt->fetch()) {
                $con->rollBack();
                error_log("Delete Product Error: Product ID $product_id not found");
                return ['success' => false, 'error' => 'Product not found'];
            }
            // Check for dependencies
            $stmt = $con->prepare("SELECT COUNT(*) as count FROM cart WHERE Product_ID = ?");
            $stmt->execute([$product_id]);
            if ($stmt->fetch(PDO::FETCH_ASSOC)['count'] > 0) {
                $con->rollBack();
                error_log("Delete Product Error: Product ID $product_id is in cart");
                return ['success' => false, 'error' => 'Cannot delete product because it is in one or more carts'];
            }
            $stmt = $con->prepare("SELECT COUNT(*) as count FROM transaction_items WHERE Product_ID = ?");
            $stmt->execute([$product_id]);
            if ($stmt->fetch(PDO::FETCH_ASSOC)['count'] > 0) {
                $con->rollBack();
                error_log("Delete Product Error: Product ID $product_id is in transaction items");
                return ['success' => false, 'error' => 'Cannot delete product because it is part of one or more transactions'];
            }
            // Delete related records
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
        return $con->query("SELECT * FROM category")->fetchAll(PDO::FETCH_ASSOC);
    }

    function viewCategoryID($id) {
        $con = $this->opencon();
        $stmt = $con->prepare("SELECT * FROM category WHERE Category_ID = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    function getProductsWithPrices() {
        $con = $this->opencon();
        $query = "SELECT 
                    p.Product_ID, 
                    p.Product_Name, 
                    p.Product_Stock, 
                    pp.Price, 
                    p.Product_Image,
                    GROUP_CONCAT(c.Category_ID) as Category_IDs,
                    GROUP_CONCAT(c.Category_Name) as Category_Names
                  FROM product p
                  LEFT JOIN (
                      SELECT Product_ID, Price, 
                             ROW_NUMBER() OVER (PARTITION BY Product_ID ORDER BY 
                                 CASE WHEN Effective_From <= CURDATE() AND (Effective_To >= CURDATE() OR Effective_To IS NULL) 
                                 THEN 0 ELSE 1 END,
                                 ABS(DATEDIFF(Effective_From, CURDATE()))
                             ) as rn
                      FROM product_price
                  ) pp ON p.Product_ID = pp.Product_ID AND pp.rn = 1
                  LEFT JOIN product_category pc ON p.Product_ID = pc.Product_ID
                  LEFT JOIN category c ON pc.Category_ID = c.Category_ID
                  WHERE p.Product_Stock >= 0
                  GROUP BY p.Product_ID, p.Product_Name, p.Product_Stock, pp.Price, p.Product_Image
                  ORDER BY p.Product_Name
                  ";
        $stmt = $con->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    function getProductDetails($product_id) {
        $con = $this->opencon();
        try {
            $query = "SELECT 
                        p.Product_ID, 
                        p.Product_Name, 
                        p.Product_Stock, 
                        COALESCE(pp.Price, 0) as Price, 
                        pp.Effective_From, 
                        pp.Effective_To, 
                        p.Product_Image,
                        GROUP_CONCAT(pc.Category_ID) as Category_IDs
                      FROM product p
                      LEFT JOIN product_price pp ON p.Product_ID = pp.Product_ID
                      LEFT JOIN product_category pc ON p.Product_ID = pc.Product_ID
                      WHERE p.Product_ID = ?
                      GROUP BY p.Product_ID, p.Product_Name, p.Product_Stock, pp.Price, p.Product_Image, pp.Effective_From, pp.Effective_To
                      LIMIT 1";
            $stmt = $con->prepare($query);
            $stmt->execute([$product_id]);
            $product = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$product) {
                error_log("Get Product Details Error: Product ID $product_id not found");
                return false;
            }
            error_log("Get Product Details Success: Product ID $product_id retrieved");
            return $product;
        } catch (PDOException $e) {
            error_log('Get Product Details Error: ' . $e->getMessage() . ' in ' . $e->getFile() . ' on line ' . $e->getLine());
            return false;
        }
    }

    function addCustomerMembership($firstName, $lastName, $phoneNumber, $street, $barangay, $city) {
        $con = $this->opencon();
        try {
            $con->beginTransaction();
            $stmt = $con->prepare("SELECT Customer_ID FROM customer WHERE Customer_Phone = ?");
            $stmt->execute([$phoneNumber]);
            if ($stmt->fetch()) {
                $con->rollBack();
                return ['success' => false, 'error' => 'Phone number already registered'];
            }
            $stmt = $con->prepare("INSERT INTO customer_address (CA_Street, CA_Barangay, CA_City) VALUES (?, ?, ?)");
            $stmt->execute([$street, $barangay, $city]);
            $caId = $con->lastInsertId();
            $stmt = $con->prepare("INSERT INTO customer (CA_ID, Customer_FirstName, Customer_LastName, Customer_Phone, Membership_Status) VALUES (?, ?, ?, ?, 1)");
            $stmt->execute([$caId, $firstName, $lastName, $phoneNumber]);
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
        $query = "
            SELECT 
                c.Customer_ID, 
                c.Customer_FirstName, 
                c.Customer_LastName, 
                c.Customer_Phone,
                ca.CA_Street,
                ca.CA_Barangay,
                ca.CA_City
            FROM customer c
            LEFT JOIN customer_address ca ON c.CA_ID = ca.CA_ID
            WHERE c.Membership_Status = 1
            ";
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

    function updateUserPosition($user_id, $new_position) {
        $con = $this->opencon();
        try {
            if (!in_array($new_position, self::$validPositions)) {
                error_log("Update User Position Error: Invalid position $new_position for User ID $user_id");
                return ['success' => false, 'error' => 'Invalid position selected'];
            }
            $con->beginTransaction();
            $stmt = $con->prepare("
                SELECT ua.User_Account_ID, up.Position_Details_ID
                FROM user_account ua
                JOIN user_position up ON ua.User_Account_ID = up.User_Account_ID
                WHERE ua.User_Account_ID = ? AND up.User_Status = 1
            ");
            $stmt->execute([$user_id]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$user) {
                $con->rollBack();
                error_log("Update Position Error: User ID $user_id not found or inactive");
                return ['success' => false, 'error' => 'User not found or inactive'];
            }
            $stmt = $con->prepare("
                SELECT Position_Details_ID 
                FROM position_details 
                WHERE Position = ? AND Position_Status = 'Active'
            ");
            $stmt->execute([$new_position]);
            $position = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($position) {
                $positionDetailsID = $position['Position_Details_ID'];
            } else {
                $stmt = $con->prepare("
                    INSERT INTO position_details (Position, Position_Status) 
                    VALUES (?, 'Active')
                ");
                $stmt->execute([$new_position]);
                $positionDetailsID = $con->lastInsertId();
            }
            $stmt = $con->prepare("
                UPDATE user_position 
                SET Position_Details_ID = ? 
                WHERE User_Account_ID = ? AND User_Status = 1
            ");
            $stmt->execute([$positionDetailsID, $user_id]);
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
            $stmt = $con->prepare("
                SELECT ua.User_Account_ID
                FROM user_account ua
                JOIN user_position up ON ua.User_Account_ID = up.User_Account_ID
                WHERE ua.User_Account_ID = ?
            ");
            $stmt->execute([$user_id]);
            if (!$stmt->fetch()) {
                $con->rollBack();
                error_log("Soft Delete User Error: User ID $user_id not found");
                return ['success' => false, 'error' => 'User not found'];
            }
            $stmt = $con->prepare("UPDATE user_position SET User_Status = 0, End_Date = NOW() WHERE User_Account_ID = ?");
            $stmt->execute([$user_id]);
            $rowCount = $stmt->rowCount();
            if ($rowCount === 0) {
                $con->rollBack();
                error_log("Soft Delete User Error: No rows updated for User ID $user_id");
                return ['success' => false, 'error' => 'No rows updated'];
            }
            $con->commit();
            error_log("Soft Delete User Success: User ID $user_id marked inactive");
            return ['success' => true];
        } catch (PDOException $e) {
            $con->rollBack();
            error_log('Soft Delete User Error: ' . $e->getMessage() . ' in ' . $e->getFile() . ' on line ' . $e->getLine());
            return ['success' => false, 'error' => 'Failed to mark user as inactive'];
        }
    }

    function addToCart($customerId, $productId, $quantity, $price) {
        $con = $this->opencon();
        try {
            if (strpos($customerId, 'guest_') === 0) {
                if (!isset($_SESSION['guest_cart'])) {
                    $_SESSION['guest_cart'] = [];
                }
                $found = false;
                foreach ($_SESSION['guest_cart'] as &$item) {
                    if ($item['Product_ID'] == $productId) {
                        $item['Quantity'] += $quantity;
                        $found = true;
                        break;
                    }
                }
                if (!$found) {
                    $_SESSION['guest_cart'][] = [
                        'Product_ID' => $productId,
                        'Quantity' => $quantity,
                        'Price' => $price
                    ];
                }
                return ['success' => true];
            }
            $stmt = $con->prepare("SELECT Product_Stock FROM product WHERE Product_ID = ?");
            $stmt->execute([$productId]);
            $product = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$product || $product['Product_Stock'] < $quantity) {
                return ['success' => false, 'error' => 'Insufficient stock'];
            }
            $stmt = $con->prepare("INSERT INTO cart (Customer_ID, Product_ID, Quantity, Price) VALUES (?, ?, ?, ?)");
            $stmt->execute([$customerId, $productId, $quantity, $price]);
            return ['success' => true];
        } catch (PDOException $e) {
            error_log('Add to Cart Error: ' . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    function getCart($customerId) {
        if (strpos($customerId, 'guest_') === 0) {
            if (!isset($_SESSION['guest_cart'])) {
                return [];
            }
            $con = $this->opencon();
            $cartItems = [];
            foreach ($_SESSION['guest_cart'] as $item) {
                $stmt = $con->prepare("SELECT Product_ID, Product_Name, Product_Stock, Product_Image FROM product WHERE Product_ID = ?");
                $stmt->execute([$item['Product_ID']]);
                $product = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($product) {
                    $cartItems[] = [
                        'Cart_ID' => 'guest_' . $item['Product_ID'],
                        'Product_ID' => $item['Product_ID'],
                        'Product_Name' => $product['Product_Name'],
                        'Quantity' => $item['Quantity'],
                        'Price' => $item['Price'],
                        'Product_Stock' => $product['Product_Stock'],
                        'Product_Image' => $product['Product_Image']
                    ];
                }
            }
            return $cartItems;
        }
        $con = $this->opencon();
        try {
            $stmt = $con->prepare("SELECT c.Cart_ID, c.Product_ID, p.Product_Name, c.Quantity, c.Price, p.Product_Stock, p.Product_Image
                                    FROM cart c
                                    JOIN product p ON c.Product_ID = p.Product_ID
                                    WHERE c.Customer_ID = ?");
            $stmt->execute([$customerId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('Get Cart Error: ' . $e->getMessage());
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

    function addCategory($categName) {
        $con = $this->opencon();
        try {
            $stmt = $con->prepare("INSERT INTO category (Category_Name) VALUES (?)");
            $stmt->execute([$categName]);
            return ['success' => true, 'categoryId' => $con->lastInsertId()];
        } catch (PDOException $e) {
            error_log('Add Category Error: ' . $e->getMessage() . ' in ' . $e->getFile() . ' on line ' . $e->getLine());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}
?>