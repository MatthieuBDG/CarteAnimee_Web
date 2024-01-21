<script>
    // Éviter le renvoi des données lorsque la page est rafraîchie
    if (window.history.replaceState) {
        window.history.replaceState(null, null, window.location.href);
    }
</script>
<?php
require 'include/connexion_bdd.php';

require 'include/verif_user_connect.php';

if (isset($_GET['id_utilisateur']) && !empty($_GET['id_utilisateur'])) {
    $id_utilisateur = $_GET['id_utilisateur'];
    $verif_utilisateur_exist = $dbh->prepare('SELECT * FROM users WHERE ID_User = ?');
    $verif_utilisateur_exist->execute(array($id_utilisateur));
    if ($verif_utilisateur_exist->rowCount() > 0) {
        $utilisateurs_infos = $verif_utilisateur_exist->fetch();
        try {
            $roles_utilisateur = $dbh->prepare('SELECT * FROM roles WHERE ID_Role = ?');
            $roles_utilisateur->execute(array($utilisateurs_infos['Role']));
        } catch (PDOException $e) {
            echo "Erreur!: " . $e->getMessage() . "<br/>";
            die();
        }
    } else {
        $error = 'Le numéro de l\'utilisateur n\'est pas valide ou n\'existe pas';
        header('Location: ./listeutilisateur?messageerror=' . $error);
    }
} else {
    $error = 'Le numéro de l\'utilisateur n\'est pas renseigné';
    header('Location: ./listeutilisateur?messageerror=' . $error);
}
if (isset($_POST['submit'])) {
    try {
        $deleteutilisateurautorisation = $dbh->prepare('DELETE FROM autorisations_series WHERE ID_User = ?');
        $deleteutilisateurautorisation->execute(array($id_utilisateur));
    } catch (PDOException $e) {
        echo "Erreur!: " . $e->getMessage() . "<br/>";
        die();
    }
    try {
        $deleteutilisateur = $dbh->prepare('DELETE FROM users WHERE ID_User = ?');
        $deleteutilisateur->execute(array($id_utilisateur));
    } catch (PDOException $e) {
        echo "Erreur!: " . $e->getMessage() . "<br/>";
        die();
    }
    $success = 'L\'utilisateur ' . $utilisateurs_infos['Prenom'] . ' ' . $utilisateurs_infos['Nom'] . ' à bien été supprimé !';
    header('Location: ./listeutilisateur?message=' . $success);
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Suppression d'un utilisateur</title>
    <link href="css/table.css" rel="stylesheet" />
    <link href="css/styles.css" rel="stylesheet" />
    <script src="js/all.js" crossorigin="anonymous"></script>
</head>

<body>
    <?php
    require 'include/navigation.php';
    ?>
    <div id="layoutSidenav">
        <?php
        require 'include/sidebar.php';
        ?>
        <div id="layoutSidenav_content">
            <main>
                <div class="container-fluid px-4">
                    <h1 class="mt-4">Suppression d'un utilisateur</h1>
                    <ol class="breadcrumb mb-4">
                        <li class="breadcrumb-item">Pages</li>
                        <li class="breadcrumb-item"><a href="listeutilisateur">Liste des utilisateur</li></a>
                        <li class="breadcrumb-item active">Suppression d'un utilisateur</li>
                    </ol>
                    <div class="row justify-content-center">
                        <div class="col-lg-7">
                            <div class="card-header">
                                <h3 class="text-center font-weight-light my-4">Suppression d'un utilisateur</h3>
                            </div>
                            <div class="card-body">
                                <?php
                                if (isset($erreur)) { ?>
                                    <div class="alert alert-danger" role="alert">
                                        <?php echo $erreur ?>
                                    </div>
                                <?php }
                                ?>
                                <div class="card">
                                    <div class="card-body">
                                        <form method="post">
                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <div class="form-floating mb-3">
                                                        <input class="form-control" type="text" placeholder="Prenom & Nom" value="<?php echo $utilisateurs_infos['Prenom'] . ' ' . $utilisateurs_infos['Nom'] ?>" disabled />
                                                        <label>Prenom & Nom</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-floating mb-3">
                                                        <input class="form-control" type="email" placeholder="Adresse Email" value="<?php echo $utilisateurs_infos['Email']; ?>" disabled />
                                                        <label>Adresse Email</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="form-floating mb-3">
                                                        <select class="form-control" disabled>
                                                            <?php foreach ($roles_utilisateur as $role_utilisateur) { ?>
                                                                <option value="<?php echo $role_utilisateur['ID_Role']; ?>"><?php echo $role_utilisateur['Nom']; ?></option>
                                                            <?php } ?>
                                                        </select>
                                                        <label>Rôle</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="card">
                                                        <div class="card-body">
                                                            <h4>Séries associées : </h4>
                                                            <?php
                                                            try {
                                                                $series_associe = $dbh->prepare('SELECT ID_Serie FROM autorisations_series WHERE ID_User = ?');
                                                                $series_associe->execute(array($id_utilisateur));
                                                                $series_associe_count = $series_associe->rowCount();

                                                                echo '<td>';
                                                                if ($series_associe_count > 0) {
                                                                    echo '<ul>'; // Ouvrir la liste
                                                                    while ($series_associes = $series_associe->fetch()) {
                                                                        try {
                                                                            $series_user = $dbh->prepare('SELECT Nom FROM series WHERE ID_Serie = ?');
                                                                            $series_user->execute(array($series_associes['ID_Serie']));
                                                                            $series_user = $series_user->fetch();
                                                                            echo '<li>' . $series_user['Nom'] . '</li>';
                                                                        } catch (PDOException $e) {
                                                                            echo "Erreur!: " . $e->getMessage() . "<br/>";
                                                                            die();
                                                                        }
                                                                    }
                                                                    echo '</ul>'; // Fermer la liste
                                                                } else {
                                                                    echo 'Aucune série est associée';
                                                                }
                                                                echo '</td>';
                                                            } catch (PDOException $e) {
                                                                echo "Erreur!: " . $e->getMessage() . "<br/>";
                                                                die();
                                                            }
                                                            ?>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="mt-4 mb-0">
                                                    <input type="submit" name="submit" class="btn btn-danger" value="Supprimer">
                                                </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
            <?php
            require 'include/footer.php';
            ?>
        </div>
    </div>
    <script src="js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="js/scripts.js"></script>
</body>

</html>