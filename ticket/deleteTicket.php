<?php
header('Content-Type: application/json');

include "../db.php";
include "../auth/checkRole.php";

verifierRole(['client', 'admin']);

$id = $_POST['id'] ?? '';

if (!$id) {
    echo json_encode([
        "success" => false,
        "message" => "id est requis."
    ]);
    exit;
}

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

if ($_SESSION['role'] == 'client' && $ticket['id_createur'] != $_SESSION['id']) {
    echo json_encode([
        "success" => false,
        "message" => "Vous n'êtes pas autorisé à supprimer ce ticket."
    ]);
    exit;
}

// Commentaires et historique ne sont pas protégés par une clé étrangère : sans ce
// nettoyage explicite ils survivraient au ticket et resteraient rattachés à un id réutilisé.
try {
    $db->beginTransaction();

    $stmt = $db->prepare("DELETE FROM commentaire WHERE ticket_id = ?");
    $stmt->execute([$id]);

    $stmt = $db->prepare("DELETE FROM historique WHERE ticket_id = ?");
    $stmt->execute([$id]);

    $stmt = $db->prepare("DELETE FROM ticket WHERE id = ?");
    $stmt->execute([$id]);

    $db->commit();
} catch (Exception $e) {
    $db->rollBack();

    echo json_encode([
        "success" => false,
        "message" => "Suppression impossible."
    ]);
    exit;
}

echo json_encode([
    "success" => true
]);
