<?php
include 'db_config.php'; 

// ডাটাবেস থেকে তথ্য বাংলা ফন্টে সুন্দরভাবে আনার জন্য এই লাইনটি যোগ করুন
mysqli_set_charset($conn, "utf8mb4");

$sql = "SELECT * FROM diseases";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <title>অসুখের তথ্য | MedX</title>
    <style>
        body { font-family: 'SolaimanLipi', Arial, sans-serif; background-color: #f0f4f8; margin: 0; }
        .header { background: #2c3e50; color: white; text-align: center; padding: 20px; }
        .container { display: flex; flex-wrap: wrap; justify-content: center; padding: 20px; }
        .card { 
            background: white; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            width: 300px; margin: 15px; padding: 20px; border-top: 5px solid #27ae60;
        }
        .card h2 { color: #27ae60; font-size: 22px; margin-top: 0; }
        .card p { font-size: 15px; color: #555; line-height: 1.6; }
        .label { font-weight: bold; color: #333; }
        .search-bar { text-align: center; margin-top: 20px; }
        input[type="text"] { padding: 10px; width: 50%; border-radius: 5px; border: 1px solid #ddd; }
    </style>
</head>
<body>

<div class="header">
    <h1>১০০+ অসুখ ও তার প্রতিকার</h1>
    <p>আপনার স্বাস্থ্য আমাদের অগ্রাধিকার</p>
</div>

<div class="search-bar">
    <input type="text" id="myInput" onkeyup="searchFunc()" placeholder="অসুখের নাম সার্চ করুন...">
</div>

<div class="container" id="diseaseWrapper">
    <?php
    if (mysqli_num_rows($result) > 0) {
        while($row = mysqli_fetch_assoc($result)) {
            echo '<div class="card disease-item">';
            echo '<h2>🦠 ' . $row['disease_name'] . '</h2>';
            echo '<p><span class="label">লক্ষণ:</span> ' . $row['symptoms'] . '</p>';
            echo '<p><span class="label">বর্ণনা:</span> ' . $row['description'] . '</p>';
            echo '<p style="color: #c0392b;"><span class="label">চিকিৎসা:</span> ' . $row['treatment'] . '</p>';
            echo '</div>';
        }
    } else {
        echo "<p>কোনো তথ্য পাওয়া যায়নি।</p>";
    }
    ?>
</div>

<script>
// সার্চ করার জন্য ছোট একটি জাভাস্ক্রিপ্ট
function searchFunc() {
    let input = document.getElementById('myInput').value.toUpperCase();
    let cards = document.getElementsByClassName('disease-item');
    for (i = 0; i < cards.length; i++) {
        let title = cards[i].getElementsByTagName('h2')[0];
        if (title.innerHTML.toUpperCase().indexOf(input) > -1) {
            cards[i].style.display = "";
        } else {
            cards[i].style.display = "none";
        }
    }
}
</script>

</body>
</html>