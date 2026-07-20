<?php

// Règle d'accès unique à un ticket, partagée par les tickets, les commentaires et l'historique :
// le client ne voit que ce qu'il a créé, l'agent que ce qu'on lui a assigné, l'admin tout.
// Retourne le ticket si l'accès est permis, false sinon.
function chargerTicketAutorise($db, $ticket_id)
{
    $stmt = $db->prepare("SELECT * FROM ticket WHERE id = ?");
    $stmt->execute([$ticket_id]);
    $ticket = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$ticket) {
        return false;
    }

    if ($_SESSION['role'] == 'admin') {
        return $ticket;
    }

    if ($_SESSION['role'] == 'client' && $ticket['id_createur'] == $_SESSION['id']) {
        return $ticket;
    }

    if ($_SESSION['role'] == 'agent' && $ticket['id_assigne'] == $_SESSION['id']) {
        return $ticket;
    }

    return false;
}
