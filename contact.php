<?php
include 'db_config.php'; // আপনার মূল কানেকশন ফাইল

// ডাটাবেস পরিবর্তন করে 'doctors' ডাটাবেসে কানেক্ট করা
mysqli_select_db($conn, "doctors"); 

// বাংলা ফন্ট সাপোর্ট করার জন্য
mysqli_set_charset($conn, "utf8mb4");

// কুয়েরি চালানো
$sql = "SELECT * FROM doctors";
$result = mysqli_query($conn, $sql);

// যদি কুয়েরিতে ভুল থাকে তবে এরর দেখাবে
if (!$result) {
    die("Query Failed: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <title>আমাদের ডাক্তারগণ | MedX</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body { font-family: 'Segoe UI', Tahoma, Arial, sans-serif; background-color: #f0f2f5; margin: 0; }
        .header { background: #007bff; color: white; text-align: center; padding: 30px; }
        .container { padding: 40px 10%; display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; }
        .doctor-card { 
            background: white; 
            padding: 20px; 
            border-radius: 12px; 
            box-shadow: 0 4px 10px rgba(0,0,0,0.1); 
            border-left: 5px solid #007bff;
            transition: 0.3s;
        }
        .doctor-card:hover { transform: translateY(-5px); }
        .doctor-card h3 { color: #333; margin: 0 0 10px 0; }
        .specialty { color: #007bff; font-weight: bold; font-size: 14px; margin-bottom: 5px; }
        .phone { font-size: 18px; color: #28a745; margin: 10px 0; font-weight: bold; }
        .chamber { font-size: 13px; color: #666; line-height: 1.4; }
        .call-btn { 
            display: inline-block; 
            margin-top: 15px; 
            padding: 10px 20px; 
            background: #28a745; 
            color: white; 
            text-decoration: none; 
            border-radius: 5px; 
            font-weight: bold; 
            text-align: center;
        }
        .call-btn:hover { background: #218838; }
        .back-link { text-align: center; margin: 30px 0; }
        .back-link a { text-decoration: none; color: #007bff; font-weight: bold; }
    </style>
</head>
<body>

<div class="header">
    <h1>বিশেষজ্ঞ ডাক্তারদের তালিকা</h1>
    <p>সরাসরি কথা বলতে নিচের নাম্বারে কল করুন</p>
</div>

<div class="container">
    <?php 
    if (mysqli_num_rows($result) > 0) {
        while($row = mysqli_fetch_assoc($result)) {
    ?>
            <div class="doctor-card">
                <h3>👨‍⚕️ <?php echo htmlspecialchars($row['doctor_name']); ?></h3>
                <p class="specialty">🎯 <?php echo htmlspecialchars($row['specialist']); ?></p>
                <p class="phone">📞 <?php echo htmlspecialchars($row['phone']); ?></p>
                <p class="chamber">📍 চেম্বার: <?php echo htmlspecialchars($row['chamber']); ?></p>
                <a href="tel:<?php echo $row['phone']; ?>" class="call-btn">কল করুন</a>
            </div>
    <?php 
        }
    } else {
        echo "<p style='text-align:center; width:100%;'>কোনো ডাক্তারের তথ্য পাওয়া যায়নি।</p>";
    }
    ?>
</div>

<div class="back-link">
    <a href="index.php">← হোম পেজে ফিরে যান</a>
</div>

</body>
</html>