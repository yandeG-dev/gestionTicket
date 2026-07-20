<?php

header('Content-Type: application/json');
include "../db.php";

session_start();

$email = $_POST['email'] ?? '';
$mdp   = $_POST['mdp'] ?? '';

if (!$email || !$mdp) {
    echo json_encode([
        "success" => false,
        "message" => "Email et mot de passe requis"
    ]);
    exit;
}

$stmt = $db->prepare("SELECT * FROM utilisateurs WHERE email = ?");
$stmt->execute([$email]);

$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo json_encode([
        "success" => false,
        "message" => "Email ou mot de passe incorrect"
    ]);
    exit;
}

// Les comptes créés avant le passage au hachage ont leur mot de passe en clair.
// On les accepte une dernière fois, puis on les convertit à la volée : aucun compte
// existant ne casse, et le stockage en clair disparaît dès la première connexion.
$hashStocke = $user['mdp'];

if (password_get_info($hashStocke)['algo']) {
    $motDePasseValide = password_verify($mdp, $hashStocke);
} else {
    $motDePasseValide = hash_equals($hashStocke, $mdp);

    if ($motDePasseValide) {
        $stmtUpgrade = $db->prepare("UPDATE utilisateurs SET mdp = ? WHERE id = ?");
        $stmtUpgrade->execute([password_hash($mdp, PASSWORD_DEFAULT), $user['id']]);
    }
}

if (!$motDePasseValide) {
    // Même message que pour un email inconnu : distinguer les deux cas permettrait
    // d'énumérer les comptes existants.
    echo json_encode([
        "success" => false,
        "message" => "Email ou mot de passe incorrect"
    ]);
    exit;
}

// Nouvel identifiant de session à la connexion (protection contre la fixation de session)
session_regenerate_id(true);

$_SESSION['id']   = $user['id'];
$_SESSION['role'] = $user['role'];
$_SESSION['nom']  = $user['nom'];

echo json_encode([
    "success" => true,
    "message" => "Connexion réussie",
    "user" => [
        "id"     => $user["id"],
        "nom"    => $user["nom"],
        "prenom" => $user["prenom"],
        "email"  => $user["email"],
        "role"   => $user["role"]
    ]
]);
