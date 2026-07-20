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
        "message" => "Accès non autorisé à cet historique."
    ]);
    exit;
}

$stmt = $db->prepare("
    SELECT h.id, h.action, h.ticket_id, h.utilisateur_id, h.dateAction,
           u.nom AS nom_utilisateur, u.prenom AS prenom_utilisateur
    FROM historique h
    LEFT JOIN utilisateurs u ON u.id = h.utilisateur_id
    WHERE h.ticket_id = ?
    ORDER BY h.id DESC
");
$stmt->execute([$ticket_id]);

$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode([
    "success"    => true,
    "historique" => $result
]);
