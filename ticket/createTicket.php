<?php
header('Content-Type: application/json');
include "../db.php";
include "../auth/checkRole.php";

verifierRole(['client']);

$titre = $_POST['titre'];
$description = $_POST['description'];
$categorie=$_POST['categorie'];
$priorite = $_POST['priorite'];
$status=$_POST['status'];
$dateCreation=$_POST['dateCreation'];


$id_createur = $_SESSION['id'];

$stmt = $db->prepare("INSERT INTO ticket (titre, description,categorie, priorite, status, dateCreation,id_createur) VALUES (?, ?, ?, ?, ?,?,?)");

$result = $stmt->execute([
    $titre,
    $description,
    $categorie,
    $priorite,
    $status,
    $dateCreation,
    $id_createur
]);

echo json_encode([
    'success' => $result
]);
?>