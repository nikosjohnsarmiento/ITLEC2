<?php
require_once __DIR__ . '/../../../database/dbconnect.php';
include_once __DIR__ . '/../../../config/settings-config.php';
require_once __DIR__ . '/../../../src/vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class ADMIN 
{   
    private $conn;
    private $settings;
    private $smtp_email;
    private $smtp_password;
    private $password;

    public function __construct() 
    {
        $this->settings = new SystemConfig();
        $this->smtp_email = $this->settings->getSmtpEmail();
        $this->smtp_password = $this->settings->getSmtpPassword();


        $database = new Database();
        $this->conn = $database->dbConnection();
    }

    public function sendOtp($email) {
        if ($email == NULL) {
            echo "<script>alert('No Email Found'); window.location.href = '../../../';</script>";
            exit;
        }
        else {
            $stmt = $this->runQuery("SELECT * FROM user WHERE email = :email");
            $stmt->execute(array(":email" => $email));
            $stmt->fetch(PDO::FETCH_ASSOC);
        }
    }


    public function addAdmin($csrf_token, $username, $email, $password)
    {
        $stmt = $this->runQuery("SELECT * FROM user WHERE email = :email");
        $stmt->execute(array(":email" => $email));

        if ($stmt->rowCount() > 0) {
            echo "<script>alert('Email already exists. Please try another one'); window.location.href = '../../../';</script>";
            exit;
        } 
        else {
            $_SESSION['OTP'] = $otp;

            $subject = "OTP VERIFICATION";
            $message = " 
                <!DOCTPYE html>
<html>
<head>
	<meta charset='UTF-8'>
	<title>OTP Verification</title>
<style>
body {
	font-family:Arial , sans-serif;
	background-color: #f5f5f5;
	margin:0;
	padding:0;
}

.container {
	max-width:600px;
	margin:0 auto;
	padding: 30px;
	background-color: #ffffff;
	border-radius:4px;
	box-shadow:0 2px 4px rgba(0,0,0,0.1);
	}

h1 {
color: #333333;
font-size:24px;
margin-bottom:20px;
}

p {
color: #666666;
font-size:16px ; 
margin-bottom:10px;
}

.button {
display: inline-block;
padding:12px 24px ; 
background-color:#0088cc;
color:#ffffff;
text-decoration: none;
border-radius: 4px;
font-size:16px;
margin-top:20px;
}

.logo {
display:block;
text-align:center;
margin-bottom:30px;
}
</style>
</head>
<body>
	<div class='container'>
	<div class='logo'>
	<img src='cid:logo' alt='Logo' width='150>
	</div>
	<h1>OTP Verification</h1>
	<p>Hello, $email</p>
	<p>Your OTP is : $otp </p>
	<p>If you didn't request an OTP , please ignore this email.</p>
	</div>
</body>
</html>";

            $this->send_email($email , $message , $subject ,  $this->smtp_email ,  $this->smtp_password);
            echo "<script>alert('We sent you an OTP to $email'); window.location.href = '../../../verify-otp';</script>";
        }
    }   
}



        if (!isset($csrf_token) || !hash_equals($_SESSION['csrf_token'], $csrf_token)) {
            echo "<script>alert('Invalid CSRF token.'); window.location.href = '../../../';</script>";
            exit;
        }

        unset($_SESSION['csrf_token']);

        $hash_password = md5($password);

        $stmt = $this->runQuery('INSERT INTO user (username, email, password) VALUES (:username, :email, :password)');
        $exec = $stmt->execute(array(
            ":username" => $username,
            ":email" => $email,
            ":password" => $hash_password
        ));

        if ($exec) {
            echo "<script>alert('Admin added successfully.'); window.location.href = '../../../';</script>";
            exit;
        } else 
        {
            echo "<script>alert('Error adding admin.'); window.location.href = '../../../';</script>";
            exit;
        }
    
    public function adminSignin($email, $password, $csrf_token , $otp) 
    {
       try 
       {
        if (!isset($csrf_token) || !hash_equals($_SESSION['csrf_token'], $csrf_token)) {
            echo "<script>alert('Invalid CSRF token.'); window.location.href = '../../../';</script>";
            exit;
        }

        unset($_SESSION['csrf_token']);
        
        $stmt = $this->runQuery("SELECT * FROM user WHERE email = :email");
        $stmt->execute(array(":email" => $email));
        $userRow = $stmt->fetch (PDO::FETCH_ASSOC);

        if($stmt->rowCount() == 1 && $userRow['password'] == md5($password)) {
            $activity = "Has Successfully sign in ";
            $user_id = $userRow['id'];
            $this->logs($activity, $user_id);

             $_SESSION['adminSession'] = $user_id;

                echo "<script>alert('Welcome'); window.location.href = '../';</script>";
                exit;
    } 
        else{
            echo "<script>alert('Invalid Credintials'); window.location.href = '../../../';</script>";
            exit;
        }

    } catch(PDOException $ex){
        echo $ex->getMessage();
        }
    }

    public function adminSignout()
    {
        unset($_SESSION['adminSession']);
        echo "<script>alert('Sign Out Succefully.'); window.location.href = '../../../';</script>";
                exit;
    }

    function send_email($email, $message , $smtp_email , $smtp_password) { 
        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->SMTPDebug = 0;
        $mail->SMTPAuth = true ;
        $mail->SMTPSecure = "tls"; 
        $mail->Host = "smtp.gmail.com";
        $mail->Port = 587; 
        $mail->addAddress($email);
        $mail->Username = $smtp_email;
        $mail->Password = $smtp_password;
        $mail->SetFrom($smtp_email,"Wrenchner");
        $mail->Subject = $subject; 
        $mail->msgHTML($message);
        $mail->Send();

    }

    public function logs($activity, $user_id) 
    {
        $stmt = $this->runQuery("INSERT INTO logs (user_id , activity) VALUES (:user_id , :activity)");
        $stmt->execute(array(":user_id" => $user_id, ":activity" => $activity));

    }

    public function isUserLoggedIn()
    {
        if(isset($_SESSION['adminSession']))
        {
            return true;
        }
    }

    public function redirect()
    {
        echo "<script>alert('Admin must loggin first'); window.location.href = '../../../';</script>";
        exit;
    }

    public function runQuery($sql)
{
    $stmt = $this->conn->prepare($sql); 
    return $stmt;
}




if (isset($_POST['btn_signup'])) {
   $_SESSION['not verify_username'] = trim($_POST['username']);
   $_SESSION['not verify_email'] = trim($_POST['email']);
   $_SESSION['not verify_password'] = trim($_POST['password']);
   $_SESSION['not verify_csrf_token'] = trim($_POST['csrf_token']);
    
   $email = trim($_POST['email']);
   $otp = rand(100000 , 999999);


    $addAdmin = new ADMIN();
    $addAdmin->sendOtp($email); 
}

if (isset($_POST['btn_signin'])) {
    $csrf_token = trim($_POST['csrf_token']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    $adminSignin = new ADMIN();
    $adminSignin->adminSignin($email, $password, $csrf_token);
}

if(isset($_GET['admin_signout']))
{
    $adminSignout= new ADMIN();
    $adminSignout->adminSignout();
}

?>
