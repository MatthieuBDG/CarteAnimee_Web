<script>
    // Éviter le renvoi des données lorsque la page est rafraîchie
    if (window.history.replaceState) {
        window.history.replaceState(null, null, window.location.href);
    }
</script>
<?php
require 'include/connexion_bdd.php';
require 'include/verif_user_connect.php';
// Vérification des extensions

$extensionsGifAutorisees = array('gif');
$extensionsAudioAutorisees = array('mp3', 'wav');

if (isset($_POST['submit'])) {
    $nomAnimation = htmlspecialchars($_POST['nomanimation']);
    $cheminAudio = 'assets/music/' . basename($_FILES['audio']['name']);
    $cheminGifReel = 'assets/img/' . basename($_FILES['gif_reel']['name']);
    $cheminGifFictif = 'assets/img/' . basename($_FILES['gif_fictif']['name']);

    if (!empty($nomAnimation) && isset($nomAnimation) && !empty($cheminAudio) && isset($cheminAudio) && !empty($cheminGifReel) && isset($cheminGifReel) && !empty($cheminGifFictif) && isset($cheminGifFictif)) {
        $animationexist = $dbh->prepare('SELECT Nom FROM animations WHERE Nom = ?');
        $animationexist->execute(array($nomAnimation));

        if ($animationexist->rowCount() == 0) {
            // Gestion de l'upload du fichier GIF
            $extensionGifReel = pathinfo($_FILES['gif_reel']['name'], PATHINFO_EXTENSION);
            $extensionGifFictif = pathinfo($_FILES['gif_fictif']['name'], PATHINFO_EXTENSION);

            // Limite des dimensions du GIF
            $maxWidth = 1000;
            $maxHeight = 1000;

            // Limite pour l'audio (durée)
            $maxAudioDuration = 30; // en secondes

            // Vérification des dimensions du GIF
            list($width_reel, $height_reel) = getimagesize($_FILES['gif_reel']['tmp_name']);
            list($width_fictif, $height_fictif) = getimagesize($_FILES['gif_fictif']['tmp_name']);

            // Gestion de l'upload du fichier audio
            $extensionAudio = pathinfo($_FILES['audio']['name'], PATHINFO_EXTENSION);

            // Vérification de la durée de l'audio avec getID3
            require 'include/getID3-master/getid3/getid3.php';
            $getID3 = new getID3;
            $audioInfo = $getID3->analyze($_FILES['audio']['tmp_name']);

            if ($width_fictif > $maxWidth || $height_fictif > $maxHeight) {
                $erreur = "Les dimensions du GIF Fictif ne doivent pas dépasser {$maxWidth}x{$maxHeight} pixels";
            } else if ($width_reel > $maxWidth || $height_reel > $maxHeight) {
                $erreur = "Les dimensions du GIF Réel ne doivent pas dépasser {$maxWidth}x{$maxHeight} pixels";
            } else
            if ($audioInfo['playtime_seconds'] > $maxAudioDuration) {
                $erreur = "La durée de l'audio ne doit pas dépasser {$maxAudioDuration} secondes";
            } else {
                if (in_array($extensionGifReel, $extensionsGifAutorisees) || in_array($extensionGifFictif, $extensionsGifAutorisees)) {
                    if (in_array($extensionAudio, $extensionsAudioAutorisees)) {

                        move_uploaded_file($_FILES['gif_reel']['tmp_name'], $cheminGifReel);
                        move_uploaded_file($_FILES['gif_fictif']['tmp_name'], $cheminGifFictif);
                        move_uploaded_file($_FILES['audio']['tmp_name'], $cheminAudio);

                        try {
                            $insertAnimation = $dbh->prepare('INSERT INTO animations(Nom, Chemin_Gif_Reel, Chemin_Gif_Fictif, Chemin_Audio) VALUES (?, ?, ?, ?)');
                            $insertAnimation->execute(array($nomAnimation, $cheminGifReel, $cheminGifFictif, $cheminAudio));
                            $success = "L'animation $nomAnimation a bien été ajoutée !";
                            header('Location: ./listeanimation?message=' . $success);
                        } catch (PDOException $e) {
                            echo "Erreur!: " . $e->getMessage() . "<br/>";
                            die();
                        }
                    } else {
                        $erreur = 'Les extensions de fichier autorisées pour l\'audio sont uniquement du .mp3 ou .wav';
                    }
                } else {
                    $erreur = 'Les extensions de fichier autorisées pour l\'image sont uniquement du .gif';
                }
            }
        }
    }
}

?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Ajout d'une animation</title>
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
                    <h1 class="mt-4">Ajout d'une animation</h1>
                    <ol class="breadcrumb mb-4">
                        <li class="breadcrumb-item">Pages</li>
                        <li class="breadcrumb-item"><a href="listeanimation">Liste des animations</li></a>
                        <li class="breadcrumb-item active">Ajout d'une animation</li>
                    </ol>
                    <div class="row justify-content-center">
                        <div class="col-lg-7">
                            <div class="card-header">
                                <h3 class="text-center font-weight-light my-4">Ajout d'une animation</h3>
                            </div>
                            <div class="card-body">
                                <?php if (isset($erreur)) { ?>
                                    <div class="alert alert-danger" role="alert">
                                        <?php echo $erreur ?>
                                    </div>
                                <?php } ?>
                                <div class="card">
                                    <div class="card-body">
                                        <form method="post" enctype="multipart/form-data"> <!-- Ajout de l'attribut enctype pour gérer les fichiers -->
                                            <!-- Ajout des champs pour les fichiers GIF et audio -->
                                            <div class="row mb-3">
                                                <div class="col-md-12">
                                                    <div class="form-floating">
                                                        <input class="form-control" name="nomanimation" type="text" placeholder="Nom de l'animation" value="<?php if (isset($nomAnimation)) {
                                                                                                                                                                echo $nomAnimation;
                                                                                                                                                            } ?>" />
                                                        <label>Nom de l'animation</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-md-12">
                                                    <div class="form-floating">
                                                        <input type="file" class="form-control" name="gif_reel" accept=".gif" required />
                                                        <label>Image GIF Réel</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-md-12">
                                                    <div class="form-floating">
                                                        <input type="file" class="form-control" name="gif_fictif" accept=".gif" required />
                                                        <label>Image GIF Fictif</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-md-12">
                                                    <div class="form-floating">
                                                        <input type="file" class="form-control" name="audio" accept="audio/*" required />
                                                        <label>Fichier Audio</label>
                                                    </div>
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