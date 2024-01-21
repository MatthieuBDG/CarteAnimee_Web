<script>
    // Éviter le renvoi des données lorsque la page est rafraîchie
    if (window.history.replaceState) {
        window.history.replaceState(null, null, window.location.href);
    }
</script>
<?php
require 'include/connexion_bdd.php';

require 'include/verif_user_connect.php';

if (isset($_GET['id_serie']) && !empty($_GET['id_serie'])) {
    $id_serie = $_GET['id_serie'];
    $verif_serie_exist = $dbh->prepare('SELECT * FROM series WHERE ID_Serie = ?');
    $verif_serie_exist->execute(array($id_serie));
    if ($verif_serie_exist->rowCount() > 0) {
        $series_infos = $verif_serie_exist->fetch();
    } else {
        $error = 'Le numéro de la série n\'est pas valide ou n\'existe pas';
        header('Location: ./listeserie?messageerror=' . $error);
    }
} else {
    $error = 'Le numéro de la série n\'est pas renseigné';
    header('Location: ./listeserie?messageerror=' . $error);
}
if (isset($_POST['submit'])) {
    try {
        $deleteserieautorisation = $dbh->prepare('DELETE FROM autorisations_series WHERE ID_Serie = ?');
        $deleteserieautorisation->execute(array($id_serie));
    } catch (PDOException $e) {
        echo "Erreur!: " . $e->getMessage() . "<br/>";
        die();
    }
    try {
        $deleteseries_animations = $dbh->prepare('DELETE FROM series_animations WHERE ID_Serie = ?');
        $deleteseries_animations->execute(array($id_serie));
    } catch (PDOException $e) {
        echo "Erreur!: " . $e->getMessage() . "<br/>";
        die();
    }
    try {
        $deleteserie = $dbh->prepare('DELETE FROM series WHERE ID_Serie = ?');
        $deleteserie->execute(array($id_serie));
    } catch (PDOException $e) {
        echo "Erreur!: " . $e->getMessage() . "<br/>";
        die();
    }
    $success = 'La série ' . $series_infos['Nom'] . ' à bien été supprimé !';
    header('Location: ./listeserie?message=' . $success);
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Suppression d'une série</title>
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
                    <h1 class="mt-4">Suppression d'une série</h1>
                    <ol class="breadcrumb mb-4">
                        <li class="breadcrumb-item">Pages</li>
                        <li class="breadcrumb-item"><a href="listeserie">Liste des séries</li></a>
                        <li class="breadcrumb-item active">Suppression d'une série</li>
                    </ol>
                    <div class="row justify-content-center">
                        <div class="col-lg-7">
                            <div class="card-header">
                                <h3 class="text-center font-weight-light my-4">Suppression d'une série</h3>
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
                                                <div class="col-md-12">
                                                    <div class="form-floating mb-3">
                                                        <input class="form-control" name="nomserie" type="text" placeholder="Animaux" value="<?php echo $series_infos['Nom'] ?>" disabled />
                                                        <label>Nom de la série</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="card">
                                                        <div class="card-body">
                                                            <h4>Enfants/Parents autorisés : </h4>
                                                            <?php
                                                            try {
                                                                $series_user_autorise = $dbh->prepare('SELECT ID_User FROM autorisations_series WHERE ID_Serie = ?');
                                                                $series_user_autorise->execute(array($id_serie));
                                                                $series_user_autorise_count = $series_user_autorise->rowCount();

                                                                if ($series_user_autorise_count > 0) {
                                                                    echo '<ul>'; // Ouvrir la liste
                                                                    while ($series_user_autorises = $series_user_autorise->fetch()) {
                                                                        try {
                                                                            $series_user = $dbh->prepare('SELECT Prenom, Nom FROM users WHERE ID_User = ?');
                                                                            $series_user->execute(array($series_user_autorises['ID_User']));
                                                                            $series_users = $series_user->fetch();
                                                                            echo '<li>' . $series_users['Prenom'] . ' ' . $series_users['Nom'] . '</li>';
                                                                        } catch (PDOException $e) {
                                                                            echo "Erreur!: " . $e->getMessage() . "<br/>";
                                                                            die();
                                                                        }
                                                                    }
                                                                    echo '</ul>'; // Fermer la liste
                                                                } else {
                                                                    echo 'Aucun enfant/parent autorisé';
                                                                }
                                                            } catch (PDOException $e) {
                                                                echo "Erreur!: " . $e->getMessage() . "<br/>";
                                                                die();
                                                            }
                                                            ?>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="card">
                                                        <div class="card-body">
                                                            <h4>Animations associées : </h4>
                                                            <?php
                                                            try {
                                                                $series_animation_associee = $dbh->prepare('SELECT ID_Animation FROM series_animations WHERE ID_Serie = ?');
                                                                $series_animation_associee->execute(array($id_serie));
                                                                $series_animation_associee_count = $series_animation_associee->rowCount();
                                                                echo '<td>';
                                                                if ($series_animation_associee_count > 0) {
                                                                    echo '<ul>'; // Ouvrir la liste
                                                                    while ($series_animation_associees = $series_animation_associee->fetch()) {
                                                                        try {
                                                                            $series_animation = $dbh->prepare('SELECT * FROM animations WHERE ID_Animation = ?');
                                                                            $series_animation->execute(array($series_animation_associees['ID_Animation']));
                                                                            $series_animations = $series_animation->fetch();
                                                                            echo '<li>' . $series_animations['Nom'] . '</li>';
                                                                        } catch (PDOException $e) {
                                                                            echo "Erreur!: " . $e->getMessage() . "<br/>";
                                                                            die();
                                                                        }
                                                                    }
                                                                    echo '</ul>'; // Fermer la liste
                                                                } else {
                                                                    echo 'Aucune animation associées';
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