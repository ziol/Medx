<?php
// ডাটাবেস কানেকশন
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "medx_db"; // আপনার ডাটাবেস এর নাম দিন

$conn = mysqli_connect($host, $user, $pass, $dbname);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// ডাটাবেস থেকে রোগসমূহ আনা
$sql = "SELECT * FROM diseases";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Disease Database | MedX</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <style>
        .disease-container { padding: 50px 10%; display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; }
        .disease-card { background: #fff; padding: 20px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); border-top: 5px solid #007bff; }
        .disease-card h2 { color: #007bff; margin-bottom: 10px; }
        .disease-card p { color: #555; line-height: 1.6; }
    </style>
</head>
<body>

<header class="navbar">
    <div class="logo">Med<span>X</span></div>
    <nav>
        <a href="index.php">Home</a>
        <a href="medicine.php">Medicines</a>
        <a href="diseases.php" style="color: #ffcc00;">Diseases</a>
        <a href="#prescription-reader">Prescription Reader</a>
        <a href="login.php">Admin</a>
    </nav>
</header>

<div style="text-align: center; margin-top: 40px;">
    <h1>Disease Information Database</h1>
    <p>Find details about various diseases and treatments.</p>
</div>

<div class="disease-container">
    <?php
    if (mysqli_num_rows($result) > 0) {
        while($row = mysqli_fetch_assoc($result)) {
            echo '<div class="disease-card">';
            echo '<h2>🦠 ' . $row['disease_name'] . '</h2>';
            echo '<p><strong>Symptoms:</strong> ' . $row['symptoms'] . '</p>';
            echo '<p><strong>Description:</strong> ' . substr($row['description'], 0, 150) . '...</p>';
            echo '<button style="margin-top:10px; padding:8px 15px; cursor:pointer;">Read More</button>';
            echo '</div>';
        }
    } else {
        echo "<p>No disease data available.</p>";
    }
    ?>
</div>

<footer>
    <p>© <?php echo date("Y"); ?> MedX | Smart Healthcare Platform</p>
</footer>

</body>
</html>