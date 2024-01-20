<?php

if (!isset($_SESSION['ID_User'])) {
    header('Location: ./connexion?connect=1');
}
