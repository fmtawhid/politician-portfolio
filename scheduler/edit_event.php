<?php
// Use the Bangla header for consistency and to handle authentication.
require_once 'header.php';

// --- ADMIN ACCESS CHECK ---
// **FIX**: Changed 'is_admin' to 'role' and check for >= 1
if (!isset($_SESSION['role']) || $_SESSION['role'] < 1) {
    echo "<main class='container mx-auto px-6 py-12 flex-grow'><p class='text-red-500 font-bold text-lg text-center'>আপনার এই পৃষ্ঠাটি সম্পাদনা করার অনুমতি নেই।</p></main>";
    require_once 'footer.php';
    exit;
}

// Initialize variables
$event_title = $event_date = $event_time = $event_location = $event_description = "";
$error_message = $success_message = "";
$event_id = $_GET['id'] ?? 0;

// --- Handle Form Submission (POST Request) ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get data from form
    $event_id = $_POST['event_id'];
    $title = trim($_POST['title']);
    $event_date = trim($_POST['event_date']);
    $event_time = trim($_POST['event_time']);
    $location = trim($_POST['location']);
    $description = trim($_POST['description']);

    // Validate
    if (empty($title) || empty($event_date) || empty($event_time) || empty($location)) {
        $error_message = "অনুগ্রহ করে শিরোনাম, তারিখ, সময় এবং স্থান পূরণ করুন।";
    } else {
        // Admins can edit any event.
        $sql = "UPDATE events SET title = ?, event_date = ?, event_time = ?, location = ?, description = ? WHERE id = ?";

        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("sssssi", $title, $event_date, $event_time, $location, $description, $event_id);

            if ($stmt->execute()) {
                $success_message = "ইভেন্ট সফলভাবে আপডেট করা হয়েছে।";
                // Optionally redirect after success
                // header("Location: view_event.php?id=" . $event_id . "&status=updated");
                // exit;
            } else {
                $error_message = "কিছু একটা ভুল হয়েছে। অনুগ্রহ করে আবার চেষ্টা করুন।";
            }
            $stmt->close();
        }
    }
    // Re-assign values to show in form after POST
    $event_title = $title;
    $event_date = $event_date; // These are already assigned
    $event_time = $event_time;
    $event_location = $location;
    $event_description = $description;
} elseif ($event_id > 0) { // --- Fetch Event Data for Form (GET Request) ---
    // Admins can load any event.
    $sql_fetch = "SELECT title, event_date, event_time, location, description FROM events WHERE id = ?";
    if ($stmt_fetch = $conn->prepare($sql_fetch)) {
        $stmt_fetch->bind_param("i", $event_id);
        $stmt_fetch->execute();
        $stmt_fetch->store_result();

        if ($stmt_fetch->num_rows == 1) {
            $stmt_fetch->bind_result($event_title, $event_date, $event_time, $event_location, $event_description);
            $stmt_fetch->fetch();
        } else {
            $error_message = "ইভেন্ট খুঁজে পাওয়া যায়নি বা আপনার এটি সম্পাদনা করার অনুমতি নেই।";
             $event_id = 0; // Prevent form from showing if event not found/accessible
        }
        $stmt_fetch->close();
    } else {
         $error_message = "ডাটাবেস থেকে ইভেন্ট লোড করার সময় ত্রুটি হয়েছে।";
         $event_id = 0; // Prevent form
    }
} else {
    $error_message = "অবৈধ ইভেন্ট আইডি।";
}
?>

<title>ইভেন্ট সম্পাদনা করুন | ক্যাম্পেইন শিডিউলার</title>

<!-- Add flex-grow for sticky footer -->
<main class="container mx-auto px-6 py-12 flex-grow">

    <!-- New Page Header -->
    <div class="mb-10">
        <h1 class="text-3xl font-extrabold text-gray-900">ইভেন্ট সম্পাদনা করুন</h1>
        <p class="text-gray-500 mt-1">ইভেন্টের বিবরণ আপডেট করুন।</p>
    </div>

     <!-- New Two-Column Layout -->
    <div class="flex flex-col md:flex-row gap-10">

        <!-- Left Column: The Form -->
        <div class="w-full md:w-2/3">
            <div class="bg-white p-8 rounded-2xl shadow-lg">
                <?php if (!empty($error_message)): ?>
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-md" role="alert">
                        <p class="font-bold">ত্রুটি</p>
                        <p><?php echo htmlspecialchars($error_message); ?></p>
                    </div>
                <?php endif; ?>

                <?php if (!empty($success_message)): ?>
                    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-md" role="alert">
                        <p class="font-bold">সফল</p>
                        <p><?php echo htmlspecialchars($success_message); ?></p>
                         <p class="mt-2"><a href="view_event.php?id=<?php echo $event_id; ?>" class="text-green-800 underline">আপডেট করা ইভেন্ট দেখুন</a></p>
                    </div>
                <?php endif; ?>


                <?php if ($event_id > 0): // Show form only if event ID is valid and data was fetched (or after successful post) ?>
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . "?id=" . $event_id; ?>" method="post">
                    <input type="hidden" name="event_id" value="<?php echo $event_id; ?>">

                    <!-- Event Title with Icon -->
                    <div class="mb-5">
                        <label for="title" class="block text-gray-700 text-sm font-bold mb-2">শিরোনাম</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i data-feather="type" class="w-5 h-5 text-gray-400"></i>
                            </div>
                            <input type="text" name="title" id="title" value="<?php echo htmlspecialchars($event_title); ?>" class="shadow appearance-none border rounded-lg w-full py-3 px-4 pl-10 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        </div>
                    </div>

                    <!-- Date and Time Fields with Icons -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-5">
                        <div>
                            <label for="event_date" class="block text-gray-700 text-sm font-bold mb-2">তারিখ</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i data-feather="calendar" class="w-5 h-5 text-gray-400"></i>
                                </div>
                                <input type="date" name="event_date" id="event_date" value="<?php echo htmlspecialchars($event_date); ?>" class="shadow appearance-none border rounded-lg w-full py-3 px-4 pl-10 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                            </div>
                        </div>
                        <div>
                            <label for="event_time" class="block text-gray-700 text-sm font-bold mb-2">সময়</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i data-feather="clock" class="w-5 h-5 text-gray-400"></i>
                                </div>
                                <input type="time" name="event_time" id="event_time" value="<?php echo htmlspecialchars($event_time); ?>" class="shadow appearance-none border rounded-lg w-full py-3 px-4 pl-10 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                            </div>
                        </div>
                    </div>

                    <!-- Location with Icon -->
                    <div class="mb-5">
                        <label for="location" class="block text-gray-700 text-sm font-bold mb-2">স্থান</label>
                         <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i data-feather="map-pin" class="w-5 h-5 text-gray-400"></i>
                            </div>
                            <input type="text" name="location" id="location" value="<?php echo htmlspecialchars($event_location); ?>" class="shadow appearance-none border rounded-lg w-full py-3 px-4 pl-10 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="mb-6">
                        <label for="description" class="block text-gray-700 text-sm font-bold mb-2">বিবরণ</label>
                        <textarea name="description" id="description" rows="5" class="shadow appearance-none border rounded-lg w-full py-3 px-4 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="ইভেন্টের এজেন্ডা, উদ্দেশ্য বা নোট লিখুন..."><?php echo htmlspecialchars($event_description); ?></textarea>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center justify-end gap-4 border-t pt-6">
                        <a href="view_event.php?id=<?php echo $event_id; ?>" class="text-gray-600 hover:text-blue-600 font-semibold transition">বাতিল করুন</a>
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 px-6 rounded-lg focus:outline-none focus:shadow-outline transition-all transform hover:scale-105 duration-300 shadow-md flex items-center gap-2">
                            <i data-feather="save" class="w-5 h-5"></i>
                           আপডেট সংরক্ষণ করুন
                        </button>
                    </div>
                </form>
                <?php endif; ?>
            </div>
        </div>

        <!-- Right Column: Info Panel -->
        <div class="w-full md:w-1/3">
            <div class="bg-blue-50 p-6 rounded-2xl border border-blue-200 sticky top-24">
                <div class="flex items-center space-x-3 mb-4">
                    <div class="bg-blue-100 p-2 rounded-full">
                        <i data-feather="edit-3" class="text-blue-600 w-5 h-5"></i>
                    </div>
                    <h3 class="text-lg font-bold text-gray-800">ইভেন্ট সম্পাদনা</h3>
                </div>
                 <p class="text-sm text-gray-700 space-y-2">
                    এখানে আপনি ইভেন্টের বিবরণ পরিবর্তন করতে পারেন। নিশ্চিত করুন যে সমস্ত তথ্য সঠিক এবং আপ-টু-ডেট আছে। "আপডেট সংরক্ষণ করুন" বাটনে ক্লিক করে পরিবর্তনগুলি সংরক্ষণ করুন।
                 </p>
                 <a href="view_event.php?id=<?php echo $event_id; ?>" class="mt-4 inline-block text-sm text-blue-600 hover:underline">বিস্তারিত পৃষ্ঠায় ফিরে যান &rarr;</a>
            </div>
        </div>

    </div>
</main>

<?php
require_once 'footer.php';
?>

