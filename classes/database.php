<?php
class database {
    private static $validPositions = ['Admin', 'Cashier', 'Staff', 'Sales Lady', 'Manager'];

    function opencon(): PDO {
        $con = new PDO('mysql:host=127.0.0.1;dbname=testml2;charset=utf8mb4', 'root', '');
        $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $con;
    }

    function signupUser($username, $password, $position, $profile_picture_path) {
    $con = $this->opencon();
    try {
        $con->beginTransaction();

        // Hash the password

        // Insert into user_account
        $stmt = $con->prepare("INSERT INTO user_account (Username, Pass, User_Photo) VALUES (?, ?, ?)");
        $stmt->execute([$username, $password, $profile_picture_path]);
        $userID = $con->lastInsertId();

        // Insert into user_position with provided Position_Details_ID
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
            return ['user_id' => $user['User_Account_ID'], 'position' => $user['Position']];
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
            $stmt = $con->prepare("INSERT INTO customer (Customer_FirstName, Customer_LastName, Customer_Phone, Membership_Status) VALUES (?, ?, ?, 1)");
            $stmt->execute([$firstName, $lastName, $phoneNumber]);
            $customerId = $con->lastInsertId();
            $con->commit();

            $stmt = $con->prepare("INSERT INTO customer_address (CA_ID, CA_Street, CA_Barangay, CA_City) VALUES (?, ?, ?, ?)");
            $stmt->execute([$street, $barangay, $city]);
            $customerId = $con->lastInsertId();
            $con->commit();



            return ['success' => true, 'customerId' => $customerId];
        } catch (PDOException $e) {
            $con->rollBack();
            error_log('Add Membership Error: ' . $e->getMessage() . ' in ' . $e->getFile() . ' on line ' . $e->getLine());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    // function getMembers() {
    //     $con = $this->opencon();
    //     $query = "SELECT Customer_ID, Customer_FirstName, Customer_LastName, Customer_Phone
    //               FROM customer
    //               WHERE Membership_Status = 1";
    //     return $con->query($query)->fetchAll(PDO::FETCH_ASSOC);
    // }

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
            LEFT JOIN customer_address_link cal ON c.Customer_ID = cal.Customer_ID
            LEFT JOIN customer_address ca ON cal.CA_ID = ca.CA_ID
            WHERE c.Membership_Status = 1";

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

    // function getAllUsersWithPositions() {
    //     $con = $this->opencon();
    //     $query = "SELECT ua.User_Account_ID, ua.Username, ua.POSITION
    //               FROM user_account ua
    //               JOIN user_position up ON ua.User_Account_ID = up.User_Account_ID
    //               JOIN position_details pd ON up.Position_ID = pd.Position_ID
    //               WHERE up.User_Status = 1 AND pd.Position_Status = 'Active'";
    //     return $con->query($query)->fetchAll(PDO::FETCH_ASSOC);
    // }

    // function getAllUsersWithPositions() {
    // $con = $this->opencon();
    //     try {
    //         $query = "
    //             SELECT ua.User_Account_ID, ua.Username, pd.Position
    //             FROM user_account ua
    //             JOIN user_position up ON ua.User_Account_ID = up.User_Account_ID
    //             JOIN position_details pd ON up.Position_Details_ID = pd.Position_Details_ID
    //             WHERE up.User_Status = 1 AND pd.Position_Status = 'Active'
    //         ";
    //         $stmt = $con->query($query);
    //         $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    //         return $users ?: [];
    //     } catch (PDOException $e) {
    //         error_log('Get Users Error: ' . $e->getMessage() . ' in ' . $e->getFile() . ' on line ' . $e->getLine());
    //         return [];
    //     }
    // }

    public function getAllUsersWithPositions() {
    $db = $this->opencon();
        $query = $db->prepare("
            SELECT u.User_Account_ID, u.Username, up.Position_Details_ID, pd.Position
            FROM user_account u
            JOIN user_position up ON u.User_Account_ID = up.User_Account_ID
            JOIN position_details pd ON up.Position_Details_ID = pd.Position_Details_ID
            WHERE up.User_Status = 1
        ");
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    function updateUserPosition($user_id, $new_position) {
    $con = $this->opencon();
    try {
        if (!in_array($new_position, self::$validPositions)) {
            error_log("Update Position Error: Invalid position $new_position for User ID $user_id");
            return ['success' => false, 'error' => 'Invalid position selected'];
        }

        $con->beginTransaction();

        // Check if user exists and is active
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

        // Find or create position in position_details
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

        // Update user_position with new Position_Details_ID
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

    // function deleteUser($user_id) {
    // $con = $this->opencon();
    // try {
    //     $con->beginTransaction();

    //     // Check if user exists
    //     $stmt = $con->prepare("
    //         SELECT ua.User_Account_ID, up.Position_Details_ID
    //         FROM user_account ua
    //         JOIN user_position up ON ua.User_Account_ID = up.User_Account_ID
    //         WHERE ua.User_Account_ID = ?
    //     ");
    //     $stmt->execute([$user_id]);
    //     $user = $stmt->fetch(PDO::FETCH_ASSOC);
    //     if (!$user) {
    //         $con->rollBack();
    //         error_log("Delete User Error: User ID $user_id not found");
    //         return ['success' => false, 'error' => 'User not found'];
    //     }

    //     // Delete from user_account (cascades to user_position, employee_salary, product)
    //     $stmt = $con->prepare("DELETE FROM user_account WHERE User_Account_ID = ?");
    //     $stmt->execute([$user_id]);
    //     $rowCount = $stmt->rowCount();

    //     if ($rowCount === 0) {
    //         $con->rollBack();
    //         error_log("Delete User Error: No rows deleted for User ID $user_id");
    //         return ['success' => false, 'error' => 'No rows deleted'];
    //     }

    //     // Optionally clean up position_details if no other users reference it
    //     $stmt = $con->prepare("
    //         SELECT COUNT(*) as count 
    //         FROM user_position 
    //         WHERE Position_Details_ID = ?
    //     ");
    //     $stmt->execute([$user['Position_Details_ID']]);
    //     $count = $stmt->fetchColumn();

    //     if ($count == 0) {
    //         $stmt = $con->prepare("
    //             DELETE FROM position_details 
    //             WHERE Position_Details_ID = ?
    //         ");
    //         $stmt->execute([$user['Position_Details_ID']]);
    //     }

    //         $con->commit();
    //         error_log("Delete User Success: User ID $user_id deleted");
    //         return ['success' => true];
    //     } catch (PDOException $e) {
    //         $con->rollBack();
    //         error_log('Delete User Error: ' . $e->getMessage() . ' in ' . $e->getFile() . ' on line ' . $e->getLine());
    //         return ['success' => false, 'error' => $e->getMessage()];
    //     }
    // }

    function deleteUser($user_id) {
    $con = $this->opencon();
        try {
            $con->beginTransaction();

            // Check if user exists
            $stmt = $con->prepare("
                SELECT ua.User_Account_ID
                FROM user_account ua
                JOIN user_position up ON ua.User_Account_ID = up.User_Account_ID
                WHERE ua.User_Account_ID = ?
            ");
            $stmt->execute([$user_id]);
            if (!$stmt->fetch(PDO::FETCH_ASSOC)) {
                $con->rollBack();
                error_log("Soft Delete User Error: User ID $user_id not found");
                return ['success' => false, 'error' => 'User not found'];
            }

            // Mark user as inactive in user_position
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

    function addCategory($categName) {
                $con = $this->opencon();
                try {
                $con->beginTransaction();

                // Insert into Users table
                $stmt = $con->prepare("INSERT INTO Category (Category_Name) VALUES (?)");
                $stmt->execute([$categName]);

                //Get the newly inserted user_id
                $Category_ID = $con->lastInsertId();

                $con->commit();
                return $Category_ID; //return user_id for further use (like inserting address)
            } catch(PDOException $e) {
                $con->rollBack();
                return false;
            }

        }

        function addPosition($posiName) {
                $con = $this->opencon();
                try {
                $con->beginTransaction();

                // Insert into Users table
                $stmt = $con->prepare("INSERT INTO Position_Details (Position, Position_Status) VALUES (?,1)");
                $stmt->execute([$posiName]);

                //Get the newly inserted user_id
                $Position_Details_ID = $con->lastInsertId();

                $con->commit();
                return $Position_Details_ID; //return user_id for further use (like inserting address)
            } catch(PDOException $e) {
                $con->rollBack();
                return false;
            }

        }

        function viewPositions() {
        $con = $this->opencon();
        return $con->query("SELECT * FROM position_details")->fetchAll();
    }

        function viewPositionsID($id) {
            $con = $this->opencon();
            $stmt = $con->prepare("SELECT * FROM position_details WHERE Position_Details_ID = ?");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
}
?>