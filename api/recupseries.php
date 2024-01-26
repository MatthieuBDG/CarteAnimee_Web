<?php
include '../include/connexion_bdd.php';

$response = [];

if (isset($_GET['id_user'])) {
    $id_user = $_GET['id_user'];
} else {
    $response['success'] = false;
    $response['error_msg'] = 'Paramètres id_user introuvable';
    exit(json_encode($response, JSON_UNESCAPED_UNICODE));
}

try {
    $req_recup_serie_user = $dbh->prepare("SELECT ae.ID_Serie, Nom FROM autorisations_series ae, series s WHERE ID_User = ? AND s.ID_Serie = ae.ID_Serie");
    $req_recup_serie_user->execute([$id_user]);
    $count_req_recup_serie_user = $req_recup_serie_user->rowCount();
} catch (PDOException $e) {
    $response['success'] = false;
    $response['error_msg'] = 'Erreur de la base de données : ' . $e->getMessage();
    exit(json_encode($response, JSON_UNESCAPED_UNICODE));
}

if ($count_req_recup_serie_user > 0) {
    $resultat_req_recup_serie_user = $req_recup_serie_user->fetchAll(PDO::FETCH_ASSOC);
    $series = [];
    foreach ($resultat_req_recup_serie_user as $row) {
        $series[] = [
            'ID_Serie' => $row['ID_Serie'],
            'Nom' => $row['Nom']
        ];
    }
    $response['success'] = true;
    $response['series'] = $series;
} else {
    $response['success'] = false;
    $response['error_msg'] = 'Aucune série trouvée pour cet utilisateur';
}

echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
?>
