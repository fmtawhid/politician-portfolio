<?php ob_start(); // FIX: Start output buffering at the very top to prevent "headers already sent" errors. ?>
<?php
// Use the Bangla header for consistency and to handle authentication.
require_once 'header.php'; // Ensure this is the correct header file

// --- Handle Event Deletion (Admin Only) ---
// Check if role >= 1 (Admin or Super Admin)
if (isset($_GET['delete_id']) && isset($_SESSION['role']) && $_SESSION['role'] >= 1) {
    $delete_id = (int)$_GET['delete_id']; // Cast to integer for security

    // Admin can delete any event.
    $sql_delete = "DELETE FROM events WHERE id = ?";
    if ($stmt_delete = $conn->prepare($sql_delete)) {
        $stmt_delete->bind_param("i", $delete_id);
        if ($stmt_delete->execute()) {
            // Redirect to prevent re-deletion on refresh
            // This redirect will now work because of ob_start().
            header("Location: events.php?status=deleted_event");
            exit();
        } else {
            error_log("Error deleting event ID " . $delete_id . ": " . $conn->error);
            // Optionally set an error message for the user, but avoid echo before header
            // For now, we'll let the redirect happen, or potentially fall through if execute fails badly
        }
        $stmt_delete->close();
    } else {
            error_log("Error preparing delete statement: " . $conn->error);
            // Optionally set an error message
    }
}

// --- Fetch All Events ---
$events = [];
$filter_date = $_GET['filter_date'] ?? '';
$search_term = trim($_GET['search_term'] ?? '');

// Base SQL (fetches for all users)
$sql_fetch = "SELECT id, title, event_date, event_time, location
              FROM events";
$params = [];
$types = "";
$where_conditions = [];

// Add search filter
if (!empty($search_term)) {
    $where_conditions[] = "(title LIKE ? OR location LIKE ?)";
    $search_param = "%{$search_term}%";
    $params[] = $search_param;
    $params[] = $search_param;
    $types .= "ss";
}

// Add date filter
if (!empty($filter_date)) {
    // Basic validation for date format (YYYY-MM-DD)
    if (preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $filter_date)) {
        $where_conditions[] = "event_date = ?";
        $params[] = $filter_date;
        $types .= "s";
    } else {
        // Handle invalid date format if necessary, e.g., ignore filter or show error
        $filter_date = ''; // Ignore invalid date
        // Optionally set an error message: $error_message = "অবৈধ তারিখ ফরম্যাট।";
    }
}


// Append WHERE conditions if any
if (count($where_conditions) > 0) {
    $sql_fetch .= " WHERE " . implode(" AND ", $where_conditions);
}

// Add ordering
$sql_fetch .= " ORDER BY
                CASE WHEN CONCAT(event_date, ' ', event_time) >= NOW() THEN 0 ELSE 1 END,
                event_date ASC,
                event_time ASC";

if ($stmt_fetch = $conn->prepare($sql_fetch)) {
    if (!empty($types)) {
        // Use argument unpacking (...) for variable number of parameters
        $stmt_fetch->bind_param($types, ...$params);
    }

    if ($stmt_fetch->execute()) {
        // **FIX**: Use bind_result() instead of get_result()
        $stmt_fetch->bind_result($event_id, $event_title, $event_date, $event_time, $event_location); // Bind columns to variables

        // Fetch results row by row
        while ($stmt_fetch->fetch()) {
                // Create an associative array for each row
                $events[] = [
                'id' => $event_id,
                'title' => $event_title,
                'event_date' => $event_date,
                'event_time' => $event_time,
                'location' => $event_location
            ];
        }
    } else {
        error_log("Error executing fetch events statement: " . $stmt_fetch->error);
        // Optionally set an error message for the user: $error_message = "ইভেন্ট আনতে সমস্যা হয়েছে।";
    }
    $stmt_fetch->close();
} else {
    error_log("Error preparing fetch events statement: " . $conn->error);
    // Optionally set an error message: $error_message = "ডাটাবেস কোয়েরি প্রস্তুত করতে সমস্যা হয়েছে।";
}

// Function to convert English digits to Bangla
function toBanglaNum($num) {
    $en = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
    $bn = ['০', '১', '২', '৩', '৪', '৫', '৬', '৭', '৮', '৯'];
    return is_scalar($num) ? str_replace($en, $bn, (string)$num) : $num; // Check if scalar before replace
}

// Function to format date into Bangla
function formatToBanglaDate($date_str) {
    try {
        if (empty($date_str)) return "অজানা তারিখ";
        $date = new DateTime($date_str); // Consider setting timezone if needed: new DateTimeZone('Asia/Pabna')
        $day = toBanglaNum($date->format('d'));

        $month_en = $date->format('F');
        $en_months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
        $bn_months = ['জানুয়ারি', 'ফেব্রুয়ারি', 'মার্চ', 'এপ্রিল', 'মে', 'জুন', 'জুলাই', 'আগস্ট', 'সেপ্টেম্বর', 'অক্টোবর', 'নভেম্বর', 'ডিসেম্বর'];
        $month_bn = str_replace($en_months, $bn_months, $month_en);

        return "{$day} {$month_bn}";
    } catch (Exception $e) {
        error_log("Error formatting date: " . $e->getMessage() . " for date: " . $date_str);
        return "অবৈধ তারিখ";
    }
}

// Display status message from redirect
$action_message = '';
// Use session for flash messages for better reliability across redirects
if (isset($_SESSION['action_message'])) {
    $action_message = $_SESSION['action_message'];
    unset($_SESSION['action_message']); // Clear message after displaying once
}
// Set message in session after actions instead of using query parameters
if (isset($_GET['status'])) {
    if($_GET['status'] == 'deleted_event') {
        $action_message = "ইভেন্ট সফলভাবে মুছে ফেলা হয়েছে।";
    } elseif ($_GET['status'] == 'added') {
            $action_message = "ইভেন্ট সফলভাবে যোগ করা হয়েছে।";
    }
    // Consider storing these in session flash messages instead
}


?>

<title>সকল ইভেন্ট | ক্যাম্পেইন শিডিউলার</title>

<main class="container mx-auto px-6 py-12 flex-grow">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-6 gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-gray-900">সকল ইভেন্ট</h1>
            <p class="text-gray-500 mt-1">আপনার সমস্ত নির্ধারিত ইভেন্ট এখানে দেখুন এবং পরিচালনা করুন।</p>
        </div>
        <a href="add_event.php" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 px-5 rounded-full whitespace-nowrap flex items-center gap-2 transition-all transform hover:scale-105 shadow-md hover:shadow-lg">
            <i data-feather="plus" class="w-5 h-5"></i>
            <span>নতুন ইভেন্ট যোগ করুন</span>
        </a>
    </div>

    <?php if ($action_message): ?>
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-md" role="alert">
            <p><?php echo htmlspecialchars($action_message); ?></p>
        </div>
    <?php endif; ?>
    <?php // Display generic error message if set
        // if (!empty($error_message)) {
        //    echo '<div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-md" role="alert"><p>' . htmlspecialchars($error_message) . '</p></div>';
        // }
    ?>


    <!-- Filter and Search Section -->
    <div class="mb-8">
        <form action="events.php" method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
            <div class="md:col-span-2">
                <label for="search_term" class="block text-sm font-medium text-gray-700">ইভেন্ট খুঁজুন</label>
                <div class="relative mt-1">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i data-feather="search" class="h-5 w-5 text-gray-400"></i>
                    </div>
                    <input type="text" id="search_term" name="search_term" value="<?php echo htmlspecialchars($search_term); ?>" class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="শিরোনাম বা স্থান...">
                </div>
            </div>
            <div class="md:col-span-2">
                <label for="filter_date" class="block text-sm font-medium text-gray-700">নির্দিষ্ট তারিখ</label>
                <input type="date" id="filter_date" name="filter_date" value="<?php echo htmlspecialchars($filter_date); ?>" class="mt-1 w-full border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 p-2">
            </div>
            <div class="md:col-span-1 flex items-center gap-2">
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg shadow-sm transition">ফিল্টার</button>
                <?php if (!empty($search_term) || !empty($filter_date)): ?>
                    <a href="events.php" class="p-2 text-gray-500 hover:text-gray-700 bg-gray-200 hover:bg-gray-300 rounded-lg" title="ফিল্টার মুছুন">
                        <i data-feather="x" class="h-5 w-5"></i>
                    </a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <!-- Event Cards Grid -->
    <?php if (count($events) > 0): ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($events as $event):
                $is_past = false;
                try {
                    // Ensure both date and time are not empty before creating DateTime
                    if (!empty($event['event_date']) && !empty($event['event_time'])) {
                        // Use a consistent timezone, ideally UTC in DB or explicit like Asia/Pabna
                        $event_datetime = new DateTime($event['event_date'] . ' ' . $event['event_time'], new DateTimeZone('Asia/Pabna')); // Assume server time is correct
                        $now = new DateTime('now', new DateTimeZone('Asia/Pabna'));
                        $is_past = $event_datetime < $now;
                    } else {
                            error_log("Missing date or time for event ID " . ($event['id'] ?? 'N/A'));
                    }
                } catch (Exception $e) {
                    error_log("Invalid date/time for event ID " . ($event['id'] ?? 'N/A') . ": " . ($event['event_date'] ?? 'N/A') . ' ' . ($event['event_time'] ?? 'N/A') . " Error: " . $e->getMessage());
                }
            ?>
                <div class="bg-white rounded-2xl shadow-lg flex flex-col transition-shadow duration-300 hover:shadow-xl <?php if ($is_past) echo 'opacity-60'; ?>">
                    <div class="p-6 flex-grow">
                        <span class="text-sm font-semibold <?php echo $is_past ? 'text-gray-500' : 'text-blue-600'; ?>">
                            <?php echo formatToBanglaDate($event['event_date']); ?>
                        </span>
                        <h3 class="text-xl font-bold text-gray-800 mt-1 mb-2 truncate" title="<?php echo htmlspecialchars($event['title']); ?>"><?php echo htmlspecialchars($event['title']); ?></h3>
                        <p class="text-gray-600 flex items-center text-sm">
                            <i data-feather="map-pin" class="w-4 h-4 mr-2 flex-shrink-0"></i>
                            <span class="truncate" title="<?php echo htmlspecialchars($event['location']); ?>"><?php echo htmlspecialchars($event['location']); ?></span>
                        </p>
                    </div>
                    <div class="border-t p-4 flex items-center justify-end space-x-2 bg-gray-50 rounded-b-2xl">
                        <!-- "View" button is visible to everyone -->
                        <a href="view_event.php?id=<?php echo $event['id']; ?>" class="text-sm font-semibold text-gray-600 hover:text-blue-600 p-2 rounded-md transition">দেখুন</a>

                        <!-- Edit and Delete buttons are now for Admins (1) or Super Admins (2) -->
                        <?php if (isset($_SESSION['role']) && $_SESSION['role'] >= 1): ?>
                            <a href="edit_event.php?id=<?php echo $event['id']; ?>" class="text-sm font-semibold text-gray-600 hover:text-green-600 p-2 rounded-md transition">সম্পাদনা</a>
                            
                            <!-- FIX: Changed <a> tag to <button> to trigger custom modal. Removed onclick="confirm()". -->
                            <button type="button" 
                                    data-delete-id="<?php echo $event['id']; ?>" 
                                    onclick="openDeleteModal(this.dataset.deleteId)" 
                                    class="text-sm font-semibold text-gray-600 hover:text-red-600 p-2 rounded-md transition">
                                মুছুন
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="text-center bg-white rounded-2xl shadow-lg p-10">
            <h2 class="text-xl font-semibold text-gray-700">কোনো ইভেন্ট পাওয়া যায়নি</h2>
            <p class="text-gray-500 mt-2">
                <?php if (!empty($filter_date) || !empty($search_term)): ?>
                    আপনার অনুসন্ধান বা ফিল্টারের সাথে মেলে এমন কোনো ইভেন্ট পাওয়া যায়নি।
                <?php else: ?>
                    এখনও কোনো ইভেন্ট তৈরি করা হয়নি। শুরু করতে একটি নতুন ইভেন্ট যোগ করুন।
                <?php endif; ?>
            </p>
            <a href="add_event.php" class="mt-6 inline-block bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-5 rounded-lg transition-transform transform hover:scale-105">
                প্রথম ইভেন্ট যোগ করুন
            </a>
        </div>
    <?php endif; ?>
</main>

<!-- FIX: Add Delete Confirmation Modal HTML -->
<div id="deleteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full flex items-center justify-center z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="relative p-7 bg-white w-full max-w-md m-auto flex-col flex rounded-2xl shadow-lg">
        <div class="flex items-center justify-between mb-4">
            <h3 id="modal-title" class="text-xl font-bold text-gray-800">নিশ্চিতকরণ মুছুন</h3>
            <button type="button" onclick="closeDeleteModal()" class="text-gray-400 hover:text-gray-600" aria-label="Close modal">
                <i data-feather="x" class="w-6 h-6"></i>
            </button>
        </div>
        <p class="text-gray-600 mb-7">আপনি কি নিশ্চিত যে আপনি এই ইভেন্টটি মুছে ফেলতে চান? এই ক্রিয়াটি বাতিল করা যাবে না।</p>
        <div class="flex justify-end gap-4">
            <button type="button" onclick="closeDeleteModal()" class="text-gray-700 bg-gray-100 hover:bg-gray-200 font-bold py-2.5 px-5 rounded-lg transition">
                বাতিল করুন
            </button>
            <!-- This link's href will be set by JavaScript -->
            <a id="confirmDeleteLink" href="#" class="text-white bg-red-600 hover:bg-red-700 font-bold py-2.5 px-5 rounded-lg transition">
                মুছুন
            </a>
        </div>
    </div>
</div>


<script>
    document.addEventListener('DOMContentLoaded', () => {
        feather.replace(); // Ensure Feather icons render on this page too
    });

    // --- FIX: JavaScript for custom delete modal ---
    const deleteModal = document.getElementById('deleteModal');
    const confirmDeleteLink = document.getElementById('confirmDeleteLink');

    function openDeleteModal(eventId) {
        if (!deleteModal || !confirmDeleteLink) return;
        
        // Set the correct delete link for the confirm button
        confirmDeleteLink.href = `events.php?delete_id=${eventId}`;
        
        // Show the modal
        deleteModal.classList.remove('hidden');
    }

    function closeDeleteModal() {
        if (!deleteModal) return;
        
        // Hide the modal
        deleteModal.classList.add('hidden');
    }
    
    // Optional: Close modal if clicking on the background overlay
    window.addEventListener('click', function(event) {
        if (event.target == deleteModal) {
            closeDeleteModal();
        }
    });
    // --- End of modal JavaScript ---
</script>


<?php
require_once 'footer.php';
?>
<?php ob_end_flush(); // FIX: Flush the output buffer at the very end. ?>
