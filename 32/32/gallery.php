<?php 
require 'db.php';
include 'header.php'; 

// Determine Language (Default bn)
$lang = isset($_GET['lang']) && $_GET['lang'] == 'en' ? 'en' : 'bn';

// Fetch All Images
$stmt = $pdo->query("SELECT * FROM gallery ORDER BY created_at DESC");
$images = $stmt->fetchAll();
?>

<!-- Page Header -->
<div class="bg-[#00523A] py-16 text-center text-white">
    <h1 class="text-4xl md:text-5xl font-black">
        <?php echo $lang == 'bn' ? 'ছবির গ্যালারি' : 'Photo Gallery'; ?>
    </h1>
    <p class="mt-4 text-yellow-300 font-bold tracking-widest">
        <?php echo $lang == 'bn' ? 'প্রচারণার মুহূর্ত' : 'CAMPAIGN MOMENTS'; ?>
    </p>
</div>

<!-- Gallery Grid -->
<section class="py-16 px-4 bg-white min-h-screen">
    <div class="container mx-auto">
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            <?php foreach($images as $img): ?>
                <div class="group relative overflow-hidden rounded-lg shadow-lg cursor-pointer">
                    <img src="<?php echo htmlspecialchars($img['src']); ?>" 
                         alt="<?php echo htmlspecialchars($img['alt_' . $lang]); ?>" 
                         class="w-full h-64 object-cover transform transition-transform duration-500 group-hover:scale-110"
                         onerror="this.src='https://placehold.co/600x400/ccc/333?text=Image'">
                    
                    <!-- Overlay Caption -->
                    <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-60 transition-all duration-300 flex items-end justify-start p-4">
                        <p class="text-white font-bold translate-y-4 opacity-0 group-hover:translate-y-0 group-hover:opacity-100 transition-all duration-300">
                            <?php echo htmlspecialchars($img['alt_' . $lang]); ?>
                        </p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <?php if(count($images) == 0): ?>
            <p class="text-center text-gray-500 py-10">
                <?php echo $lang == 'bn' ? 'কোনো ছবি পাওয়া যায়নি।' : 'No images found.'; ?>
            </p>
        <?php endif; ?>
    </div>
</section>

<!-- Custom Script to sync with Header Language Toggle -->
<script>
    // We override the default toggle behavior for this PHP page
    document.addEventListener('DOMContentLoaded', () => {
        const currentLang = "<?php echo $lang; ?>";
        
        // Update the header toggle button text
        const toggleBtn = document.getElementById('lang-toggle');
        if(toggleBtn) {
            toggleBtn.innerText = currentLang === 'bn' ? 'English' : 'বাংলা';
            
            toggleBtn.onclick = function(e) {
                e.preventDefault();
                const newLang = currentLang === 'bn' ? 'en' : 'bn';
                window.location.search = '?lang=' + newLang;
            };
        }

        // Also update the mobile toggle
        const mobToggle = document.getElementById('mobile-lang-toggle');
        if(mobToggle) {
            mobToggle.innerText = currentLang === 'bn' ? 'English' : 'বাংলা';
            mobToggle.onclick = function(e) {
                e.preventDefault();
                const newLang = currentLang === 'bn' ? 'en' : 'bn';
                window.location.search = '?lang=' + newLang;
            };
        }
    });
</script>

<?php include 'footer.php'; ?>