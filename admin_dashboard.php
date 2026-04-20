<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit;
}
include 'db_config.php';

// মেডিসিন যোগ করার লজিক
if (isset($_POST['add_medicine'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $generic = mysqli_real_escape_string($conn, $_POST['generic']);
    $brand = mysqli_real_escape_string($conn, $_POST['brand']);
    $price = $_POST['price'];
    $details = mysqli_real_escape_string($conn, $_POST['details']);

    $sql = "INSERT INTO medicines (name, generic, brand, price, details) VALUES ('$name', '$generic', '$brand', '$price', '$details')";
    if (mysqli_query($conn, $sql)) {
        $msg = "Medicine added successfully!";
    } else {
        $msg = "Error: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard | MedX</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .dashboard-container { display: flex; min-height: 100vh; }
        .sidebar { width: 250px; background: #212529; color: white; padding: 20px; flex-shrink: 0; }
        .sidebar h2 { color: #20c997; font-size: 22px; margin-bottom: 30px; }
        .sidebar a { display: block; color: #adb5bd; text-decoration: none; padding: 15px 0; border-bottom: 1px solid #343a40; transition: 0.3s; }
        .sidebar a:hover { color: white; padding-left: 10px; }
        .main-content { flex: 1; padding: 40px; background: #f8f9fa; }
        
        .add-form { background: white; padding: 30px; border-radius: 12px; box-shadow: 0 5px 20px rgba(0,0,0,0.05); max-width: 600px; }
        .add-form h3 { margin-bottom: 20px; color: #333; }
        .add-form input, .add-form textarea { width: 100%; padding: 12px; margin-bottom: 15px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; }
        .btn-post { background: #20c997; color: white; border: none; padding: 15px; border-radius: 8px; width: 100%; cursor: pointer; font-weight: bold; font-size: 16px; transition: 0.3s; }
        .btn-post:hover { background: #1ba37a; }
        
        .success-msg { color: #155724; background: #d4edda; padding: 10px; border-radius: 5px; margin-bottom: 15px; }
        .logout-link { color: #ff6b6b !important; font-weight: bold; margin-top: 20px; border: none !important; }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <div class="sidebar">
            <h2>MedX Admin</h2>
            <a href="admin_dashboard.php">Add Medicine</a>
            <a href="manage_medicines.php">Manage Medicines</a>
            <a href="logout.php" class="logout-link">Logout</a>
        </div>

        <div class="main-content">
            <h2>Welcome to Admin Panel</h2>
            <p>Ekhane apni medicine add ebong manage korte parben.</p>
            <hr style="margin: 20px 0; opacity: 0.2;">

            <div class="add-form">
                <h3>Add New Medicine</h3>
                <?php if(isset($msg)) echo "<div class='success-msg'>$msg</div>"; ?>
                
                <form method="POST">
                    <input type="text" name="name" placeholder="Medicine Name (e.g. Napa)" required>
                    <input type="text" name="generic" placeholder="Generic Name (e.g. Paracetamol)">
                    <input type="text" name="brand" placeholder="Manufacturer/Brand (e.g. Beximco)">
                    <input type="number" step="0.01" name="price" placeholder="Price (৳)">
                    <textarea name="details" rows="4" placeholder="Indications / How to use"></textarea>
                    <button type="submit" name="add_medicine" class="btn-post">Post Medicine</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>