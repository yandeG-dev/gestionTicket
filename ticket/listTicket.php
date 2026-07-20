<?php
header('Content-Type: application/json');

include "../db.php";
include "../auth/checkRole.php";

verifierRole(['client', 'agent', 'admin']);

// Les noms du créateur et de l'assigné sont joints ici : la liste doit pouvoir
// afficher "assigné à X" sans un appel par ligne.
$selection = "
    SELECT t.*,
           uc.nom AS nom_createur, uc.prenom AS prenom_createur,
           ua.nom AS nom_assigne,  ua.prenom AS prenom_assigne
    FROM ticket t
    LEFT JOIN utilisateurs uc ON uc.id = t.id_createur
    LEFT JOIN utilisateurs ua ON ua.id = t.id_assigne
";

if ($_SESSION['role'] == 'client') {

    $stmt = $db->prepare($selection . " WHERE t.id_createur = ? ORDER BY t.id DESC");
    $stmt->execute([$_SESSION['id']]);

} elseif ($_SESSION['role'] == 'agent') {

    $stmt = $db->prepare($selection . " WHERE t.id_assigne = ? ORDER BY t.id DESC");
    $stmt->execute([$_SESSION['id']]);

} else {

    // Admin
    $stmt = $db->prepare($selection . " ORDER BY t.id DESC");
    $stmt->execute();
}

$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Format aligné sur les autres endpoints : {success, ...}. Avant, celui-ci
// renvoyait un tableau brut, obligeant le client à gérer deux formats.
echo json_encode([
    "success" => true,
    "tickets" => $result
]);
