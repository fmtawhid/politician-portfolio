<?php 
require 'db.php';
include 'header.php'; 

// Determine Language
$lang = isset($_GET['lang']) && $_GET['lang'] == 'en' ? 'en' : 'bn';

// Fetch All Press Items
$stmt = $pdo->query("SELECT * FROM press ORDER BY publish_date DESC");
$articles = $stmt->fetchAll();
?>

<!-- Page Header -->
<div class="bg-[#00523A] py-16 text-center text-white">
    <h1 class="text-4xl md:text-5xl font-black">
        <?php echo $lang == 'bn' ? 'খবরের শিরোনামে' : 'In The Headlines'; ?>
    </h1>
    <p class="mt-4 text-yellow-300 font-bold tracking-widest">
        <?php echo $lang == 'bn' ? 'প্রেস ও মিডিয়া' : 'PRESS & MEDIA'; ?>
    </p>
</div>

<!-- Press Grid -->
<section class="py-16 px-4 bg-gray-50 min-h-screen">
    <div class="container mx-auto">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <?php foreach($articles as $item): ?>
                <a href="<?php echo htmlspecialchars($item['url']); ?>" target="_blank" rel="noopener noreferrer" class="bg-white rounded-lg shadow-lg overflow-hidden flex flex-col group transition-transform duration-300 hover:scale-[1.02]">
                    
                    <div class="relative h-56 overflow-hidden">
                        <img src="<?php echo htmlspecialchars($item['image']); ?>" 
                             alt="<?php echo htmlspecialchars($item['title_' . $lang]); ?>" 
                             class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110"
                             onerror="this.src='https://placehold.co/600x400/eee/333?text=News'">
                        <div class="absolute top-0 right-0 bg-yellow-400 text-[#00523A] font-bold px-3 py-1 rounded-bl-lg text-sm">
                            <?php echo htmlspecialchars($item['source_' . $lang]); ?>
                        </div>
                    </div>

                    <div class="p-6 flex flex-col flex-grow">
                        <p class="text-sm text-gray-500 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            <?php echo date('d M, Y', strtotime($item['publish_date'])); ?>
                        </p>
                        <h3 class="text-xl font-bold text-[#00523A] mt-3 mb-4 group-hover:text-green-700 transition-colors">
                            <?php echo htmlspecialchars($item['title_' . $lang]); ?>
                        </h3>
                        <p class="mt-auto text-red-600 font-bold group-hover:translate-x-2 transition-transform duration-300 inline-flex items-center">
                            <?php echo $lang == 'bn' ? 'বিস্তারিত পড়ুন' : 'Read More'; ?> &rarr;
                        </p>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
        
        <?php if(count($articles) == 0): ?>
            <p class="text-center text-gray-500 py-10">
                <?php echo $lang == 'bn' ? 'কোনো সংবাদ পাওয়া যায়নি।' : 'No articles found.'; ?>
            </p>
        <?php endif; ?>
    </div>
</section>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const currentLang = "<?php echo $lang; ?>";
        
        const toggleBtn = document.getElementById('lang-toggle');
        if(toggleBtn) {
            toggleBtn.innerText = currentLang === 'bn' ? 'English' : 'বাংলা';
            toggleBtn.onclick = function(e) {
                e.preventDefault();
                const newLang = currentLang === 'bn' ? 'en' : 'bn';
                window.location.search = '?lang=' + newLang;
            };
        }
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