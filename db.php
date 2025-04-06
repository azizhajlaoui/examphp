<?php
$host = "localhost"; // Change if needed
$dbname = "examphp"; // Use the name of your database
$username = "root"; // Default for XAMPP/MAMP
$password = ""; // Leave empty if using XAMPP, use your password if changed

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}
?>
