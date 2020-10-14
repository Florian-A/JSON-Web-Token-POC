<?php

// Démarrage d'une session PHP.
session_start();

// Retourne le premier paramètre de l'URL qui indiquera le nom de la classe à utiliser.
function askedController()
{
    if (isset(array_keys($_GET)[0]) && preg_match("[^Aa-Zz_]", array_keys($_GET)[0]) == 0) {
        return array_keys($_GET)[0];
    }
}

// Retourne le deuxième paramètre de l'URL qui indiquera le nom de la méthode de la classe à utiliser.
function askedMethod()
{
    if (isset(array_keys($_GET)[1]) && preg_match("[^Aa-Zz_]", array_keys($_GET)[1]) == 0) {
        return array_keys($_GET)[1];
    }
}

$controllerName = askedController();
$methodName = askedMethod();

if ($controllerName !== NULL and $methodName !== NULL) {

    // Importation dynamique du fichier contenant la class.
    require_once 'controller/' . $controllerName . '.php';

    // Assignation de la classe dans un objet qui aura pour nom le nom de la dite classe.
    $a = $controllerName;
    $$a = new $controllerName;

    // Appel de la méthode puis déstruction de l'instance de connexion à la base de données.
    $$controllerName->$methodName();
    //$$className->destructDatabaseConnection();

    // Affichage du haut de page.
    require_once 'views/header.php';

    // Affichage de la vue.
    require_once 'views/' . str_replace('Controller', '', $controllerName) . '/' . $methodName . '.php';

    // Affichage du bas de page.
    require_once 'views/header.php';
} else {
    // Si mauvaise route appelée, redirection vers la page d'authentification.
    return header('Location: index.php?LoginController&check');
}
