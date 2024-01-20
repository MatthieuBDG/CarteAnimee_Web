<?php
//Connexion BDD PFE
session_start();
$whitelist = array(
    '127.0.0.1',
    '::1'
);

if (in_array($_SERVER['REMOTE_ADDR'], $whitelist)) {
    //Mode test localhost
    $host_name = 'localhost';
    $database = 'PFE';
    $user_name = 'root';
    $password = '';
} else {
    $host_name = '109.234.161.176';
    $database = 'mahq1168_PFE';
    $user_name = 'mahq1168_limayrac';
    $password = 'Limayrac2024';
}
try {
    $dbh = new PDO("mysql:host=$host_name; dbname=$database;", $user_name, $password);
} catch (PDOException $e) {
    echo "Erreur!: " . $e->getMessage() . "<br/>";
    die();
}
