<?php

/**
 *  Mise en place de l'accès aux ressources
 * Appel depuis tous les scripts sauf ceux concernant la personnalisation du mot de passe.
 */

// Définition de la constante RACINE pour permettre un accès aux ressources par un adressage absolu
define('RACINE', $_SERVER['DOCUMENT_ROOT']);

// Chargement dynamique des classes
spl_autoload_register(function ($name) {
    $name = strtolower($name);
    if (file_exists(RACINE . "/class/class.$name.php"))
        require RACINE . "/class/class.$name.php";
    else
        require RACINE . "/$name/class/class.$name.php";
});


// Accès aux variables de session
session_start();