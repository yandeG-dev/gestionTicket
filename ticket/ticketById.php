<?php
header('Content-Type: application/json');

include "../db.php";
include "../auth/checkRole.php";

verifierRole(['client', 'agent', 'admin']);

$id = $_POST['id'];

if ($_SESSION['role'] == 'client') {

    $stmt = $db->prepare("
        SELECT *
        FROM ticket
        WHERE id = ? AND id_createur = ?
    ");

    $stmt->execute([$id, $_SESSION['id']]);

} elseif ($_SESSION['role'] == 'agent') {

    $stmt = $db->prepare("
        SELECT *
        FROM ticket
        WHERE id = ? AND id_assigne = ?
    ");

    $stmt->execute([$id, $_SESSION['id']]);

} else {

    // Admin
    $stmt = $db->prepare("
        SELECT *
        FROM ticket
        WHERE id = ?
    ");

    $stmt->execute([$id]);
}

$ticket = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$ticket) {
    echo json_encode([
        "success" => false,
        "message" => "Ticket introuvable ou accès non autorisé."
    ]);
    exit;
}

echo json_encode([
    "success" => true,
    "ticket" => $ticket
]);