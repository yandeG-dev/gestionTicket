<?php
header('Content-Type: application/json');

include "../db.php";
include "../auth/checkRole.php";
include "../ticket/accesTicket.php";

verifierRole(['client', 'agent', 'admin']);

$ticket_id = $_POST['ticket_id'] ?? '';

if (!$ticket_id) {
    echo json_encode([
        "success" => false,
        "message" => "ticket_id est requis."
    ]);
    exit;
}

if (!chargerTicketAutorise($db, $ticket_id)) {
    echo json_encode([
        "success" => false,
        "message" => "Accès non autorisé à ces commentaires."
    ]);
    exit;
}

$stmt = $db->prepare("
    SELECT c.id, c.texteCommentaire, c.ticket_id, c.utilisateur_id, c.dateCommentaire,
           u.nom AS nom_auteur, u.prenom AS prenom_auteur
    FROM commentaire c
    LEFT JOIN utilisateurs u ON u.id = c.utilisateur_id
    WHERE c.ticket_id = ?
    ORDER BY c.id ASC
");
$stmt->execute([$ticket_id]);

$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode([
    "success"      => true,
    "commentaires" => $result
]);
