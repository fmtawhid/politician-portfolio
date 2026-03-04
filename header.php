<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dr. Ahmed Mostofa Noman - Pabna-5</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Tailwind Configuration -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        'sans-bengali': ['"Hind Siliguri"', 'sans-serif'], 
                        'sans-english': ['"Inter"', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            green: '#00523A',
                            red: '#D81E05',
                            yellow: '#FACC15'
                        }
                    }
                }
            }
        }
    </script>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@300;400;500;600;700&family=Inter:wght@300;400;600;700;900&display=swap" rel="stylesheet">

    <style>
        body { font-family: 'Hind Siliguri', sans-serif; }
        .font-english { font-family: 'Inter', sans-serif; }
        /* Smooth scrolling for anchor links */
        html { scroll-behavior: smooth; }
    </style>
</head>
<body class="bg-gray-50 text-gray-800 flex flex-col min-h-screen">

<!-- Sticky Header -->
<header class="bg-[#00523A] text-white sticky top-0 z-50 shadow-lg">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-20">
            
            <!-- Logo / Title -->
            <div class="flex-shrink-0 mr-4">
                <a href="index.php" class="text-white block">
                    <h1 class="text-2xl sm:text-3xl font-black tracking-tighter whitespace-nowrap" data-key="hero.title">ডাঃ আহমেদ মোস্তফা নোমান</h1>
                    <p class="text-[10px] sm:text-xs text-yellow-300 font-bold tracking-widest whitespace-nowrap" data-key="hero.subtitle">পাবনা মানুষের জন্য</p>
               
                </a>
            </div>

            <!-- Desktop Menu (Visible only on Extra Large Screens 1280px+) -->
            <nav class="hidden xl:flex flex-grow justify-end items-center space-x-4 lg:space-x-5">
                <a href="index.php#about" class="hover:text-yellow-300 transition-colors font-medium text-sm whitespace-nowrap" data-key="nav.about">সম্পর্কে</a>
                <a href="index.php#platform" class="hover:text-yellow-300 transition-colors font-medium text-sm whitespace-nowrap" data-key="nav.platform">কর্মসূচি</a>
                <a href="index.php#endorsements" class="hover:text-yellow-300 transition-colors font-medium text-sm whitespace-nowrap" data-key="nav.endorsements">সমর্থন</a>
                <a href="index.php#press" class="hover:text-yellow-300 transition-colors font-medium text-sm whitespace-nowrap" data-key="nav.press">প্রেস</a>
                <a href="index.php#gallery" class="hover:text-yellow-300 transition-colors font-medium text-sm whitespace-nowrap" data-key="nav.gallery">গ্যালারি</a>
                <a href="index.php#blog" class="hover:text-yellow-300 transition-colors font-medium text-sm whitespace-nowrap" data-key="nav.blog">ব্লগ</a>
                <a href="index.php#volunteer" class="hover:text-yellow-300 transition-colors font-medium text-sm whitespace-nowrap" data-key="nav.volunteer">স্বেচ্ছাসেবক</a>
                <a href="manifesto.php" class="hover:text-yellow-300 transition-colors font-medium text-sm whitespace-nowrap" data-key="nav.manifesto">রূপকল্প</a>
                
                <!-- Action Buttons -->
                <a href="/scheduler/index.php" class="bg-yellow-400 text-[#00523A] px-4 py-2 rounded-md font-bold text-sm hover:bg-yellow-300 transition-colors whitespace-nowrap shadow-sm" data-key="nav.campaign_scheduler">সময়সূচী</a>
                <button id="lang-toggle" class="bg-white text-[#00523A] px-4 py-2 rounded-md font-bold text-sm hover:bg-gray-200 transition-colors whitespace-nowrap shadow-sm">
                    English
                </button>
            </nav>

            <!-- Mobile/Tablet Menu Button (Visible on screens smaller than 1280px) -->
            <div class="xl:hidden">
                <button id="mobile-menu-btn" class="inline-flex items-center justify-center p-2 rounded-md text-white hover:text-yellow-300 hover:bg-white/10 focus:outline-none transition-colors">
                    <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile/Tablet Dropdown Menu -->
    <div class="hidden bg-[#004430] border-t border-white/10 shadow-xl absolute w-full left-0 top-20" id="mobile-menu">
        <div class="px-4 pt-4 pb-6 space-y-2">
            <a href="index.php#about" class="block px-3 py-3 rounded-md text-base font-medium text-white hover:bg-white/10 hover:text-yellow-300 border-b border-white/5" data-key="nav.about">সম্পর্কে</a>
            <a href="index.php#platform" class="block px-3 py-3 rounded-md text-base font-medium text-white hover:bg-white/10 hover:text-yellow-300 border-b border-white/5" data-key="nav.platform">কর্মসূচি</a>
            <a href="index.php#endorsements" class="block px-3 py-3 rounded-md text-base font-medium text-white hover:bg-white/10 hover:text-yellow-300 border-b border-white/5" data-key="nav.endorsements">সমর্থন</a>
            <a href="index.php#press" class="block px-3 py-3 rounded-md text-base font-medium text-white hover:bg-white/10 hover:text-yellow-300 border-b border-white/5" data-key="nav.press">প্রেস</a>
            <a href="index.php#gallery" class="block px-3 py-3 rounded-md text-base font-medium text-white hover:bg-white/10 hover:text-yellow-300 border-b border-white/5" data-key="nav.gallery">গ্যালারি</a>
            <a href="index.php#blog" class="block px-3 py-3 rounded-md text-base font-medium text-white hover:bg-white/10 hover:text-yellow-300 border-b border-white/5" data-key="nav.blog">ব্লগ</a>
            <a href="index.php#volunteer" class="block px-3 py-3 rounded-md text-base font-medium text-white hover:bg-white/10 hover:text-yellow-300 border-b border-white/5" data-key="nav.volunteer">স্বেচ্ছাসেবক</a>
            <a href="manifesto.php" class="block px-3 py-3 rounded-md text-base font-medium text-white hover:bg-white/10 hover:text-yellow-300 border-b border-white/5" data-key="nav.manifesto">রূপকল্প</a>
            
            <div class="grid grid-cols-2 gap-4 mt-4">
                <a href="/scheduler/index.php" class="text-center bg-yellow-400 text-[#00523A] px-4 py-3 rounded-md font-bold hover:bg-yellow-300 transition-colors shadow-sm" data-key="nav.campaign_scheduler">সময়সূচী</a>
                <button id="mobile-lang-toggle" class="text-center bg-white text-[#00523A] px-4 py-3 rounded-md font-bold hover:bg-gray-200 transition-colors shadow-sm">English</button>
            </div>
        </div>
    </div>
</header>
<main class="flex-grow">