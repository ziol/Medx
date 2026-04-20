<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>MedX | Medicine Database</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header class="navbar">
    <div class="logo">Med<span>X</span></div>
    <nav>
        <a href="index.php">Home</a>
        <a href="medicine.php">Medicines</a>
        <a href="#">Diseases</a>
        <a href="index.php#prescription-reader">Prescription Reader</a>
        <a href="#">Contact</a>
    </nav>
</header>

<main class="medicine-page">
    <div class="med-header" style="text-align: center; padding: 40px 5%;">
        <h1>Medicine Database</h1>
        <p>বিখ্যাত সব ওষুধের তালিকা এবং বিস্তারিত তথ্য নিচে দেখুন।</p>
    </div>

    <div class="features"> <?php
        // ওষুধের একটি তালিকা (Array) - এখানে আপনি ১০০+ নাম যোগ করতে পারবেন
        $medicines = [
            ["name" => "Napa Extend", "generic" => "Paracetamol", "price" => "2.50", "brand" => "Beximco"],
            ["name" => "Ace Plus", "generic" => "Paracetamol + Caffeine", "price" => "3.00", "brand" => "Square"],
            ["name" => "Fexo 120", "generic" => "Fexofenadine", "price" => "8.00", "brand" => "ACI"],
            ["name" => "Seclo 20", "generic" => "Omeprazole", "price" => "5.00", "brand" => "Square"],
            ["name" => "Sergel 20", "generic" => "Esomeprazole", "price" => "7.00", "brand" => "Healthcare"],
            ["name" => "Monas 10", "generic" => "Montelukast", "price" => "16.00", "brand" => "ACME"],
            ["name" => "Fenadin 120", "generic" => "Fexofenadine", "price" => "8.00", "brand" => "Renata"],
            ["name" => "Zithrin 500", "generic" => "Azithromycin", "price" => "35.00", "brand" => "Square"],
            ["name" => "Esonix 20", "generic" => "Esomeprazole", "price" => "7.00", "brand" => "Incepta"],
            ["name" => "Bextrum Gold", "generic" => "Multivitamin", "price" => "10.00", "brand" => "Beximco"],
            // এভাবে আপনি ১০০টি লাইন যোগ করতে পারবেন...
        ];

        // লুপ চালিয়ে কার্ডগুলো তৈরি করা হচ্ছে
        foreach ($medicines as $med) {
            echo '
            <div class="feature-card">
                <h3>💊 ' . $med['name'] . '</h3>
                <p><strong>Generic:</strong> ' . $med['generic'] . '</p>
                <p><strong>Brand:</strong> ' . $med['brand'] . '</p>
                <p style="color: #0d6efd; font-weight: bold; margin-top: 10px;">Price: ৳ ' . $med['price'] . '</p>
                <button type="button">Details</button>
            </div>';
        }
        ?>
    </div>
</main>

<footer>
    <p>© <?php echo date("Y"); ?> MedX | Smart Healthcare Platform</p>
</footer>

</body>
</html>