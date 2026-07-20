<?php
header('Content-Type: application/json');

include "../db.php";
include "../auth/checkRole.php";

verifierRole(['client', 'agent', 'admin']);

$id = $_POST['id'] ?? '';

if (!$id) {
    echo json_encode([
        "success" => false,
        "message" => "id est requis."
    ]);
    exit;
}

$stmt = $db->prepare("SELECT utilisateur_id FROM commentaire WHERE id = ?");
$stmt->execute([$id]);
$commentaire = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$commentaire) {
    echo json_encode([
        "success" => false,
        "message" => "Commentaire introuvable."
    ]);
    exit;
}

// L'auteur peut supprimer son propre commentaire, l'admin peut supprimer n'importe lequel (modération)
if ($commentaire['utilisateur_id'] != $_SESSION['id'] && $_SESSION['role'] != 'admin') {
    echo json_encode([
        "success" => false,
        "message" => "Vous n'êtes pas autorisé à supprimer ce commentaire."
    ]);
    exit;
}

$stmt = $db->prepare("DELETE FROM commentaire WHERE id = ?");
$result = $stmt->execute([$id]);

echo json_encode([
    "success" => $result
]);
