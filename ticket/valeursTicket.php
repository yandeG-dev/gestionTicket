<?php

// MySQL n'est pas en mode strict sur cette installation : une valeur hors ENUM est
// enregistrée en chaîne vide au lieu de déclencher une erreur. Sans cette validation,
// un ticket peut être créé avec une catégorie vide et personne ne s'en aperçoit.
// Ces listes doivent rester alignées sur les ENUM de la table ticket.
function valeursTicketValides()
{
    return [
        'categorie' => ['Matériel', 'Logiciel', 'Réseau'],
        'priorite'  => ['Faible', 'Moyenne', 'Haute', 'Urgente'],
        'status'    => ['Ouvert', 'En cours', 'En attente', 'Résolu', 'Fermé'],
    ];
}

// Retourne un message d'erreur, ou null si tout est valide.
function erreurValeursTicket($categorie, $priorite)
{
    $valides = valeursTicketValides();

    if (!in_array($categorie, $valides['categorie'], true)) {
        return "Catégorie invalide. Valeurs attendues : " . implode(', ', $valides['categorie']);
    }

    if (!in_array($priorite, $valides['priorite'], true)) {
        return "Priorité invalide. Valeurs attendues : " . implode(', ', $valides['priorite']);
    }

    return null;
}
