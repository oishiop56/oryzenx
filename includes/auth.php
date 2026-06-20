<?php
/**
 * Authentication System
 * User Registration, Login, Session Management
 */

class Auth {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function register($full_name, $email, $phone, $address, $password, $confirm_password) {
        if (empty($full_name) || empty($email) || empty($password)) {
            return array('success' => false, 'message' => 'All fields are required');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return array('success' => false, 'message' => 'Invalid email format');
        }

        if ($password !== $confirm_password) {
            return array('success' => false, 'message' => 'Passwords do not match');
        }

        if (strlen($password) < 8) {
            return array('success' => false, 'message' => 'Password must be at least 8 characters');
        }

        $existing = $this->db->fetch('SELECT id FROM users WHERE email = ?', array($email));
        if ($existing) {
            return array('success' => false, 'message' => 'Email already registered');
        }

        $hashed_password = password_hash($password, PASSWORD_BCRYPT, array('cost' => 12));

        try {
            $this->db->query(
                'INSERT INTO users (full_name, email, phone, address, password) VALUES (?, ?, ?, ?, ?)',
                array($full_name, $email, $phone, $address, $hashed_password)
            );

            return array('success' => true, 'message' => 'Registration successful. Please login.');
        } catch (Exception $e) {
            error_log('Registration Error: ' . $e->getMessage());
            return array('success' => false, 'message' => 'Registration failed');
        }
    }

    public function login($email, $password) {
        if (empty($email) || empty($password)) {
            return array('success' => false, 'message' => 'Email and password required');
        }

        $user = $this->db->fetch('SELECT * FROM users WHERE email = ? AND status = ?', array($email, 'active'));

        if (!$user) {
            return array('success' => false, 'message' => 'Invalid email or password');
        }

        if (!password_verify($password, $user['password'])) {
            return array('success' => false, 'message' => 'Invalid email or password');
        }

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_name'] = $user['full_name'];
        $_SESSION['is_admin'] = $user['is_admin'];
        $_SESSION['login_time'] = time();

        return array('success' => true, 'message' => 'Login successful', 'user' => $user);
    }

    public function googleLogin($google_id, $email, $full_name, $profile_image = null) {
        try {
            $user = $this->db->fetch('SELECT * FROM users WHERE google_id = ?', array($google_id));

            if (!$user) {
                $this->db->query(
                    'INSERT INTO users (google_id, email, full_name, profile_image) VALUES (?, ?, ?, ?)',
                    array($google_id, $email, $full_name, $profile_image)
                );
                $user_id = $this->db->lastInsertId();
                $user = $this->db->fetch('SELECT * FROM users WHERE id = ?', array($user_id));
            }

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_name'] = $user['full_name'];
            $_SESSION['is_admin'] = $user['is_admin'];
            $_SESSION['login_time'] = time();

            return array('success' => true, 'message' => 'Google login successful', 'user' => $user);
        } catch (Exception $e) {
            error_log('Google Login Error: ' . $e->getMessage());
            return array('success' => false, 'message' => 'Google login failed');
        }
    }

    public function isLoggedIn() {
        if (isset($_SESSION['user_id'])) {
            if (time() - $_SESSION['login_time'] > SESSION_TIMEOUT) {
                $this->logout();
                return false;
            }
            $_SESSION['login_time'] = time();
            return true;
        }
        return false;
    }

    public function isAdmin() {
        return isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1;
    }

    public function getCurrentUserId() {
        return isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
    }

    public function getCurrentUser() {
        if (!$this->isLoggedIn()) {
            return null;
        }
        return $this->db->fetch('SELECT * FROM users WHERE id = ?', array($_SESSION['user_id']));
    }

    public function logout() {
        session_destroy();
        return true;
    }

    public function updateProfile($user_id, $full_name, $phone, $address) {
        try {
            $this->db->query(
                'UPDATE users SET full_name = ?, phone = ?, address = ? WHERE id = ?',
                array($full_name, $phone, $address, $user_id)
            );
            $_SESSION['user_name'] = $full_name;
            return array('success' => true, 'message' => 'Profile updated successfully');
        } catch (Exception $e) {
            return array('success' => false, 'message' => 'Profile update failed');
        }
    }

    public function changePassword($user_id, $old_password, $new_password, $confirm_password) {
        $user = $this->db->fetch('SELECT * FROM users WHERE id = ?', array($user_id));

        if (!$user) {
            return array('success' => false, 'message' => 'User not found');
        }

        if (!password_verify($old_password, $user['password'])) {
            return array('success' => false, 'message' => 'Old password is incorrect');
        }

        if ($new_password !== $confirm_password) {
            return array('success' => false, 'message' => 'New passwords do not match');
        }

        if (strlen($new_password) < 8) {
            return array('success' => false, 'message' => 'New password must be at least 8 characters');
        }

        $hashed = password_hash($new_password, PASSWORD_BCRYPT, array('cost' => 12));
        $this->db->query('UPDATE users SET password = ? WHERE id = ?', array($hashed, $user_id));

        return array('success' => true, 'message' => 'Password changed successfully');
    }
}

$auth = new Auth();
?>
