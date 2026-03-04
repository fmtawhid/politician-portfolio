<?php
// logout.php
session_start();
session_unset();
session_destroy();

header("Location: login_signup.php");
exit();
?>
