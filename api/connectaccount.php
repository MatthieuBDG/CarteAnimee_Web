<?php
include '../include/connexion_bdd.php';

$response = [];


if (isset($_GET['email']) && isset($_GET['mdp'])) {
    $email = $_GET['email'];
    $mdp = $_GET['mdp'];
} else {
    $response['success'] = false;
    $response['error_msg'] = 'Paramètres email et mdp non trouvés';
    exit(json_encode($response, JSON_UNESCAPED_UNICODE));
}

try {
    $req_verif_existe_user = $dbh->prepare("SELECT * FROM users WHERE Email = ? AND Role = ?");
    $req_verif_existe_user->execute([$email, 3]);
    $resultat_verif_existe_user = $req_verif_existe_user->rowCount();
} catch (PDOException $e) {
    $response['success'] = false;
    $response['error_msg'] = 'Erreur de la base de données : ' . $e->getMessage();
    exit(json_encode($response, JSON_UNESCAPED_UNICODE));
}

if ($resultat_verif_existe_user > 0) {
    $resultat_user = $req_verif_existe_user->fetch();

    // Comparaison du mot de passe envoyé via le formulaire avec celui enregistré en base
    $isPasswordCorrect = password_verify($mdp, $resultat_user['Mdp']);

    if ($isPasswordCorrect) {
        try {
            $req_recup_role = $dbh->prepare("SELECT Nom FROM roles WHERE ID_Role = ?");
            $req_recup_role->execute([$resultat_user['Role']]);
            $role = $req_recup_role->fetch();

            $response['success'] = true;
            $response['user'] = [
                'ID_User' => $resultat_user['ID_User'],
                'Prenom' => $resultat_user['Prenom'],
                'Nom' => $resultat_user['Nom'],
                'Email' => $resultat_user['Email'],
                'ID_Role' => $resultat_user['Role'],
                'Role' => $role['Nom']
            ];
        } catch (PDOException $e) {
            $response['success'] = false;
            $response['error_msg'] = 'Erreur de la base de données : ' . $e->getMessage();
        }
    } else {
        $response['success'] = false;
        $response['error_msg'] = 'Mot de passe incorrect';
    }
} else {
    $response['success'] = false;
    $response['error_msg'] = 'Utilisateur non trouvé';
}

echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
