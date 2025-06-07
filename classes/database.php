<?php
    class database {
        function opencon(): PDO {

            //pachange nalang nito if iba ang name ng db niyo
            return new PDO('mysql:host=localhost;
            dbname=ml_dbs',
            username: 'root',
            password: '');

            require_once('classes/database.php');
            $con = new database();
            $data = $con->opencon();
        }

        function signupUser($username, $password, $position, $profile_picture_path) {
        $con = $this->opencon();
        
        try {
            $con->beginTransaction();

            // Insert into user_account
            $stmt = $con->prepare("INSERT INTO user_account (Username, Pass, POSITION, User_Photo) VALUES (?, ?, ?, ?)");
            $stmt->execute([$username, $password, $position, $profile_picture_path]);
            $userID = $con->lastInsertId();

            // Insert into user_position
            $stmt = $con->prepare("INSERT INTO user_position (User_Account_ID, User_Status) VALUES (?, 1)");
            $stmt->execute([$userID]);
            $positionID = $con->lastInsertId();

            // Insert into position_details
            $stmt = $con->prepare("INSERT INTO position_details (Position_ID, Position, Position_Status) VALUES (?, ?, 'Active')");
            $stmt->execute([$positionID, $position]);

            $con->commit();
            return $userID;
        } catch (PDOException $e) {
            $con->rollback();
            return false;
        }
    }

    function loginUser($username, $password) {
        $con = $this->opencon();
            try {
                $stmt = $con->prepare("SELECT User_Account_ID, Pass FROM user_account WHERE Username = ?");
                $stmt->execute([$username]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($user && password_verify($password, $user['Pass'])) {
                    return $user['User_Account_ID'];
                }
                return false;
            } catch (PDOException $e) {
                return false;
            }
    }

        
        function addNewProduct($user_account_id, $prod_name, $prod_quantity, $prod_price, $date_added, $price_effective_from, $price_effective_to, $category_ids = []) {
            $con = $this->opencon();
            try {
                $con->beginTransaction();

                //Insert into Product table
                $stmt = $con->prepare("INSERT INTO Product (User_Account_ID, Product_Name, Product_Stock) VALUES (?,?,?)");
                $stmt->execute([$user_account_id, $prod_name, $prod_quantity]);
                    
                //Get the newly inserted product_id
                $product_id = $con->lastInsertId();

                // Insert into Product_Price table
                $stmt = $con->prepare("INSERT INTO Product_Price (Product_ID, Price, Effective_From, Effective_To, Created_At) VALUES (?,?,?,?,?)");
                $stmt->execute([$product_id, $prod_price, $price_effective_from, $price_effective_to, $date_added]);
                    
                // Insert into Product_Category table
                foreach ($category_ids as $category_id) {
                $stmt = $con->prepare("INSERT INTO Product_Category (Product_ID, Category_ID) VALUES (?,?)");
                $stmt->execute([$product_id, $category_id]);
                }

                $con->commit();
                return $product_id;
            } catch (PDOException $e) {
                $con->rollBack();
                return false; // Update failed
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

        function viewCategory() {
            $con = $this->opencon();
                return $con->query("SELECT * FROM Category")
                ->fetchAll();
        }

        function viewCategoryID($id) {
            $con = $this->opencon();
                $stmt = $con->prepare("SELECT * FROM Category WHERE Category_ID = ?");
                $stmt->execute([$id]);
                return $stmt->fetch(PDO::FETCH_ASSOC);
        }

        function viewProduct() {
            $con = $this->opencon();
            $query = "
                SELECT 
                    p.Product_ID,
                    p.Product_Name,
                    p.Product_Stock,
                    pr.Price_ID,
                    pr.Price,
                    pr.Effective_From,
                    pr.Effective_To,
                    pr.Created_At
                FROM 
                    Product p
                LEFT JOIN 
                    Product_Price pr ON p.Product_ID = pr.Product_ID
            ";
            return $con->query($query)->fetchAll(PDO::FETCH_ASSOC);
        }

        function viewProductID($id) {
        $con = $this->opencon();
        $stmt = $con->prepare("
            SELECT
                p.Product_ID,
                p.Product_Name,
                p.Product_Stock,
                pr.Price_ID,
                pr.Price,
                pr.Effective_From,
                pr.Effective_To,
                pr.Created_At
            FROM
                Product p
            LEFT JOIN
                Product_Price pr ON p.Product_ID = pr.Product_ID
            WHERE
                p.Product_ID = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    function updateProduct($id, $user_account_id, $prod_name, $prod_quantity, $prod_price, $date_added, $price_effective_from, $price_effective_to, $category_ids = []) {
        $con = $this->opencon();
        try {
            $con->beginTransaction();

            // Update Product table
            $stmt = $con->prepare("UPDATE Product SET Product_Name = ?, Product_Stock = ? WHERE Product_ID = ?");
            $stmt->execute([$prod_name, $prod_quantity, $id]);

            // Insert new price record in Product_Price table
            $date_added = $date_added ? date('Y-m-d H:i:s', strtotime($date_added)) : date('Y-m-d H:i:s');
            $stmt = $con->prepare("INSERT INTO Product_Price (Product_ID, Price, Effective_From, Effective_To, Created_At) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$id, $prod_price, $price_effective_from, $price_effective_to, $date_added]);

            // Delete existing categories for the product
            $stmt = $con->prepare("DELETE FROM Product_Category WHERE Product_ID = ?");
            $stmt->execute([$id]);

            // Insert new categories (if provided)
            if (!empty($category_ids)) {
                foreach ($category_ids as $category_id) {
                    $stmt = $con->prepare("INSERT INTO Product_Category (Product_ID, Category_ID) VALUES (?, ?)");
                    $stmt->execute([$id, $category_id]);
                }
            }

            $con->commit();
            return true;
        } catch (PDOException $e) {
            $con->rollBack();
            error_log("Error updating product: " . $e->getMessage());
            return false;
        }
    }


    }
    
    
?>