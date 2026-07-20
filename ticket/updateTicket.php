<?php

header('Content-Type: application/json');

include "../db.php";
include "../auth/checkRole.php";
include "../historique/enregistrerHistorique.php";
include "./valeursTicket.php";

verifierRole(['client', 'admin']);

$id          = $_POST['id'] ?? '';
$titre       = $_POST['titre'] ?? '';
$description = $_POST['description'] ?? '';
$categorie   = $_POST['categorie'] ?? '';
$priorite    = $_POST['priorite'] ?? '';

if (!$id || !$titre || !$description || !$categorie || !$priorite) {
    echo json_encode([
        "success" => false,
        "message" => "Tous les champs sont requis."
    ]);
    exit;
}

$erreur = erreurValeursTicket($categorie, $priorite);

if ($erreur) {
    echo json_encode([
        "success" => false,
        "message" => $erreur
    ]);
    exit;
}

$stmt = $db->prepare("SELECT id_createur FROM ticket WHERE id = ?");
$stmt->execute([$id]);
$ticket = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$ticket) {
    echo json_encode([
        "success" => false,
        "message" => "Ticket introuvable."
    ]);
    exit;
}

if ($_SESSION['role'] == 'client' && $ticket['id_createur'] != $_SESSION['id']) {
    echo json_encode([
        "success" => false,
        "message" => "Vous ne pouvez modifier que vos propres tickets."
    ]);
    exit;
}

// Le statut passe par changeStatus.php et la date de création n'est pas modifiable :
// cet endpoint ne touche qu'au contenu décrit par le demandeur.
$stmt = $db->prepare("
    UPDATE ticket
    SET titre = ?, description = ?, categorie = ?, priorite = ?
    WHERE id = ?
");

$result = $stmt->execute([
    $titre,
    $description,
    $categorie,
    $priorite,
    $id
]);

if ($result) {
    enregistrerHistorique($db, $id, $_SESSION['id'], "Ticket modifié");
}

echo json_encode([
    "success" => $result
]);
