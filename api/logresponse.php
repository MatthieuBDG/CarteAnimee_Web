<?php

function log_response($json_response)
{
    global $dbh;
    $url = $_SERVER['SCRIPT_NAME'];
    $date = date('Y-m-d H:i:s'); // Récupération de la date actuelle
    $query = $dbh->prepare("INSERT INTO api_usage (Response_Json, Api_Url, Date) VALUES (?, ?, ?)");
    $query->execute([$json_response, $url, $date]);
}
