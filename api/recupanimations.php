<?php
include '../include/connexion_bdd.php';

$response = [];

if (isset($_GET['id_serie'])) {
    $id_serie = $_GET['id_serie'];
} else {
    $response['success'] = false;
    $response['error_msg'] = 'Paramètres id_serie introuvable';
    exit(json_encode($response, JSON_UNESCAPED_UNICODE));
}

try {
    $req_recup_animation_serie = $dbh->prepare("SELECT a.ID_Animation,a.Nom,Chemin_Gif,Chemin_Audio FROM series_animations sa,series s,animations a WHERE s.ID_Serie = ? AND s.ID_Serie = sa.ID_Serie AND a.ID_Animation = sa.ID_Animation");
    $req_recup_animation_serie->execute([$id_serie]);
    $count_req_recup_animation_serie = $req_recup_animation_serie->rowCount();
} catch (PDOException $e) {
    $response['success'] = false;
    $response['error_msg'] = 'Erreur de la base de données : ' . $e->getMessage();
    exit(json_encode($response, JSON_UNESCAPED_UNICODE));
}

if ($count_req_recup_animation_serie > 0) {
    $resultat_req_recup_animation_serie = $req_recup_animation_serie->fetchAll(PDO::FETCH_ASSOC);
    $animations = [];
    foreach ($resultat_req_recup_animation_serie as $row) {
        $animations[] = [
            'ID_Animation' => $row['ID_Animation'],
            'Nom' => $row['Nom'],
            'Chemin_Gif' => $row['Chemin_Gif'],
            'Chemin_Audio' => $row['Chemin_Audio']
        ];
    }
    $response['success'] = true;
    $response['animations'] = $animations;
} else {
    $response['success'] = false;
    $response['error_msg'] = 'Aucune animations trouvée pour cette série';
}

echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
?>
