<script>
    // Éviter le renvoi des données lorsque la page est rafraîchie
    if (window.history.replaceState) {
        window.history.replaceState(null, null, window.location.href);
    }
</script>
<?php
require 'include/connexion_bdd.php';

require 'include/verif_user_connect.php';

if (isset($_GET['id_animation']) && !empty($_GET['id_animation'])) {
    $id_animation = $_GET['id_animation'];
    $verif_animation_exist = $dbh->prepare('SELECT * FROM animations WHERE ID_Animation = ?');
    $verif_animation_exist->execute(array($id_animation));
    if ($verif_animation_exist->rowCount() > 0) {
        $animation_infos = $verif_animation_exist->fetch();
    } else {
        $error = 'Le numéro de l\'animation n\'est pas valide ou n\'existe pas';
        header('Location: ./listeanimation?messageerror=' . $error);
    }
} else {
    $error = 'Le numéro de l\'animation n\'est pas renseigné';
    header('Location: ./listeanimation?messageerror=' . $error);
}
if (isset($_POST['submit'])) {
    try {
        $verif_gif_usage = $dbh->prepare('SELECT * FROM animations WHERE Chemin_Gif = ? AND ID_Animation != ?');
        $verif_gif_usage->execute(array($animation_infos['Chemin_Gif'], $id_animation));
        if ($verif_gif_usage->rowCount() == 0) {
            try {
                unlink($animation_infos['Chemin_Gif']);
            } catch (PDOException $e) {
                echo "Erreur!: " . $e->getMessage() . "<br/>";
                die();
            }
        }
    } catch (PDOException $e) {
        echo "Erreur!: " . $e->getMessage() . "<br/>";
        die();
    }
    try {
        $verif_audio_usage = $dbh->prepare('SELECT * FROM animations WHERE Chemin_Audio = ? AND ID_Animation != ?');
        $verif_audio_usage->execute(array($animation_infos['Chemin_Audio'], $id_animation));
        if ($verif_audio_usage->rowCount() == 0) {
            try {
                unlink($animation_infos['Chemin_Audio']);
            } catch (PDOException $e) {
                echo "Erreur!: " . $e->getMessage() . "<br/>";
                die();
            }
        }
    } catch (PDOException $e) {
        echo "Erreur!: " . $e->getMessage() . "<br/>";
        die();
    }
    try {
        $deleteanimation = $dbh->prepare('DELETE FROM animations WHERE ID_Animation = ?');
        $deleteanimation->execute(array($id_animation));
    } catch (PDOException $e) {
        echo "Erreur!: " . $e->getMessage() . "<br/>";
        die();
    }
    try {
        $deleteseries_animations = $dbh->prepare('DELETE FROM series_animations WHERE ID_Animation = ?');
        $deleteseries_animations->execute(array($id_animation));
        
        $success = 'L\'animation ' . $animation_infos['Nom'] . ' à bien été supprimé !';
        header('Location: ./listeanimation?message=' . $success);
    } catch (PDOException $e) {
        echo "Erreur!: " . $e->getMessage() . "<br/>";
        die();
    }

}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Suppression d'une animation</title>
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
                    <h1 class="mt-4">Suppression d'une animation</h1>
                    <ol class="breadcrumb mb-4">
                        <li class="breadcrumb-item">Pages</li>
                        <li class="breadcrumb-item"><a href="listeanimation">Liste des animations</li></a>
                        <li class="breadcrumb-item active">Suppression d'une animation</li>
                    </ol>
                    <div class="row justify-content-center">
                        <div class="col-lg-7">
                            <div class="card-header">
                                <h3 class="text-center font-weight-light my-4">Suppression d'une animation</h3>
                            </div>
                            <div class="card-body">
                                <?php
                                if (isset($erreur)) { ?>
                                    <div class="alert alert-danger" role="alert">
                                        <?php echo $erreur ?>
                                    </div>
                                <?php }
                                ?>
                                <div class="card mb-5">
                                    <div class="card-body">
                                        <form method="post">
                                            <div class="row mb-3">
                                                <div class="col-md-12">
                                                    <div class="form-floating mb-3">
                                                        <input class="form-control" type="text" placeholder="Feu" value="<?php echo $animation_infos['Nom'] ?>" disabled />
                                                        <label>Nom de la l'animation</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <div class="card">
                                                        <div class="card-body">
                                                            <img src="<?php echo $animation_infos['Chemin_Gif']; ?>" alt="Gif de l'animation" class="rounded img-fluid" />
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="card">
                                                        <div class="card-body">
                                                            <audio controls class="w-100">
                                                                <source src="<?php echo $animation_infos['Chemin_Audio']; ?>" type="audio/mpeg">
                                                                Your browser does not support the audio tag.
                                                            </audio>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="card">
                                                    <div class="card-body">
                                                        <h4>Séries associées : </h4>
                                                        <?php
                                                        try {
                                                            $series_associe = $dbh->prepare('SELECT ID_Serie FROM series_animations WHERE ID_Animation = ?');
                                                            $series_associe->execute(array($id_animation));
                                                            $series_associe_count = $series_associe->rowCount();

                                                            echo '<td>';
                                                            if ($series_associe_count > 0) {
                                                                echo '<ul>'; // Ouvrir la liste
                                                                while ($series_associes = $series_associe->fetch()) {
                                                                    try {
                                                                        $series_animation = $dbh->prepare('SELECT Nom FROM series WHERE ID_Serie = ?');
                                                                        $series_animation->execute(array($series_associes['ID_Serie']));
                                                                        $series_animations = $series_animation->fetch();
                                                                        echo '<li>' . $series_animations['Nom'] . '</li>';
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