<?php
header('Content-Type: application/json');

include "../db.php";
include "../auth/checkRole.php";
include "./enregistrerHistorique.php";

// L'historique est désormais écrit par le serveur lors de la création, de l'affectation,
// de la modification et du changement de statut. Cet endpoint n'est plus appelé par le
// client WPF ; il reste réservé à l'admin pour une correction manuelle, car ouvert à tous
// il permettrait de fabriquer de fausses entrées d'historique.
verifierRole(['admin']);

$ticket_id = $_POST['ticket_id'] ?? '';
$action    = $_POST['action'] ?? '';

if (!$ticket_id || !$action) {
    echo json_encode([
        "success" => false,
        "message" => "ticket_id et action sont requis."
    ]);
    exit;
}

enregistrerHistorique($db, $ticket_id, $_SESSION['id'], $action);

echo json_encode([
    "success" => true
]);
