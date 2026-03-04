<?php 
require 'db.php';
include 'header.php'; 

// Determine Language
$lang = isset($_GET['lang']) && $_GET['lang'] == 'en' ? 'en' : 'bn';

// Fetch Manifesto Items
$stmt = $pdo->query("SELECT * FROM manifesto ORDER BY created_at DESC");
$items = $stmt->fetchAll();
?>

<!-- Page Header -->
<div class="bg-gray-100 py-16 text-center text-black">
    <h1 class="text-4xl md:text-5xl font-black">
        <?php echo $lang == 'bn' ? 'বিএনপির ১৮ দফা রূপকল্প' : 'BNP 18-Point Manifesto'; ?>
    </h1>

</div>

<!-- Manifesto Grid -->
<section class="py-16 px-4 bg-gray-100 min-h-screen">
    <div class="container mx-auto">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php foreach($items as $item): ?>
                <div class="bg-white p-6 rounded-lg shadow-lg border-t-4 border-yellow-400 flex flex-col hover:shadow-2xl transition-shadow duration-300">
                    <!-- Cover Image -->
                    <div class="overflow-hidden rounded-md mb-4">
    <img src="<?php echo htmlspecialchars($item['image_url']); ?>" 
         alt="<?php echo htmlspecialchars($item['title_' . $lang]); ?>" 
         class="w-full h-auto object-top"
         onerror="this.src='https://placehold.co/400x500/eee/333?text=Manifesto'">
</div>

                    <!-- Title -->
                    <h2 class="text-2xl font-bold text-[#00523A]">
                        <?php echo htmlspecialchars($item['title_' . $lang]); ?>
                    </h2>

                    <!-- Description -->
                    <p class="mt-3 text-gray-600 flex-grow">
                        <?php echo htmlspecialchars($item['description_' . $lang]); ?>
                    </p>

                    <!-- View Button -->
                    <div class="mt-6 text-center">
                        <a href="view_pdf.php?file=<?php echo urlencode($item['pdf_file']); ?>&lang=<?php echo $lang; ?>" 
                           class="inline-block bg-[#00523A] text-white font-bold py-3 px-8 rounded-lg hover:bg-green-800 transition-colors duration-200">
                            <?php echo $lang == 'bn' ? 'আরও দেখুন' : 'Read Full PDF'; ?>
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <?php if(count($items) == 0): ?>
            <div class="text-center py-20 text-gray-500">
                <h3 class="text-xl"><?php echo $lang == 'bn' ? 'কোনো তথ্য পাওয়া যায়নি।' : 'No manifestos found.'; ?></h3>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Sync Language Toggle -->
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const currentLang = "<?php echo $lang; ?>";
        const toggleBtn = document.getElementById('lang-toggle');
        const mobToggle = document.getElementById('mobile-lang-toggle');
        
        const updateLink = (btn) => {
            if(btn) {
                btn.innerText = currentLang === 'bn' ? 'English' : 'বাংলা';
                btn.onclick = (e) => {
                    e.preventDefault();
                    const newLang = currentLang === 'bn' ? 'en' : 'bn';
                    window.location.search = '?lang=' + newLang;
                };
            }
        };

        updateLink(toggleBtn);
        updateLink(mobToggle);
    });
</script>

<?php include 'footer.php'; ?>