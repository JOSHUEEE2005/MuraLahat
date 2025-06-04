<?php
class database {
    function opencon(): PDO {
        return new PDO(
            dsn: 'mysql:host=localhost;dbname=muralahat',
            username: 'root',
            password: ''
        );
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
}
?>