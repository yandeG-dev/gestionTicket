<?php
header('Content-Type: application/json');

include "../db.php";
include "../auth/checkRole.php";

verifierRole(['client', 'admin']);

$id = $_POST['id'];

if ($_SESSION['role'] == 'client') {

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

    if ($ticket['id_createur'] != $_SESSION['id']) {
        echo json_encode([
            "success" => false,
            "message" => "Vous n'êtes pas autorisé à supprimer ce ticket."
        ]);
        exit;
    }
}

$stmt = $db->prepare("DELETE FROM ticket WHERE id = ?");

$result = $stmt->execute([$id]);

echo json_encode([
    "success" => $result
]);