<!DOCTYPE html>
<html>
<head>
  <title>PPIChem_Search</title>
  <meta charset="UTF-8">
  <link rel="Stylesheet" href="style.css" type="text/css">
  <script type="text/javascript" src="../js/overlib.js"> </script>
</head>


<body style="margin: 0px; background: linear-gradient(to right, #778899 150px, #bbb 150px 153px, white 153px);">
  <div id="wrap">
    <div id="header"></div>
    <div id="fluid">
      <div id="sidebar"> <!-- afffichage de la barre de menue laterale -->
          <center><br>
            <a href="http://www.chemaxon.com" target="_blank" ><img src="buttons/chemaxon_button.png" WIDTH="100px" alt="ChemAxon"/></a><br>
            <a href="https://www.molport.com/shop/index" target="_blank" ><img src="buttons/molport_button.png" WIDTH="100px" alt="MolPort"/></a><br><br>
            <a href="http://10.36.3.233:8888/2p2i/Fr-PPIChemDB/php/ppichem.html" target="_blank" ><img src="buttons/ppichemdb_button.png" WIDTH="100px" alt="PPIChem"/></a><br>
            <a href="http://2p2idb.cnrs-mrs.fr/" target="_blank" ><img src="buttons/2p2idb_button.png" WIDTH="100px" alt="2P2Idb"/></a><br><br>
            <a href="marvin_interface.php"><img src="buttons/home_button.png" WIDTH="100px" alt="PPIChem_Search"/></a><br>
            <a href="historical.php"/><img src="buttons/historical_button.png" WIDTH="100px" alt="historical"/></a>
          </center>
      </div>


      <div id="content">

        <?php #recuperations des variables $_POST 
        $UNIQID = $_POST['UNIQID'];
        $real_smile = $_POST['smile'];
        $method = $_POST['method'];
        $similarityThreshold = $_POST['similarityThreshold'];
        $element = $_POST['element'];

        $START = "false"; #indique que le formulaire ne provient pas de la premiere page de l interface
        #permet d effectuer un retour sur la page precedetne pour modifier l affichage des resultats sans reeffectuer la recherche

        #envoie des variables dans le formulaire en tant que variables cachees
        echo '
          <form method="post" name="return" action="post_smile.php">
          <input id="start" name="start" type="hidden" value="'.$START.'">
          <input id="UNIQID" name="UNIQID" type="hidden" value="'.$UNIQID.'">
          <input id="smile" name="smile" type="hidden" value="'.$real_smile.'">
          <input id="method" name="method" type="hidden" value="'.$method.'">
          <input id="similarityThreshold" name="similarityThreshold" type="hidden" value="'.$similarityThreshold.'">
          <input id="element" name="element" type="hidden" value="'.$element.'">
          <input  type="image" name="retour" src="buttons/return_button.png" WIDTH="35px" alt="ok"/><i>Return</i>          
          </form>

        ';

        ?>
        <center>
          <?php #creation des noms de fichiers de resultats
          $DOSSIER = $_POST['NAME'];
          $file = $DOSSIER."results.sdf";
          $smile = $DOSSIER."result.smi";
          $file_svg = $DOSSIER."search_red.svg";
          $file_png = $DOSSIER."search_red.png";
          $file_csv = $DOSSIER."results.csv";



          $octet=filesize($file); #permet de verifier si un fichier est vide
          #s il est vide c est que jcsearch n a pas trouve d analogues donc on renvoie un message et aucun resultat ne s affiche
          if ($octet==0){
          echo "<center><br><br><br><br><br><br><br>
          <embed src=\"images/no_results.png\" type=\"image/svg+xml\" width=\"100\" height=\"100\"></center>
          <br>Sorry, no result found. 
          <br><br><br><a href=\"marvin_interface.php\"><img src=\"buttons/new_search.png\" WIDTH=\"150px\" alt=\"Search\"/>
          ";
          }else{ #execution si fichier non vide
            $number_results = $_POST['number_results'];
            $ORDER = $_POST['order'];
            $ORDERED = $_POST['ordered'];
            $SMILE = $_POST['smile'];
            $total_results = $_POST['total_results'];

            $descriptors_array = make_POST_array($_POST, $ORDER); #creation de la liste des informations a afficher
            $ORDER = get_order($descriptors_array, $ORDER); #recupere l ID de la colonne d information a utiliser pour afficher des resultats ordonnes
            $results_array = make_results_array($descriptors_array, $file); #creation de l array d arrays contenant toues les info a afficher

            create_csv($descriptors_array, $results_array, $DOSSIER); #creation d un fichier csv des resulats 



            $obabel = "/usr/local/bin/obabel"; #localisation de obabel
            $obabel_color_svg = "$obabel $file -s $smile red -l100 -xu -xC -O $file_svg"; #creation d une image vectoriel des representations 2D des hits
            shell_exec($obabel_color_svg);
            $obabel_color_png = "$obabel $file -s $smile red -l100 -xu -xC -O $file_png -xp2500"; #creation d un PNG des representations 2D des hits
            #le smiles est indique en rouge sur les molecules
            #le nombre de molecules sur le fichier est limite a 100
            #les hydrogenes ne sont pas affiches
            

            #affichage des boutons de telechargement des resultats
            echo '
            <a href="'.$file.'" download="'.$file.'" ><img src="buttons/sdf_format.png" WIDTH="150px"/></a>
            <a href="'.$file_csv.'" download="'.$file_csv.'"><img src="buttons/csv_format.png" WIDTH="150px"/></a>
            <a href="'.$file_svg.'" download="'.$file_svg.'"><img src="buttons/svg_format.png" WIDTH="150px"/></a>
            <a href="'.$file_png.'" download="'.$file_png.'"><img src="buttons/png_format.png" WIDTH="150px"/></a>
            <a href="'.$file_svg.'" target="_blank"><img src="buttons/large_view.png" WIDTH="150px"/></a>
            <br>
            <embed src="'.$file_svg.'" type="image/svg+xml" width="800" height="400" BORDER="1">
            <br><br>
            <br><tab><b>Reference:</b> '.$UNIQID.'</b></tab>
            <b>number of results:</b> '.$total_results.'<br><br><br>

            ';
            

            
            #affichage du tableau de resultats
            print_results ($descriptors_array, $results_array, $number_results, $ORDER, $ORDERED);
          }
          ?>
        </center>
      </div>
    </div>
  </div>
</body>
</html>






<?php
#=======================================
#             FUNCTIONS
#=======================================


function make_descriptors_names_array (){ #fonction permettant de creer la liste de tous les descripteurs
  $descriptors_names= array();
    $descriptors_names []='Integer';
    $descriptors_names []='AmbID';
    $descriptors_names []='MolPort ID';
    $descriptors_names []='Supplier';
    $descriptors_names []='Supplier Catalog No';
    $descriptors_names []='Mol Weight';
    $descriptors_names []='Prediction';
    $descriptors_names []='Formula';
    $descriptors_names []='SMILES';
    $descriptors_names []='INCHIKEY';
    $descriptors_names []='dissimilarity';
    return $descriptors_names;
}



function make_POST_array ($POST){ #creer la liste des descripteurs demandees
  $descriptors_names = make_descriptors_names_array();
  $descriptors_array[0]='Integer';
  foreach ($POST as $key => $value){
    if (is_int($key)){
    $descriptors_array [$key]=$descriptors_names[$key];
    }    
  }
  return $descriptors_array;
}



function get_order ($descriptors_array, $order){ #renvoie l ID de la colonne qui va servir a afficher les resultats dans un ordre precis
  $order = strval($order);
  $new_order = 0;
  $cpt = 0;
  foreach ($descriptors_array as $key => $value){
    if ($order == $key){
      $new_order = $cpt;
      break;
    }      
    $cpt = $cpt + 1;
  }
  return $new_order;
}



function make_results_array($descriptors_array, $file){ #creation d un array d arrays contenant les resultats souhaitees
  $results_array = array();
  $f = fopen($file, 'r');
  $cpt=1;
  $temporary_array = array();
  while (!feof($f)){
    $line=fgets($f);
    if (substr($line, 0, 4) == ">  <"){
      $Data_name = substr($line,4);
      $Data_name = substr_replace($Data_name, "", -2);
      if (in_array($Data_name, $descriptors_array)){
        $line=fgets($f);
        $temporary_array[]=substr($line,0,-1);
      }
    }elseif (substr($line, 0, 4) == "$$$$"){
      $results_array[$cpt]=$temporary_array;
      $cpt=$cpt+1;
      $temporary_array=array();
    }
  }
  fclose($f);
  return $results_array;
}



function create_csv ($descriptors_array, $results_array, $dossier){ #permet de creer un fichier de resulats a partir de l array de resultats
  $file = $dossier."results.csv";
  array_unshift($results_array, $descriptors_array);
  if(!file_exists($file)){
    unlink($file);
  }
  $f = fopen($file, 'w+');
  foreach ($results_array as $key => $value) {
    $temporary_array = $value;
    $line='';
    foreach ($temporary_array as $key2 => $value2) {
      $line=$line.$value2.";";
    }
    $line=substr($line, 0, -1);
    fputs($f, $line."\n");
  }
  fclose($f);
}



function arrange_array($results_array, $order, $ordered = ""){ #range l array en fonction de l ordre demande
  foreach($results_array as $key => $value) {
    $colomn[$key] = $value[$order];
  }
  if ($ordered == "decreasing"){
    array_multisort($colomn, SORT_DESC, $results_array); #ordre decroissant
  }else{
    array_multisort($colomn, SORT_ASC, $results_array); #ordre croissant
  }
  return $results_array;
}



function print_results ($descriptors_array, $results_array, $number_results, $order, $ordered){
  $results_array = arrange_array ($results_array, $order, $ordered);
  array_unshift($results_array, $descriptors_array); #array-unshift permet de fusionner le tableau de noms de colonne et le tableau de resultats
  foreach ($results_array as $key => $value) {
    if ($number_results == -1) break;
    $temporary_array = $value;
    if ($key == 0){
      echo '<table>
      <tr class="principal_line">';
    }elseif ($key%2 == 0){
      echo '<tr class="pair_line">';
    }else{
    echo '<tr class="odd_line">';      
    }
    foreach ($temporary_array as $key2 => $value2) {
      if ($key2 == 0 && $key != 0){
        $PNG=take_PNG($value2);    #en dessous la fonction overlibe permettant de generer une infobulle
        echo "<td><a href='http://10.36.3.233:8888/2p2i/Fr-PPIChemDB/php/show_ID.php?id=$value2' target=\"_popup\" onmouseover=\"return overlib('$PNG',BORDER,1,BGCOLOR,'#008080',CAPTION,'Fr-PPIChem_$value2',CELLPAD,5,FGCOLOR,'#FFFFFF',HAUTO, VAUTO,WRAP);\" onmouseout=\"return nd();\"/>$value2</td>";
      }else{
      echo "<td>$value2</td>";      
      }
    }
    echo '</tr>';
    $number_results=$number_results-1;
  }
  echo '</table><br>';
}



function take_PNG($ID){  #permet de recuperer l emplacement de l image correspondant a un ID donnee
$ID = intval($ID);
$min = 1;
$max = 1000;
  while ($ID>$max){
    $min=$min+1000;
    $max=$max+1000;
  }
  if ($max>10000){
    $max=10314;
  }
  $min=int_to_string($min);
  $max=int_to_string($max);
  $ID=int_to_string($ID);
  $dossier=$min."-".strval($max);
  $name="Fr-PPIChem_".$ID.".png";
  $way="PPI_PNG/".$dossier."/".$name;
  $PNG="<IMG SRC=$way WIDTH=200px></IMG>";
  return $PNG;
}



function int_to_string($n){ #fonction permettant de convertir entier en string a 5 chiffres
  $n = strval($n);
  while (strlen($n)!=5){
    $n='0'.$n;
  }
  return $n;
}




