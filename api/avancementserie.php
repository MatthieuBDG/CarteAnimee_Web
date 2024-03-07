<?php
include '../include/connexion_bdd.php';

$response = [];

if (isset($_GET['id_serie']) && isset($_GET['id_user']) && isset($_GET['last_animation'])) {
    $id_serie = $_GET['id_serie'];
    $id_user = $_GET['id_user'];
    $last_animation = $_GET['last_animation'];
} else {
    $response['success'] = false;
    $response['error_msg'] = 'Paramètres id_serie et id_user et last_animation introuvable';
    exit(json_encode($response, JSON_UNESCAPED_UNICODE));
}
try {
    $req_count_animations_series = $dbh->prepare("SELECT ID_Animation FROM series_animations WHERE ID_Serie = ?");
    $req_count_animations_series->execute([$id_serie]);
    $req_count_animations_series = $req_count_animations_series->rowCount();
} catch (PDOException $e) {
    $response['success'] = false;
    $response['error_msg'] = 'Erreur de la base de données : ' . $e->getMessage();
    exit(json_encode($response, JSON_UNESCAPED_UNICODE));
}
if ($req_count_animations_series > 0) {
    try {
        $req_count_verif_autorisations_series = $dbh->prepare("SELECT ID_User FROM autorisations_series WHERE ID_User = ? AND ID_Serie = ?");
        $req_count_verif_autorisations_series->execute([$id_user, $id_serie]);
        $req_count_verif_autorisations_series = $req_count_verif_autorisations_series->rowCount();
    } catch (PDOException $e) {
        $response['success'] = false;
        $response['error_msg'] = 'Erreur de la base de données : ' . $e->getMessage();
        exit(json_encode($response, JSON_UNESCAPED_UNICODE));
    }
    if ($req_count_verif_autorisations_series > 0) {
        $pourcentage_avancement = ($last_animation / $req_count_animations_series) * 100;
        try {
            $req_recup_avancement_series = $dbh->prepare("SELECT Pourcentage,Derniere_Animation FROM avancement_series WHERE ID_User = ? AND ID_Serie = ?");
            $req_recup_avancement_series->execute([$id_user, $id_serie]);
            $count_recup_avancement_series = $req_recup_avancement_series->rowCount();
        } catch (PDOException $e) {
            $response['success'] = false;
            $response['error_msg'] = 'Erreur de la base de données : ' . $e->getMessage();
            exit(json_encode($response, JSON_UNESCAPED_UNICODE));
        }
        if ($count_recup_avancement_series > 0) {
            try {
                $update_avancement_series = $dbh->prepare('UPDATE avancement_series SET Pourcentage = ?,Derniere_Animation = ?  WHERE ID_User = ? AND ID_Serie = ?');
                $update_avancement_series->execute([$pourcentage_avancement, $last_animation, $id_user, $id_serie]);
            } catch (PDOException $e) {
                $response['success'] = false;
                $response['error_msg'] = 'Erreur de la base de données : ' . $e->getMessage();
                exit(json_encode($response, JSON_UNESCAPED_UNICODE));
            }
            $response['success'] = true;
        } else {
            try {
                $insert_avancement_series = $dbh->prepare('INSERT INTO avancement_series(ID_User,ID_Serie,Pourcentage,Derniere_Animation) VALUES (?,?,?,?)');
                $insert_avancement_series->execute([$id_user, $id_serie, $pourcentage_avancement, $last_animation]);
            } catch (PDOException $e) {
                $response['success'] = false;
                $response['error_msg'] = 'Erreur de la base de données : ' . $e->getMessage();
                exit(json_encode($response, JSON_UNESCAPED_UNICODE));
            }
            $response['success'] = true;
        }
    } else {
        $response['success'] = false;
        $response['error_msg'] = 'L\'utilisateur n\'a pas acces à cette serie Veuillez réessayer';
    }
} else {
    $response['success'] = false;
    $response['error_msg'] = 'Aucune animations trouvée pour cette série. Veuillez réessayer';
}
// Enregistrement de la réponse dans la base de données
include 'logresponse.php';
log_response(json_encode($response));

echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
