<?php

session_start();

// Vérification si une session est active (un utilisateur est connecté)
if (isset($_SESSION['ID_User'])) {
    // Si une session est active, efface toutes les données de session
    $_SESSION = array();
    session_destroy();
}
header('Location:./connexion?disconnect=1');
