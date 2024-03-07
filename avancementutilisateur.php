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
    <title>Avancement Utilisateurs</title>
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
                    <h1 class="mt-4">Avancement Utilisateurs</h1>
                    <ol class="breadcrumb mb-4">
                        <li class="breadcrumb-item">Pages</li>
                        <li class="breadcrumb-item"><a href="avancementutilisateur">Avancement Utilisateurs</li></a>
                    </ol>
                    <?php if (isset($_GET['message']) && !isset($_GET['messageerror'])) { ?>
                        <div class="alert alert-success mb-3" role="success"><?php echo $_GET['message'] ?></div>
                    <?php } ?>
                    <?php if (isset($_GET['messageerror']) && !isset($_GET['message'])) { ?>
                        <div class="alert alert-danger mb-3" role="danger"><?php echo $_GET['messageerror'] ?></div>
                    <?php } ?>
                    <div class="card mb-4">
                        <div class="card-body">
                            <table id="datatablesSimple">
                                <thead>
                                    <tr>
                                        <th>Enfants/Parents</th>
                                        <th>Nom de série</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    try {
                                        $users = $dbh->prepare('SELECT * FROM users');
                                        $users->execute(array());

                                        while ($user = $users->fetch()) {
                                            echo '<tr>';
                                            try {
                                                $series_user_autorise = $dbh->prepare('SELECT ID_Serie FROM autorisations_series WHERE ID_User = ?');
                                                $series_user_autorise->execute(array($user['ID_User']));
                                                $series_user_autorise_count = $series_user_autorise->rowCount();
                                                echo '<td>' . $user['Prenom'] . ' ' . $user['Nom'] . '</td>';
                                                echo '<td>';
                                                if ($series_user_autorise_count > 0) {
                                                    echo '<ul>'; // Ouvrir la liste
                                                    while ($series_user_autorises = $series_user_autorise->fetch()) {
                                                        try {
                                                            $series_user = $dbh->prepare('SELECT Nom FROM series WHERE ID_Serie = ?');
                                                            $series_user->execute(array($series_user_autorises['ID_Serie']));
                                                            $series_users = $series_user->fetch();
                                                            try {
                                                                $avancement_series = $dbh->prepare('SELECT Pourcentage FROM avancement_series WHERE ID_User = ? AND ID_Serie = ?');
                                                                $avancement_series->execute(array($user['ID_User'], $series_user_autorises['ID_Serie']));
                                                                $avancement_series_count = $avancement_series->rowCount();
                                                                if ($avancement_series_count > 0) {
                                                                    $avancement_series = $avancement_series->fetch();
                                                                    echo '<li>' . $series_users['Nom'] . ' (' . $avancement_series['Pourcentage'] . '%)</li>';
                                                                } else {
                                                                    echo '<li>' . $series_users['Nom'] . '</li>';
                                                                }
                                                            } catch (PDOException $e) {
                                                                echo "Erreur!: " . $e->getMessage() . "<br/>";
                                                                die();
                                                            }
                                                        } catch (PDOException $e) {
                                                            echo "Erreur!: " . $e->getMessage() . "<br/>";
                                                            die();
                                                        }
                                                    }
                                                    echo '</ul>'; // Fermer la liste
                                                } else {
                                                    echo 'Aucun série associées';
                                                }
                                                echo '</td>';
                                            } catch (PDOException $e) {
                                                echo "Erreur!: " . $e->getMessage() . "<br/>";
                                                die();
                                            }
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