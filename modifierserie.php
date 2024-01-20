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

        try {
            $usersQuery = $dbh->prepare('SELECT *,u.ID_User as ID_User_User FROM users u LEFT JOIN autorisations_series a ON u.ID_User = a.ID_User AND a.ID_Serie = ?');

            $usersQuery->execute(array($id_serie));

            $usersWithAuthorization = $usersQuery->fetchAll();

            // Utilisez la fonction array_filter pour obtenir directement les utilisateurs avec autorisation
            $usersaffectes = array_filter($usersWithAuthorization, function ($user) {
                return !empty($user['ID_Serie']);
            });

            // Utilisez la fonction array_filter pour obtenir directement les utilisateurs sans autorisation
            $usersdeaffectes = array_filter($usersWithAuthorization, function ($user) {
                return empty($user['ID_Serie']);
            });

            // Maintenant, vous pouvez utiliser $usersWithAuthorization, $usersAffected et $usersDeaffected comme nécessaire
        } catch (PDOException $e) {
            echo "Erreur!: " . $e->getMessage() . "<br/>";
            die();
        }
    } else {
        $error = 'Le numéro de la série n\'est pas valide ou n\'existe pas';
        header('Location: ./listeserie?messageerror=' . $error);
    }
} else {
    $error = 'Le numéro de la série n\'est pas renseigné';
    header('Location: ./listeserie?messageerror=' . $error);
}
if (isset($_POST['submit'])) {
    $nomserie = htmlspecialchars($_POST['nomserie']);

    if (!empty($nomserie) && isset($nomserie)) {
        try {
            $updateserie = $dbh->prepare('UPDATE series SET Nom = ? WHERE ID_Serie = ?');
            $updateserie->execute(array($nomserie, $id_serie));
            $success = "La série $nomserie à bien été modifié !";
            header('Location: ./listeserie?message=' . $success);
        } catch (PDOException $e) {
            echo "Erreur!: " . $e->getMessage() . "<br/>";
            die();
        }
    } else {
        $erreur = "Tous les champs doivent être complétés";
    }
}
if (isset($_POST['submitaffectation'])) {
    $usersdeaffecte = htmlspecialchars($_POST['usersdeaffecte']);

    if (!empty($usersdeaffecte) && isset($usersdeaffecte)) {
        try {
            $insertautorisations_series = $dbh->prepare('INSERT INTO autorisations_series (ID_User, ID_Serie) VALUES (?, ?)');
            $insertautorisations_series->execute(array($usersdeaffecte, $id_serie));
            $success = "La série à bien été modifié !";
            header('Location: ./listeserie?message=' . $success);
        } catch (PDOException $e) {
            echo "Erreur!: " . $e->getMessage() . "<br/>";
            die();
        }
    } else {
        $erreur = "Tous les champs doivent être complétés";
    }
}
if (isset($_POST['submitdeaffectation'])) {
    $usersaffecte = htmlspecialchars($_POST['usersaffecte']);

    if (!empty($usersaffecte) && isset($usersaffecte)) {
        try {
            $deleteserieautorisation = $dbh->prepare('DELETE FROM autorisations_series WHERE ID_Serie = ? AND ID_User = ?');
            $deleteserieautorisation->execute(array($id_serie, $usersaffecte));
            $success = "La série à bien été modifié !";
            header('Location: ./listeserie?message=' . $success);
        } catch (PDOException $e) {
            echo "Erreur!: " . $e->getMessage() . "<br/>";
            die();
        }
    } else {
        $erreur = "Tous les champs doivent être complétés";
    }
}
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
                    <h1 class="mt-4">Modification d'une série</h1>
                    <ol class="breadcrumb mb-4">
                        <li class="breadcrumb-item">Pages</li>
                        <li class="breadcrumb-item"><a href="listeserie">Liste des séries</li></a>
                        <li class="breadcrumb-item active">Modification d'une série</li>
                    </ol>
                    <div class="row justify-content-center">
                        <div class="col-lg-7">
                            <div class="card-header">
                                <h3 class="text-center font-weight-light my-4">Modification d'une série</h3>
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
                                    <div class="card-header">
                                        Nom
                                    </div>
                                    <div class="card-body">
                                        <form method="post">
                                            <div class="row mb-3">
                                                <div class="col-md-12">
                                                    <div class="form-floating">
                                                        <input class="form-control" name="nomserie" type="text" placeholder="Animaux" value="<?php echo $series_infos['Nom'] ?>" require />
                                                        <label>Nom de la série</label>
                                                    </div>
                                                </div>
                                                <div class="mt-4 mb-0">
                                                    <input type="submit" name="submit" class="btn btn-primary" value="Enregistrer">
                                                </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div class="card mt-3">
                                <div class="card-header">
                                    Affecté/Désaffecté un Enfants/Parents
                                </div>
                                <div class="card-body">

                                    <div class="row">

                                        <div class="col-md-6">
                                            <form method="post">
                                                <div class="card">
                                                    <div class="card-body">
                                                        <div class="form-floating mb-3">
                                                            <select class="form-control" name="usersdeaffecte" required>
                                                                <?php foreach ($usersdeaffectes as $usersdeaffecte) { ?>
                                                                    <option value="<?php echo $usersdeaffecte['ID_User_User']; ?>"><?php echo $usersdeaffecte['Prenom'] . ' ' . $usersdeaffecte['Nom'] ?></option>
                                                                <?php } ?>
                                                            </select>
                                                            <label>Enfant/Parent à affecté*</label>
                                                        </div>
                                                        <div class="mt-4 mb-0 text-center">
                                                            <input type="submit" name="submitaffectation" class="btn btn-primary " value="Enregistrer">
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                        <div class="col-md-6">
                                            <form method="post">
                                                <div class="card">
                                                    <div class="card-body">
                                                        <div class="form-floating mb-3">
                                                            <select class="form-control" name="usersaffecte" required>
                                                                <?php foreach ($usersaffectes as $usersaffecte) { ?>
                                                                    <option value="<?php echo $usersaffecte['ID_User_User']; ?>"><?php echo $usersaffecte['Prenom'] . ' ' . $usersaffecte['Nom'] ?></option>
                                                                <?php } ?>
                                                            </select>
                                                            <label>Enfant/Parent à desaffecté*</label>
                                                        </div>
                                                        <div class="mt-4 mb-0 text-center">
                                                            <input type="submit" name="submitdeaffectation" class="btn btn-primary" value="Enregistrer">
                                                        </div>
                                                    </div>
                                            </form>
                                        </div>

                                    </div>
                                </div>
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