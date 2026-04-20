<?php
include 'db_config.php';
mysqli_set_charset($conn, "utf8mb4");

$query = isset($_GET['query']) ? trim($_GET['query']) : '';

// Search medicines (from array)
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
];

$medResults = [];
if ($query !== '') {
    foreach ($medicines as $med) {
        if (
            stripos($med['name'], $query) !== false ||
            stripos($med['generic'], $query) !== false ||
            stripos($med['brand'], $query) !== false
        ) {
            $medResults[] = $med;
        }
    }
}

// Search diseases (from database)
$diseaseResults = [];
if ($query !== '') {
    $safe = mysqli_real_escape_string($conn, $query);
    $sql = "SELECT * FROM diseases WHERE disease_name LIKE '%$safe%' OR symptoms LIKE '%$safe%' OR description LIKE '%$safe%' OR treatment LIKE '%$safe%'";
    $result = mysqli_query($conn, $sql);
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $diseaseResults[] = $row;
        }
    }
}

// Search doctors
$doctorResults = [];
if ($query !== '') {
    mysqli_select_db($conn, "doctors");
    $safe = mysqli_real_escape_string($conn, $query);
    $sql = "SELECT * FROM doctors WHERE doctor_name LIKE '%$safe%' OR specialist LIKE '%$safe%' OR chamber LIKE '%$safe%'";
    $result = mysqli_query($conn, $sql);
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $doctorResults[] = $row;
        }
    }
}

$totalResults = count($medResults) + count($diseaseResults) + count($doctorResults);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Search: <?php echo htmlspecialchars($query); ?> | MedX</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <style>
        .search-results { padding: 30px 5%; max-width: 1100px; margin: 0 auto; }
        .search-header { 
            background: white; padding: 25px 30px; border-radius: 12px; 
            box-shadow: 0 4px 15px rgba(0,0,0,0.06); margin-bottom: 25px;
            border-left: 5px solid #0d6efd;
        }
        .search-header h1 { font-size: 22px; color: #333; }
        .search-header h1 span { color: #0d6efd; }
        .search-header p { color: #888; font-size: 14px; margin-top: 5px; }
        .result-section { margin-bottom: 30px; }
        .result-section h2 { 
            font-size: 18px; color: #0d6efd; margin-bottom: 15px; 
            padding-bottom: 8px; border-bottom: 2px solid #eef5ff; 
        }
        .result-card {
            background: white; padding: 20px; border-radius: 10px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.05); margin-bottom: 12px;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .result-card:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(0,0,0,0.1); }
        .result-card h3 { margin: 0 0 8px; color: #333; font-size: 16px; }
        .result-card p { margin: 3px 0; font-size: 14px; color: #555; }
        .result-tag {
            display: inline-block; padding: 3px 10px; border-radius: 12px;
            font-size: 11px; font-weight: 600; margin-right: 8px;
        }
        .tag-medicine { background: #eef5ff; color: #0d6efd; }
        .tag-disease { background: #e8fff0; color: #27ae60; }
        .tag-doctor { background: #fff3e0; color: #e67e22; }
        .no-results {
            text-align: center; padding: 60px 20px; color: #999;
        }
        .no-results span { font-size: 48px; display: block; margin-bottom: 15px; }
        .back-btn {
            display: inline-block; margin-top: 15px; padding: 10px 25px;
            background: #0d6efd; color: white; text-decoration: none;
            border-radius: 8px; font-weight: 600;
        }
        .price-tag { color: #0d6efd; font-weight: bold; font-size: 15px; }
    </style>
</head>
<body>

<header class="navbar">
    <div class="logo">Med<span>X</span></div>
    <nav>
        <a href="index.php">Home</a>
        <a href="medicine.php">Medicines</a>
        <a href="diseases.php">Diseases</a>
        <a href="index.php#prescription-reader">Prescription Reader</a>
        <a href="contact.php">Contact</a>
    </nav>
</header>

<div class="search-results">

    <!-- Search Header -->
    <div class="search-header">
        <h1>🔍 Search results for: <span>"<?php echo htmlspecialchars($query); ?>"</span></h1>
        <p><?php echo $totalResults; ?> result(s) found</p>
    </div>

    <!-- Search Again -->
    <section class="search-box" style="margin-top: 0; margin-bottom: 25px;">
        <form action="search.php" method="GET" class="search-input-group">
            <input name="query" id="mainSearch" type="text" placeholder="Search medicine, disease or doctor..." value="<?php echo htmlspecialchars($query); ?>">
            <button type="submit" id="searchBtn">Search</button>
        </form>
    </section>

    <?php if ($query === ''): ?>
        <div class="no-results">
            <span>🔍</span>
            <h2>Enter a search term</h2>
            <p>Search for medicines, diseases, or doctors</p>
            <a href="index.php" class="back-btn">← Back to Home</a>
        </div>

    <?php elseif ($totalResults === 0): ?>
        <div class="no-results">
            <span>😔</span>
            <h2>No results found</h2>
            <p>Try searching with a different keyword</p>
            <a href="index.php" class="back-btn">← Back to Home</a>
        </div>

    <?php else: ?>

        <!-- Medicine Results -->
        <?php if (count($medResults) > 0): ?>
        <div class="result-section">
            <h2>💊 Medicines (<?php echo count($medResults); ?>)</h2>
            <?php foreach ($medResults as $med): ?>
            <div class="result-card">
                <h3>
                    <span class="result-tag tag-medicine">Medicine</span>
                    💊 <?php echo htmlspecialchars($med['name']); ?>
                </h3>
                <p><strong>Generic:</strong> <?php echo htmlspecialchars($med['generic']); ?></p>
                <p><strong>Brand:</strong> <?php echo htmlspecialchars($med['brand']); ?></p>
                <p class="price-tag">Price: ৳ <?php echo htmlspecialchars($med['price']); ?></p>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <!-- Disease Results -->
        <?php if (count($diseaseResults) > 0): ?>
        <div class="result-section">
            <h2>🦠 Diseases (<?php echo count($diseaseResults); ?>)</h2>
            <?php foreach ($diseaseResults as $d): ?>
            <div class="result-card">
                <h3>
                    <span class="result-tag tag-disease">Disease</span>
                    🦠 <?php echo htmlspecialchars($d['disease_name']); ?>
                </h3>
                <p><strong>Symptoms:</strong> <?php echo htmlspecialchars($d['symptoms']); ?></p>
                <p><strong>Treatment:</strong> <?php echo htmlspecialchars($d['treatment']); ?></p>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <!-- Doctor Results -->
        <?php if (count($doctorResults) > 0): ?>
        <div class="result-section">
            <h2>👨‍⚕️ Doctors (<?php echo count($doctorResults); ?>)</h2>
            <?php foreach ($doctorResults as $doc): ?>
            <div class="result-card">
                <h3>
                    <span class="result-tag tag-doctor">Doctor</span>
                    👨‍⚕️ <?php echo htmlspecialchars($doc['doctor_name']); ?>
                </h3>
                <p><strong>Specialty:</strong> <?php echo htmlspecialchars($doc['specialist']); ?></p>
                <p><strong>Phone:</strong> <?php echo htmlspecialchars($doc['phone']); ?></p>
                <p><strong>Chamber:</strong> <?php echo htmlspecialchars($doc['chamber']); ?></p>
                <a href="tel:<?php echo $doc['phone']; ?>" class="back-btn" style="background: #27ae60; font-size: 13px; padding: 8px 18px;">📞 Call</a>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

    <?php endif; ?>

</div>

<footer>
    <p>© <?php echo date("Y"); ?> MedX | Smart Healthcare Platform</p>
</footer>

</body>
</html>
