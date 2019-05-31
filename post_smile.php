<!DOCTYPE html>
<html>
<head>
	<title>PPIChem_Search</title>
	<meta charset="UTF-8">
	<link rel="Stylesheet" href="style.css" type="text/css">
	<script type="text/javascript" src="../../js/overlib.js"> </script>
	<script language='JavaScript' type='text/javascript'></script>
</head>

<body  style="margin: 0px; background: linear-gradient(to right, #778899 150px, #bbb 150px 153px, white 153px);">
	<div id="wrap">
	    <div id="header"></div>
	    <div id="fluid">
		    <div id="sidebar"> <!-- barre de menue -->
		        <br>
		        <center>
		        	<a href="http://www.chemaxon.com" target="_blank" ><img src="buttons/chemaxon_button.png" WIDTH="100px" alt="ChemAxon"/></a><br>
		        	<a href="https://www.molport.com/shop/index" target="_blank" ><img src="buttons/molport_button.png" WIDTH="100px" heigth="25" alt="MolPort"/></a><br><br>
		        	<a href="http://10.36.3.233:8888/2p2i/Fr-PPIChemDB/php/ppichem.html" target="_blank" ><img src="buttons/ppichemdb_button.png" WIDTH="100px" alt="PPIChem"/></a><br>
		            <a href="http://2p2idb.cnrs-mrs.fr/" target="_blank" ><img src="buttons/2p2idb_button.png" WIDTH="100px" alt="2P2Idb"/></a><br><br>
		        	<a href="marvin_interface.php"><img src="buttons/home_button.png" WIDTH="100px" alt="PPIChem_Search"/></a><br>
                	<a href="historical.php"/><img src="buttons/historical_button.png" WIDTH="100px" alt="historical"/></a>
		    	</center>
		    </div>
			<div>

				<?php
				set_time_limit(0);
				$UNIQID = $_POST['UNIQID']; #recupere l'ID  genere par la page precedente qui sera utilise comme reference dossier
				$START = $_POST['start']; #indique la source de la soumisson pour savoir si la recherche doit etre effectue ou non
				$DOSSIER = "output/".$UNIQID;
				if (!file_exists($DOSSIER)){
					mkdir ($DOSSIER); #creation du dossier qui contiendra tous les fichiers de resultats
				}

				$complete_name = $DOSSIER."/".$UNIQID."_";
				$SMILE_NAME = $complete_name."result.smi";
				$SMILE_PNG = $complete_name."smile.png";
				$SMILE = $_POST['smile'];
				create_smi ($SMILE, $SMILE_NAME);
				$obabel = "/usr/local/bin/obabel";
				$Bash_obabel = "$obabel $SMILE_NAME -xC -xb none -O $SMILE_PNG -xp250"; #genere une representation 2D du smile soumis
				shell_exec($Bash_obabel); #execute la commande le terminal

				$T = $_POST['method'] ; 
				if ($T == "i"){ #determine la methode demande
					$Similarity = $_POST['similarityThreshold'] ;
					$Element = False;
				}elseif ($T == "e") {
					$Similarity = False ;			
					$Element = $_POST['element'];
				}else{
					$Similarity = False ;
					$Element = False;
				}
				?>			
				<br><br><br><br>
				<center>
					<div id=left_coin>
						<strong><i>PARAMETERS </i></strong> 
						<br><br>
						<?php #affichage des parametres de recherche
						$TYPE = make_array_options ($T); #creer la liste d options disponibles pour selectionner celle soumis par l utilisateur
						echo "<br><b>Search type:</b> ${TYPE}" ;
						if ($T == "i"){
							echo "<br><b>Similarity threshold:</b> ${Similarity}" ;
						}elseif ($T == "e"){
							echo "<br><b>Complementary element:</b> ${Element}";
						}
						echo '
						<br><br>
						<b>Smile:</b> '.$SMILE;
						?>
					</div>

					<?php
					echo '<img src="'.$SMILE_PNG.'" style=" border:dashed #2c3143;  position: absolute; top: 30px; margin:100px; ">
					<br><br><br>';


					if ($START != "false"){ #si $STAR est egale a True cela signifie que la page a ete genere apres soumission du formulaire de la page d interface de marvin, on effectue donc la recherche appropie
						JCsearch ($SMILE, $T, $Similarity, $Element, $complete_name);
						$total_results = create_index ($SMILE, $T, $Similarity, $Element, $complete_name); #creation d un fichier index dans le dossier reportoriant les informations liees a la recherche
					}

					$descriptors_names = make_descriptors_names_array($T); #creer la liste de descripteurs de la molecule pour afficher pour chaque un checkbox dans le formulaire


					?>
					<form method="post" name="queryForm" action="results.php"> 
					<?php #creation du formulaire qui sera soumis a la page de resultats
						#envoie a partir du formulaire des informations liees a la recherche dans des variables caches
						echo '
						<input id="NAME" name="NAME" type="hidden" value="'.$complete_name.'">
						<input id="UNIQID" name="UNIQID" type="hidden" value="'.$UNIQID.'">
						<input id="smile" name="smile" type="hidden" value="'.$SMILE.'">
						<input id="method" name="method" type="hidden" value="'.$T.'">
						<input id="total_results" name="total_results" type="hidden" value="'.$total_results.'">';
						if ($T == "i"){
							echo '
							<input id="similarityThreshold" name="similarityThreshold" type="hidden" value="'.$Similarity.'">
							<input id="element" name="element" type="hidden" value="False">';
						}elseif ($T == "e"){
							echo'
							<input id="similarityThreshold" name="similarityThreshold" type="hidden" value="False">
							<input id="element" name="element" type="hidden" value="'.$Element.'">';
						}else{
							echo'
							<input id="similarityThreshold" name="similarityThreshold" type="hidden" value="False">
							<input id="element" name="element" type="hidden" value="False">';
						}

						foreach ($descriptors_names as $key => $elt){ #creer la liste de chekbox dans le formulaire
							if ($key==0){
								echo '<div id=coin>';
							}elseif ($key==10){
								echo "<br><br>";
							}elseif ($key==5){
								echo '</div>
								<br><br><br>
								<div id=coin>';
							}if ($key != 0){
								echo "<tab><input type=\"checkbox\" name=$key value=$key >$elt</tab>";
							}
						}
						echo '</div><br><br><br>';
						?>

						<div id=coin>
							<font size="1" face="helvetica"> Number max of résults: </font>
							<select id="number_results" name="number_results">
					            <option>10</option>
					            <option>20</option>
					            <option selected="">30</option>
					            <option>50</option>
					            <option>100</option>
					        </select>

							<tab></tab>
					        <font size="1" face="helvetica"> Order by: </font>
							<select id="order" name="order">
								<?php
								foreach ($descriptors_names as $key => $elt){
										echo "<option value=$key>$elt</option>";
								}
								?>
					        </select>
					        <tab></tab>
					        <select id="ordered" name="ordered">
						        <option selected="" value="increasing">Increasing</option>
						        <option value="decreasing">Decreasing</option>
					        </select>
					    </div>
						<br><br><br>
						<input type="submit" value="results" />
					</form>
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


function make_array_options ($TYPE){ #fonction creant la liste des options disponibles 
	$options = array();
		$options ['f']='Exact';
		$options ['ff']='Exact Fragment';
		$options ['s']='Substructure';
		$options ['i']='Similarity';
		$options ['u']='Superstructure';
		$options ['e']='with a particular element';		
		$T = $options[$TYPE];
		return $T;
}


function make_descriptors_names_array ($T){ #fonction creeant la liste des descripteurs existants
	$descriptors_names= array();
		$descriptors_array []='Integer';
		$descriptors_array []='AmbID';
		$descriptors_array []='MolPort ID';
		$descriptors_array []='Supplier';
		$descriptors_array []='Supplier Catalog No';
		$descriptors_array []='Mol Weight';
		$descriptors_array []='Prediction';
		$descriptors_array []='Formula';
		$descriptors_array []='SMILES';
		$descriptors_array []='INCHIKEY';
		if ($T == "i"){
		$descriptors_array []='dissimilarity';
		} 
		return $descriptors_array;
}


function create_index ($smile, $T, $Similarity = False, $Element = False, $NAME){ #fonction de creation de l index de la recherche
	$start =  ">  <Integer>";
	$results = $NAME."results.sdf";	
	$NAME = $NAME."index.txt";

	$CommandBash = "grep -c \"$start\" $results";
	$number = (shell_exec($CommandBash));

	if(file_exists($NAME)){
    	unlink($NAME);
	}
	$f = fopen($NAME, "w+");
	$line = "Smile: $smile\n";
	$TYPE = make_array_options ($T);	
	$line = $line."Type: $TYPE\n";
	if ($T == "i"){
		$line = $line."similarity Threshold: $Similarity\n";
	}elseif ($T == "e") {
		$line = $line."Added Element: $Element\n";
	}
	$line= $line."Number of results: $number";
	fputs($f, $line);
	fclose($f);
	return $number;
}


function create_smi ($smile, $NAME){ #permet d'envoyer le smile de la recherche dans un fichier texte pour la fonction obabel
	if(file_exists($NAME)){
    	unlink($NAME);
	}
	$f = fopen($NAME, "w+");
	fputs($f, $smile);
	fclose($f);
}


function JCsearch ($SMILE, $T, $Similarity = False, $Element = False, $complete_name){ #execution de la recherche demande
	$jcsearch = "/Applications/ChemAxon/JChem/bin//jcsearch" ;
	$complete_name = $complete_name."results.sdf";
	if ($T == 'i'){
		$CommandBash="$jcsearch -q \"$SMILE\" -t:i:$Similarity -f sdf input/Fr-PPIChem.sdf > $complete_name" ;
	}elseif ($T == 'e'){
		$CommandBash="$jcsearch -q \"$SMILE\" --and -q \"$Element\" -f sdf input/Fr-PPIChem.sdf > $complete_name" ;
	}else{
		$CommandBash="$jcsearch -q \"$SMILE\" -t:$T -f sdf input/Fr-PPIChem.sdf > $complete_name" ;
	}
	shell_exec($CommandBash) ;
}


?>


