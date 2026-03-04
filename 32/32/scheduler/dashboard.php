<?php
session_start();

$timeout_duration = 1800; 

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("Location: login_signup.php");
    exit;
}

if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $timeout_duration) {
    session_unset();
    session_destroy();
    header("Location: login_signup.php?expired=1");
    exit;
}

$_SESSION['last_activity'] = time();

require_once 'db_connect.php'; 
require_once 'header.php';

$stats = [
    'upcoming' => 0,
    'total_events' => 0,
    'total_users' => 0
];

// UPCOMING EVENTS COUNT
$sql_upcoming_count = "SELECT COUNT(id) FROM events WHERE CONCAT(event_date, ' ', event_time) > NOW()";
if ($stmt_uc = $conn->prepare($sql_upcoming_count)) {
    if ($stmt_uc->execute()) {
        $stmt_uc->bind_result($upcoming_count);
        if ($stmt_uc->fetch()) {
            $stats['upcoming'] = $upcoming_count;
        }
    } else {
        error_log("Error executing upcoming count statement: " . $stmt_uc->error);
    }
    $stmt_uc->close();
} else {
    error_log("Error preparing upcoming count statement: " . $conn->error);
}

// TOTAL EVENTS COUNT
$sql_total_count = "SELECT COUNT(id) FROM events";
if ($stmt_tc = $conn->prepare($sql_total_count)) {
    if ($stmt_tc->execute()) {
        $stmt_tc->bind_result($total_count);
        if ($stmt_tc->fetch()) {
            $stats['total_events'] = $total_count;
        }
    } else {
        error_log("Error executing total count statement: " . $stmt_tc->error);
    }
    $stmt_tc->close();
} else {
    error_log("Error preparing total count statement: " . $conn->error);
}

// TOTAL APPROVED USERS COUNT 
$sql_user_count = "SELECT COUNT(id) FROM users WHERE status = 'approved'";
if ($stmt_usrc = $conn->prepare($sql_user_count)) {
    if ($stmt_usrc->execute()) {
        $stmt_usrc->bind_result($user_count);
        if ($stmt_usrc->fetch()) {
            $stats['total_users'] = $user_count;
        }
    } else {
        error_log("Error executing user count statement: " . $stmt_usrc->error);
    }
    $stmt_usrc->close();
} else {
    error_log("Error preparing user count statement: " . $conn->error);
}

// FETCH UPCOMING EVENTS 
$upcoming_events = [];

$sql_events = "SELECT id, title, event_date, event_time, location 
               FROM events 
               WHERE CONCAT(event_date, ' ', event_time) > NOW() 
               ORDER BY event_date ASC, event_time ASC 
               LIMIT 5";

if ($stmt_events = $conn->prepare($sql_events)) {
    if ($stmt_events->execute()) {
        $result = $stmt_events->get_result();
        while ($row = $result->fetch_assoc()) {
            $upcoming_events[] = $row;
        }
    } else {
        error_log("Error executing upcoming events statement: " . $stmt_events->error);
    }
    $stmt_events->close();
} else {
    error_log("Error preparing upcoming events statement: " . $conn->error);
}

// CLOSE CONNECTION
$conn->close();
?>


<main class="flex-1 p-6 bg-gray-100">

    <div class="mb-8">
        <h1 class="text-3xl font-extrabold text-gray-800">স্বাগতম, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
        <p class="text-gray-600">আপনার ক্যাম্পেইনের একটি সারসংক্ষেপ নিচে দেওয়া হলো।</p>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <!-- Upcoming Events Card -->
        <div class="bg-white p-6 rounded-lg shadow-lg border-l-4 border-blue-500">
            <div class="flex items-center">
                <div class="p-3 bg-blue-100 rounded-full">
                    <i data-feather="calendar" class="text-blue-600"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-gray-500 uppercase font-medium">আসন্ন ইভেন্ট</h3>
                    <p class="text-3xl font-bold text-gray-800"><?php echo htmlspecialchars($stats['upcoming']); ?></p>
                </div>
            </div>
        </div>
        <!-- Total Events Card -->
        <div class="bg-white p-6 rounded-lg shadow-lg border-l-4 border-green-500">
            <div class="flex items-center">
                <div class="p-3 bg-green-100 rounded-full">
                    <i data-feather="archive" class="text-green-600"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-gray-500 uppercase font-medium">মোট ইভেন্ট</h3>
                    <p class="text-3xl font-bold text-gray-800"><?php echo htmlspecialchars($stats['total_events']); ?></p>
                </div>
            </div>
        </div>
        <!-- Total Users Card -->
        <div class="bg-white p-6 rounded-lg shadow-lg border-l-4 border-purple-500">
            <div class="flex items-center">
                <div class="p-3 bg-purple-100 rounded-full">
                    <i data-feather="users" class="text-purple-600"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-gray-500 uppercase font-medium">অনুমোদিত ব্যবহারকারী</h3>
                    <p class="text-3xl font-bold text-gray-800"><?php echo htmlspecialchars($stats['total_users']); ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Upcoming Events List -->
    <div class="bg-white p-6 rounded-lg shadow-lg">
        <h2 class="text-xl font-bold text-gray-800 mb-4">শীর্ষ ৫টি আসন্ন ইভেন্ট</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ইভেন্ট</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">স্থান</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">তারিখ ও সময়</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">কাউন্টডাউন</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if (empty($upcoming_events)): ?>
                        <tr>
                            <td colspan="4" class="px-6 py-4 whitespace-nowrap text-center text-gray-500">কোনো আসন্ন ইভেন্ট পাওয়া যায়নি।</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($upcoming_events as $event): 
                            $event_datetime = $event['event_date'] . ' ' . $event['event_time'];
                            $date_obj = new DateTime($event_datetime);
                            
                           $formatted_datetime = $date_obj->format('F j, Y, g:i A'); 
                            $iso_datetime = $date_obj->format('Y-m-d\TH:i:s');
                        ?>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($event['title']); ?></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-600"><?php echo htmlspecialchars($event['location']); ?></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-600"><?php echo htmlspecialchars($formatted_datetime); ?></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="countdown-timer text-sm text-gray-900" data-datetime="<?php echo $iso_datetime; ?>">
                                    লোড হচ্ছে...
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</main>

<?php

if (file_exists('footer.php')) {
    include_once 'footer.php';
}
?>

<script>
    function toBangla(en_num) {
        const bangla_digits = ['০', '১', '২', '৩', '৪', '৫', '৬', '৭', '৮', '৯'];
        return String(en_num).replace(/\d/g, (d) => bangla_digits[d]);
    }

    const timers = document.querySelectorAll('.countdown-timer');
    const activeTimers = [];

  
     activeTimers.forEach(timerInfo => clearInterval(timerInfo.id));
     activeTimers.length = 0; // Clear the array

    timers.forEach(timer => {
        const targetDate = new Date(timer.dataset.datetime).getTime();

        function updateTimer() {
            const now = new Date().getTime();
            const distance = targetDate - now;

            if (distance < 0) {
                clearInterval(intervalId);
                timer.innerHTML = "<span class='text-red-500 font-medium'>ইভেন্ট শেষ</span>";
                return;
            }

            const days = Math.floor(distance / (1000 * 60 * 60 * 24));
            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);

            timer.innerHTML = `
                <div class="grid grid-cols-4 gap-2 text-center">
                    <div>
                        <div class="text-3xl font-bold text-gray-800">${toBangla(days)}</div>
                        <div class="text-xs text-gray-500 uppercase">দিন</div>
                    </div>
                    <div>
                        <div class="text-3xl font-bold text-gray-800">${toBangla(hours)}</div>
                        <div class="text-xs text-gray-500 uppercase">ঘন্টা</div>
                    </div>
                    <div>
                        <div class="text-3xl font-bold text-gray-800">${toBangla(minutes)}</div>
                        <div class="text-xs text-gray-500 uppercase">মিনিট</div>
                    </div>
                    <div>
                        <div class="text-3xl font-bold text-gray-800">${toBangla(seconds)}</div>
                        <div class="text-xs text-gray-500 uppercase">সেকেন্ড</div>
                    </div>
                </div>`;
        }

        updateTimer();
        const intervalId = setInterval(updateTimer, 1000);
        activeTimers.push({ id: intervalId, element: timer });
    });

     window.addEventListener('beforeunload', () => {
        activeTimers.forEach(timerInfo => clearInterval(timerInfo.id));
    });
</script>
