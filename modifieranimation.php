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
    $verif_serie_exist = $dbh->prepare('SELECT * FROM animations WHERE ID_Animation = ?');
    $verif_serie_exist->execute(array($id_animation));
    if ($verif_serie_exist->rowCount() > 0) {
        $animation_infos = $verif_serie_exist->fetch();

        try {
            $serieQuery = $dbh->prepare('SELECT *,s.ID_Serie as ID_Serie_Serie FROM series s LEFT JOIN series_animations sa ON s.ID_Serie = sa.ID_Serie AND sa.ID_Animation = ?');

            $serieQuery->execute(array($id_animation));

            $serieAssocied = $serieQuery->fetchAll();

            // Utilisez la fonction array_filter pour obtenir directement les utilisateurs avec autorisation
            $serieassocies = array_filter($serieAssocied, function ($animation) {
                return !empty($animation['ID_Animation']);
            });

            // Utilisez la fonction array_filter pour obtenir directement les utilisateurs sans autorisation
            $seriedeassocies = array_filter($serieAssocied, function ($animation) {
                return empty($animation['ID_Animation']);
            });

            // Maintenant, vous pouvez utiliser $usersWithAuthorization, $usersAffected et $usersDeaffected comme nécessaire
        } catch (PDOException $e) {
            echo "Erreur!: " . $e->getMessage() . "<br/>";
            die();
        }
    } else {
        $error = 'Le numéro de l\'animation n\'est pas valide ou n\'existe pas';
        header('Location: ./listeanimation?messageerror=' . $error);
    }
} else {
    $error = 'Le numéro de l\'animation n\'est pas renseigné';
    header('Location: ./listeserie?messageerror=' . $error);
}

$extensionsGifAutorisees = array('gif');
$extensionsAudioAutorisees = array('mp3', 'wav');

if (isset($_POST['submit'])) {
    $nomAnimation = htmlspecialchars($_POST['nomanimation']);

    if (!empty($nomAnimation) && isset($nomAnimation)) {
        $animationexist = $dbh->prepare('SELECT Nom FROM animations WHERE Nom = ? AND ID_Animation != ?');
        $animationexist->execute(array($nomAnimation, $id_animation));

        if ($animationexist->rowCount() == 0) {
            // Gestion de la modification du fichier GIF
            $cheminGifReel = $animation_infos['Chemin_Gif_Reel'];
            $cheminGifFictif = $animation_infos['Chemin_Gif_Fictif'];

            // Limite des dimensions du GIF
            $maxWidth = 1000;
            $maxHeight = 1000;

            if (!empty($_FILES['gif_reel']['name'])) {
                $extensionGif = pathinfo($_FILES['gif_reel']['name'], PATHINFO_EXTENSION);

                if (in_array($extensionGif, $extensionsGifAutorisees)) {
                    $cheminGifReel = 'assets/img/' . basename($_FILES['gif_reel']['name']);

                    list($width, $height) = getimagesize($_FILES['gif_reel']['tmp_name']);
                    if ($width <= $maxWidth && $height <= $maxHeight) {
                        move_uploaded_file($_FILES['gif_reel']['tmp_name'], $cheminGifReel);
                        try {
                            $verif_gif_usage = $dbh->prepare('SELECT * FROM animations WHERE Chemin_Gif_Reel = ? AND ID_Animation != ?');
                            $verif_gif_usage->execute(array($animation_infos['Chemin_Gif_Reel'], $id_animation));
                            if ($verif_gif_usage->rowCount() == 0) {
                                try {
                                    unlink($animation_infos['Chemin_Gif_Reel']);
                                } catch (PDOException $e) {
                                    echo "Erreur!: " . $e->getMessage() . "<br/>";
                                    die();
                                }
                            }
                        } catch (PDOException $e) {
                            echo "Erreur!: " . $e->getMessage() . "<br/>";
                            die();
                        }
                    } else {
                        $erreur_taille_gif = true;
                    }
                } else {
                    $erreur_gif = true;
                }
            }

            if (!empty($_FILES['gif_fictif']['name'])) {
                $extensionGif = pathinfo($_FILES['gif_fictif']['name'], PATHINFO_EXTENSION);

                if (in_array($extensionGif, $extensionsGifAutorisees)) {
                    $cheminGifFictif = 'assets/img/' . basename($_FILES['gif_fictif']['name']);

                    list($width, $height) = getimagesize($_FILES['gif_fictif']['tmp_name']);
                    if ($width <= $maxWidth && $height <= $maxHeight) {
                        move_uploaded_file($_FILES['gif_fictif']['tmp_name'], $cheminGifFictif);
                        try {
                            $verif_gif_usage = $dbh->prepare('SELECT * FROM animations WHERE Chemin_Gif_Fictif = ? AND ID_Animation != ?');
                            $verif_gif_usage->execute(array($animation_infos['Chemin_Gif_Fictif'], $id_animation));
                            if ($verif_gif_usage->rowCount() == 0) {
                                try {
                                    unlink($animation_infos['Chemin_Gif_Fictif']);
                                } catch (PDOException $e) {
                                    echo "Erreur!: " . $e->getMessage() . "<br/>";
                                    die();
                                }
                            }
                        } catch (PDOException $e) {
                            echo "Erreur!: " . $e->getMessage() . "<br/>";
                            die();
                        }
                    } else {
                        $erreur_taille_gif = true;
                    }
                } else {
                    $erreur_gif = true;
                }
            }

            // Gestion de la modification du fichier audio
            $cheminAudio = $animation_infos['Chemin_Audio'];

            // Limite pour l'audio (durée)
            $maxAudioDuration = 30; // en secondes

            if (!empty($_FILES['audio']['name'])) {
                $extensionAudio = pathinfo($_FILES['audio']['name'], PATHINFO_EXTENSION);

                require 'include/getID3-master/getid3/getid3.php';
                $getID3 = new getID3;
                $audioInfo = $getID3->analyze($_FILES['audio']['tmp_name']);

                if ($audioInfo['playtime_seconds'] > $maxAudioDuration) {
                    $erreur_duree_audio = true;
                } else 
                if (in_array($extensionAudio, $extensionsAudioAutorisees)) {
                    $cheminAudio = 'assets/music/' . basename($_FILES['audio']['name']);
                    move_uploaded_file($_FILES['audio']['tmp_name'], $cheminAudio);
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
                } else {
                    $erreur_audio = true;
                }
            }

            if (!isset($erreur_gif)) {
                if (!isset($erreur_taille_gif)) {
                    if (!isset($erreur_duree_audio)) {
                        if (!isset($erreur_audio)) {
                            try {
                                $updateAnimation = $dbh->prepare('UPDATE animations SET Nom=?, Chemin_Gif_Reel=?, Chemin_Gif_Fictif=?, Chemin_Audio=? WHERE ID_Animation=?');
                                $updateAnimation->execute(array($nomAnimation, $cheminGifReel, $cheminGifFictif, $cheminAudio, $id_animation));
                                $success = "L'animation $nomAnimation a bien été modifiée !";
                                header('Location: ./listeanimation?message=' . $success);
                            } catch (PDOException $e) {
                                echo "Erreur!: " . $e->getMessage() . "<br/>";
                                die();
                            }
                        } else {
                            $erreur = 'Les extensions de fichier autorisées pour l\'audio sont uniquement du .mp3 ou .wav';
                        }
                    } else {
                        $erreur = "La durée de l'audio ne doit pas dépasser {$maxAudioDuration} secondes";
                    }
                } else {
                    $erreur = "Les dimensions du GIF ne doivent pas dépasser {$maxWidth}x{$maxHeight} pixels";
                }
            } else {
                $erreur = 'Les extensions de fichier autorisées pour l\'image sont uniquement du .gif';
            }
        } else {
            $erreur = 'Le nom de l\'animation est déjà utilisé';
        }
    } else {
        $erreur = "Tous les champs doivent être complétés";
    }
}

if (isset($_POST['submitaffectationserie'])) {
    $seriedeaffecte = htmlspecialchars($_POST['seriedeaffecte']);

    if (!empty($seriedeaffecte) && isset($seriedeaffecte)) {
        try {
            $insertserieassociee = $dbh->prepare('INSERT INTO series_animations (ID_Serie, ID_Animation) VALUES (?, ?)');
            $insertserieassociee->execute(array($seriedeaffecte, $id_animation));
            $success = "La série à bien été modifié !";
            header('Location: ./listeanimation?message=' . $success);
        } catch (PDOException $e) {
            echo "Erreur!: " . $e->getMessage() . "<br/>";
            die();
        }
    } else {
        $erreur = "Tous les champs doivent être complétés";
    }
}
if (isset($_POST['submitdeaffectationserie'])) {
    $serieaffecte = htmlspecialchars($_POST['serieaffecte']);

    if (!empty($serieaffecte) && isset($serieaffecte)) {
        try {
            $deleteserieassociee = $dbh->prepare('DELETE FROM series_animations WHERE ID_Animation = ? AND ID_Serie = ?');
            $deleteserieassociee->execute(array($id_animation, $serieaffecte));
            $success = "La série à bien été modifié !";
            header('Location: ./listeanimation?message=' . $success);
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
    <title>Modification d'une animation</title>
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
                    <h1 class="mt-4">Modification d'une animation</h1>
                    <ol class="breadcrumb mb-4">
                        <li class="breadcrumb-item">Pages</li>
                        <li class="breadcrumb-item"><a href="listeanimation">Liste des animations</li></a>
                        <li class="breadcrumb-item active">Modification d'une animation</li>
                    </ol>
                    <div class="row justify-content-center">
                        <div class="col-lg-7">
                            <div class="card-header">
                                <h3 class="text-center font-weight-light my-4">Modification d'une animation</h3>
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
                                        <form method="post" enctype="multipart/form-data">
                                            <div class="row mb-3">
                                                <div class="col-md-12 mt-3">
                                                    <div class="form-floating">
                                                        <input class="form-control" name="nomanimation" type="text" placeholder="Animaux" value="<?php echo $animation_infos['Nom'] ?>" required />
                                                        <label>Nom de l'animation</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <div class="card">
                                                        <div class="card-body">
                                                            <img src="<?php echo $animation_infos['Chemin_Gif_Reel']; ?>" alt="Gif réel de l'animation" class="rounded img-fluid" />
                                                            <div class="form-floating mt-3">
                                                                <input type="file" class="form-control" name="gif_reel" accept=".gif" />
                                                                <label>Nouvelle Image Réel GIF</label>
                                                            </div>
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
                                                            <div class="form-floating mt-3">
                                                                <input type="file" class="form-control" name="audio" accept="audio/*" />
                                                                <label>Nouveau Fichier Audio</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="card">
                                                        <div class="card-body">
                                                            <img src="<?php echo $animation_infos['Chemin_Gif_Fictif']; ?>" alt="Gif fictif de l'animation" class="rounded img-fluid" />
                                                            <div class="form-floating mt-3">
                                                                <input type="file" class="form-control" name="gif_fictif" accept=".gif" />
                                                                <label>Nouvelle Image Fictif GIF</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="mt-4 mb-0">
                                                <input type="submit" name="submit" class="btn btn-primary" value="Enregistrer">
                                            </div>
                                    </div>
                                    </form>
                                </div>
                            </div>
                            <div class="card mt-3 mb-5">
                                <div class="card-header">
                                    Affecté/Désaffecté une série
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <form method="post">
                                                <div class="card">
                                                    <div class="card-body">
                                                        <div class="form-floating mb-3">
                                                            <select class="form-control" name="seriedeaffecte" required>
                                                                <?php foreach ($seriedeassocies as $seriedeassocie) { ?>
                                                                    <option value="<?php echo $seriedeassocie['ID_Serie_Serie']; ?>"><?php echo $seriedeassocie['Nom'] ?></option>
                                                                <?php } ?>
                                                            </select>
                                                            <label>Série à affecté*</label>
                                                        </div>
                                                        <div class="mt-4 mb-0 text-center">
                                                            <input type="submit" name="submitaffectationserie" class="btn btn-primary " value="Enregistrer">
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
                                                            <select class="form-control" name="serieaffecte" required>
                                                                <?php foreach ($serieassocies as $serieassocie) { ?>
                                                                    <option value="<?php echo $serieassocie['ID_Serie_Serie']; ?>"><?php echo $serieassocie['Nom'] ?></option>
                                                                <?php } ?>
                                                            </select>
                                                            <label>Série à desaffecté*</label>
                                                        </div>
                                                        <div class="mt-4 mb-0 text-center">
                                                            <input type="submit" name="submitdeaffectationserie" class="btn btn-primary" value="Enregistrer">
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