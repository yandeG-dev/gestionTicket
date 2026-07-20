<?php
header('Content-Type: application/json');
include "../db.php";
include "../auth/checkRole.php";
include "../historique/enregistrerHistorique.php";
include "./valeursTicket.php";

verifierRole(['client']);

$titre       = $_POST['titre'] ?? '';
$description = $_POST['description'] ?? '';
$categorie   = $_POST['categorie'] ?? '';
$priorite    = $_POST['priorite'] ?? '';

if (!$titre || !$description || !$categorie || !$priorite) {
    echo json_encode([
        "success" => false,
        "message" => "Titre, description, catégorie et priorité sont requis."
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

$id_createur = $_SESSION['id'];

// id_assigne reste NULL : le ticket part dans la file non affectée, l'admin le distribue.
// C'était la cause de l'échec systématique des créations (FK vers utilisateurs(id) sur un 0).
// status et dateCreation sont imposés par le serveur : un ticket naît toujours "Ouvert" aujourd'hui.
$stmt = $db->prepare("
    INSERT INTO ticket (titre, description, categorie, priorite, status, dateCreation, id_createur, id_assigne)
    VALUES (?, ?, ?, ?, 'Ouvert', CURDATE(), ?, NULL)
");

$result = $stmt->execute([
    $titre,
    $description,
    $categorie,
    $priorite,
    $id_createur
]);

if (!$result) {
    echo json_encode([
        "success" => false,
        "message" => "Création du ticket impossible."
    ]);
    exit;
}

$ticket_id = $db->lastInsertId();

enregistrerHistorique($db, $ticket_id, $id_createur, "Ticket créé");

echo json_encode([
    'success'   => true,
    'ticket_id' => (int) $ticket_id
]);
?>
