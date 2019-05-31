<!DOCTYPE html>
<html>
<head>
    <title>Dose Response</title>
    <meta charset="UTF-8">     
    <link rel="Stylesheet" href="style.css" type="text/css">
</head>


<body bgcolor="#eeeeee">
    <div id="wrap">
        <div id="header"></div>
        <div id="fluid">
            <div id="sidebar"></div>
     	</div>
     </div>


<br><br>
<center>
	<form method="post" action="dose_response.php" enctype="multipart/form-data">
		<label for="mon_fichier">Fichier (format CSV | max. 1 Mo) :</label><br />
		<input type="hidden" name="MAX_FILE_SIZE" value="1000" />
		<input type="file" name="fichier" id=fichier />
        <input type="submit" name="submit" value="submit" /><br><br><br>
    </form>



<?php
#$_FILES['test']['name'];     //Le nom original du fichier, comme sur le disque du visiteur (exemple : mon_icone.png).
#$_FILES['test']['type'];     //Le type du fichier. Par exemple, cela peut être « image/png ».
#$_FILES['test']['size'];     //La taille du fichier en octets.
#$_FILES['test']['tmp_name']; //L'adresse vers le fichier uploadé dans le répertoire temporaire.
#$_FILES['test']['error'];    //Le code d'erreur, qui permet de savoir si le fichier a bien été uploadé.

$erreur ="";
if ($_FILES['fichier']['error'] > 0) $erreur = "Erreur lors du transfert";
if ($_FILES['fichier']['size'] > 1000 ) $erreur = "Le fichier est trop gros";
echo $erreur;

$extension_valide = "csv";
$extension_upload = strtolower(  substr(  strrchr($_FILES['fichier']['name'], '.')  ,1)  );
if ( $extension_upload == $extension_valide){
 echo "Extension correcte<br>";
}


$R = "/usr/local/bin/R";



$nom = $_FILES['fichier']['name'];
$nom_sortie = substr($nom, 0, -4)."_normalized".".csv";
$resultat = move_uploaded_file($_FILES['fichier']['tmp_name'],$nom);
if ($resultat){
    $commandBach = "$
    R --vanilla $nom $nom_sortie < normalization.r";
    echo $commandBach."<br>";
    shell_exec($commandBach);
    echo "<a href=\"$nom_sortie\" download=\"$nom_sortie\">Download</a>";
}







?>
    <br><br><br><hr><br>
    <form method="post" action="dose_response.php" enctype="multipart/form-data">
        <label for="mon_fichier">Fichier (format CSV | max. 1 Mo) :</label><br />
        <input type="hidden" name="MAX_FILE_SIZE" value="1000" />
        <input type="file" name="fichier2" id=fichier2 />
        <input type="checkbox" id="Normalized" name="Normalized">Data normalized?<br><br>
        <label>min:</label>
        <input type="number" name="max" id="max" min="0">
        <label>max:</label>
        <input type="number" name="max" id="max" min="0"><br><br>
        <input type="checkbox" id="Normalize" name="Normalize">Normalize
        <input type="checkbox" id="graph" name="graph">global Graph<br><br>
        <input type="radio" id="plica" name="plica" value="1" checked>Monoplicat
        <input type="radio" id="plica" name="plica" value="2">Diplicat
        <input type="radio" id="plica" name="plica" value="3">Triplicat<br><br>
        <input type="submit" name="submit" value="submit" /><br><br><br>
    </form>
    <hr><br>RESULTATS


<?php
echo "<br>";
$random = uniqid('', true);
echo $random;



$erreur ="";
if ($_FILES['fichier2']['error'] > 0) $erreur = "Erreur lors du transfert";
if ($_FILES['fichier2']['size'] > 1000 ) $erreur = "Le fichier est trop gros";
echo $erreur;

$extension_valide = "csv";
$extension_upload = strtolower(  substr(  strrchr($_FILES['fichier2']['name'], '.')  ,1)  );
if ( $extension_upload == $extension_valide){
 echo "Extension correcte<br>";
}


$R = "/usr/local/bin/R";



$nom = $_FILES['fichier']['name'];
$nom_sortie = $random."_normalized".".csv";
$resultat = move_uploaded_file($_FILES['fichier']['tmp_name'],$nom);
if ($resultat){
    $commandBach = "$
    R --vanilla $nom $nom_sortie < normalization.r";
    echo $commandBach."<br>";
    shell_exec($commandBach);
    echo "<a href=\"$nom_sortie\" download=\"$nom_sortie\">Download</a>";
}

?>



</body>








</html>