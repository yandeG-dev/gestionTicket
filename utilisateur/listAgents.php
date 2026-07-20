<?php
header('Content-Type: application/json');

include "../db.php";
include "../auth/checkRole.php";

// Sert la liste déroulante d'affectation, donc réservé à qui peut affecter.
verifierRole(['admin']);

$stmt = $db->prepare("
    SELECT id, nom, prenom, email
    FROM utilisateurs
    WHERE role = 'agent'
    ORDER BY nom, prenom
");
$stmt->execute();

echo json_encode([
    "success" => true,
    "agents"  => $stmt->fetchAll(PDO::FETCH_ASSOC)
]);
