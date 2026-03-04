<?php 
// Validate File Input to prevent hacking
$file = isset($_GET['file']) ? basename($_GET['file']) : '';
$lang = isset($_GET['lang']) && $_GET['lang'] == 'en' ? 'en' : 'bn';

// Check if file exists
$filePath = 'uploads/' . $file;
$fileExists = !empty($file) && file_exists($filePath);

// We need a simplified header for the viewer (optional, but cleaner)
include 'header.php'; 
?>

<!-- PDF.js Library -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
<script>
    // Set worker path manually
    pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
</script>

<div class="bg-gray-200 min-h-screen flex flex-col items-center py-8">
    
    <!-- Viewer Header controls -->
    <div class="w-full max-w-4xl flex justify-between items-center px-4 mb-6">
        <h1 class="text-2xl font-bold text-[#00523A]">
            <?php echo $lang == 'bn' ? 'দলিল প্রদর্শনী' : 'Document Viewer'; ?>
        </h1>
        <a href="manifesto.php?lang=<?php echo $lang; ?>" class="bg-[#00523A] text-white font-bold py-2 px-6 rounded-lg hover:bg-green-800 transition flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            <?php echo $lang == 'bn' ? 'ফিরে যান' : 'Back'; ?>
        </a>
    </div>

    <!-- PDF Container -->
    <div id="pdf-wrapper" class="w-full max-w-4xl bg-white shadow-2xl rounded-lg overflow-hidden min-h-[500px] p-4 flex flex-col items-center gap-4 relative">
        <?php if ($fileExists): ?>
            <!-- Loading Spinner -->
            <div id="loading-msg" class="text-xl font-bold text-gray-500 py-20">
                <?php echo $lang == 'bn' ? 'পিডিএফ লোড হচ্ছে...' : 'Loading PDF...'; ?>
            </div>
        <?php else: ?>
            <div class="text-red-600 font-bold py-20">
                <?php echo $lang == 'bn' ? 'ফাইলটি পাওয়া যায়নি।' : 'File not found.'; ?> <br>
                <span class="text-sm text-gray-400">File: <?php echo htmlspecialchars($file); ?></span>
            </div>
        <?php endif; ?>
    </div>

    <!-- Bottom Back Button -->
    <div class="w-full max-w-4xl flex justify-end px-4 mt-6">
        <a href="manifesto.php?lang=<?php echo $lang; ?>" class="text-[#00523A] font-bold hover:underline">
            <?php echo $lang == 'bn' ? 'ফিরে যান' : 'Back to Manifesto'; ?>
        </a>
    </div>
</div>

<?php if ($fileExists): ?>
<script>
    const url = '<?php echo $filePath; ?>';

    const renderPDF = async () => {
        const wrapper = document.getElementById('pdf-wrapper');
        const loadingMsg = document.getElementById('loading-msg');
        
        try {
            const pdf = await pdfjsLib.getDocument(url).promise;
            
            // Hide loading message
            loadingMsg.style.display = 'none';

            // Loop through all pages
            for (let pageNum = 1; pageNum <= pdf.numPages; pageNum++) {
                const page = await pdf.getPage(pageNum);
                
                // Create Canvas for each page
                const canvas = document.createElement('canvas');
                canvas.className = 'shadow-md mb-4 border border-gray-200 w-full h-auto';
                canvas.id = `page-${pageNum}`;
                wrapper.appendChild(canvas);

                // Calculate viewport (responsive width)
                // We want the PDF to fit the container width
                const containerWidth = wrapper.clientWidth - 40; // padding consideration
                const unscaledViewport = page.getViewport({ scale: 1 });
                const scale = containerWidth / unscaledViewport.width;
                const viewport = page.getViewport({ scale: scale });

                // Set canvas dimensions
                canvas.height = viewport.height;
                canvas.width = viewport.width;

                // Render
                const renderContext = {
                    canvasContext: canvas.getContext('2d'),
                    viewport: viewport
                };
                await page.render(renderContext).promise;
            }

        } catch (error) {
            console.error('Error rendering PDF:', error);
            loadingMsg.innerText = 'Error loading PDF document.';
            loadingMsg.classList.add('text-red-500');
        }
    };

    // Handle Window Resize to re-render (optional, for responsiveness)
    // Simple implementation: reload page on massive resize or CSS handles width
    document.addEventListener('DOMContentLoaded', renderPDF);

</script>
<?php endif; ?>

<?php include 'footer.php'; ?>