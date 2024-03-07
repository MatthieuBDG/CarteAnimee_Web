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
    $req_recup_serie_user = $dbh->prepare("
        SELECT DISTINCT s.ID_Serie, s.Nom 
        FROM autorisations_series ae 
        JOIN series s ON s.ID_Serie = ae.ID_Serie 
        JOIN series_animations sa ON s.ID_Serie = sa.ID_Serie 
        WHERE ID_User = ?
    ");
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
        $serieItem = [
            'ID_Serie' => $row['ID_Serie'],
            'Nom' => $row['Nom'],
            'Pourcentage' => 0, // Initialiser le pourcentage à null par défaut
            'Derniere_Animation' => 0
        ];
    
        try {
            $req_recup_serie_pourcentage = $dbh->prepare("SELECT Pourcentage,Derniere_Animation FROM avancement_series WHERE ID_Serie = ?");
            $req_recup_serie_pourcentage->execute([$row['ID_Serie']]);
            $resultat_req_recup_serie_pourcentage = $req_recup_serie_pourcentage->fetchAll(PDO::FETCH_ASSOC);
    
            // S'il y a un résultat pour le pourcentage, l'assigner
            if (!empty($resultat_req_recup_serie_pourcentage)) {
                $serieItem['Pourcentage'] = $resultat_req_recup_serie_pourcentage[0]['Pourcentage'];
                $serieItem['Derniere_Animation'] = $resultat_req_recup_serie_pourcentage[0]['Derniere_Animation'];
            }
        } catch (PDOException $e) {
            $response['success'] = false;
            $response['error_msg'] = 'Erreur de la base de données : ' . $e->getMessage();
            exit(json_encode($response, JSON_UNESCAPED_UNICODE));
        }
    
        // Ajouter l'élément de série à la liste
        $series[] = $serieItem;
    }
    $response['success'] = true;
    $response['series_count'] = $count_req_recup_serie_user;
    $response['series'] = $series;
} else {
    $response['success'] = false;
    $response['error_msg'] = 'Aucune série trouvée pour cet utilisateur. Veuillez réessayer';
}
// Enregistrement de la réponse dans la base de données
include 'logresponse.php';
log_response(json_encode($response));
echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
