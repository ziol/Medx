// ===== AI Prescription Reader - Gemini API Integration =====
// MedX | Smart Medical Platform

(function () {
    'use strict';

    // Gemini API Configuration
    const GEMINI_API_KEY = 'AIzaSyAdLNLcH8Mk6PDA3khIOuPureHnH34Wrro';
    
    // Multiple models for fallback - if one fails, try the next
    const GEMINI_MODELS = [
        'gemini-2.5-flash-lite',
        'gemini-3-flash-preview',
        'gemini-2.5-flash',
        'gemini-3.1-flash-lite-preview'
    ];

    function getApiUrl(model) {
        return `https://generativelanguage.googleapis.com/v1beta/models/${model}:generateContent?key=${GEMINI_API_KEY}`;
    }

    // DOM Elements
    const dropZone = document.getElementById('rxDropZone');
    const fileInput = document.getElementById('fileInput');
    const browseBtn = document.getElementById('browseBtn');
    const dropContent = document.getElementById('rxDropContent');
    const previewDiv = document.getElementById('rxPreview');
    const previewImg = document.getElementById('previewImg');
    const removeImgBtn = document.getElementById('removeImgBtn');
    const analyzeBtn = document.getElementById('analyzeBtn');
    const loadingDiv = document.getElementById('rxLoading');
    const resultsDiv = document.getElementById('rxResults');
    const resultContent = document.getElementById('rxResultContent');
    const errorDiv = document.getElementById('rxError');
    const errorMsg = document.getElementById('rxErrorMsg');
    const newScanBtn = document.getElementById('newScanBtn');
    const retryBtn = document.getElementById('retryBtn');

    // Exit if not on a page with the prescription reader
    if (!dropZone) return;

    let selectedFile = null;
    let base64Image = null;
    let selectedLang = 'en'; // Default language

    // Language toggle buttons
    const langEnBtn = document.getElementById('langEnBtn');
    const langBnBtn = document.getElementById('langBnBtn');

    if (langEnBtn && langBnBtn) {
        langEnBtn.addEventListener('click', function () {
            selectedLang = 'en';
            langEnBtn.style.background = '#0d6efd';
            langEnBtn.style.color = 'white';
            langBnBtn.style.background = '#e0e0e0';
            langBnBtn.style.color = '#333';
        });
        langBnBtn.addEventListener('click', function () {
            selectedLang = 'bn';
            langBnBtn.style.background = '#0d6efd';
            langBnBtn.style.color = 'white';
            langEnBtn.style.background = '#e0e0e0';
            langEnBtn.style.color = '#333';
        });
    }

    // ===== Event Listeners =====

    // Browse button click
    browseBtn.addEventListener('click', function (e) {
        e.stopPropagation();
        fileInput.click();
    });

    // Drop zone click
    dropZone.addEventListener('click', function () {
        fileInput.click();
    });

    // File input change
    fileInput.addEventListener('change', function () {
        if (this.files && this.files[0]) {
            handleFile(this.files[0]);
        }
    });

    // Drag and drop events
    dropZone.addEventListener('dragover', function (e) {
        e.preventDefault();
        e.stopPropagation();
        dropZone.classList.add('drag-over');
    });

    dropZone.addEventListener('dragleave', function (e) {
        e.preventDefault();
        e.stopPropagation();
        dropZone.classList.remove('drag-over');
    });

    dropZone.addEventListener('drop', function (e) {
        e.preventDefault();
        e.stopPropagation();
        dropZone.classList.remove('drag-over');

        if (e.dataTransfer.files && e.dataTransfer.files[0]) {
            handleFile(e.dataTransfer.files[0]);
        }
    });

    // Remove image
    removeImgBtn.addEventListener('click', function (e) {
        e.stopPropagation();
        resetUpload();
    });

    // Analyze button
    analyzeBtn.addEventListener('click', function () {
        if (base64Image) {
            analyzePrescription();
        }
    });

    // New scan button
    newScanBtn.addEventListener('click', function () {
        resetAll();
    });

    // Retry button
    retryBtn.addEventListener('click', function () {
        errorDiv.style.display = 'none';
        if (base64Image) {
            analyzePrescription();
        } else {
            resetAll();
        }
    });

    // ===== Functions =====

    function handleFile(file) {
        // Validate file type
        const validTypes = ['image/png', 'image/jpeg', 'image/jpg', 'image/webp'];
        if (!validTypes.includes(file.type)) {
            showError('Please upload a valid image file (PNG, JPG, or WEBP).');
            return;
        }

        // Validate file size (max 10MB)
        if (file.size > 10 * 1024 * 1024) {
            showError('Image size must be under 10MB.');
            return;
        }

        selectedFile = file;

        // Convert to base64
        const reader = new FileReader();
        reader.onload = function (e) {
            base64Image = e.target.result.split(',')[1];
            previewImg.src = e.target.result;
            
            // Show preview, hide drop content
            dropContent.style.display = 'none';
            previewDiv.style.display = 'flex';
            previewDiv.style.justifyContent = 'center';

            // Enable analyze button
            analyzeBtn.disabled = false;
            analyzeBtn.style.opacity = '1';
            analyzeBtn.style.cursor = 'pointer';
        };
        reader.readAsDataURL(file);
    }

    function resetUpload() {
        selectedFile = null;
        base64Image = null;
        fileInput.value = '';
        previewDiv.style.display = 'none';
        dropContent.style.display = 'block';
        analyzeBtn.disabled = true;
        analyzeBtn.style.opacity = '0.5';
        analyzeBtn.style.cursor = 'not-allowed';
    }

    function resetAll() {
        resetUpload();
        loadingDiv.style.display = 'none';
        resultsDiv.style.display = 'none';
        errorDiv.style.display = 'none';
        resultContent.innerHTML = '';
        dropZone.style.display = 'flex';
        analyzeBtn.style.display = 'inline-block';
    }

    function showError(message) {
        loadingDiv.style.display = 'none';
        resultsDiv.style.display = 'none';
        errorDiv.style.display = 'block';
        errorMsg.textContent = message;
        analyzeBtn.style.display = 'inline-block';
    }

    // English prompt
    const PROMPT_EN = `You are an expert pharmacist and prescription reader. Read this prescription image carefully.

RULES:
1. Output ONLY plain text with emojis. NEVER return JSON or code.
2. If you can read a medicine name, USE YOUR MEDICAL KNOWLEDGE to fill in its generic name, what it does, common dosage, and type. Do not write "unclear" if you know the medicine.
3. ONLY show fields that have actual information. Do NOT show a field just to write "unclear" or "not visible". Skip it entirely.
4. Read the handwriting carefully. Try your best to identify medicine names, dosages written as numbers (like 1+0+1), and durations.
5. Keep it clean and simple.

📋 PRESCRIPTION INFO
━━━━━━━━━━━━━━━━━━━━━━━━
👨‍⚕️ Doctor: [name]
🏥 Specialty: [if visible]
🏠 Clinic: [if visible]
👤 Patient: [if visible]
📅 Date: [if visible]
🆔 Age: [if visible]
(Skip any of these if not readable)

🔍 DIAGNOSIS
━━━━━━━━━━━━━━━━━━━━━━━━
[What the doctor diagnosed, or skip this section if not mentioned]

💊 MEDICINES
━━━━━━━━━━━━━━━━━━━━━━━━

For each medicine, show ONLY the fields you have info for:

💊 Medicine 1: [Name] [Strength if visible]
   🧪 Generic: [Use your pharma knowledge]
   💉 Type: [Tablet/Syrup/Capsule etc.]
   ⏰ Dose: [e.g. 1+0+1 = Morning & Night]
   🗓️ Duration: [e.g. 7 days]
   🍽️ When: [Before/After meals - only if specified]
   💡 Used for: [Brief purpose from your medical knowledge]

(Repeat for all medicines. Skip fields with no info.)

🧪 TESTS
━━━━━━━━━━━━━━━━━━━━━━━━
[Only show if tests are mentioned, otherwise skip this section]

📝 DOCTOR'S ADVICE
━━━━━━━━━━━━━━━━━━━━━━━━
[Follow-up, diet advice, or other notes. Skip if none.]

⚠️ Note: AI analysis — always confirm with your doctor.`;

    // Bangla prompt
    const PROMPT_BN = `আপনি একজন বিশেষজ্ঞ ফার্মাসিস্ট এবং প্রেসক্রিপশন রিডার। এই প্রেসক্রিপশন ইমেজটি ভালোভাবে পড়ুন।

নিয়ম:
1. শুধু সাধারণ বাংলা টেক্সট দিন। JSON বা কোড দিবেন না।
2. ওষুধের নাম পড়তে পারলে আপনার ফার্মাসি জ্ঞান ব্যবহার করে জেনেরিক নাম, কাজ, ধরন ইত্যাদি লিখুন। ওষুধ চেনা গেলে "অস্পষ্ট" লিখবেন না।
3. শুধু যে তথ্য পাওয়া যায় সেটাই দেখান। খালি ফিল্ডে "অস্পষ্ট" বা "দেখা যাচ্ছে না" লিখে দেখাবেন না — সেই ফিল্ডটি বাদ দিন।
4. হাতের লেখা ভালোভাবে পড়ার চেষ্টা করুন। সেবনবিধি (1+0+1), সময়কাল ইত্যাদি সংখ্যা পড়ুন।
5. সহজ ও পরিষ্কার রাখুন।

📋 প্রেসক্রিপশন তথ্য
━━━━━━━━━━━━━━━━━━━━━━━━
👨‍⚕️ ডাক্তার: [নাম]
🏥 বিশেষজ্ঞতা: [যদি থাকে]
🏠 ক্লিনিক: [যদি থাকে]
👤 রোগী: [যদি থাকে]
📅 তারিখ: [যদি থাকে]
🆔 বয়স: [যদি থাকে]
(যেটা পড়া যায় না সেটা বাদ দিন)

🔍 রোগ নির্ণয়
━━━━━━━━━━━━━━━━━━━━━━━━
[ডাক্তার কী রোগ ধরেছেন। না থাকলে এই সেকশন বাদ দিন]

💊 ওষুধের তালিকা
━━━━━━━━━━━━━━━━━━━━━━━━

প্রতিটি ওষুধের জন্য শুধু যে তথ্য আছে সেটাই দেখান:

💊 ওষুধ ১: [নাম] [মাত্রা যদি দেখা যায়]
   🧪 জেনেরিক: [আপনার ফার্মাসি জ্ঞান থেকে]
   💉 ধরন: [ট্যাবলেট/সিরাপ/ক্যাপসুল ইত্যাদি]
   ⏰ সেবনবিধি: [যেমন ১+০+১ = সকাল ও রাত]
   🗓️ সময়কাল: [যেমন ৭ দিন]
   🍽️ কখন: [খাবারের আগে/পরে - শুধু উল্লেখ থাকলে]
   💡 কাজ: [আপনার মেডিকেল জ্ঞান থেকে সংক্ষেপে]

(সব ওষুধের জন্য একইভাবে। তথ্য না থাকলে সেই ফিল্ড বাদ দিন।)

🧪 পরীক্ষা
━━━━━━━━━━━━━━━━━━━━━━━━
[শুধু উল্লেখ থাকলে দেখান, না থাকলে এই সেকশন বাদ দিন]

📝 ডাক্তারের পরামর্শ
━━━━━━━━━━━━━━━━━━━━━━━━
[ফলো-আপ, খাদ্যাভ্যাস, অন্যান্য পরামর্শ। না থাকলে বাদ দিন।]

⚠️ দ্রষ্টব্য: এটি AI বিশ্লেষণ — ওষুধ সেবনের আগে ডাক্তারের সাথে নিশ্চিত করুন।`;

    async function analyzePrescription() {
        // Show loading
        loadingDiv.style.display = 'block';
        resultsDiv.style.display = 'none';
        errorDiv.style.display = 'none';
        analyzeBtn.style.display = 'none';

        // Determine MIME type
        let mimeType = 'image/jpeg';
        if (selectedFile) {
            mimeType = selectedFile.type || 'image/jpeg';
        }

        // Select prompt based on language
        const activePrompt = selectedLang === 'bn' ? PROMPT_BN : PROMPT_EN;

        // Build the request body
        const requestBody = {
            contents: [
                {
                    parts: [
                        { text: activePrompt },
                        {
                            inline_data: {
                                mime_type: mimeType,
                                data: base64Image
                            }
                        }
                    ]
                }
            ],
            generationConfig: {
                temperature: 0.2,
                maxOutputTokens: 4096
            }
        };

        // Try each model until one works (fallback system)
        let lastError = null;

        for (const model of GEMINI_MODELS) {
            try {
                console.log(`Trying model: ${model}...`);
                
                const response = await fetch(getApiUrl(model), {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(requestBody)
                });

                if (!response.ok) {
                    const errorData = await response.json().catch(() => ({}));
                    const errMsg = errorData.error?.message || `Status ${response.status}`;
                    console.warn(`Model ${model} failed: ${errMsg}`);
                    lastError = errMsg;
                    continue; // Try next model
                }

                const data = await response.json();

                // Extract the text response
                if (data.candidates && data.candidates[0] && data.candidates[0].content) {
                    const text = data.candidates[0].content.parts[0].text;
                    console.log(`Success with model: ${model}`);
                    displayResults(text, model);
                    return; // Success! Stop trying other models
                } else {
                    lastError = 'No analysis results received.';
                    continue;
                }

            } catch (error) {
                console.warn(`Model ${model} error:`, error.message);
                lastError = error.message;
                continue; // Try next model
            }
        }

        // All models failed
        showError('All AI models are busy right now. Please try again in a few seconds. Error: ' + (lastError || 'Unknown error'));
    }

    function displayResults(text, modelUsed) {
        loadingDiv.style.display = 'none';
        resultsDiv.style.display = 'block';

        // Check if the response is JSON and convert it to readable text
        let cleanText = text;
        
        // Remove markdown code block wrapper if present
        cleanText = cleanText.replace(/^```json\s*/i, '').replace(/^```\s*/i, '').replace(/\s*```$/i, '');
        
        // Try to detect and parse JSON response
        try {
            const jsonData = JSON.parse(cleanText);
            // If it parsed as JSON, convert to readable format
            cleanText = convertJsonToReadable(jsonData);
        } catch (e) {
            // Not JSON, use as plain text — this is the expected path
        }

        // Convert plain text formatting to beautiful HTML
        let formatted = cleanText
            // Section separator lines ━━━
            .replace(/━+/g, '<hr style="border: none; border-top: 2px solid #e0e8f0; margin: 8px 0;">')
            // Bold text: **text**
            .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
            // Section headers (lines with section emojis like 📋, 🔍, 💊 MEDICINES, 🧪, 📝, ⚠️)
            .replace(/^((?:📋|🔍|🧪|⚠️)\s+.+)$/gm, '<div style="margin: 20px 0 8px; padding: 10px 14px; background: linear-gradient(135deg, #eef5ff, #e8f4f8); border-radius: 8px; border-left: 4px solid #0d6efd; font-size: 16px; font-weight: 700; color: #0d6efd;">$1</div>')
            .replace(/^(💊\s+MEDICINES.*)$/gm, '<div style="margin: 20px 0 8px; padding: 10px 14px; background: linear-gradient(135deg, #f0fff4, #e8f8ee); border-radius: 8px; border-left: 4px solid #20c997; font-size: 16px; font-weight: 700; color: #155724;">$1</div>')
            .replace(/^(📝\s+ADDITIONAL.*)$/gm, '<div style="margin: 20px 0 8px; padding: 10px 14px; background: linear-gradient(135deg, #fff8e1, #fff3cd); border-radius: 8px; border-left: 4px solid #f59e0b; font-size: 16px; font-weight: 700; color: #856404;">$1</div>')
            // Medicine name headers (💊 Medicine 1: Name)
            .replace(/^(💊\s+Medicine\s+\d+:.*)$/gm, '<div style="margin: 16px 0 6px; padding: 10px 14px; background: #f0fff4; border-radius: 8px; border-left: 4px solid #20c997; font-weight: 700; color: #155724; font-size: 15px;">$1</div>')
            // Indented medicine detail lines (3 spaces + emoji)
            .replace(/^\s+(🧪|💉|📏|⏰|🗓️|🍽️|📝|💡)(.*?)$/gm, '<div style="padding: 5px 10px 5px 32px; margin: 2px 0; font-size: 13.5px; color: #444; border-bottom: 1px solid #f0f0f0;">$1$2</div>')
            // Info lines with emojis (👨‍⚕️, 🏥, 🏠, 👤, 📅, etc.)
            .replace(/^((?:👨‍⚕️|🏥|🏠|👤|📅|🆔).*?)$/gm, '<div style="padding: 6px 10px 6px 16px; margin: 2px 0; font-size: 14px; color: #333;">$1</div>')
            // Bullet points: - text
            .replace(/^- (.*?)$/gm, '<div style="padding: 4px 8px 4px 20px; margin: 2px 0;"><span style="color: #0d6efd; margin-right: 8px;">●</span>$1</div>')
            // Line breaks
            .replace(/\n/g, '<br>')
            // Clean up excessive breaks
            .replace(/(<br>){3,}/g, '<br>')
            // Remove <br> right after or before div/hr tags
            .replace(/<br>(<div|<hr)/g, '$1')
            .replace(/(<\/div>)<br>/g, '$1');

        resultContent.innerHTML = formatted;
    }

    // Convert JSON response to human-readable text (fallback)
    function convertJsonToReadable(data) {
        let lines = [];

        // Prescription Overview
        if (data.prescription_overview) {
            const o = data.prescription_overview;
            lines.push('📋 PRESCRIPTION OVERVIEW');
            lines.push('━━━━━━━━━━━━━━━━━━━━━━━━');
            if (o.doctor_name) lines.push('👨‍⚕️ Doctor: ' + o.doctor_name);
            if (o.doctor_designation_specialty) lines.push('🏥 Designation: ' + o.doctor_designation_specialty);
            if (o.clinic_hospital) lines.push('🏠 Clinic: ' + o.clinic_hospital);
            if (o.patient_name) lines.push('👤 Patient: ' + o.patient_name);
            if (o.date) lines.push('📅 Date: ' + o.date);
            if (o.patient_age_gender) lines.push('🆔 Age/Gender: ' + o.patient_age_gender);
            lines.push('');
        }

        // Diagnosis
        if (data.diagnosis_chief_complaint) {
            lines.push('🔍 DIAGNOSIS');
            lines.push('━━━━━━━━━━━━━━━━━━━━━━━━');
            const diag = Array.isArray(data.diagnosis_chief_complaint) ? data.diagnosis_chief_complaint.join(', ') : data.diagnosis_chief_complaint;
            lines.push(diag);
            lines.push('');
        }

        // Medicines
        if (data.medicines_prescribed && data.medicines_prescribed.length > 0) {
            lines.push('💊 MEDICINES PRESCRIBED');
            lines.push('━━━━━━━━━━━━━━━━━━━━━━━━');
            lines.push('');
            data.medicines_prescribed.forEach((med, i) => {
                lines.push('💊 Medicine ' + (i + 1) + ': ' + (med.brand_name || 'Unknown'));
                if (med.generic_name) lines.push('   🧪 Generic: ' + med.generic_name);
                if (med.type) lines.push('   💉 Type: ' + med.type);
                if (med.dosage_strength) lines.push('   📏 Dosage: ' + med.dosage_strength);
                if (med.frequency) lines.push('   ⏰ Frequency: ' + med.frequency);
                if (med.duration) lines.push('   🗓️ Duration: ' + med.duration + (String(med.duration).match(/day/i) ? '' : ' days'));
                if (med.timing) lines.push('   🍽️ Timing: ' + med.timing);
                if (med.special_instructions) lines.push('   📝 Instructions: ' + med.special_instructions);
                if (med.what_this_medicine_does) lines.push('   💡 Purpose: ' + med.what_this_medicine_does);
                lines.push('');
            });
        }

        // Tests
        if (data.tests_recommended) {
            lines.push('🧪 TESTS RECOMMENDED');
            lines.push('━━━━━━━━━━━━━━━━━━━━━━━━');
            const tests = Array.isArray(data.tests_recommended) ? data.tests_recommended : [data.tests_recommended];
            tests.forEach(t => lines.push('- ' + t));
            lines.push('');
        }

        // Additional Instructions
        if (data.additional_instructions) {
            lines.push('📝 ADDITIONAL INSTRUCTIONS');
            lines.push('━━━━━━━━━━━━━━━━━━━━━━━━');
            const inst = Array.isArray(data.additional_instructions) ? data.additional_instructions : [data.additional_instructions];
            inst.forEach(item => lines.push('- ' + item));
            lines.push('');
        }

        // Disclaimer
        lines.push('⚠️ DISCLAIMER');
        lines.push('━━━━━━━━━━━━━━━━━━━━━━━━');
        lines.push('This is an AI-generated analysis. Always verify with your doctor or pharmacist.');

        return lines.join('\n');
    }

})();
