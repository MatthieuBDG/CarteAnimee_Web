<script>
    // Éviter le renvoi des données lorsque la page est rafraîchie
    if (window.history.replaceState) {
        window.history.replaceState(null, null, window.location.href);
    }
</script>
<?php
require 'include/connexion_bdd.php';

if (isset($_SESSION['ID_User'])) {
    header('Location: ./deconnexion');
}
// Gestion des messages en fonction des paramètres GET
if (isset($_GET['disconnect']) && !empty($_GET['disconnect']) && !isset($_SESSION['ID_User'])) {
    $message_warning = "Vous avez été déconnecté !";
}
/*
if (isset($_GET['connect']) && !empty($_GET['connect']) && !isset($_SESSION['ID_User'])) {
    $message_info = "Vous devez être connecté !";
}
if (isset($_GET['error']) && !empty($_GET['error'])) {
    $message_warning = "Vous devez être connecté en tant qu'administrateur !";
}
*/
if (isset($_POST['submit'])) {
    $email = htmlspecialchars($_POST['email']);
    $mdp = htmlspecialchars($_POST['mdp']);
    if (!empty($email) && isset($email) || !empty($mdp) && isset($mdp)) {
        try {
            $req_verif_existe_user = $dbh->prepare("SELECT * FROM users WHERE Email = ?");
            $req_verif_existe_user->execute(array($email));
            $resultat_verif_existe_user = $req_verif_existe_user->rowCount();
        } catch (PDOException $e) {
            echo "Erreur!: " . $e->getMessage() . "<br/>";
            die();
        }
        if ($resultat_verif_existe_user > 0) {
            $resultat_user = $req_verif_existe_user->fetch();
            // Comparaison du mot de passe envoyé via le formulaire avec celui enregistré en base
            $isPasswordCorrect = password_verify($mdp, $resultat_user['Mdp']);
            if ($isPasswordCorrect == 1) {
                try {
                    $req_recup_role = $dbh->prepare("SELECT Nom FROM roles WHERE ID_Role = ?");
                    $req_recup_role->execute(array($resultat_user['Role']));
                    $req_recup_role = $req_recup_role->fetch();
                } catch (PDOException $e) {
                    echo "Erreur!: " . $e->getMessage() . "<br/>";
                    die();
                }
                // Démarrage de la session et enregistrement des informations de l'utilisateur
                session_start();
                $_SESSION['ID_User'] = $resultat_user['ID_User'];
                $_SESSION['Prenom'] = $resultat_user['Prenom'];
                $_SESSION['Nom'] = $resultat_user['Nom'];
                $_SESSION['Email'] = $resultat_user['Email'];
                $_SESSION['ID_Role'] = $resultat_user['Role'];
                $_SESSION['Role'] = $req_recup_role['Nom'];
                header('Location: ./'); // Redirection vers la page d'accueil après connexion réussie
            } else {
                $erreur = "Email ou mot de passe incorrect";
            }
        } else {
            $erreur = "L'utilisateur n'existe pas !";
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
                    <h1 class="mt-4">Connexion</h1>
                    <ol class="breadcrumb mb-4">
                        <li class="breadcrumb-item">Pages</li>
                        <li class="breadcrumb-item active">Connexion</li>
                    </ol>
                    <div class="row justify-content-center">
                        <div class="col-lg-7">
                            <div class="card-header">
                                <h3 class="text-center font-weight-light my-4">Connexion à votre compte</h3>
                            </div>
                            <div class="card-body">

                                <?php if (isset($message_warning) && !isset($erreur)) { ?>
                                    <div class="alert alert-danger mb-3" role="alert"><?php echo $message_warning ?></div>
                                <?php } ?>
                                <?php if (isset($erreur)) { ?>
                                    <div class="alert alert-danger" role="alert">
                                        <?php echo $erreur ?>
                                    </div>
                                <?php }
                                ?>
                                <form method="post">
                                    <div class="row mb-3">
                                        <div class="form-floating mb-3">
                                            <input class="form-control" name="email" type="email" placeholder="prenom.nom@gmail.com" required />
                                            <label>Adresse Email</label>
                                        </div>
                                        <div class="form-floating mb-3">
                                            <input class="form-control" name="mdp" type="password" placeholder="Mot de passe" required />
                                            <label>Mot de passe</label>
                                        </div>
                                        <div class="mt-4 mb-0">
                                            <input type="submit" name="submit" class="btn btn-primary" value="Connexion">
                                        </div>
                                </form>
                            </div>
                            <div class="card-footer text-center py-3">
                                <div class="small"><a href="inscription">Besoin d'un compte ? S'inscrire !</a></div>
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="js/scripts.js"></script>
</body>

</html>