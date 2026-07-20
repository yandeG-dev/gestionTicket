<?php
header('Content-Type: application/json');

include "../db.php";
include "../auth/checkRole.php";
include "../historique/enregistrerHistorique.php";
include "./valeursTicket.php";

// Endpoint distinct de updateTicket.php : faire avancer un ticket est le métier de
// l'agent, alors que modifier le contenu du ticket appartient au demandeur.
verifierRole(['agent', 'admin']);

$ticket_id = $_POST['ticket_id'] ?? '';
$status    = $_POST['status'] ?? '';

$valides = valeursTicketValides();

if (!$ticket_id || !$status) {
    echo json_encode([
        "success" => false,
        "message" => "ticket_id et status sont requis."
    ]);
    exit;
}

if (!in_array($status, $valides['status'], true)) {
    echo json_encode([
        "success" => false,
        "message" => "Statut invalide. Valeurs attendues : " . implode(', ', $valides['status'])
    ]);
    exit;
}

$stmt = $db->prepare("SELECT status, id_assigne FROM ticket WHERE id = ?");
$stmt->execute([$ticket_id]);
$ticket = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$ticket) {
    echo json_encode([
        "success" => false,
        "message" => "Ticket introuvable."
    ]);
    exit;
}

if ($_SESSION['role'] == 'agent' && $ticket['id_assigne'] != $_SESSION['id']) {
    echo json_encode([
        "success" => false,
        "message" => "Vous ne pouvez modifier que les tickets qui vous sont assignés."
    ]);
    exit;
}

$ancienStatut = $ticket['status'];

if ($ancienStatut === $status) {
    echo json_encode([
        "success" => true,
        "message" => "Statut inchangé."
    ]);
    exit;
}

$stmt = $db->prepare("UPDATE ticket SET status = ? WHERE id = ?");
$result = $stmt->execute([$status, $ticket_id]);

if ($result) {
    enregistrerHistorique($db, $ticket_id, $_SESSION['id'], "Statut : {$ancienStatut} → {$status}");
}

echo json_encode([
    "success" => $result
]);
