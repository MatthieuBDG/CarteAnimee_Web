<?php

if (isset($_SESSION['ID_User'])) {
    if ($_SESSION['ID_Role'] <> 1) {
        header('Location: ./connexion?pasautorise=1');
    }
}
