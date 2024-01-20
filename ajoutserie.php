<script>
    // Éviter le renvoi des données lorsque la page est rafraîchie
    if (window.history.replaceState) {
        window.history.replaceState(null, null, window.location.href);
    }
</script>
<?php
require 'include/connexion_bdd.php';

require 'include/verif_user_connect.php';

if (isset($_POST['submit'])) {
    $nomserie = htmlspecialchars($_POST['nomserie']);

    if (!empty($nomserie) && isset($nomserie)) {
        $serieexist = $dbh->prepare('SELECT Nom FROM series WHERE Nom = ?');
        $serieexist->execute(array($nomserie));
        if ($serieexist->rowCount() == 0) {
            try {
                $insertnewserie = $dbh->prepare('INSERT INTO series(Nom) VALUES (?)');
                $insertnewserie->execute(array($nomserie));
                $success = "La série $nomserie à bien été crée !";
                header('Location: ./listeserie?message=' . $success);
            } catch (PDOException $e) {
                echo "Erreur!: " . $e->getMessage() . "<br/>";
                die();
            }
        } else {
            $erreur = "Le nom de la série est déja utilisé";
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
    <title>Connexion</title>
    <link href="css/styles.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
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
                    <h1 class="mt-4">Ajout d'une série</h1>
                    <ol class="breadcrumb mb-4">
                        <li class="breadcrumb-item">Pages</li>
                        <li class="breadcrumb-item"><a href="listeserie">Liste des séries</li></a>
                        <li class="breadcrumb-item active">Ajout d'une série</li>
                    </ol>
                    <div class="row justify-content-center">
                        <div class="col-lg-7">
                            <div class="card-header">
                                <h3 class="text-center font-weight-light my-4">Ajout d'une série</h3>
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
                                                    <div class="form-floating">
                                                        <input class="form-control" name="nomserie" type="text" placeholder="Animaux" required />
                                                        <label>Nom de la série</label>
                                                    </div>
                                                </div>
                                                <div class="mt-4 mb-0">
                                                    <input type="submit" name="submit" class="btn btn-primary" value="Ajouter">
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