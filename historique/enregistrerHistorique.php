<?php

// L'historique est écrit par le serveur, jamais par le client : une trace que
// l'appelant peut oublier ou falsifier n'a aucune valeur.
function enregistrerHistorique($db, $ticket_id, $utilisateur_id, $action)
{
    $stmt = $db->prepare("INSERT INTO historique (action, ticket_id, utilisateur_id) VALUES (?, ?, ?)");
    $stmt->execute([$action, $ticket_id, $utilisateur_id]);
}
