<?php
session_start();

include_once __DIR__ . '/../DATABASE/dbconnect.php';

// Error display 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// CSRF Token for security
if (empty($_SESSION['csrf_token'])) {
    $csrf_token = bin2hex(random_bytes(32));
    $_SESSION['csrf_token'] = $csrf_token;
} else {
    $csrf_token = $_SESSION['csrf_token'];
}

class SystemConfig {
    private $conn;
    private $stmt_email;
    private $stmt_password;

    public function __construct() 
    {
        $database = new Database();
        $db = $database->dbConnection();
        $this->conn = $db; 

        $stmt = $this->runQuery("SELECT * FROM email_config");
        $stmt->execute();
        $email_config = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->stmt_email = $email_config['email'];
        $this->stmt_password = $email_config['password'];
    }

    public function getSmtpEmail() {
        return $this->stmt_email;
    }

    public function getSmtpPassword() {
        return $this->stmt_password;
    }
    
    public function runQuery($sql) {
        $stmt = $this->conn->prepare($sql);
        return $stmt;
    }
}
?>
