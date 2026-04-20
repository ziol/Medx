<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>MedX | Smart Medical Platform</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header class="navbar">
    <div class="logo">Med<span>X</span></div>
    <nav>
        <a href="index.php">Home</a>
        <a href="medicine.php">Medicines</a>
        <a href="diseases.php">Diseases</a> 
        <a href="#prescription-reader">Prescription Reader</a>
        <a href="contact.php">Contact</a>
        <a href="login.php" style="background: rgba(255,255,255,0.2); padding: 5px 15px; border-radius: 20px; font-weight: bold; border: 1px solid rgba(255,255,255,0.5);">Admin</a>
    </nav>
</header>

<section class="hero">
    <div class="hero-text">
        <h1>Your Trusted Medical Information Hub</h1>
        <p>Search medicines, diseases & read prescriptions instantly.</p>
        <button>Explore Now</button>
    </div>
</section>

<section class="search-box">
    <form action="search.php" method="GET" class="search-input-group">
        <button type="button" class="filter-btn">FILTER</button>
        <div class="search-autocomplete">
            <input name="query" id="mainSearch" type="text" placeholder="Search medicine, disease or doctor...">
            <ul id="searchList" class="search-list hidden"></ul>
        </div>
        <button type="submit" id="searchBtn">Search</button>
    </form>
</section>

<section class="features">
    <div class="feature-card">
        <h3>💊 Medicine Database</h3>
        <p>Complete medicine details including dosage, price & alternatives.</p>
        <a href="medicine.php" style="text-decoration:none;"><button type="button">View Medicines</button></a>
    </div>

    <div class="feature-card">
        <h3>🦠 Disease Information</h3>
        <p>Symptoms, causes & treatment guidelines in simple language.</p>
        <a href="diseases.php" style="text-decoration:none;"><button type="button">View Diseases</button></a>
    </div>

    <div class="feature-card highlight">
        <h3>📄 Prescription Reader</h3>
        <p>Upload or scan doctor prescriptions and get medicine details automatically.</p>
        <a href="#prescription-reader" style="text-decoration:none;"><button id="featureUploadBtn">Upload Prescription</button></a>
    </div>
</section>

<section class="prescription" id="prescription-reader">
    <h2>🤖 AI Prescription Reader</h2>
    <p>Upload a prescription image and let AI read it for you.</p>

    <!-- Drop Zone -->
    <div class="rx-drop-zone" id="rxDropZone">
        <input type="file" id="fileInput" accept="image/*" hidden>
        <div id="rxDropContent">
            <div style="font-size: 48px; margin-bottom: 10px;">📄</div>
            <p style="font-weight: 600; font-size: 16px; color: #333;">Drop your prescription here</p>
            <p style="color: #888; font-size: 13px; margin: 5px 0 15px;">or click to browse · PNG, JPG, WEBP</p>
            <button type="button" class="btn-upload" id="browseBtn" style="font-size: 14px; padding: 10px 25px;">
                📁 Choose File
            </button>
        </div>

        <!-- Image Preview -->
        <div id="rxPreview" style="display: none; position: relative; width: 100%;">
            <img id="previewImg" src="" alt="Prescription Preview" style="max-width: 100%; max-height: 350px; border-radius: 10px; object-fit: contain;">
            <button type="button" id="removeImgBtn" style="position: absolute; top: 8px; right: 8px; background: #e74c3c; color: white; border: none; border-radius: 50%; width: 32px; height: 32px; cursor: pointer; font-size: 16px; font-weight: bold; display: flex; align-items: center; justify-content: center;">✕</button>
        </div>
    </div>

    <!-- Language Toggle -->
    <div id="rxLangToggle" style="margin-top: 15px; display: flex; align-items: center; gap: 10px;">
        <span style="font-size: 14px; font-weight: 600; color: #555;">📝 Report Language:</span>
        <button type="button" class="btn-upload rx-lang-btn active" id="langEnBtn" style="padding: 8px 18px; font-size: 13px; border-radius: 20px;">🇬🇧 English</button>
        <button type="button" class="btn-upload rx-lang-btn" id="langBnBtn" style="padding: 8px 18px; font-size: 13px; border-radius: 20px; background: #e0e0e0; color: #333;">🇧🇩 বাংলা</button>
    </div>

    <!-- Analyze Button -->
    <button type="button" class="btn-upload" id="analyzeBtn" disabled style="margin-top: 15px; padding: 14px 35px; font-size: 16px; opacity: 0.5; cursor: not-allowed;">
        ⚡ Analyze Prescription
    </button>

    <!-- Loading State -->
    <div id="rxLoading" style="display: none; margin-top: 20px;">
        <div class="rx-spinner"></div>
        <p style="margin-top: 10px; color: #0d6efd; font-weight: 600;">🔍 AI is reading your prescription...</p>
        <div class="rx-progress-bar"><div class="rx-progress-fill" id="rxProgressFill"></div></div>
    </div>

    <!-- Results -->
    <div class="output-box" id="rxResults" style="display: none;">
        <div style="display: flex; align-items: center; justify-content: center; gap: 8px; margin-bottom: 15px;">
            <span style="font-size: 22px;">✅</span>
            <p style="font-weight: 700; font-size: 18px; color: #0d6efd; margin: 0;">Prescription Analysis</p>
        </div>
        <div id="rxResultContent" style="text-align: left; line-height: 1.8; white-space: pre-wrap; font-size: 14px; color: #333;"></div>
        <button type="button" class="btn-upload" id="newScanBtn" style="margin-top: 20px; background: #20c997;">
            🔄 Scan Another Prescription
        </button>
    </div>

    <!-- Error -->
    <div class="output-box" id="rxError" style="display: none; border: 2px solid #e74c3c;">
        <p style="color: #e74c3c; font-weight: 600;">❌ Error</p>
        <p id="rxErrorMsg" style="color: #666;"></p>
        <button type="button" class="btn-upload" id="retryBtn" style="margin-top: 10px; background: #e74c3c;">
            🔁 Try Again
        </button>
    </div>
</section>

<footer>
    <p>© <?php echo date("Y"); ?> MedX | Smart Healthcare Platform</p>
</footer>

<script src="script.js"></script>
</body>
</html>