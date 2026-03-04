
const ADMIN_URL = 'https://bnp24.tv/admin';
let currentLang = 'bn';

document.addEventListener('DOMContentLoaded', () => {
    // 1. Initialize UI based on default or local storage
    updateLanguageUI();

    // 2. Mobile Menu Logic
    const mobileBtn = document.getElementById('mobile-menu-btn');
    const mobileMenu = document.getElementById('mobile-menu');
    if(mobileBtn){
        mobileBtn.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
        });
    }
    
    // 3. Popup Logic
    const popup = document.getElementById('popup-modal');
    const closeBtn = document.getElementById('close-popup');
    if (popup) {
        setTimeout(() => popup.classList.remove('hidden'), 500);
        closeBtn.addEventListener('click', () => popup.classList.add('hidden'));
        popup.addEventListener('click', (e) => {
            if(e.target === popup) popup.classList.add('hidden');
        });
    }

    // 4. Language Toggle Listeners
    const toggleBtn = document.getElementById('lang-toggle');
    if(toggleBtn) toggleBtn.addEventListener('click', toggleLanguage);
    
    const mobLang = document.getElementById('mobile-lang-toggle');
    if(mobLang) mobLang.addEventListener('click', toggleLanguage);


        setupComplaintForm();

});
function setupComplaintForm() {
    const form = document.getElementById('complaint-form');
    const successOverlay = document.getElementById('form-success');
    const submitBtn = document.getElementById('submit-btn');
    const btnText = document.getElementById('btn-text');
    const spinner = document.getElementById('btn-spinner');
    const retryBtn = document.getElementById('retry-btn');

    if (!form) return;

    // Handle Submit
    form.addEventListener('submit', async function(e) {
        e.preventDefault(); // STOP PAGE REFRESH

        // UI Loading State
        submitBtn.disabled = true;
        submitBtn.classList.add('opacity-75', 'cursor-not-allowed');
        btnText.classList.add('hidden');
        spinner.classList.remove('hidden');

        const formData = new FormData(form);

        try {
            const response = await fetch('submit_complaint.php', {
                method: 'POST',
                body: formData
            });
            
            // Parse JSON safely
            const text = await response.text();
            let data;
            try {
                data = JSON.parse(text);
            } catch (err) {
                throw new Error("Invalid Server Response");
            }

            if (data.status === 'success') {
                successOverlay.classList.remove('hidden'); // Show Success Message
                form.reset();
            } else {
                alert('Error: ' + data.message);
            }

        } catch (error) {
            console.error('Error:', error);
            alert('Something went wrong. Please try again.');
        } finally {
            // Reset UI State
            submitBtn.disabled = false;
            submitBtn.classList.remove('opacity-75', 'cursor-not-allowed');
            btnText.classList.remove('hidden');
            spinner.classList.add('hidden');
        }
    });

    // Handle Retry Button
    if(retryBtn) {
        retryBtn.addEventListener('click', () => {
            successOverlay.classList.add('hidden');
        });
    }
}


function toggleLanguage() {
    currentLang = currentLang === 'bn' ? 'en' : 'bn';
    
    // Update Toggle Button Text
    const btnText = currentLang === 'bn' ? 'English' : 'বাংলা';
    const toggleBtn = document.getElementById('lang-toggle');
    const mobToggle = document.getElementById('mobile-lang-toggle');
    
    if(toggleBtn) toggleBtn.innerText = btnText;
    if(mobToggle) mobToggle.innerText = btnText;

    updateLanguageUI();
}

function updateLanguageUI() {
    const data = contentData[currentLang];

    // 1. Toggle Fonts
    if (currentLang === 'en') {
        document.body.classList.add('font-english');
    } else {
        document.body.classList.remove('font-english');
    }

    // 2. Update Text & Placeholders via data-key
    document.querySelectorAll('[data-key]').forEach(el => {
        const key = el.getAttribute('data-key');
        const keys = key.split('.');
        let val = data;
        keys.forEach(k => { if(val) val = val[k]; });
        
        if(val) {
            // If it's an input or textarea, update placeholder
            if (el.tagName === 'INPUT' || el.tagName === 'TEXTAREA') {
                el.setAttribute('placeholder', val);
            } else {
                // Otherwise update text
                el.innerText = val;
            }
        }
    });

    // 3. Handle specific visibility toggles (for buttons/links like 'bn-text')
    // ... (Keep your existing logic for bn-text/en-text/db-content here) ...
    if (currentLang === 'en') {
        document.querySelectorAll('.bn-text').forEach(el => el.classList.add('hidden'));
        document.querySelectorAll('.en-text').forEach(el => el.classList.remove('hidden'));
    } else {
        document.querySelectorAll('.bn-text').forEach(el => el.classList.remove('hidden'));
        document.querySelectorAll('.en-text').forEach(el => el.classList.add('hidden'));
    }
    
    document.querySelectorAll('.db-content').forEach(el => {
        const text = el.getAttribute('data-' + currentLang);
        if(text) el.innerText = text;
    });
    
    // ... Platform points logic ...
    const platformContainer = document.getElementById('platform-points-container');
    if(platformContainer && data.platform.points) {
        platformContainer.innerHTML = data.platform.points.map(p => `
            <div class="flex gap-4">
                <div class="flex-shrink-0 w-8 h-8 rounded-full bg-[#00523A] mt-1"></div>
                <div>
                    <h3 class="text-2xl font-bold text-[#00523A]">${p.title}</h3>
                    <p class="mt-2 text-gray-700 leading-relaxed">${p.body}</p>
                </div>
            </div>
        `).join('');
    }
}
 let resizeTimer;
    window.addEventListener('resize', () => {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(() => {
            if (window.FB) {
                window.FB.XFBML.parse(); // Re-parses the plugin to fit new width
            }
        }, 250);
    });