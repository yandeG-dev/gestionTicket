<?php
header('Content-Type: application/json');

include "../db.php";
include "../auth/checkRole.php";
include "../historique/enregistrerHistorique.php";

verifierRole(['admin']);

$ticket_id = $_POST['ticket_id'] ?? '';
// agent_id vide = on retire l'affectation et le ticket retourne dans la file commune.
$agent_id  = $_POST['agent_id'] ?? '';

if (!$ticket_id) {
    echo json_encode([
        "success" => false,
        "message" => "ticket_id est requis."
    ]);
    exit;
}

$stmt = $db->prepare("SELECT id FROM ticket WHERE id = ?");
$stmt->execute([$ticket_id]);

if (!$stmt->fetch(PDO::FETCH_ASSOC)) {
    echo json_encode([
        "success" => false,
        "message" => "Ticket introuvable."
    ]);
    exit;
}

if ($agent_id) {
    // On refuse d'affecter à un client : sans ce contrôle la contrainte de clé
    // étrangère laisserait passer n'importe quel utilisateur.
    $stmt = $db->prepare("SELECT nom, prenom FROM utilisateurs WHERE id = ? AND role = 'agent'");
    $stmt->execute([$agent_id]);
    $agent = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$agent) {
        echo json_encode([
            "success" => false,
            "message" => "Agent introuvable."
        ]);
        exit;
    }

    $stmt = $db->prepare("UPDATE ticket SET id_assigne = ? WHERE id = ?");
    $result = $stmt->execute([$agent_id, $ticket_id]);
    $action = "Ticket affecté à {$agent['prenom']} {$agent['nom']}";
} else {
    $stmt = $db->prepare("UPDATE ticket SET id_assigne = NULL WHERE id = ?");
    $result = $stmt->execute([$ticket_id]);
    $action = "Affectation retirée";
}

if ($result) {
    enregistrerHistorique($db, $ticket_id, $_SESSION['id'], $action);
}

echo json_encode([
    "success" => $result
]);
