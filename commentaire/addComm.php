<?php
header('Content-Type: application/json');

include "../db.php";
include "../auth/checkRole.php";
include "../ticket/accesTicket.php";

verifierRole(['client', 'agent', 'admin']);

$ticket_id        = $_POST['ticket_id'] ?? '';
$texteCommentaire = $_POST['texteCommentaire'] ?? '';

// On enregistre toujours au nom de l'utilisateur connecté (pas besoin de le passer en POST)
$utilisateur_id = $_SESSION['id'];

if (!$ticket_id || !$texteCommentaire) {
    echo json_encode([
        "success" => false,
        "message" => "ticket_id et texteCommentaire sont requis."
    ]);
    exit;
}

// Sans ce contrôle, n'importe quel utilisateur connecté pouvait commenter n'importe
// quel ticket en devinant son identifiant.
if (!chargerTicketAutorise($db, $ticket_id)) {
    echo json_encode([
        "success" => false,
        "message" => "Accès non autorisé à ce ticket."
    ]);
    exit;
}

$stmt = $db->prepare("
    INSERT INTO commentaire (texteCommentaire, ticket_id, utilisateur_id, dateCommentaire)
    VALUES (?, ?, ?, CURDATE())
");
$result = $stmt->execute([$texteCommentaire, $ticket_id, $utilisateur_id]);

echo json_encode([
    "success" => $result
]);
