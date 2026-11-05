<?php
// User management functions
require_once 'db_config.php';

class UserManager {
    private $pdo;
    
    public function __construct() {
        $this->pdo = getConnection();
    }
    
    // Register new user
    public function registerUser($username, $email, $password, $firstName = null, $lastName = null, $phone = null) {
        try {
            // Hash the password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            
            $sql = "INSERT INTO users (username, email, password, first_name, last_name, phone) 
                    VALUES (:username, :email, :password, :first_name, :last_name, :phone)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':password', $hashedPassword);
            $stmt->bindParam(':first_name', $firstName);
            $stmt->bindParam(':last_name', $lastName);
            $stmt->bindParam(':phone', $phone);
            
            if ($stmt->execute()) {
                return $this->pdo->lastInsertId();
            }
            return false;
        } catch(PDOException $e) {
            error_log("Error registering user: " . $e->getMessage());
            return false;
        }
    }
    
    // Authenticate user
    public function authenticateUser($username, $password) {
        try {
            $sql = "SELECT * FROM users WHERE username = :username OR email = :username";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':username', $username);
            $stmt->execute();
            
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user && password_verify($password, $user['password'])) {
                // Update last login
                $this->updateLastLogin($user['id']);
                return $user;
            }
            return false;
        } catch(PDOException $e) {
            error_log("Error authenticating user: " . $e->getMessage());
            return false;
        }
    }
    
    // Update last login
    private function updateLastLogin($userId) {
        try {
            $sql = "UPDATE users SET updated_at = CURRENT_TIMESTAMP WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id', $userId, PDO::PARAM_INT);
            $stmt->execute();
        } catch(PDOException $e) {
            error_log("Error updating last login: " . $e->getMessage());
        }
    }
    
    // Get user by ID
    public function getUserById($id) {
        try {
            $sql = "SELECT id, username, email, first_name, last_name, phone, address, city, state, 
                           zip_code, country, user_type, created_at FROM users WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Error fetching user: " . $e->getMessage());
            return false;
        }
    }
    
    // Get all users (for admin panel)
    public function getAllUsers() {
        try {
            $sql = "SELECT id, username, email, first_name, last_name, phone, user_type, created_at 
                    FROM users ORDER BY created_at DESC";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Error fetching users: " . $e->getMessage());
            return false;
        }
    }
    
    // Update user profile
    public function updateUser($id, $username, $email, $firstName = null, $lastName = null, $phone = null, $address = null, $city = null, $state = null, $zipCode = null, $country = null) {
        try {
            $sql = "UPDATE users SET username = :username, email = :email, first_name = :first_name, 
                    last_name = :last_name, phone = :phone, address = :address, city = :city, 
                    state = :state, zip_code = :zip_code, country = :country 
                    WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':first_name', $firstName);
            $stmt->bindParam(':last_name', $lastName);
            $stmt->bindParam(':phone', $phone);
            $stmt->bindParam(':address', $address);
            $stmt->bindParam(':city', $city);
            $stmt->bindParam(':state', $state);
            $stmt->bindParam(':zip_code', $zipCode);
            $stmt->bindParam(':country', $country);
            
            return $stmt->execute();
        } catch(PDOException $e) {
            error_log("Error updating user: " . $e->getMessage());
            return false;
        }
    }
    
    // Update user password
    public function updateUserPassword($id, $newPassword) {
        try {
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            
            $sql = "UPDATE users SET password = :password WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':password', $hashedPassword);
            
            return $stmt->execute();
        } catch(PDOException $e) {
            error_log("Error updating password: " . $e->getMessage());
            return false;
        }
    }
    
    // Check if username exists
    public function usernameExists($username) {
        try {
            $sql = "SELECT COUNT(*) FROM users WHERE username = :username";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':username', $username);
            $stmt->execute();
            
            return $stmt->fetchColumn() > 0;
        } catch(PDOException $e) {
            error_log("Error checking username: " . $e->getMessage());
            return false;
        }
    }
    
    // Check if email exists
    public function emailExists($email) {
        try {
            $sql = "SELECT COUNT(*) FROM users WHERE email = :email";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            
            return $stmt->fetchColumn() > 0;
        } catch(PDOException $e) {
            error_log("Error checking email: " . $e->getMessage());
            return false;
        }
    }
    
    // Promote user to admin
    public function promoteToAdmin($userId) {
        try {
            $sql = "UPDATE users SET user_type = 'admin' WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id', $userId, PDO::PARAM_INT);
            
            return $stmt->execute();
        } catch(PDOException $e) {
            error_log("Error promoting user to admin: " . $e->getMessage());
            return false;
        }
    }
    
    // Demote admin to user
    public function demoteToUser($userId) {
        try {
            $sql = "UPDATE users SET user_type = 'customer' WHERE id = :id AND user_type = 'admin'";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id', $userId, PDO::PARAM_INT);
            
            return $stmt->execute();
        } catch(PDOException $e) {
            error_log("Error demoting admin to user: " . $e->getMessage());
            return false;
        }
    }
}

// Session management functions
class SessionManager {
    public static function init() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }
    
    public static function set($key, $value) {
        $_SESSION[$key] = $value;
    }
    
    public static function get($key) {
        return isset($_SESSION[$key]) ? $_SESSION[$key] : null;
    }
    
    public static function has($key) {
        return isset($_SESSION[$key]);
    }
    
    public static function remove($key) {
        unset($_SESSION[$key]);
    }
    
    public static function destroy() {
        session_destroy();
    }
    
    public static function isLoggedIn() {
        return self::has('user_id');
    }
    
    public static function isAdmin() {
        return self::has('user_type') && self::get('user_type') === 'admin';
    }
    
    public static function getCurrentUserId() {
        return self::get('user_id');
    }
    
    public static function getCurrentUserType() {
        return self::get('user_type');
    }
}
?>