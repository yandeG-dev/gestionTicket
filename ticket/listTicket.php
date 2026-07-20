<?php
header('Content-Type: application/json');

include "../db.php";
include "../auth/checkRole.php";

verifierRole(['client', 'agent', 'admin']);

if ($_SESSION['role'] == 'client') {
    //pour les clients
    $stmt = $db->prepare("
        SELECT *
        FROM ticket
        WHERE id_createur = ?
    ");

    $stmt->execute([$_SESSION['id']]);

} elseif ($_SESSION['role'] == 'agent') {
//pour les agents
    $stmt = $db->prepare("
        SELECT *
        FROM ticket
        WHERE id_assigne = ?
    ");

    $stmt->execute([$_SESSION['id']]);

} else {

    // Admin
    $stmt = $db->prepare("SELECT * FROM ticket");
    $stmt->execute();
}

$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($result);