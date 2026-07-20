<?php

function verifierRole($rolesAutorises)
{
    session_start();

    if (!isset($_SESSION['role'])) {
        echo json_encode([
            "success"=>false,
            "message"=>"Utilisateur non connecté"
        ]);
        exit;
    }


    if (!in_array($_SESSION['role'], $rolesAutorises)) {

        echo json_encode([
            "success"=>false,
            "message"=>"Accès interdit"
        ]);

        exit;
    }
}