<?php
ob_start();
error_reporting(E_ALL); 
ini_set('display_errors', 1); 


require_once 'header.php';

// --- ADMIN ACCESS CHECK ---
// Block if role is not Admin (1) or Super Admin (2)
if (!isset($_SESSION['role']) || $_SESSION['role'] < 1) {
    echo "<main class='container mx-auto px-6 py-12 flex-grow'><p class='text-red-500'>আপনার এই পৃষ্ঠাটি দেখার অনুমতি নেই।</p></main>";
    require_once 'footer.php';
    exit;
}


$admin_role = $_SESSION['role'];
$admin_id = $_SESSION['user_id'];


$action_message = "";
$error_message = ""; 

if (isset($_SESSION['action_message'])) {
    $action_message = $_SESSION['action_message'];
    unset($_SESSION['action_message']);
}
if (isset($_SESSION['error_message'])) {
    $error_message = $_SESSION['error_message'];
    unset($_SESSION['error_message']);
}

// --- HANDLE ADMIN ACTIONS ---
$action_taken = false; // Flag to track if we need to redirect

if ($_SERVER["REQUEST_METHOD"] == "GET") {

    // --- Approve/Deny Logic (All Admins) ---
    if (isset($_GET['approve_id'])) {
        $action_taken = true;
        $user_id_to_action = (int)$_GET['approve_id'];
        
        // Fetch username
        $username_to_action = 'অজানা ব্যবহারকারী';
        $stmt_get_user = $conn->prepare("SELECT username FROM users WHERE id = ?");
        $stmt_get_user->bind_param("i", $user_id_to_action);
        if ($stmt_get_user->execute()) {

            $stmt_get_user->bind_result($username_from_db);
            if ($stmt_get_user->fetch()) {
                $username_to_action = $username_from_db;
            }
           
        }
        $stmt_get_user->close();

        $stmt = $conn->prepare("UPDATE users SET status = 'approved' WHERE id = ?");
        $stmt->bind_param("i", $user_id_to_action);
        if ($stmt->execute()) {
            $_SESSION['action_message'] = "ব্যবহারকারী '" . htmlspecialchars($username_to_action) . "' কে সফলভাবে অনুমোদন করা হয়েছে।";
        } else {
            $_SESSION['error_message'] = "ব্যবহারকারী '" . htmlspecialchars($username_to_action) . "' অনুমোদনের সময় ত্রুটি হয়েছে: " . $conn->error;
        }
        $stmt->close();

    } elseif (isset($_GET['deny_id'])) {
        $action_taken = true;
        $user_id_to_action = (int)$_GET['deny_id'];
        
        $username_to_action = 'অজানা ব্যবহারকারী';
        $stmt_get_user = $conn->prepare("SELECT username FROM users WHERE id = ?");
        $stmt_get_user->bind_param("i", $user_id_to_action);
        if ($stmt_get_user->execute()) {
            $stmt_get_user->bind_result($username_from_db);
            if ($stmt_get_user->fetch()) {
                $username_to_action = $username_from_db;
            }
           
        }
        $stmt_get_user->close();

        
        $stmt = $conn->prepare("UPDATE users SET status = 'pending', role = 0 WHERE id = ?");
        $stmt->bind_param("i", $user_id_to_action);
         if ($stmt->execute()) {
            $_SESSION['action_message'] = "ব্যবহারকারী '" . htmlspecialchars($username_to_action) . "' -এর অনুমোদন বাতিল করা হয়েছে।";
        } else {
            $_SESSION['error_message'] = "ব্যবহারকারী '" . htmlspecialchars($username_to_action) . "' -এর অনুমোদন বাতিলের সময় ত্রুটি হয়েছে: " . $conn->error;
        }
        $stmt->close();
    }

    if ($admin_role == 2) { // Only Super Admin
        if (isset($_GET['promote_id'])) {
            $action_taken = true;
            $user_id_to_action = (int)$_GET['promote_id'];
            if ($user_id_to_action != $admin_id) { // Cannot promote self
                
               
                $username_to_action = 'অজানা ব্যবহারকারী';
                $stmt_get_user = $conn->prepare("SELECT username FROM users WHERE id = ? AND role = 0");
                $stmt_get_user->bind_param("i", $user_id_to_action);
                if ($stmt_get_user->execute()) {
                    
                    $stmt_get_user->bind_result($username_from_db);
                    if ($stmt_get_user->fetch()) {
                        $username_to_action = $username_from_db;
                    }
                }
                $stmt_get_user->close();

                $stmt = $conn->prepare("UPDATE users SET role = 1 WHERE id = ? AND role = 0");
                $stmt->bind_param("i", $user_id_to_action);
                if ($stmt->execute() && $stmt->affected_rows > 0) {
                    $_SESSION['action_message'] = "ব্যবহারকারী '" . htmlspecialchars($username_to_action) . "' কে অ্যাডমিন হিসাবে সফলভাবে উন্নীত করা হয়েছে।";
                } elseif ($stmt->affected_rows == 0) {
                     $_SESSION['error_message'] = "ব্যবহারকারী '" . htmlspecialchars($username_to_action) . "' কে উন্নীত করা যায়নি (হয়তো ইতিমধ্যে অ্যাডমিন বা খুঁজে পাওয়া যায়নি)।";
                } else {
                    $_SESSION['error_message'] = "ব্যবহারকারী '" . htmlspecialchars($username_to_action) . "' কে উন্নীত করার সময় ত্রুটি হয়েছে: " . $conn->error;
                }
                $stmt->close();
            }
        } elseif (isset($_GET['demote_id'])) {
            $action_taken = true;
            $user_id_to_action = (int)$_GET['demote_id'];
             if ($user_id_to_action != $admin_id) { 
                
                $username_to_action = 'অজানা ব্যবহারকারী';
                $stmt_get_user = $conn->prepare("SELECT username FROM users WHERE id = ? AND role = 1"); 
                $stmt_get_user->bind_param("i", $user_id_to_action);
                if ($stmt_get_user->execute()) {
                    $stmt_get_user->bind_result($username_from_db);
                    if ($stmt_get_user->fetch()) {
                        $username_to_action = $username_from_db;
                    }
                   
                }
                $stmt_get_user->close();

                $stmt = $conn->prepare("UPDATE users SET role = 0 WHERE id = ? AND role = 1");
                $stmt->bind_param("i", $user_id_to_action);
                 if ($stmt->execute() && $stmt->affected_rows > 0) {
                    $_SESSION['action_message'] = "ব্যবহারকারী '" . htmlspecialchars($username_to_action) . "' কে সফলভাবে অ্যাডমিন থেকে নামিয়ে দেওয়া হয়েছে।";
                } elseif ($stmt->affected_rows == 0) {
                    $_SESSION['error_message'] = "ব্যবহারকারী '" . htmlspecialchars($username_to_action) . "' কে নামিয়ে দেওয়া যায়নি (হয়তো ইতিমধ্যে ব্যবহারকারী বা খুঁজে পাওয়া যায়নি)।";
                } else {
                    $_SESSION['error_message'] = "ব্যবহারকারী '" . htmlspecialchars($username_to_action) . "' কে নামিয়ে দেওয়ার সময় ত্রুটি হয়েছে: " . $conn->error;
                }
                $stmt->close();
            }
        }
    }

   
    if (isset($_GET['revoke_id'])) {
        $action_taken = true;
        $user_id_to_action = (int)$_GET['revoke_id'];
        $can_revoke = false;
        $username_to_action = 'অজানা ব্যবহারকারী';
        $user_to_action = null;
        $stmt_check = $conn->prepare("SELECT role, username FROM users WHERE id = ?");
        $stmt_check->bind_param("i", $user_id_to_action);
        if ($stmt_check->execute()) {
            $stmt_check->bind_result($role_from_db, $username_from_db);
            if ($stmt_check->fetch()) {
                $user_to_action = ['role' => $role_from_db, 'username' => $username_from_db];
                $username_to_action = $username_from_db;
            }
        }
        // --- End Fix ---
        $stmt_check->close();

        if ($user_to_action && $user_id_to_action != $admin_id) {
            if ($admin_role == 2 && $user_to_action['role'] < 2) { 
                // Super Admin can revoke Admins and Users
                $can_revoke = true;
            } elseif ($admin_role == 1 && $user_to_action['role'] == 0) {
                // Admin can only revoke Users
                $can_revoke = true;
            }
        }

        if ($can_revoke) {
            // Revoking sets status to 'pending' and role to 0 (user)
            $stmt = $conn->prepare("UPDATE users SET status = 'pending', role = 0 WHERE id = ?");
            $stmt->bind_param("i", $user_id_to_action);
             if ($stmt->execute()) {
                $_SESSION['action_message'] = "ব্যবহারকারী '" . htmlspecialchars($username_to_action) . "' -এর অ্যাক্সেস সফলভাবে প্রত্যাহার করা হয়েছে।";
            } else {
                $_SESSION['error_message'] = "ব্যবহারকারী '" . htmlspecialchars($username_to_action) . "' -এর অ্যাক্সেস প্রত্যাহারের সময় ত্রুটি হয়েছে: " . $conn->error;
            }
            $stmt->close();
        } elseif ($user_id_to_action == $admin_id) {
             $_SESSION['error_message'] = "আপনি নিজের অ্যাক্সেস প্রত্যাহার করতে পারবেন না।";
        } else {
             $_SESSION['error_message'] = "আপনার এই ব্যবহারকারীর অ্যাক্সেস প্রত্যাহার করার অনুমতি নেই।";
        }
    }


    // --- Delete User Logic (Admins) ---
    if (isset($_GET['delete_user_id'])) {
        $action_taken = true;
        $user_id_to_delete = (int)$_GET['delete_user_id'];
        $can_delete = false;
        $username_to_delete = 'অজানা ব্যবহারকারী';
        $user_to_delete = null; 

        $stmt_check = $conn->prepare("SELECT role, username FROM users WHERE id = ?");
        $stmt_check->bind_param("i", $user_id_to_delete);
        
       if ($stmt_check->execute()) {
            $stmt_check->bind_result($role_from_db, $username_from_db);
            if ($stmt_check->fetch()) {
                $user_to_delete = ['role' => $role_from_db, 'username' => $username_from_db];
                $username_to_delete = $username_from_db;
            }
        }
        $stmt_check->close();

        if ($user_to_delete && $user_id_to_delete != $admin_id) {
            if ($admin_role == 2 && $user_to_delete['role'] < 2) { 
                $can_delete = true;
            } elseif ($admin_role == 1 && $user_to_delete['role'] == 0) {
                // Admin can only delete Users
                $can_delete = true;
            }
        }

        if ($can_delete) {

            $conn->begin_transaction();
            try {
                // Delete events created by the user
                
                $stmt_del_events = $conn->prepare("DELETE FROM events WHERE user_id = ?");
                $stmt_del_events->bind_param("i", $user_id_to_delete);
                $stmt_del_events->execute();
                $stmt_del_events->close();

                // delete the user
                $stmt_del_user = $conn->prepare("DELETE FROM users WHERE id = ?");
                $stmt_del_user->bind_param("i", $user_id_to_delete);
                $stmt_del_user->execute();
                $stmt_del_user->close();

                $conn->commit();
                $_SESSION['action_message'] = "ব্যবহারকারী '" . htmlspecialchars($username_to_delete) . "' এবং তাদের সমস্ত ইভেন্ট সফলভাবে মুছে ফেলা হয়েছে।";

            } catch (mysqli_sql_exception $exception) {
                $conn->rollback();
                $_SESSION['error_message'] = "ব্যবহারকারী '" . htmlspecialchars($username_to_delete) . "' মুছে ফেলার সময় একটি ত্রুটি ঘটেছে: " . $exception->getMessage();
                error_log("Error deleting user ID " . $user_id_to_delete . ": " . $exception->getMessage());
            }

        } elseif ($user_id_to_delete == $admin_id) {
            $_SESSION['error_message'] = "আপনি নিজেকে মুছে ফেলতে পারবেন না।";
        } else {
            $_SESSION['error_message'] = "আপনার এই ব্যবহারকারীকে মুছে ফেলার অনুমতি নেই।";
        }
    }

    if ($action_taken) {
        header("Location: admin_approval.php");
        exit;
    }
}


// Super Admins see everyone. Admins see everyone except other Admins/Super Admins.
$sql_fetch_users = "SELECT id, username, role, status FROM users";
if ($admin_role == 1) { // If regular Admin
    $sql_fetch_users .= " WHERE role = 0"; // Only show regular users
}
$sql_fetch_users .= " ORDER BY role DESC, status, username";


$users_result = $conn->query($sql_fetch_users);
$users_list = [];
if ($users_result) {
    while ($row = $users_result->fetch_assoc()) {
        $users_list[] = $row;
    }
} else {
    if (empty($error_message)) {
        $error_message = "ব্যবহারকারীদের তালিকা আনতে ত্রুটি হয়েছে: " . $conn->error;
    }
}

?>

<title>ব্যবহারকারী ব্যবস্থাপনা | ক্যাম্পেইন শিডিউলার</title>

<main class="container mx-auto px-6 py-12 flex-grow">
    <h1 class="text-3xl font-extrabold text-gray-900 mb-6">ব্যবহারকারী ব্যবস্থাপনা</h1>

    <!-- Action Messages -->
    <?php if ($action_message): ?>
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-md" role="alert">
            <p><?php echo htmlspecialchars($action_message); ?></p>
        </div>
    <?php endif; ?>
    <?php if ($error_message): ?>
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-md" role="alert">
            <p><?php echo htmlspecialchars($error_message); ?></p>
        </div>
    <?php endif; ?>


    <!-- Users Table -->
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
        <div class="px-6 py-4 border-b">
            <h2 class="text-xl font-bold text-gray-800">ব্যবহারকারীর তালিকা</h2>
            <p class="text-gray-500 mt-1">ব্যবহারকারীদের অনুমোদন, ভূমিকা পরিবর্তন বা মুছে ফেলুন।</p>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ব্যবহারকারীর নাম</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ভূমিকা</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">স্ট্যাটাস</th>
                        <th scope="col" class="relative px-6 py-3">
                            <span class="sr-only">পদক্ষেপ</span>
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($users_list as $user): ?>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo htmlspecialchars($user['username']); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?php
                                    if ($user['role'] == 2) {
                                        echo '<span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">সুপার অ্যাডমিন</span>';
                                    } elseif ($user['role'] == 1) {
                                        echo '<span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">অ্যাডমিন</span>';
                                    } else {
                                        echo '<span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">ব্যবহারকারী</span>';
                                    }
                                ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <?php if ($user['status'] == 'approved'): ?>
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">অনুমোদিত</span>
                                <?php else: ?>
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">বিচারাধীন</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <?php // --- Action: Approve/Deny --- ?>
                                <?php if ($user['status'] == 'pending'): ?>
                                    <a href="admin_approval.php?approve_id=<?php echo $user['id']; ?>" class="text-green-600 hover:text-green-900" title="অনুমোদন করুন">অনুমোদন</a>
                                <?php endif; ?>
                                
                                <?php 
                                ?>
                                <?php if ($admin_role == 2 && $user['id'] != $admin_id && $user['status'] == 'approved'): ?>
                                    <?php if ($user['role'] == 0): ?>
                                        <a href="admin_approval.php?promote_id=<?php echo $user['id']; ?>" class="ml-4 text-blue-600 hover:text-blue-900" title="অ্যাডমিন বানান">প্রমোট</a>
                                    <?php elseif ($user['role'] == 1): ?>
                                        <a href="admin_approval.php?demote_id=<?php echo $user['id']; ?>" class="ml-4 text-gray-600 hover:text-gray-900" title="ব্যবহারকারী বানান">ডিমোট</a>
                                    <?php endif; ?>
                                <?php endif; ?>

                                <?php
                                ?>
                                <?php
                                    $can_revoke_this_user = false;
                                    if ($user['status'] == 'approved' && $user['id'] != $admin_id) { // Cannot revoke self
                                        if ($admin_role == 2 && $user['role'] < 2) { 
                                        // Super Admin can revoke Admins and Users
                                            $can_revoke_this_user = true;
                                        } elseif ($admin_role == 1 && $user['role'] == 0) {
                                            // Admin can only revoke Users
                                            $can_revoke_this_user = true;
                                        }
                                    }
                                ?>
                                <?php if ($can_revoke_this_user): ?>
                                    
                                    <button type="button" class="ml-4 text-yellow-600 hover:text-yellow-900 modal-trigger-btn"
                                        data-action-url="admin_approval.php?revoke_id=<?php echo $user['id']; ?>"
                                        data-modal-title="অ্যাক্সেস প্রত্যাহার করুন"
                                        data-modal-text="আপনি কি নিশ্চিত যে আপনি এই ব্যবহারকারীর অ্যাক্সেস প্রত্যাহার করতে চান?"
                                        data-modal-confirm-text="প্রত্যাহার করুন"
                                        data-modal-color="yellow"
                                        title="অ্যাক্সেস প্রত্যাহার করুন">
                                        অ্যাক্সেস প্রত্যাহার করুন
                                    </button>
                                <?php endif; ?>

                                <?php // --- Action: Delete User --- ?>
                                <?php
                                    $can_delete_this_user = false;
                                    if ($user['id'] != $admin_id) { 
                                        // Cannot delete self
                                        if ($admin_role == 2 && $user['role'] < 2) { 
                                        // Super Admin can delete Admins and Users
                                            $can_delete_this_user = true;
                                        } elseif ($admin_role == 1 && isset($user_to_delete['role']) && $user_to_delete['role'] == 0) { 
                                            // Admin can only delete Users
                                            
                                            $can_delete_this_user = true;
                                        }
                                    }
                                ?>
                                <?php if ($can_delete_this_user): ?>

                                    <button type="button" class="ml-4 text-red-600 hover:text-red-900 modal-trigger-btn"
                                        data-action-url="admin_approval.php?delete_user_id=<?php echo $user['id']; ?>"
                                        data-modal-title="ব্যবহারকারী মুছে ফেলুন"
                                        data-modal-text="আপনি কি নিশ্চিত যে আপনি এই ব্যবহারকারীকে মুছে ফেলতে চান? এটি স্থায়ীভাবে ব্যবহারকারীর সমস্ত ইভেন্ট মুছে ফেলবে।"
                                        data-modal-confirm-text="মুছে ফেলুন"
                                        data-modal-color="red"
                                        title="ব্যবহারকারী মুছে ফেলুন">
                                        মুছে ফেলুন
                                    </button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Custom Confirmation Modal -->
    <div id="custom-modal" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background overlay -->
            <div id="custom-modal-overlay" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>

            <!-- Modal panel -->
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div id="custom-modal-icon-bg" class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                            <i id="custom-modal-icon" data-feather="alert-triangle" class="h-6 w-6 text-red-600"></i>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="custom-modal-title">
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500" id="custom-modal-text">
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button id="custom-modal-confirm-btn" type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                        <!-- Text will be set by JS -->
                    </button>
                    <button id="custom-modal-cancel-btn" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        বাতিল
                    </button>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('custom-modal');
    if (!modal) return;

    const confirmBtn = document.getElementById('custom-modal-confirm-btn');
    const cancelBtn = document.getElementById('custom-modal-cancel-btn');
    const modalOverlay = document.getElementById('custom-modal-overlay');
    const modalTitle = document.getElementById('custom-modal-title');
    const modalText = document.getElementById('custom-modal-text');
    const modalIconBg = document.getElementById('custom-modal-icon-bg');
    const modalIcon = document.getElementById('custom-modal-icon');
    let actionUrl = '';

    const iconColors = {
        red: {
            bg: 'bg-red-100',
            text: 'text-red-600'
        },
        yellow: {
            bg: 'bg-yellow-100',
            text: 'text-yellow-600'
        }
    };

    const btnColors = {
        red: {
            bg: 'bg-red-600',
            hover: 'hover:bg-red-700',
            focus: 'focus:ring-red-500'
        },
        yellow: {
            bg: 'bg-yellow-600',
            hover: 'hover:bg-yellow-700',
            focus: 'focus:ring-yellow-500'
        }
    };

    document.querySelectorAll('.modal-trigger-btn').forEach(button => {
        button.addEventListener('click', () => {
            actionUrl = button.dataset.actionUrl;
            const title = button.dataset.modalTitle;
            const text = button.dataset.modalText;
            const confirmText = button.dataset.modalConfirmText;
            const color = button.dataset.modalColor || 'red'; // Default to red

            // Set content
            modalTitle.textContent = title;
            modalText.textContent = text;
            confirmBtn.textContent = confirmText;

            Object.values(iconColors).forEach(c => {
                modalIconBg.classList.remove(c.bg);
                modalIcon.classList.remove(c.text);
            });
            Object.values(btnColors).forEach(c => {
                confirmBtn.classList.remove(c.bg, c.hover, c.focus);
            });

            if (iconColors[color]) {
                modalIconBg.classList.add(iconColors[color].bg);
                modalIcon.classList.add(iconColors[color].text);
            }
             if (btnColors[color]) {
                confirmBtn.classList.add(btnColors[color].bg, btnColors[color].hover, btnColors[color].focus);
            }

            // Show modal
            modal.classList.remove('hidden');
            
            // Re-render icons if they are in the modal
            if (typeof feather !== 'undefined') {
                feather.replace();
            }
        });
    });

    const closeModal = () => {
        modal.classList.add('hidden');
        actionUrl = '';
    };

    cancelBtn.addEventListener('click', closeModal);
    modalOverlay.addEventListener('click', closeModal);

    confirmBtn.addEventListener('click', () => {
        if (actionUrl) {
            window.location.href = actionUrl; 
        }
    });
});
</script>

<?php
require_once 'footer.php';
ob_end_flush(); 
?>
