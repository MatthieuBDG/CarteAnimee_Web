<?php
require 'include/connexion_bdd.php';

require 'include/verif_user_connect.php';


?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Liste des séries</title>
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
                    <h1 class="mt-4">Liste des séries</h1>
                    <ol class="breadcrumb mb-4">
                        <li class="breadcrumb-item">Pages</li>
                        <li class="breadcrumb-item"><a href="listeserie">Liste des séries</li></a>
                    </ol>
                    <?php if (isset($_GET['message']) && !isset($_GET['messageerror'])) { ?>
                        <div class="alert alert-success mb-3" role="success"><?php echo $_GET['message'] ?></div>
                    <?php } ?>
                    <?php if (isset($_GET['messageerror']) && !isset($_GET['message'])) { ?>
                        <div class="alert alert-danger mb-3" role="danger"><?php echo $_GET['messageerror'] ?></div>
                    <?php } ?>
                    <div class="card mb-4">
                        <div class="card-body">
                            <a href="ajoutserie" class="btn btn-primary mb-3">Ajouter une série</a>
                            <table id="datatablesSimple">
                                <thead>
                                    <tr>
                                        <th>Nom de série</th>
                                        <th>Enfants/Parents autorisés</th>
                                        <th>Animations associées</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    try {
                                        $series = $dbh->prepare('SELECT * FROM series');
                                        $series->execute(array());

                                        while ($serie = $series->fetch()) {
                                            echo '<tr>';
                                            echo '<td>' . $serie['Nom'] . '</td>';
                                            try {
                                                $series_user_autorise = $dbh->prepare('SELECT ID_User FROM autorisations_series WHERE ID_Serie = ?');
                                                $series_user_autorise->execute(array($serie['ID_Serie']));
                                                $series_user_autorise_count = $series_user_autorise->rowCount();

                                                echo '<td>';
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
                                                echo '</td>';
                                            } catch (PDOException $e) {
                                                echo "Erreur!: " . $e->getMessage() . "<br/>";
                                                die();
                                            }
                                            try {
                                                $series_animation_associee = $dbh->prepare('SELECT ID_Animation FROM series_animations WHERE ID_Serie = ?');
                                                $series_animation_associee->execute(array($serie['ID_Serie']));
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
                                            echo '<td>';
                                            echo '<a href="modifierserie?id_serie=' . $serie['ID_Serie'] . '" class="btn btn-primary btn-sm me-2">Modifier</a>';
                                            echo '<a href="supprimerserie?id_serie=' . $serie['ID_Serie'] . '" class="btn btn-danger btn-sm">Supprimer</a>';
                                            echo '</td>';
                                            echo '</tr>';
                                        }
                                    } catch (PDOException $e) {
                                        echo "Erreur!: " . $e->getMessage() . "<br/>";
                                        die();
                                    }
                                    ?>

                                </tbody>
                            </table>
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
    <script src="js/simple-datatables.min.js" crossorigin="anonymous"></script>
    <script src="js/datatables-simple-demo.js"></script>
</body>

</html>