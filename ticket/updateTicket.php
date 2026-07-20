<?php

header('Content-Type: application/json');

include "../db.php";
include "../auth/checkRole.php";

verifierRole(['client', 'admin']);

$id = $_POST['id'];
$titre = $_POST['titre'];
$description = $_POST['description'];
$categorie = $_POST['categorie'];
$priorite = $_POST['priorite'];
$status = $_POST['status'];
$dateCreation = $_POST['dateCreation'];


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
            "message" => "Vous ne pouvez modifier que vos propres tickets."
        ]);
        exit;
    }
}

$stmt = $db->prepare("
    UPDATE ticket
    SET
        titre = ?,
        description = ?,
        categorie = ?,
        priorite = ?,
        status = ?,
        dateCreation = ?
    WHERE id = ?
");

$result = $stmt->execute([
    $titre,
    $description,
    $categorie,
    $priorite,
    $status,
    $dateCreation,
    $id
]);

echo json_encode([
    "success" => $result
]);