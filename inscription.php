<?php
require 'include/connexion_bdd.php';
$roles = $dbh->prepare('SELECT * FROM roles');
$roles->execute(array());
if (isset($_POST['submit'])) {
    $nom = htmlspecialchars($_POST['nom']);
    $prenom = htmlspecialchars($_POST['prenom']);
    $email = htmlspecialchars($_POST['email']);
    $mdp = htmlspecialchars($_POST['mdp']);
    $mdpconfirm = htmlspecialchars($_POST['mdpconfirm']);
    $role = htmlspecialchars($_POST['role']);
    $vemail = '@';
    $vespace  = ' ';
    $espace = strpos($prenom, $vespace);
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        if ($espace === false) {
            if (!empty($nom) && isset($nom) || !empty($prenom) && isset($prenom) || !empty($email) && isset($email)  || !empty($mdp) && isset($mdp) || !empty($mdpconfirm) && isset($mdpconfirm)|| !empty($role) && isset($role)) {
                if ($mdp == $mdpconfirm) {
                    if (strlen($mdp) >= 8) {
                        $mailexist = $dbh->prepare('SELECT ID_User FROM users WHERE Email = ?');
                        $mailexist->execute(array($email));
                        if ($mailexist->rowCount() == 0) {
                            $passwordhash = password_hash($mdp, PASSWORD_DEFAULT);
                            $insertnewuser = $dbh->prepare('INSERT INTO users(Prenom,Nom,Email,Mdp,Role) VALUES (?,?,?,?,?)');
                            $insertnewuser->execute(array($prenom, $nom, $email, $passwordhash, $role));
                        } else {
                            $erreur = "L'adresse mail est déja utilisé";
                        }
                    } else {
                        $erreur = "Le mot de passe doit faire minimum 8 caractères";
                    }
                } else {
                    $erreur = "Les deux mots de passe ne correspondent pas";
                }
            } else {
                $erreur = "Tous les champs doivent être complétés";
            }
        } else {
            $erreur = "Le champ prénom ne doit pas contenir d'espaces";
        }
    } else {
        $erreur = "Adresse mail invalide";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Inscription</title>
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
                    <h1 class="mt-4">Inscription</h1>
                    <ol class="breadcrumb mb-4">
                        <li class="breadcrumb-item">Pages</li>
                        <li class="breadcrumb-item active">Inscription</li>
                    </ol>
                    <div class="row justify-content-center">
                        <div class="col-lg-7">
                            <div class="card-header">
                                <h3 class="text-center font-weight-light my-4">Création de compte</h3>
                            </div>
                            <div class="card-body">
                                <?php
                                if (isset($erreur)) { ?>
                                    <div class="alert alert-danger" role="alert">
                                        <?php echo $erreur ?>
                                    </div>
                                <?php }
                                ?>
                                <form method="post">
                                    <div class="row mb-3">
                                        <div class="form-floating mb-3">
                                            <select class="form-control" name="role" required>
                                                <?php foreach ($roles as $role) { ?>
                                                    <option value="<?php echo $role['ID_Role']; ?>"><?php echo $role['Nom']; ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-floating mb-3 mb-md-0">
                                                <input class="form-control" type="text" name="prenom" placeholder="Entrer votre prénom" required />
                                                <label>Prénom</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <input class="form-control" type="text" name="nom" placeholder="Entrer votre nom" required />
                                                <label>Nom</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-floating mb-3">
                                        <input class="form-control" name="email" type="email" placeholder="prenom.nom@gmail.com" required />
                                        <label>Adresse Email</label>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <div class="form-floating mb-3 mb-md-0">
                                                <input class="form-control" name="mdp" type="password" placeholder="Mot de passe" required />
                                                <label>Mot de passe</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-floating mb-3 mb-md-0">
                                                <input class="form-control" name="mdpconfirm" type="password" placeholder="Confirmation de mot de passe" required />
                                                <label>Confirmation mot de passe</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mt-4 mb-0">
                                        <input type="submit" name="submit" class="btn btn-primary" value="Enregistrer">
                                    </div>
                                </form>
                            </div>
                            <div class="card-footer text-center py-3">
                                <div class="small"><a href="connexion">Vous avez un compte ? Accéder à la connexion</a></div>
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