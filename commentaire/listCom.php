<?php
header('Content-Type: application/json');

include "../db.php";
include "../auth/checkRole.php";

verifierRole(['client', 'agent', 'admin']);

$ticket_id = $_POST['ticket_id'];

if ($_SESSION['role'] == 'client') {
    $stmt = $db->prepare("SELECT id_createur FROM ticket WHERE id = ?");
    $stmt->execute([$ticket_id]);
    $ticket = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$ticket || $ticket['id_createur'] != $_SESSION['id']) {
        echo json_encode([
            "success" => false,
            "message" => "Accès non autorisé à ces commentaires."
        ]);
        exit;
    }
}

$stmt = $db->prepare("
    SELECT c.id, c.texteCommentaire, c.ticket_id, c.utilisateur_id, c.dateCommentaire,
           u.nom AS nom_auteur, u.prenom AS prenom_auteur
    FROM commentaire c
    LEFT JOIN utilisateurs u ON u.id = c.utilisateur_id
    WHERE c.ticket_id = ?
    ORDER BY c.id ASC
");
$stmt->execute([$ticket_id]);

$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode([
    "success" => true,
    "commentaires" => $result
]);