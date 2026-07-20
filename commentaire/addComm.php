<?php
header('Content-Type: application/json');

include "../db.php";
include "../auth/checkRole.php";

verifierRole(['client', 'agent', 'admin']);

$ticket_id = $_POST['ticket_id'];
$action    = $_POST['action'];

// On journalise toujours au nom de l'utilisateur connecté (pas besoin de le passer en POST)
$utilisateur_id = $_SESSION['id'];

if (!$ticket_id || !$action) {
    echo json_encode([
        "success" => false,
        "message" => "ticket_id et action sont requis."
    ]);
    exit;
}

$stmt = $db->prepare("INSERT INTO historique (action, ticket_id, utilisateur_id) VALUES (?, ?, ?)");
$result = $stmt->execute([$action, $ticket_id, $utilisateur_id]);

echo json_encode([
    "success" => $result
]);