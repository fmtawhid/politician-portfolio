<?php
// Use the Bangla header for consistency and to handle authentication.
require_once 'header.php';

// Get event ID from URL.
$event_id = $_GET['id'] ?? 0;
if ($event_id <= 0) { // More robust check
    echo "<main class='container mx-auto px-6 py-12 flex-grow'><p class='text-red-500'>অবৈধ ইভেন্ট আইডি।</p></main>";
    require_once 'footer.php';
    exit;
}

// --- Fetch Event Details ---
// Fetches for any user
$sql = "SELECT e.id, e.title, e.event_date, e.event_time, e.location, e.description, u.username as created_by
        FROM events e
        JOIN users u ON e.user_id = u.id
        WHERE e.id = ?";

$event = null;
if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("i", $event_id);
    if ($stmt->execute()) {
        // **FIX**: Use bind_result() instead of get_result()
        $stmt->bind_result($fetched_id, $fetched_title, $fetched_date, $fetched_time, $fetched_location, $fetched_description, $fetched_creator); // Bind columns

        if ($stmt->fetch()) { // Fetch the single row
            // Assign fetched data to an associative array
            $event = [
                'id' => $fetched_id,
                'title' => $fetched_title,
                'event_date' => $fetched_date,
                'event_time' => $fetched_time,
                'location' => $fetched_location,
                'description' => $fetched_description,
                'created_by' => $fetched_creator
            ];
        }
    } else {
        error_log("Error executing fetch event details statement: " . $stmt->error);
    }
    $stmt->close();
} else {
    error_log("Error preparing fetch event details statement: " . $conn->error);
}

// Function to convert English digits to Bangla
function toBanglaNum($num) {
    $en = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
    $bn = ['০', '১', '২', '৩', '৪', '৫', '৬', '৭', '৮', '৯'];
     return is_scalar($num) ? str_replace($en, $bn, (string)$num) : $num;
}

// Function to format date/time into Bangla
function formatToBanglaDateTime($date_str, $time_str) {
    try {
        if (empty($date_str) || empty($time_str)) return "অজানা"; // Handle empty values
         // Specify timezone when creating DateTime object
        $datetime = new DateTime($date_str . ' ' . $time_str, new DateTimeZone('Asia/Pabna'));

        $day = toBanglaNum($datetime->format('d'));
        $year = toBanglaNum($datetime->format('Y'));
        $time = toBanglaNum($datetime->format('h:i'));
        $am_pm = $datetime->format('A') == 'AM' ? 'সকাল' : 'বিকাল';

        $month_en = $datetime->format('F');
        $en_months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
        $bn_months = ['জানুয়ারি', 'ফেব্রুয়ারি', 'মার্চ', 'এপ্রিল', 'মে', 'জুন', 'জুলাই', 'আগস্ট', 'সেপ্টেম্বর', 'অক্টোবর', 'নভেম্বর', 'ডিসেম্বর'];
        $month_bn = str_replace($en_months, $bn_months, $month_en);

        return "{$day} {$month_bn}, {$year} | {$am_pm} {$time}";
    } catch (Exception $e) {
        error_log("Error formatting date/time: " . $e->getMessage() . " for date: " . $date_str . ", time: " . $time_str);
        return "অবৈধ তারিখ/সময়";
    }
}

?>

<title><?php echo $event ? htmlspecialchars($event['title']) : 'ইভেন্টের বিবরণ'; ?> | ক্যাম্পেইন শিডিউলার</title>

<main class="container mx-auto px-6 py-12 flex-grow">
    <?php if ($event): ?>
        <div class="max-w-4xl mx-auto bg-white rounded-2xl shadow-2xl overflow-hidden">
            <!-- Header with Title and Admin-Only Edit Button -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center p-6 bg-gray-50 border-b">
                <h1 class="text-3xl font-extrabold text-gray-900 mb-2 sm:mb-0">
                    <?php echo htmlspecialchars($event['title']); ?>
                </h1>

                <!-- Edit button is now for Admins (1) or Super Admins (2) -->
                <?php if (isset($_SESSION['role']) && $_SESSION['role'] >= 1): ?>
                    <a href="edit_event.php?id=<?php echo $event['id']; ?>" class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-5 rounded-lg whitespace-nowrap flex items-center gap-2 transition-all transform hover:scale-105">
                        <i data-feather="edit-2" class="w-4 h-4"></i>
                        <span>সম্পাদনা করুন</span>
                    </a>
                <?php endif; ?>
            </div>

            <!-- Event Details Grid -->
            <div class="p-6 md:p-8 grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Date and Time Card -->
                <div class="bg-blue-50 p-5 rounded-xl flex items-start space-x-4">
                    <div class="flex-shrink-0 bg-blue-100 text-blue-600 p-3 rounded-full">
                        <i data-feather="calendar" class="w-6 h-6"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-500 uppercase">তারিখ এবং সময়</h3>
                        <p class="text-lg font-bold text-gray-800 mt-1">
                            <?php echo formatToBanglaDateTime($event['event_date'], $event['event_time']); ?>
                        </p>
                    </div>
                </div>

                <!-- Location Card -->
                <div class="bg-indigo-50 p-5 rounded-xl flex items-start space-x-4">
                    <div class="flex-shrink-0 bg-indigo-100 text-indigo-600 p-3 rounded-full">
                        <i data-feather="map-pin" class="w-6 h-6"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-500 uppercase">স্থান</h3>
                        <p class="text-lg font-bold text-gray-800 mt-1">
                            <?php echo htmlspecialchars($event['location']); ?>
                        </p>
                    </div>
                </div>

                <!-- Creator Card -->
                <div class="bg-gray-50 p-5 rounded-xl flex items-start space-x-4">
                    <div class="flex-shrink-0 bg-gray-200 text-gray-600 p-3 rounded-full">
                        <i data-feather="user" class="w-6 h-6"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-500 uppercase">তৈরি করেছেন</h3>
                        <p class="text-lg font-bold text-gray-800 mt-1">
                            <?php echo htmlspecialchars($event['created_by']); ?>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Description Section -->
            <?php if (!empty($event['description'])): ?>
                <div class="p-6 md:p-8 border-t">
                    <h3 class="text-xl font-bold text-gray-800 mb-4">ইভেন্টের বিবরণ</h3>
                    <div class="prose max-w-none text-gray-700">
                        <?php echo nl2br(htmlspecialchars($event['description'])); // nl2br converts newlines to <br> ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Footer Actions -->
            <div class="p-6 bg-gray-50 border-t text-right">
                <a href="events.php" class="text-gray-600 hover:text-blue-600 font-semibold transition">
                    &larr; সকল ইভেন্টে ফিরে যান
                </a>
            </div>
        </div>

    <?php else: ?>
        <div class="text-center bg-white rounded-2xl shadow-lg p-10">
            <h2 class="text-xl font-semibold text-red-700">ইভেন্ট পাওয়া যায়নি</h2>
            <p class="text-gray-500 mt-2">দুঃখিত, অনুরোধ করা ইভেন্টটি খুঁজে পাওয়া যায়নি।</p>
            <a href="events.php" class="mt-6 inline-block bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-5 rounded-lg transition">
                সকল ইভেন্টে ফিরে যান
            </a>
        </div>
    <?php endif; ?>
</main>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        feather.replace(); // Render icons on this page
    });
</script>


<?php
require_once 'footer.php';
?>

