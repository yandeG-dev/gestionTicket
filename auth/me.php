<?php

// Permet au client WPF de savoir si son cookie de session est encore valide
// (et de récupérer l'utilisateur courant) sans redemander le mot de passe.
header('Content-Type: application/json');
include "../db.php";

session_start();

if (!isset($_SESSION['id'])) {
    echo json_encode([
        "success" => false,
        "message" => "Utilisateur non connecté"
    ]);
    exit;
}

$stmt = $db->prepare("SELECT id, nom, prenom, email, role FROM utilisateurs WHERE id = ?");
$stmt->execute([$_SESSION['id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo json_encode([
        "success" => false,
        "message" => "Utilisateur non connecté"
    ]);
    exit;
}

echo json_encode([
    "success" => true,
    "user"    => $user
]);
