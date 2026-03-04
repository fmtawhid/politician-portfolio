<?php ob_start(); ?>
<?php
require_once 'header.php';

$title = $event_date = $event_time = $location = $description = "";
$error_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST['title']);
    $event_date = trim($_POST['event_date']);
    $event_time = trim($_POST['event_time']);
    $location = trim($_POST['location']);
    $description = trim($_POST['description']);
    // Ensure user_id is available in session
    if (!isset($_SESSION['user_id'])) {
        error_log("User ID missing in session during event add.");
            $error_message = "সেশন মেয়াদোত্তীর্ণ বা অবৈধ। অনুগ্রহ করে আবার লগইন করুন।";
    } else {
        $user_id = $_SESSION['user_id'];

        if (empty($title) || empty($event_date) || empty($event_time) || empty($location)) {
            $error_message = "অনুগ্রহ করে শিরোনাম, তারিখ, সময় এবং স্থান পূরণ করুন।";
        } else {
            $sql = "INSERT INTO events (user_id, title, event_date, event_time, location, description) VALUES (?, ?, ?, ?, ?, ?)";

            if ($stmt = $conn->prepare($sql)) {
                $stmt->bind_param("isssss", $user_id, $title, $event_date, $event_time, $location, $description);

                if ($stmt->execute()) {
                   
                    header("Location: events.php?status=added");
                    exit();
                } else {
                    error_log("Error inserting event: " . $stmt->error); 
                    $error_message = "কিছু একটা ভুল হয়েছে। অনুগ্রহ করে আবার চেষ্টা করুন।";
                }
                $stmt->close();
            } else {
                    error_log("Error preparing insert statement: " . $conn->error);
                    $error_message = "ডাটাবেস ত্রুটি। অনুগ্রহ করে আবার চেষ্টা করুন।";
            }
        }
    } 
}
?>

<title>নতুন ইভেন্ট যোগ করুন | ক্যাম্পেইন শিডিউলার</title>

<main class="container mx-auto px-6 py-12 flex-grow">


    <div class="mb-10">
        <h1 class="text-3xl font-extrabold text-gray-900">নতুন ইভেন্ট তৈরি করুন</h1>
        <p class="text-gray-500 mt-1">ইভেন্টের বিবরণ পূরণ করুন এবং সময়সূচীতে যোগ করুন।</p>
    </div>

    <div class="flex flex-col md:flex-row gap-10">

        <!-- Left Column: The Form -->
        <div class="w-full md:w-2/3">
            <div class="bg-white p-8 rounded-2xl shadow-lg">
                <?php
                // Display an error message if one exists.
                if (!empty($error_message)) {
                    echo '<div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-md" role="alert">';
                    echo '<p class="font-bold">ত্রুটি</p>';
                    echo '<p>' . htmlspecialchars($error_message) . '</p>';
                    echo '</div>';
                }
                ?>

                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    <!-- Event Title with Icon -->
                    <div class="mb-5">
                        <label for="title" class="block text-gray-700 text-sm font-bold mb-2">শিরোনাম</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i data-feather="type" class="w-5 h-5 text-gray-400"></i>
                            </div>
                            <input type="text" name="title" id="title" value="<?php echo htmlspecialchars($title); ?>" class="shadow appearance-none border rounded-lg w-full py-3 px-4 pl-10 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500" required>
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
                            <input type="text" name="location" id="location" value="<?php echo htmlspecialchars($location); ?>" class="shadow appearance-none border rounded-lg w-full py-3 px-4 pl-10 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="mb-6">
                        <label for="description" class="block text-gray-700 text-sm font-bold mb-2">বিবরণ</label>
                        <textarea name="description" id="description" rows="5" class="shadow appearance-none border rounded-lg w-full py-3 px-4 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="ইভেন্টের এজেন্ডা, উদ্দেশ্য বা নোট লিখুন..."><?php echo htmlspecialchars($description); ?></textarea>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center justify-end gap-4 border-t pt-6">
                        <a href="events.php" class="text-gray-600 hover:text-blue-600 font-semibold transition">বাতিল করুন</a>
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 px-6 rounded-lg focus:outline-none focus:shadow-outline transition-all transform hover:scale-105 duration-300 shadow-md flex items-center gap-2">
                            <i data-feather="check" class="w-5 h-5"></i>
                            ইভেন্ট সংরক্ষণ করুন
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Right Column: Tips Panel -->
        <div class="w-full md:w-1/3">
            <div class="bg-blue-50 p-6 rounded-2xl border border-blue-200 sticky top-24">
                <div class="flex items-center space-x-3 mb-4">
                    <div class="bg-blue-100 p-2 rounded-full">
                        <i data-feather="info" class="text-blue-600 w-5 h-5"></i>
                    </div>
                    <h3 class="text-lg font-bold text-gray-800">সহায়ক টিপস</h3>
                </div>
                <ul class="list-disc list-outside pl-5 text-sm text-gray-700 space-y-2">
                    <li>একটি স্পষ্ট এবং সংক্ষিপ্ত শিরোনাম দিন যা ইভেন্টের উদ্দেশ্য বর্ণনা করে।</li>
                    <li>সঠিক তারিখ এবং সময় দুবার পরীক্ষা করুন।</li>
                    <li>"স্থান" ক্ষেত্রে একটি সম্পূর্ণ ঠিকানা বা জোন অন্তর্ভুক্ত করুন।</li>
                    <li>বিবরণে এজেন্ডা, বিশেষ অতিথি বা উদ্দেশ্য উল্লেখ করুন।</li>
                </ul>
            </div>
        </div>

    </div>
</main>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        feather.replace(); 
    });
</script>

<?php
require_once 'footer.php';
?>
<?php ob_end_flush(); ?>
