<?php 
require 'db.php';
include 'header.php'; 

// 1. FETCH RECENT DATA FROM DATABASE
try {

    $stmtPopup = $pdo->query("SELECT * FROM popups WHERE is_active = 1 ORDER BY id DESC LIMIT 1");
    $activePopup = $stmtPopup->fetch();
    // Recent 3 Press Articles
    $stmtPress = $pdo->query("SELECT * FROM press ORDER BY publish_date DESC LIMIT 3");
    $recentPress = $stmtPress->fetchAll();

    // Recent 6 Gallery Images
    $stmtGallery = $pdo->query("SELECT * FROM gallery ORDER BY created_at DESC LIMIT 6");
    $recentGallery = $stmtGallery->fetchAll();

    // Recent 3 Endorsements
    $stmtEndorse = $pdo->query("SELECT * FROM endorsements ORDER BY created_at DESC LIMIT 3");
    $recentEndorsements = $stmtEndorse->fetchAll();

} catch (PDOException $e) {
    // Handle error silently or log it
    $activePopup = null; // Default to null on error
    $recentPress = [];
    $recentGallery = [];
    $recentEndorsements = [];
}


?>

<!-- Pass PHP Data to JavaScript for Language Toggling -->
<script>
    window.dbData = {
        press: <?php echo json_encode($recentPress); ?>,
        gallery: <?php echo json_encode($recentGallery); ?>,
        endorsements: <?php echo json_encode($recentEndorsements); ?>
    };
</script>

<!-- Dynamic Popup Modal -->
<?php if ($activePopup): ?>
<div id="popup-modal" class="fixed inset-0 z-[100] flex items-center justify-center bg-black bg-opacity-75 p-4 hidden">
    <div class="relative max-w-xs sm:max-w-lg w-full max-h-[95vh] flex justify-center items-center p-2" onclick="event.stopPropagation()">
        
        <img src="<?php echo htmlspecialchars($activePopup['image_url']); ?>" 
             alt="Campaign Announcement" 
             class="w-full h-auto object-contain rounded-lg shadow-xl max-h-[90vh]" 
             onerror="this.style.display='none'">
             
        <button id="close-popup" class="absolute -top-3 -right-3 bg-red-600 rounded-full p-2 text-white shadow-lg hover:bg-red-700 z-10 transition-transform hover:scale-110">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>
    </div>
</div>
<?php endif; ?>

<!-- Banner Section -->
<section class="bg-[#D81E05] text-white text-center py-10 px-4">
    <div class="container mx-auto">
        <!-- Heading -->
        <h2 class="text-4xl md:text-5xl font-black uppercase tracking-wider leading-tight" data-key="top_banner.heading">
            পরিবর্তনের জন্য ভোট দিন
        </h2>
        
        <!-- Subheading -->
        <p class="mt-3 text-lg md:text-xl font-medium opacity-90 max-w-2xl mx-auto" data-key="top_banner.subheading">
            আপনার ভোট মূল্যবান। আসুন একসাথে একটি উন্নত পাবনা গড়ি।
        </p>
        
        <!-- Buttons -->
        <div class="mt-8 flex flex-col sm:flex-row items-center justify-center gap-4">
            
            <!-- Button 1: Pollsite -->
            <a href="https://www.ecs.gov.bd/category/parliament-election-vote-centers" target="_blank" rel="noopener noreferrer" 
               class="bg-yellow-400 text-[#D81E05] font-bold py-3 px-8 rounded-lg w-full sm:w-auto hover:bg-yellow-300 transition-colors duration-200 shadow-md uppercase tracking-wide" 
               data-key="top_banner.cta1">
               আপনার ভোট কেন্দ্র খুঁজুন
            </a>

            <!-- Button 2: Legacy (The missing one) -->
            <a href="https://www.bnpbd.org/founding-historic?language=bn" target="_blank" rel="noopener noreferrer" 
               class="bg-yellow-400 text-[#D81E05] font-bold py-3 px-8 rounded-lg w-full sm:w-auto hover:bg-yellow-300 transition-colors duration-200 shadow-md uppercase tracking-wide" 
               data-key="top_banner.cta2">
               গৌরবময় ঐতিহ্য সম্পর্কে জানুন
            </a>

        </div>
    </div>
</section>
<!-- HERO SECTION -->
<section class="bg-[#00523A] text-white py-16 md:py-24 px-4 overflow-hidden">
    <div class="container mx-auto grid md:grid-cols-2 items-center gap-12 lg:gap-20">
        
        <!-- Left Side: Text Content -->
        <div class="order-2 md:order-1 text-center md:text-left">
            <!-- Big Title -->
            <h1 class="text-6xl md:text-8xl lg:text-[9rem] font-black leading-none tracking-tighter text-white mb-2" 
                data-key="hero.title">
                ডাঃ আহমেদ মোস্তফা নোমান
            </h1>
            
            <!-- Subtitle (Yellow) -->
            <h2 class="text-2xl md:text-3xl lg:text-4xl text-yellow-400 font-bold uppercase tracking-widest" 
                data-key="hero.subtitle">
                পাবনার মানুষের জন্য
            </h2>
            
            <!-- Intro Paragraph -->
            <p class="text-lg md:text-xl lg:text-2xl max-w-xl mx-auto md:mx-0 leading-relaxed mt-8 font-medium text-gray-100" 
               data-key="hero.intro">
                ডাঃ আহমেদ মোস্তফা নোমান মেয়র পদে লড়ছেন জীবনযাত্রার মান উন্নয়ন করতে এবং সবার জন্য সুযোগ তৈরি করতে।
            </p>
        </div> 

        <!-- Right Side: Image (Arch Shape) -->
        <div class="order-1 md:order-2 flex justify-center items-end">
            <div class="relative w-full max-w-md">
               
                <img src="public/aminul3.webp" 
                     alt="Dr. Ahmed Mostofa Noman Hero" 
                     class="w-full h-auto object-contain transform hover:scale-105 transition-transform duration-500" 
                     onerror="this.src='https://placehold.co/600x600/D81E05/FFF?text=Upload+Hero+Image'">
            </div>
        </div>

    </div>
</section>

<!-- ABOUT / MEET Noman SECTION -->
<section id="about" class="py-20 px-4 bg-white overflow-hidden">
    <div class="container mx-auto grid md:grid-cols-2 gap-x-16 gap-y-12 items-center">
        
        <!-- Left Side: Text Content -->
        <div class="order-2 md:order-1">
            <h2 class="text-5xl md:text-6xl font-black text-[#00523A] mb-8" data-key="meet.heading">
                নোমানের সাথে পরিচিত হন
            </h2>
            
            <div class="border-l-8 border-yellow-400 pl-6 sm:pl-8">
                <p class="text-lg md:text-xl leading-relaxed text-gray-700 text-justify bn-text" data-key="meet.body">
                    ডাঃ আহমেদ মোস্তফা নোমান পাবনা জেলার একজন সুপরিচিত রাজনৈতিক ও সামাজিক ব্যক্তিত্ব, যিনি দীর্ঘদিন ধরে জনগণের কল্যাণে সক্রিয় ভূমিকা পালন করে আসছেন। তিনি পাবনা জেলা বিএনপির সদস্য এবং সাবেক সদস্য সচিব, পাবনা পৌর বিএনপি। পাশাপাশি তিনি ডক্টরস এসোসিয়েশন অব বাংলাদেশ (ড্যাব), পাবনা শাখার সাধারণ সম্পাদক হিসেবে দায়িত্ব পালন করছেন। একজন চিকিৎসক ও জননেতা হিসেবে তিনি মানবিকতা, সততা এবং উন্নয়নমূলক ভাবনার জন্য পরিচিত। তরুণ প্রজন্মের কাছে তিনি অনুপ্রেরণার প্রতীক এবং পাবনা পৌরসভার সম্ভাব্য মেয়র হিসেবে অনেকের আস্থার কেন্দ্রবিন্দু। গণতন্ত্র, নাগরিক অধিকার এবং স্থানীয় উন্নয়ন নিয়ে তার সক্রিয় অবস্থান তাকে পাবনাবাসীর কাছে একটি গ্রহণযোগ্য ও আশাব্যঞ্জক নেতৃত্বে পরিণত করেছে।
                </p>
                <p class="text-lg md:text-xl leading-relaxed text-gray-700 text-justify en-text hidden" data-key="meet.body">
                    Dr. Ahmed Mostofa Noman is a well-known political and social figure in Pabna, actively engaged in public service and community development. He is a Member of Pabna District BNP and formerly served as the Member Secretary of Pabna Poura BNP. In addition, he holds the position of General Secretary of the Doctors Association of Bangladesh (DAB), Pabna branch. As a physician and public representative, he is recognized for his integrity, humanitarian values, and commitment to progress. Among the youth, he is regarded as an inspiring leader and a trusted potential candidate for Mayor of Pabna Municipality. His active stance on democracy, civic rights, and local development has made him a respected and promising figure among the people of Pabna.
                </p>
            </div>

            <div class="mt-8 pl-8">
                <p class="font-bold text-[#00523A] text-lg bn-text" data-key="meet.quote_attribution">
                    — ডাঃ আহমেদ মোস্তফা নোমান
                </p>
                <p class="font-bold text-[#00523A] text-lg en-text hidden" data-key="meet.quote_attribution">
                    — Dr. Ahmed Mostofa Noman
                </p>
            </div>
        </div>

        <!-- Right Side: Image -->
        <div class="order-1 md:order-2 flex justify-center">
           
            <div class="relative rounded-2xl shadow-2xl overflow-hidden border-4 border-white ring-1 ring-gray-200 transform rotate-2 hover:rotate-0 transition-transform duration-500">
                <img src="public/aminul2.webp" 
                     alt="Dr. Ahmed Mostofa Noman Profile" 
                     class="max-w-md w-full h-auto object-cover"
                     onerror="this.src='https://placehold.co/500x600/00523A/FFF?text=Noman+Photo'">
            </div>
        </div>

    </div>
</section>
<!-- Platform Section -->
<section id="platform" class="py-20 px-4 bg-gray-100">
    <div class="container mx-auto flex flex-col md:flex-row gap-12">
        <div class="md:w-1/3">
            <div class="sticky top-24 border-l-8 border-yellow-400 pl-6">
                <p class="font-bold text-red-600 uppercase" data-key="platform.tag">কর্মসূচি</p>
                <h2 class="text-4xl lg:text-5xl font-black text-[#00523A] mt-2" data-key="platform.heading">...</h2>
            </div>
        </div>
        <div class="md:w-2/3 space-y-12" id="platform-points-container">
            <!-- Points injected via JS from content.js -->
        </div>
    </div>
</section>

<!-- VOLUNTEER / GET INVOLVED SECTION -->
<section id="volunteer" class="py-20 px-4">
    <div class="container mx-auto max-w-7xl">
        
        <!-- Grid Layout with Gap (Separated Cards) -->
        <div class="grid md:grid-cols-2 gap-6 lg:gap-10 items-stretch">
            
            <!-- Left Side: Yellow Content Box -->
            <div class="bg-yellow-400 p-8 md:p-12 rounded-lg text-gray-900 flex flex-col justify-center">
                <!-- Red Tag -->
                <p class="font-bold uppercase text-[#D81E05] tracking-widest text-sm mb-2" data-key="get_involved.tag">
                    প্রচারণায় যোগ দিন
                </p>
                
                <!-- Heading -->
                <h2 class="text-4xl md:text-5xl lg:text-6xl font-black mb-6 leading-tight text-gray-900" data-key="get_involved.heading">
                    ভোটদানে উৎসাহিত করুন!
                </h2>
                
                <!-- Body Text -->
                <p class="text-lg leading-relaxed font-medium mb-8" data-key="get_involved.body">
                    আমরা প্রচারণার শেষ প্রান্তে...
                </p>
                
                <!-- Badges (Visual Only, Not Buttons) -->
                <div class="flex flex-col sm:flex-row gap-4">
                    <!-- Red Badge -->
                    <div class="bg-[#D81E05] text-white font-bold py-4 px-8 rounded-md text-center shadow-sm select-none cursor-default">
                        <span data-key="get_involved.badge1">দরজায় কড়া নাড়ুন</span>
                    </div>
                    
                    <!-- Green Badge -->
                    <div class="bg-[#00523A] text-white font-bold py-4 px-8 rounded-md text-center shadow-sm select-none cursor-default">
                        <span data-key="get_involved.badge2">ফোন করুন</span>
                    </div>
                </div>
            </div>

            <!-- Right Side: Image -->
            <div class="h-full min-h-[400px]">
                <!-- 
                    Make sure 'Noman_rally.jpg' is in your uploads folder.
                    rounded-lg matches the yellow box.
                -->
                <img src="public/aminul4.webp" 
                     alt="Campaign Rally" 
                     class="w-full h-full object-cover rounded-lg shadow-md" 
                     onerror="this.src='https://placehold.co/800x600/555/FFF?text=Rally+Image'">
            </div>

        </div>
    </div>
</section>

<!-- DYNAMIC ENDORSEMENTS SECTION -->
<section id="endorsements" class="py-20 px-4">
    <div class="container mx-auto">
        <div class="text-center max-w-3xl mx-auto">
            <p class="font-bold text-red-600 uppercase" data-key="endorsements.tag">সমর্থন</p>
            <h2 class="text-4xl lg:text-5xl font-black text-[#00523A] mt-2" data-key="endorsements.heading">বিশিষ্ট ব্যক্তিবর্গের সমর্থন</h2>
        </div>
        <div id="endorsements-grid" class="mt-16 grid md:grid-cols-3 gap-8">
            <?php foreach($recentEndorsements as $item): ?>
            <div class="bg-white p-8 rounded-lg shadow-lg border-l-8 border-yellow-400 flex flex-col items-start">
                <img src="<?php echo htmlspecialchars($item['image']); ?>" class="w-20 h-20 rounded-full object-cover mb-4 ring-2 ring-yellow-400" onerror="this.src='https://placehold.co/80x80'">
                <!-- Note: We load Bengali by default in PHP -->
                <p class="text-lg text-gray-700 leading-relaxed italic flex-grow db-content" 
                   data-bn="<?php echo htmlspecialchars($item['quote_bn']); ?>" 
                   data-en="<?php echo htmlspecialchars($item['quote_en']); ?>">
                   "<?php echo htmlspecialchars($item['quote_bn']); ?>"
                </p>
                <p class="mt-4 text-xl font-bold text-[#00523A] w-full text-right db-content"
                   data-bn="— <?php echo htmlspecialchars($item['name_bn']); ?>" 
                   data-en="— <?php echo htmlspecialchars($item['name_en']); ?>">
                   — <?php echo htmlspecialchars($item['name_bn']); ?>
                </p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- DYNAMIC PRESS SECTION -->
<section id="press" class="py-20 px-4 bg-gray-100">
    <div class="container mx-auto">
        <div class="text-center max-w-3xl mx-auto">
            <p class="font-bold text-red-600 uppercase" data-key="press.tag">প্রেস</p>
            <h2 class="text-4xl lg:text-5xl font-black text-[#00523A] mt-2" data-key="press.heading">খবরের শিরোনামে</h2>
        </div>
        <div id="press-grid" class="mt-16 grid grid-cols-1 md:grid-cols-3 gap-8">
            <?php foreach($recentPress as $item): ?>
            <a href="<?php echo htmlspecialchars($item['url']); ?>" target="_blank" class="bg-white rounded-lg shadow-lg overflow-hidden flex flex-col group transition hover:scale-[1.03]">
                <div class="relative h-48">
                    <img src="<?php echo htmlspecialchars($item['image']); ?>" class="w-full h-full object-cover" onerror="this.src='https://placehold.co/600x400'">
                    <div class="absolute top-0 right-0 bg-yellow-400 text-[#00523A] font-bold px-3 py-1 rounded-bl-lg text-sm db-content"
                         data-bn="<?php echo htmlspecialchars($item['source_bn']); ?>"
                         data-en="<?php echo htmlspecialchars($item['source_en']); ?>">
                        <?php echo htmlspecialchars($item['source_bn']); ?>
                    </div>
                </div>
                <div class="p-6 flex flex-col flex-grow">
                    <p class="text-sm text-gray-500"><?php echo date('d M, Y', strtotime($item['publish_date'])); ?></p>
                    <h3 class="text-xl font-bold text-[#00523A] mt-2 flex-grow db-content"
                        data-bn="<?php echo htmlspecialchars($item['title_bn']); ?>"
                        data-en="<?php echo htmlspecialchars($item['title_en']); ?>">
                        <?php echo htmlspecialchars($item['title_bn']); ?>
                    </h3>
                    <p class="mt-4 text-red-600 font-bold group-hover:text-red-700">
                        <span class="bn-text">বিস্তারিত পড়ুন &rarr;</span>
                        <span class="en-text hidden">Read More &rarr;</span>
                    </p>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
        <!-- View All Button -->
        <div class="text-center mt-12">
            <a href="press.php" class="inline-block border-2 border-[#00523A] text-[#00523A] font-bold py-3 px-8 rounded-lg hover:bg-[#00523A] hover:text-white transition-colors duration-300">
                <span class="bn-text">সকল সংবাদ দেখুন</span>
                <span class="en-text hidden">View All News</span>
            </a>
        </div>
    </div>
</section>

<!-- DYNAMIC GALLERY SECTION -->
<section id="gallery" class="py-20 px-4">
    <div class="container mx-auto">
        <div class="text-center max-w-3xl mx-auto">
            <p class="font-bold text-red-600 uppercase" data-key="gallery.tag">গ্যালারি</p>
            <h2 class="text-4xl lg:text-5xl font-black text-[#00523A] mt-2" data-key="gallery.heading">প্রচারণার মুহূর্ত</h2>
        </div>
        <div id="gallery-grid" class="mt-16 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
            <?php foreach($recentGallery as $item): ?>
            <div class="relative group overflow-hidden rounded-lg shadow-lg">
                <img src="<?php echo htmlspecialchars($item['src']); ?>" class="w-full h-64 object-cover transform transition hover:scale-105" onerror="this.src='https://placehold.co/600x400'">
                <!-- Caption visible on hover -->
                <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-60 transition-all flex items-end p-4">
                    <p class="text-white font-bold opacity-0 group-hover:opacity-100 translate-y-4 group-hover:translate-y-0 transition-all db-content"
                       data-bn="<?php echo htmlspecialchars($item['alt_bn']); ?>"
                       data-en="<?php echo htmlspecialchars($item['alt_en']); ?>">
                       <?php echo htmlspecialchars($item['alt_bn']); ?>
                    </p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <!-- View All Button -->
        <div class="text-center mt-12">
            <a href="gallery.php" class="inline-block bg-yellow-400 text-[#00523A] font-bold py-3 px-8 rounded-lg hover:bg-yellow-300 transition-colors duration-300">
                 <span class="bn-text">আরও ছবি দেখুন</span>
                 <span class="en-text hidden">View More Photos</span>
            </a>
        </div>
    </div>
</section>

<!-- SOCIAL MEDIA SECTION -->
<section id="blog" class="py-20 px-4 bg-gray-100 overflow-hidden">
    <div class="container mx-auto">
        <!-- Two Column Layout -->
        <div class="flex flex-col lg:flex-row items-center justify-center gap-12 lg:gap-20 max-w-6xl mx-auto">
            
            <!-- Left Side: Call to Action Content -->
            <div class="lg:w-1/2 text-center lg:text-left space-y-6 order-1">
                <!-- Tag -->
                <div class="inline-block bg-blue-600 text-white text-xs font-bold px-3 py-1 rounded-full uppercase tracking-widest mb-2">
                    <span data-key="blog.tag">ফেইসবুক লাইভ</span>
                </div>
                
                <!-- Heading -->
                <h2 class="text-4xl lg:text-6xl font-black text-[#00523A] leading-tight">
                    <span data-key="blog.heading_main">ফেইসবুক থেকে</span> <br>
                    <span class="text-blue-600" data-key="blog.heading_highlight">সরাসরি</span>
                </h2>
                
                <!-- Description -->
                <p class="text-lg text-gray-600 leading-relaxed" data-key="blog.description">
                    আমাদের সকল আপডেট...
                </p>

                <!-- Big CTA Button -->
                <div class="pt-4">
                    <a href="https://www.facebook.com/NomanBd07" target="_blank" class="inline-flex items-center gap-3 bg-blue-600 text-white font-bold py-4 px-8 rounded-lg hover:bg-blue-700 transition-all transform hover:scale-105 shadow-lg">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                        <span data-key="blog.cta_btn">ফেইসবুকে যুক্ত হোন</span>
                    </a>
                </div>
            </div>

            <!-- Right Side: The Facebook Feed -->
            <div class="lg:w-1/2 w-full flex justify-center lg:justify-end order-2">
                <div class="bg-white p-2 rounded-xl shadow-2xl border border-gray-200 w-[340px] md:w-[500px]">
                    <div id="fb-root"></div>
                    <div class="fb-page" 
                         data-href="https://www.facebook.com/NomanBd07" 
                         data-tabs="timeline" 
                         data-width="500" 
                         data-height="600" 
                         data-small-header="false" 
                         data-adapt-container-width="true" 
                         data-hide-cover="false" 
                         data-show-facepile="true">
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</section>
<!-- COMPLAINT BOX SECTION -->
<section id="complaint-box" class="relative py-24 px-4 overflow-hidden bg-gradient-to-br from-[#00523A] to-[#003d2b]">
    
    <!-- Background Decor -->
    <div class="absolute top-0 left-0 w-64 h-64 bg-white opacity-5 rounded-full -translate-x-1/2 -translate-y-1/2 blur-3xl"></div>
    <div class="absolute bottom-0 right-0 w-96 h-96 bg-yellow-400 opacity-5 rounded-full translate-x-1/3 translate-y-1/3 blur-3xl"></div>

    <div class="container mx-auto relative z-10">
        <div class="flex flex-col lg:flex-row items-center gap-12 lg:gap-20">
            
            <!-- Text Side -->
            <div class="lg:w-1/2 text-white space-y-6 text-center lg:text-left">
                <div class="inline-block bg-red-600 text-white text-xs font-bold px-3 py-1 rounded-full uppercase tracking-widest mb-2">
                    <span data-key="complaint.tag">জনগণের কণ্ঠস্বর</span>
                </div>
                <h2 class="text-4xl lg:text-6xl font-black leading-tight">
                    <span class="block" data-key="complaint.heading_main">আপনার সমস্যা</span>
                    <span class="text-yellow-400" data-key="complaint.heading_highlight">আমাদের জানান</span>
                </h2>
                <p class="text-lg text-gray-200 leading-relaxed" data-key="complaint.description">...</p>
            </div>

            <!-- Form Side -->
            <div class="lg:w-1/2 w-full">
                <div class="bg-white rounded-2xl shadow-2xl p-8 md:p-10 relative overflow-hidden" style="min-height: 400px;">
                    
                    <!-- SUCCESS OVERLAY (Hidden by default) -->
                    <div id="form-success" class="hidden absolute inset-0 bg-white z-50 flex flex-col items-center justify-center text-center p-8 animate-fade-in">
                        <div class="w-20 h-20 bg-green-100 text-green-600 rounded-full flex items-center justify-center mb-6 shadow-sm">
                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                        </div>
                        <h3 class="text-3xl font-black text-[#00523A] mb-2" data-key="complaint.success_title">ধন্যবাদ!</h3>
                        <p class="text-gray-600 text-lg" data-key="complaint.success_msg">...</p>
                        <button type="button" id="retry-btn" class="mt-8 text-[#00523A] font-bold underline hover:text-green-800 text-lg" data-key="complaint.btn_retry">
                            নতুন অভিযোগ করুন
                        </button>
                    </div>

                    <!-- FORM -->
                    <form id="complaint-form" class="space-y-5 relative z-10">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2" data-key="complaint.label_name">আপনার নাম</label>
                            <input type="text" name="name" required class="w-full px-4 py-3 rounded-lg bg-gray-50 border border-gray-300 focus:border-[#00523A] focus:ring-2 focus:ring-[#00523A]/20 outline-none" data-key="complaint.placeholder_name" placeholder="...">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2" data-key="complaint.label_phone">মোবাইল নাম্বার</label>
                            <input type="tel" name="phone" required class="w-full px-4 py-3 rounded-lg bg-gray-50 border border-gray-300 focus:border-[#00523A] focus:ring-2 focus:ring-[#00523A]/20 outline-none" data-key="complaint.placeholder_phone" placeholder="...">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2" data-key="complaint.label_problem">সমস্যার বিবরণ</label>
                            <textarea name="problem" required rows="3" class="w-full px-4 py-3 rounded-lg bg-gray-50 border border-gray-300 focus:border-[#00523A] focus:ring-2 focus:ring-[#00523A]/20 outline-none resize-none" data-key="complaint.placeholder_problem" placeholder="..."></textarea>
                        </div>

                        <button type="submit" id="submit-btn" class="w-full bg-[#D81E05] text-white font-bold py-4 rounded-lg hover:bg-red-700 transition-all shadow-lg flex justify-center items-center gap-2">
                            <span id="btn-text" data-key="complaint.btn_submit">জমা দিন</span>
                            <!-- Spinner -->
                            <svg id="btn-spinner" class="animate-spin h-5 w-5 text-white hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
<?php include 'footer.php'; ?>