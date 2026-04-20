<?php
session_start();
include 'db_config.php';

if (isset($_POST['login'])) {
    $user = mysqli_real_escape_string($conn, $_POST['username']);
    $pass = $_POST['password']; // ইউজার যা টাইপ করেছে

    // ডাটাবেস থেকে চেক করা
    $sql = "SELECT * FROM admins WHERE username = '$user' AND password = '$pass'";
    $result = mysqli_query($conn, $sql);
    
    if (mysqli_num_rows($result) === 1) {
        $admin = mysqli_fetch_assoc($result);
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_name'] = $admin['username'];
        header("Location: admin_dashboard.php");
        exit;
    } else {
        $error = "ভুল ইউজারনেম বা পাসওয়ার্ড!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Login | MedX</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body { background: #f0f2f5; display: flex; align-items: center; justify-content: center; height: 100vh; margin: 0; }
        .login-card { background: white; padding: 40px; border-radius: 15px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); width: 100%; max-width: 380px; }
        .login-card h2 { color: #0d6efd; margin-bottom: 25px; text-align: center; font-size: 24px; }
        .login-card input { width: 100%; padding: 12px; margin: 10px 0; border: 1px solid #ddd; border-radius: 8px; outline: none; }
        .login-card button { width: 100%; padding: 12px; background: #0d6efd; color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: bold; font-size: 16px; margin-top: 10px; }
        .error { color: #dc3545; background: #f8d7da; padding: 10px; border-radius: 5px; font-size: 14px; text-align: center; margin-bottom: 15px; }
    </style>
</head>
<body>
    <div class="login-card">
        <h2>MedX Admin</h2>
        <?php if(isset($error)) echo "<div class='error'>$error</div>"; ?>
        <form method="POST">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" name="login">Login</button>
        </form>
        <div style="text-align: center; margin-top: 20px;">
            <a href="index.php" style="text-decoration: none; color: #666; font-size: 13px;">← Back to Home</a>
        </div>
    </div>
</body>
</html>