<?php

header('Content-Type: application/json');
include "../db.php";


session_start();


$email = $_POST['email'];
$mdp = $_POST['mdp'];


$stmt = $db->prepare("SELECT * FROM utilisateurs WHERE email = ?");
$stmt->execute([$email]);


$user = $stmt->fetch(PDO::FETCH_ASSOC);


if (!$user) {
    echo json_encode([
        "success" => false,
        "message" => "Email incorrect"
    ]);
    exit;
}


if ($user['mdp'] != $mdp) {
    echo json_encode([
        "success" => false,
        "message" => "Mot de passe incorrect"
    ]);
    exit;
}


// Création de la session
$_SESSION['id'] = $user['id'];
$_SESSION['role'] = $user['role'];
$_SESSION['nom'] = $user['nom'];


echo json_encode([
    "success" => true,
    "message" => "Connexion réussie",
    "user" => [
        "id" => $user["id"],
        "nom" => $user["nom"],
        "prenom" => $user["prenom"],
        "email" => $user["email"],
        "role" => $user["role"]
    ]
]);