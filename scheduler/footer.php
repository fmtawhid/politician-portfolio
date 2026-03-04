<footer class="bg-white mt-auto py-4 shadow-inner">
    <div class="container mx-auto px-6 text-center text-gray-600">
        <p>&copy; <?php echo date("Y"); ?> Face of Art Technologies | সময়সূচী ব্যবস্থাপনা সিস্টেম। সর্বস্বত্ব সংরক্ষিত।</p>
    </div>
</footer>

<script>
    feather.replace();

    // Mobile menu toggle
    const mobileMenuButton = document.getElementById('mobile-menu-button');
    const mobileMenu = document.getElementById('mobile-menu');
    if(mobileMenuButton) {
        mobileMenuButton.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
        });
    }

    // --- Sticky Footer Script ---
    // This script ensures the footer sticks to the bottom of the page, even on pages with little content.
    // It works by making the body a flex container and allowing the main content to grow and fill available space.
    document.addEventListener('DOMContentLoaded', function() {
        // Add required classes to the body tag to enable flex layout
        document.body.classList.add('flex', 'flex-col', 'min-h-screen');
        
        // Find the main content area on the page
        const mainContent = document.querySelector('main');
        
        // If a <main> tag exists, make it grow to push the footer down
        if (mainContent) {
            mainContent.classList.add('flex-grow');
        }
    });
</script>

</body>
</html>

