<?php
session_start();
require 'db.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Préparation de la requête
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        // Connexion réussie
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];

        // Redirection vers tableau de bord ou autre page protégée
        header("Location: index.php");
        exit();
    } else {
        // Identifiants invalides
        $_SESSION['login_error'] = "Email ou mot de passe incorrect.";
        header("Location: login.php");
        exit();
    }
} else {
    // Redirection si accès direct
    header("Location: login.php");
    exit();
}
