<?php
    include_once __DIR__ . '/Config/settings-config.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify OTP</title>
</head>
<body>
        <h1>Enter OTP</h1>
        <form action="dashboard/admin/authentication/admin-class.php" method="POST"> 
        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token ?>">
        <input typer="number" name="otp" placeholder="Enter OTP" required ><br>
        <button type="submit" name="btn-verify" >VERIFY</button>
        </form>
</body>
</html>