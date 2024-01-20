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
    <title>Liste des animations</title>
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
                    <h1 class="mt-4">Liste des animations</h1>
                    <ol class="breadcrumb mb-4">
                        <li class="breadcrumb-item">Pages</li>
                        <li class="breadcrumb-item"><a href="listeanimation">Liste des animations</li></a>
                    </ol>
                    <?php if (isset($_GET['message']) && !isset($_GET['messageerror'])) { ?>
                        <div class="alert alert-success mb-3" role="success"><?php echo $_GET['message'] ?></div>
                    <?php } ?>
                    <?php if (isset($_GET['messageerror']) && !isset($_GET['message'])) { ?>
                        <div class="alert alert-danger mb-3" role="danger"><?php echo $_GET['messageerror'] ?></div>
                    <?php } ?>
                    <div class="card mb-4">
                        <div class="card-body">
                            <a href="ajoutanimation" class="btn btn-primary mb-3">Ajouter une animation</a>
                            <table id="datatablesSimple">
                                <thead>
                                    <tr>
                                        <th>Nom de l'animation</th>
                                        <th>GIF</th>
                                        <th>Audio</th>
                                        <th>Série associée</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    try {
                                        $animations = $dbh->prepare('SELECT * FROM animations');
                                        $animations->execute(array());

                                        while ($animation = $animations->fetch()) {
                                            echo '<tr>';
                                            echo '<td>' . $animation['Nom'] . '</td>';
                                            echo '<td>' . $animation['Chemin_Gif'] . '</td>';
                                            echo '<td>' . $animation['Chemin_Audio'] . '</td>';
                                            try {
                                                $series_associe = $dbh->prepare('SELECT ID_Serie FROM series_animations WHERE ID_Animation = ?');
                                                $series_associe->execute(array($animation['ID_Animation']));
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
                                            echo '<td>';
                                            echo '<a href="modifieranimation?id_animation=' . $animation['ID_Animation'] . '" class="btn btn-primary btn-sm me-2">Modifier</a>';
                                            echo '<a href="supprimeranimation?id_animation=' . $animation['ID_Animation'] . '" class="btn btn-danger btn-sm">Supprimer</a>';
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