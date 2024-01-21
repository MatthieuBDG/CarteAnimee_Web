<?php
require 'include/connexion_bdd.php';
if (!isset($_SESSION['ID_User'])) {
    header('Location: ./connexion?connect=1');
} else {
    header('Location: ./listeserie');
}
