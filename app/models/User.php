<?php
class User {
    private $db;

    public function __construct(){
        $this->db = new Database;
    }

    // Find user by username
    public function findUserByUsername($username){
        $this->db->query('SELECT * FROM users WHERE username = :username');
        $this->db->bind(':username', $username);

        $row = $this->db->single();

        // Check row
        if($this->db->rowCount() > 0){
            return $row;
        } else {
            return false;
        }
    }

    // Login User
    public function login($username, $password){
        $user = $this->findUserByUsername($username);

        if($user === false){
            return false;
        }

        $hashed_password = $user->password;
        if(password_verify($password, $hashed_password)){
            return $user;
        } else {
            return false;
        }
    }

    // Check if user is locked out
    public function isUserLockedOut($username) {
        $this->db->query('SELECT lockout_until FROM users WHERE username = :username');
        $this->db->bind(':username', $username);
        $row = $this->db->single();

        if ($row && $row->lockout_until && strtotime($row->lockout_until) > time()) {
            return true;
        }
        return false;
    }

    // Record a failed login attempt
    public function recordFailedLoginAttempt($username) {
        $this->db->query('UPDATE users SET login_attempts = login_attempts + 1 WHERE username = :username');
        $this->db->bind(':username', $username);
        $this->db->execute();

        // Check if the user should be locked out
        $this->db->query('SELECT login_attempts FROM users WHERE username = :username');
        $this->db->bind(':username', $username);
        $row = $this->db->single();

        if ($row && $row->login_attempts >= LOGIN_ATTEMPT_LIMIT) {
            $lockout_time = date('Y-m-d H:i:s', time() + LOGIN_LOCKOUT_TIME);
            $this->db->query('UPDATE users SET lockout_until = :lockout_until WHERE username = :username');
            $this->db->bind(':lockout_until', $lockout_time);
            $this->db->bind(':username', $username);
            $this->db->execute();
        }
    }

    // Clear login attempts on successful login
    public function clearLoginAttempts($username) {
        $this->db->query('UPDATE users SET login_attempts = 0, lockout_until = NULL WHERE username = :username');
        $this->db->bind(':username', $username);
        $this->db->execute();
    }
}
?>