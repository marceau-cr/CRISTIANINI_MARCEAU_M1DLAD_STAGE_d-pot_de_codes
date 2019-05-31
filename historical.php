<!DOCTYPE html>
<html>
<head>
	<title>PPIChem_Search</title>
	<meta charset="UTF-8">
	<link rel="Stylesheet" href="style.css" type="text/css">
</head>

<body  style="margin: 0px; background: linear-gradient(to right, #778899 150px, #bbb 150px 153px, white 153px);">
	<div id="wrap">
	    <div id="header"></div>
	    <div id="fluid">
		    <div id="sidebar">
		        <br>
		        <center>
		        	<a href="http://www.chemaxon.com" target="_blank" ><img src="buttons/chemaxon_button.png" WIDTH="100px" alt="ChemAxon"/></a><br>
		        	<a href="https://www.molport.com/shop/index" target="_blank" ><img src="buttons/molport_button.png" WIDTH="100px" heigth="25" alt="MolPort"/></a><br><br>
		        	<a href="http://10.36.3.233:8888/2p2i/Fr-PPIChemDB/php/ppichem.html" target="_blank" ><img src="buttons/ppichemdb_button.png" WIDTH="100px" alt="PPIChem"/></a><br>
		            <a href="http://2p2idb.cnrs-mrs.fr/" target="_blank" ><img src="buttons/2p2idb_button.png" WIDTH="100px" alt="2P2Idb"/></a><br><br>
		        	<a href="marvin_interface_v9.php"><img src="buttons/home_button.png" WIDTH="100px" alt="PPIChem_Search"/></a>
		    	</center>
		    </div>

			<div id="content">
				<br><br><br>
				<center>
					<form method="post" action="historical.php">
						<font size="2" face="helvetica">Paste or type reference</font>
			            <br>          
			            <textarea id="reference" name="reference" spellcheck="false" class="flex_search_box"></textarea>
			            <br>
		                <input type="submit" name="button" value="get folder">
		            </form>


					<?php
					#Si un formulaire non vide a ete soumis on peut executer l'affichage des resultats 
					if ($_SERVER["REQUEST_METHOD"] == "POST" && $_POST["button"] == "get folder" && $_POST['reference']!=""){
						$reference = $_POST['reference'];
						$file_zip = $reference.".tgz";
						$DOSSIER = "output/".$reference;
						if (is_dir($DOSSIER) == true){ #Si la reference entree correspond a un nom de dossier alors on affiche le resultat
							echo ("<br><br><hr><br><br>");
							echo '<div id=left_coin style="left:75px; padding-bottom:10px;">';		
							echo ('<b>'.$reference.'</b><br><br>'); #on affiche la reference
							$file = $DOSSIER."/".$reference."_index.txt"; 
							$f = fopen ($file, 'r'); #lecture du fichier index du dossier
							while (!feof($f)){  #on lit l'integralite de l index contenant le smile la methode de recherche et le nombre de resultats
				    			$line=fgets($f);
				    			echo ($line."<br>");
				    		}
				    		fclose($f); #fermeture du fichier
				    		echo "</div>";
				    		if (!file_exists("output_zip".$file_zip)){ #si le dossier zip n existe pas alors on le cree
				    			$bash_zip = ("/usr/bin/tar cvfz output_zip/".$file_zip." -C output ".$reference); #commande pour compresser un dossier
				    			shell_exec($bash_zip);
				    		}
				    		echo "<br><br>";	    		
				    		echo '<a href="output_zip/'.$file_zip.'" download="output_zip/'.$file_zip.'"><img src="buttons/download_button.png" WIDTH="100px"/></a>'; #bouton de telechargement
						}else{
							echo "<br><br>Invalid reference";
						}
					}
					?>
				</center>
		 	</div>
		</div>
	</div>
</body>
</html>

