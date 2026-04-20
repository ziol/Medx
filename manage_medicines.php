<?php
session_start();
// Login check: login na kora thakle login page e pathiye debe
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit;
}
include 'db_config.php';

// Delete Logic: Delete button e click korle eita kaj korbe
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $delete_query = "DELETE FROM medicines WHERE id = $id";
    if (mysqli_query($conn, $delete_query)) {
        header("Location: manage_medicines.php");
        exit;
    }
}

// Database theke sob medicine niye asa
$result = mysqli_query($conn, "SELECT * FROM medicines ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Medicines | MedX Admin</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .dashboard-container { display: flex; min-height: 100vh; }
        .sidebar { width: 250px; background: #212529; color: white; padding: 20px; flex-shrink: 0; }
        .sidebar h2 { color: #20c997; font-size: 22px; margin-bottom: 30px; }
        .sidebar a { display: block; color: #adb5bd; text-decoration: none; padding: 15px 0; border-bottom: 1px solid #343a40; transition: 0.3s; }
        .sidebar a:hover { color: white; padding-left: 10px; }
        .main-content { flex: 1; padding: 40px; background: #f8f9fa; }
        
        table { width: 100%; border-collapse: collapse; background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 5px 15px rgba(0,0,0,0.05); }
        th, td { padding: 15px; text-align: left; border-bottom: 1px solid #eee; }
        th { background: #0d6efd; color: white; font-weight: 600; }
        tr:hover { background: #f1f1f1; }
        
        .btn-edit { color: #0d6efd; text-decoration: none; font-weight: bold; padding: 5px 10px; border: 1px solid #0d6efd; border-radius: 4px; }
        .btn-edit:hover { background: #0d6efd; color: white; }
        
        .btn-delete { color: #dc3545; text-decoration: none; font-weight: bold; padding: 5px 10px; border: 1px solid #dc3545; border-radius: 4px; margin-left: 5px; }
        .btn-delete:hover { background: #dc3545; color: white; }

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
            <h2>Manage Medicines</h2>
            <p>Ekhon theke apni medicine edit ba delete korte parben.</p>
            <hr style="margin: 20px 0; opacity: 0.2;">

            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Generic</th>
                        <th>Brand</th>
                        <th>Price (৳)</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(mysqli_num_rows($result) > 0): ?>
                        <?php while($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><?php echo $row['name']; ?></td>
                            <td><?php echo $row['generic']; ?></td>
                            <td><?php echo $row['brand']; ?></td>
                            <td><?php echo $row['price']; ?></td>
                            <td>
                                <a href="edit_medicine.php?id=<?php echo $row['id']; ?>" class="btn-edit">Edit</a>
                                <a href="manage_medicines.php?delete=<?php echo $row['id']; ?>" class="btn-delete" onclick="return confirm('Are you sure you want to delete this?')">Delete</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 20px;">No medicines found in database.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>