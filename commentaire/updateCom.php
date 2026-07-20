<?php
header('Content-Type: application/json');

include "../db.php";
include "../auth/checkRole.php";

verifierRole(['client', 'agent', 'admin']);

$id               = $_POST['id'];
$texteCommentaire = $_POST['texteCommentaire'];

if (!$id || !$texteCommentaire) {
    echo json_encode([
        "success" => false,
        "message" => "id et texteCommentaire sont requis."
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

// Seul l'auteur du commentaire peut le modifier (l'admin n'a pas de passe-droit ici,
// à changer si vous voulez qu'il puisse aussi modérer)
if ($commentaire['utilisateur_id'] != $_SESSION['id']) {
    echo json_encode([
        "success" => false,
        "message" => "Vous ne pouvez modifier que vos propres commentaires."
    ]);
    exit;
}

$stmt = $db->prepare("UPDATE commentaire SET texteCommentaire = ? WHERE id = ?");
$result = $stmt->execute([$texteCommentaire, $id]);

echo json_encode([
    "success" => $result
]);