<?php
$host = 'localhost';
$db   = 'Nomanhaque24_main';
$user = 'Nomanhaque24_admin';      
$pass = 'Noman45@@@1';     
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    // In production, log this error to a file instead of showing it
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}
?>