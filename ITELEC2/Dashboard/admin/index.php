<?php
    require_once 'authentication/admin-class.php';

    $admin = new ADMIN();
    if(!$admin->isUserLoggedIn())
    {
        $admin->redirect('../../');
    }

    $smtm = $admin->runQuery("SELECT * FROM user WHERE id = :id");
    $smtm->execute(array(":id" => $_SESSION['adminSession']));
    $user_data = $smtm->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome Page</title>
    <link rel="stylesheet" href="../../src/css/homepage.css">
</head>
<body>
   
    <nav class="navbar">
        <ul class="nav-links">
            <li><a href="authentication/admin-class.php?admin_signout">Sign Out</a></li>
        </ul>
    </nav>

  
    <section class="welcome-section">
        <div class="welcome-message">
            <h1>Welcome, <span class="username"><?php echo $user_data['username']; ?></span>!</h1>
        </div>
    </section>

    <footer>
        <p>&copy; 2024 Admin Dashboard. All rights reserved.</p>
    </footer>
</body>
</html>
