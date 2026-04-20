<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit;
}
include 'db_config.php';

// ১. নির্দিষ্ট ওষুধের তথ্য তুলে আনা
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $result = mysqli_query($conn, "SELECT * FROM medicines WHERE id = $id");
    $medicine = mysqli_fetch_assoc($result);
}

// ২. আপডেট করার লজিক
if (isset($_POST['update_medicine'])) {
    $id = $_POST['id'];
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $generic = mysqli_real_escape_string($conn, $_POST['generic']);
    $brand = mysqli_real_escape_string($conn, $_POST['brand']);
    $price = $_POST['price'];
    $details = mysqli_real_escape_string($conn, $_POST['details']);

    $update_sql = "UPDATE medicines SET name='$name', generic='$generic', brand='$brand', price='$price', details='$details' WHERE id=$id";
    
    if (mysqli_query($conn, $update_sql)) {
        echo "<script>alert('Medicine updated successfully!'); window.location='manage_medicines.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Medicine | MedX Admin</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .dashboard-container { display: flex; min-height: 100vh; }
        .sidebar { width: 250px; background: #212529; color: white; padding: 20px; flex-shrink: 0; }
        .sidebar h2 { color: #20c997; font-size: 22px; margin-bottom: 30px; }
        .sidebar a { display: block; color: #adb5bd; text-decoration: none; padding: 15px 0; border-bottom: 1px solid #343a40; }
        .main-content { flex: 1; padding: 40px; background: #f8f9fa; }
        
        .edit-form { background: white; padding: 30px; border-radius: 12px; box-shadow: 0 5px 20px rgba(0,0,0,0.05); max-width: 600px; }
        input, textarea { width: 100%; padding: 12px; margin-bottom: 15px; border: 1px solid #ddd; border-radius: 8px; }
        .btn-update { background: #0d6efd; color: white; border: none; padding: 15px; border-radius: 8px; width: 100%; cursor: pointer; font-weight: bold; }
        .logout-link { color: #ff6b6b !important; font-weight: bold; margin-top: 20px; }
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
            <h2>Edit Medicine Details</h2>
            <hr style="margin: 20px 0; opacity: 0.2;">

            <div class="edit-form">
                <form method="POST">
                    <input type="hidden" name="id" value="<?php echo $medicine['id']; ?>">
                    
                    <label>Medicine Name</label>
                    <input type="text" name="name" value="<?php echo $medicine['name']; ?>" required>
                    
                    <label>Generic Name</label>
                    <input type="text" name="generic" value="<?php echo $medicine['generic']; ?>">
                    
                    <label>Manufacturer/Brand</label>
                    <input type="text" name="brand" value="<?php echo $medicine['brand']; ?>">
                    
                    <label>Price (৳)</label>
                    <input type="number" step="0.01" name="price" value="<?php echo $medicine['price']; ?>">
                    
                    <label>Details</label>
                    <textarea name="details" rows="5"><?php echo $medicine['details']; ?></textarea>
                    
                    <button type="submit" name="update_medicine" class="btn-update">Update Medicine Information</button>
                    <br><br>
                    <a href="manage_medicines.php" style="text-decoration:none; color:#666; font-size:14px;">← Cancel & Back</a>
                </form>
            </div>
        </div>
    </div>
</body>
</html>