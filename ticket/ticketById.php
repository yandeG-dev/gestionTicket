<?php
header('Content-Type: application/json');

include "../db.php";
include "../auth/checkRole.php";
include "./accesTicket.php";

verifierRole(['client', 'agent', 'admin']);

$id = $_POST['id'] ?? '';

if (!$id) {
    echo json_encode([
        "success" => false,
        "message" => "id est requis."
    ]);
    exit;
}

if (!chargerTicketAutorise($db, $id)) {
    echo json_encode([
        "success" => false,
        "message" => "Ticket introuvable ou accès non autorisé."
    ]);
    exit;
}

$stmt = $db->prepare("
    SELECT t.*,
           uc.nom AS nom_createur, uc.prenom AS prenom_createur,
           ua.nom AS nom_assigne,  ua.prenom AS prenom_assigne
    FROM ticket t
    LEFT JOIN utilisateurs uc ON uc.id = t.id_createur
    LEFT JOIN utilisateurs ua ON ua.id = t.id_assigne
    WHERE t.id = ?
");
$stmt->execute([$id]);

echo json_encode([
    "success" => true,
    "ticket"  => $stmt->fetch(PDO::FETCH_ASSOC)
]);
