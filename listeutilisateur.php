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
    <title>Liste des Utilisateurs</title>
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
                    <h1 class="mt-4">Liste des Utilisateurs</h1>
                    <ol class="breadcrumb mb-4">
                        <li class="breadcrumb-item">Pages</li>
                        <li class="breadcrumb-item"><a href="listeutilisateur">Liste des Utilisateurs</li></a>
                    </ol>
                    <?php if (isset($_GET['message']) && !isset($_GET['messageerror'])) { ?>
                        <div class="alert alert-success mb-3" role="success"><?php echo $_GET['message'] ?></div>
                    <?php } ?>
                    <?php if (isset($_GET['messageerror']) && !isset($_GET['message'])) { ?>
                        <div class="alert alert-danger mb-3" role="danger"><?php echo $_GET['messageerror'] ?></div>
                    <?php } ?>
                    <div class="card mb-4">
                        <div class="card-body">
                            <a href="inscription" class="btn btn-primary mb-3">Ajouter un utilisateur</a>
                            <table id="datatablesSimple">
                                <thead>
                                    <tr>
                                        <th>Prenom & Nom</th>
                                        <th>Adresse Email</th>
                                        <?php if ($_SESSION['ID_Role'] == 1) { ?>
                                            <th>Docteur associé</th>
                                        <?php  } ?>
                                        <th>Rôle associé</th>

                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    try {
                                        if ($_SESSION['ID_Role'] == 1) {
                                            $users = $dbh->prepare('SELECT * FROM users ORDER BY Role');
                                            $users->execute(array());
                                        } else {
                                            $users = $dbh->prepare('SELECT * FROM users u, users_liaison ul WHERE u.ID_User = ul.ID_User_Patient AND ID_User_Docteur = ? ORDER BY Role');
                                            $users->execute(array($_SESSION['ID_User']));
                                        }
                                        while ($user = $users->fetch()) {
                                            echo '<tr>';
                                            echo '<td>' . $user['Prenom'] . ' ' . $user['Nom'] . '</td>';
                                            echo '<td>' . $user['Email'] . '</td>';
                                            try {
                                                $req_recup_role = $dbh->prepare("SELECT Nom FROM roles WHERE ID_Role = ?");
                                                $req_recup_role->execute(array($user['Role']));
                                                $req_recup_role = $req_recup_role->fetch();
                                            } catch (PDOException $e) {
                                                echo "Erreur!: " . $e->getMessage() . "<br/>";
                                                die();
                                            }
                                            if ($_SESSION['ID_Role'] == 1) {
                                                if ($user['Role'] == 3) {
                                                    try {
                                                        $req_docteur_associee = $dbh->prepare("SELECT ID_User_Docteur FROM users u, users_liaison ul WHERE u.ID_User = ul.ID_User_Patient AND ID_User = ?");
                                                        $req_docteur_associee->execute(array($user['ID_User']));
                                                        $req_docteur_associee = $req_docteur_associee->fetch();
                                                    } catch (PDOException $e) {
                                                        echo "Erreur!: " . $e->getMessage() . "<br/>";
                                                        die();
                                                    }
                                                    try {
                                                        $req_docteur_associee2 = $dbh->prepare("SELECT Prenom,Nom FROM users WHERE ID_User = ?");
                                                        $req_docteur_associee2->execute(array($req_docteur_associee['ID_User_Docteur']));
                                                        $req_docteur_associee2 = $req_docteur_associee2->fetch();
                                                    } catch (PDOException $e) {
                                                        echo "Erreur!: " . $e->getMessage() . "<br/>";
                                                        die();
                                                    }
                                                    echo '<td>' . $req_docteur_associee2['Prenom'] . ' ' . $req_docteur_associee2['Nom'] . '</td>';
                                                } else {
                                                    echo '<td></td>';
                                                }
                                            }
                                            echo '<td>' . $req_recup_role['Nom'] . '</td>';
                                            echo '<td>';
                                            echo '<a href="modifierutilisateur?id_utilisateur=' . $user['ID_User'] . '" class="btn btn-primary btn-sm me-2">Modifier</a>';
                                            if ($_SESSION['ID_User'] <> $user['ID_User']) {
                                                echo '<a href="supprimerutilisateur?id_utilisateur=' . $user['ID_User'] . '" class="btn btn-danger btn-sm">Supprimer</a>';
                                            }
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