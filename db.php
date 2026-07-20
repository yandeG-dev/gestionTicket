<?php
$db_server = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "gestion_ticket";

$db_port = 3307;

// utf8mb4 pour correspondre aux tables : les ENUM contiennent des accents (Matériel, Réseau, Résolu, Fermé)
$db = new PDO("mysql:host={$db_server};port={$db_port};dbname={$db_name};charset=utf8mb4", $db_user, $db_pass);
$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
?>
