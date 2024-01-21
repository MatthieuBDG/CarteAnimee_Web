<script>
    // Éviter le renvoi des données lorsque la page est rafraîchie
    if (window.history.replaceState) {
        window.history.replaceState(null, null, window.location.href);
    }
</script>
<?php
require 'include/connexion_bdd.php';

require 'include/verif_user_connect.php';

require 'include/verif_user_admin.php';

if (isset($_GET['id_utilisateur']) && !empty($_GET['id_utilisateur'])) {
    $id_utilisateur = $_GET['id_utilisateur'];
    $verif_utilisateur_exist = $dbh->prepare('SELECT * FROM users WHERE ID_User = ?');
    $verif_utilisateur_exist->execute(array($id_utilisateur));
    if ($verif_utilisateur_exist->rowCount() > 0) {
        $utilisateurs_infos = $verif_utilisateur_exist->fetch();
        try {
            $roles_no_utilisateur = $dbh->prepare('SELECT * FROM roles WHERE ID_Role != ?');
            $roles_no_utilisateur->execute(array($utilisateurs_infos['Role']));
        } catch (PDOException $e) {
            echo "Erreur!: " . $e->getMessage() . "<br/>";
            die();
        }
        try {
            $roles_utilisateur = $dbh->prepare('SELECT * FROM roles WHERE ID_Role = ?');
            $roles_utilisateur->execute(array($utilisateurs_infos['Role']));
        } catch (PDOException $e) {
            echo "Erreur!: " . $e->getMessage() . "<br/>";
            die();
        }
    } else {
        $error = 'Le numéro de l\'utilisateur n\'est pas valide ou n\'existe pas';
        header('Location: ./listeutilisateur?messageerror=' . $error);
    }
} else {
    $error = 'Le numéro de l\'utilisateur n\'est pas renseigné';
    header('Location: ./listeutilisateur?messageerror=' . $error);
}
if (isset($_POST['submitmdp'])) {
    $mdp = htmlspecialchars($_POST['mdp']);
    $mdpconfirm = htmlspecialchars($_POST['mdpconfirm']);
    if (!empty($mdp) && isset($mdp) || !empty($mdpconfirm) && isset($mdpconfirm)) {
        if ($mdp == $mdpconfirm) {
            if (strlen($mdp) >= 8) {
                $passwordhash = password_hash($mdp, PASSWORD_DEFAULT);
                try {
                    $updatemdpuser = $dbh->prepare('UPDATE users SET Mdp = ? WHERE ID_User = ?');
                    $updatemdpuser->execute(array($passwordhash, $id_utilisateur));
                    $success = 'Le mot de passe de l\'utilisateur ' . $utilisateurs_infos['Prenom'] . ' ' . $utilisateurs_infos['Nom'] . ' à bien été modifié !';
                    header('Location: ./listeutilisateur?message=' . $success);
                } catch (PDOException $e) {
                    echo "Erreur!: " . $e->getMessage() . "<br/>";
                    die();
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
}

if (isset($_POST['submit'])) {
    $nom = htmlspecialchars($_POST['nom']);
    $prenom = htmlspecialchars($_POST['prenom']);
    $email = htmlspecialchars($_POST['email']);
    if (isset($_POST['role'])) {
        $role = htmlspecialchars($_POST['role']);
    } else {
        $role = $utilisateurs_infos['Role'];
    }

    $vemail = '@';
    $vespace  = ' ';
    $espace = strpos($prenom, $vespace);
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        if ($espace === false) {
            if (!empty($nom) && isset($nom) || !empty($prenom) && isset($prenom) || !empty($email) && isset($email)  || !empty($role) && isset($role)) {
                $mailexist = $dbh->prepare('SELECT ID_User FROM users WHERE Email = ? AND ID_User != ?');
                $mailexist->execute(array($email, $id_utilisateur));
                if ($mailexist->rowCount() == 0) {
                    try {
                        $updateuser = $dbh->prepare('UPDATE users SET Prenom = ?,Nom = ?,Email = ?,Role = ? WHERE ID_User = ?');
                        $updateuser->execute(array($prenom, $nom, $email, $role, $id_utilisateur));
                        $success = 'L\'utilisateur ' . $prenom . ' ' . $nom . ' à bien été modifié !';
                        header('Location: ./listeutilisateur?message=' . $success);
                    } catch (PDOException $e) {
                        echo "Erreur!: " . $e->getMessage() . "<br/>";
                        die();
                    }
                } else {
                    $erreur = "L'adresse mail est déja utilisé";
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
    <title>Modification d'un utilisateur</title>
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
                    <h1 class="mt-4">Modification d'un utilisateur</h1>
                    <ol class="breadcrumb mb-4">
                        <li class="breadcrumb-item">Pages</li>
                        <li class="breadcrumb-item"><a href="listeutilisateur">Liste des utilisateurs</li></a>
                        <li class="breadcrumb-item active">Modification d'un utilisateur</li>
                    </ol>
                    <div class="row justify-content-center">
                        <div class="col-lg-7">
                            <div class="card-header">
                                <h3 class="text-center font-weight-light my-4">Modification d'un utilisateur</h3>
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
                                        Informations Générales
                                    </div>
                                    <div class="card-body">
                                        <form method="post">
                                            <div class="row mb-3">
                                                <div class="col-md-12">
                                                    <?php if ($_SESSION['ID_User'] <> $utilisateurs_infos['ID_User']) { ?>
                                                        <div class="form-floating mb-3">
                                                            <select class="form-control" name="role" required>
                                                                <?php foreach ($roles_utilisateur as $role_utilisateur) { ?>
                                                                    <option value="<?php echo $role_utilisateur['ID_Role']; ?>"><?php echo $role_utilisateur['Nom']; ?></option>
                                                                <?php } ?>
                                                                <?php foreach ($roles_no_utilisateur as $role_no_utilisateur) { ?>
                                                                    <option value="<?php echo $role_no_utilisateur['ID_Role']; ?>"><?php echo $role_no_utilisateur['Nom']; ?></option>
                                                                <?php } ?>
                                                            </select>
                                                            <label>Rôle</label>
                                                        </div>
                                                    <?php } ?>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-floating mb-3 mb-md-0">
                                                        <input class="form-control" type="text" name="prenom" placeholder="Entrer votre prénom" value="<?php echo $utilisateurs_infos['Prenom'] ?>" required />
                                                        <label>Prénom</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-floating">
                                                        <input class="form-control" type="text" name="nom" placeholder="Entrer votre nom" value="<?php echo $utilisateurs_infos['Nom'] ?>" required />
                                                        <label>Nom</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-floating mb-3">
                                                <input class="form-control" name="email" type="email" placeholder="prenom.nom@gmail.com" value="<?php echo $utilisateurs_infos['Email'] ?>" required />
                                                <label>Adresse Email</label>
                                            </div>
                                            <div class="mt-4 mb-0">
                                                <input type="submit" name="submit" class="btn btn-primary" value="Enregistrer">
                                            </div>
                                        </form>

                                    </div>
                                </div>
                            </div>
                            <div class="card mt-3 mb-5">
                                <div class="card-header">
                                    Mot de passe
                                </div>
                                <div class="card-body">
                                    <form method="post">
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
                                            <div class="mt-4 mb-0">
                                                <input type="submit" name="submitmdp" class="btn btn-primary" value="Enregistrer">
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