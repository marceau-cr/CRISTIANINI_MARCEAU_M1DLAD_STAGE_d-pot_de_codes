<!DOCTYPE html>
<html>
<head>
    <title>PPIChem_Search</title>
    <meta charset="UTF-8">     
    <link rel="Stylesheet" href="style.css" type="text/css">
    <script src="https://marvinjs.chemicalize.com/v1/7a92b9394f2441b19aa80b65cb87c594/client-settings.js"></script>
    <script src="https://marvinjs.chemicalize.com/v1/client.js"></script>
    <script type="text/javascript" src="input/ppichem.js"></script>
    <script language='JavaScript' type='text/javascript'>

    var sketcherLoaded=false
    twIndex=0;

    function help(){ //fonction d affichage de l aide
        var w = window.open("queryhelp.html",
            "queryhelp"+(twIndex++).toString(),
            "resizable=yes,width=470,height=270,scrollbars=yes,menubar=yes");
        w.focus();
    }


    function submitIfLoaded(editor) { //recupere le smile associe a la molecule dessine
        form=document.queryForm;
        if(sketcherLoaded) {
            editor.then(function (marvin) {
            marvin.exportStructure("smiles").then(function (result) {
                form.smile.value = result;
                form.submit();
                });
            });
        } else {
            alert("Page loading. Please wait.");
        }
    }


    function disabler() { //permet de desactiver les options si le critere n est pas rempli
        form = document.queryForm;
        var index = form.method.selectedIndex;
        var value = form.method.options[index].value;

        if(value != "i") {
        form.similarityThreshold.disabled=true;
        }
        else {
        form.similarityThreshold.disabled=false;
        }

        if(value != "e") {
        form.element.disabled=true;
        }
        else {
        form.element.disabled=false;
        }
    }


    function updateFromSmilesStr() { //affiche la molecule du textarea sur le sketcher
        form=document.queryForm;
        mysmile = form.smilesField.value ;
        editor.then(function (marvin) {
            marvin.importStructure("smiles", mysmile);
        });
    }


    </script>


</head>


<body onload="sketcherLoaded=true;" style="margin: 0px; background: linear-gradient(to right, #778899 150px, #bbb 150px 153px, #eeeeee 153px);"> 
    <div id="wrap">
        <div id="header"></div>
        <div id="fluid">
            <div id="sidebar">
            <br>
            <center>
                <a href="http://www.chemaxon.com" target="_blank" ><img src="buttons/chemaxon_button.png" WIDTH="100px" alt="ChemAxon"/></a><br>
                <a href="https://www.molport.com/shop/index" target="_blank" ><img src="buttons/molport_button.png" WIDTH="100px" alt="MolPort"/></a><br><br>
                <a href="http://10.36.3.233:8888/2p2i/Fr-PPIChemDB/php/ppichem.html" target="_blank" ><img src="buttons/ppichemdb_button.png" WIDTH="100px" alt="PPIChem"/></a><br>
                <a href="http://2p2idb.cnrs-mrs.fr/" target="_blank" ><img src="buttons/2p2idb_button.png" WIDTH="100px" alt="2P2Idb"/></a><br><br>
                <a href="historical.php"/><img src="buttons/historical_button.png" WIDTH="100px" alt="historical"/></a>

            </center>   
            </div>

            <center>
            <table rules="all"> 
            <td id="sketch" style="width: 800px; height: 550px; padding:25px; margin: 20px; border: 2px  solid darkgrey;">
            <script>
            var editor = ChemicalizeMarvinJs.createEditor("#sketch");
            // affichage du sketcher
            editor.then(function (marvin) { 
                marvin.setDisplaySettings({'chiralFlagVisible' : false}) //permet de cacher le message associer a cette variable
                //on ajoute ensuite des boutons sur la barre d outil ouest permettant d afficher des molecules 
                var ShowDrug = {
                    "name": "aspirin",
                    "toolbar": "W",
                    "image-url": "https://fr.seaicons.com/wp-content/uploads/2016/05/Aspirin-icon.png"
                };
                var PPI1 = {
                    "name": "Fr-PPIChem_08410",
                    "toolbar": "W",
                    "image-url": "https://img.icons8.com/metro/420/1.png"
                };
                var PPI2 = {
                    "name": "Fr-PPIChem_08450",
                    "toolbar": "W",
                    "image-url": "https://img.icons8.com/metro/420/2.png"
                };
                var PPI3 = {
                    "name": "Fr-PPIChem_08625",
                    "toolbar": "W",
                    "image-url": "https://img.icons8.com/metro/420/3.png"
                };
                marvin.addButton(ShowDrug, function () {
                    marvin.importStructure("name", "aspirin");
                });
                marvin.addButton(PPI1, function () {
                    marvin.importStructure("smile", "C1=CC(=CC(=C1)S(=O)(=O)N)NC(=S)NC2=CC(=C(C=C2)F)Cl");
                });
                marvin.addButton(PPI2, function () {
                    marvin.importStructure("smile", "C1=CC(=CC(=C1)NC(=S)NC2=CC(=C(C=C2)F)[N+](=O)[O-])Cl");
                });
                marvin.addButton(PPI3, function () {
                    marvin.importStructure("smile", "C1=CC(=CC(=C1)S(=O)(=O)NCC2=CC=CO2)NC(=S)NC3=CC(=C(C=C3)F)Cl");
                });            

            });

            </script></td>


            <tr class="tdcenter">             
                <form method="post" name="queryForm" action="post_smile.php">
                    <?php
                    $UNIQID = uniqid(); #genere un id unique
                    $time = date("Y-m-d_H-i"); #recupere l'heure et la date actuelle
                    $UNIQID = $time."_".$UNIQID; #nom du dossier qui sera cree
                    $START = "true"; #variable permetant d indiquer que le formulaire provient du la premiere page et non de la troisieme 
                    #(pas de retour en arriere)
                    echo '<input id="UNIQID" name="UNIQID" type="hidden" value="'.$UNIQID.'"> 
                    <input id="start" name="start" type="hidden" value="'.$START.'">'; #on ajoute des variables cache dans le formulaire
                    ?>
                    <br>
                    <table><td>
                    <input type="hidden" name="smile"></input> 
                    <font size="1" face="helvetica"> Search Method: </font>
                    <select id="method" name="method" onchange="disabler();">
                        <option value="f">Exact</option>
                        <option value="ff">Exact Fragment</option>
                        <option value="s">Substructure</option>
                        <option value="i">Similarity</option>
                        <option value="u">Superstructure</option>
                        <option value="e">With a particular element</option>
                    </select>

                    <br><br>
                    <font size="1" face="helvetica"> Dissimilarity threshold: </font>
                    <select id="similarityThreshold" name="similarityThreshold" disabled="">
                        <option>0.10</option>
                        <option>0.20</option>
                        <option>0.30</option>
                        <option>0.40</option>
                        <option selected="">0.50</option>
                        <option>0.60</option>
                        <option>0.70</option>
                        <option>0.80</option>
                        <option>0.90</option>
                        <option>0.95</option>
                        <option>0.99</option>
                    </select>

                    <br><br>
                    <font size="1" face="helvetica"> Add Element: </font>
                    <select id="element" name="element" disabled="">
                        <option selected="" value="Br">Bromine</option>
                        <option value="C(O)=O">carboxilic acid</option>                        
                        <option value="Cl">Chlorine</option>
                        <option value="F">Fluorine</option>
                        <option value="NS(=O)=O">Sulfonamide</option> 

                    </select>
                    </td>


                    <br><br>
                    <td></td>
                    <td><table cellspacing="0" cellpadding="8;" style="border: 1px solid transparent">
                        <tr><font size="2" face="helvetica">Paste or type ID or SMILE</font></tr>
                        <tr>             
                            <center><textarea id="smilesField" name="smilesField" spellcheck="false" class="flex_search_box">e.g. c1ccccc1Cl</textarea>
                        </tr>
                        <tr>
                            <input type="button" value="Load Smile" onclick="showsmile();"> <!-- showsmile est une fonction d'un script JC permetnat de recuperer le smile en fonction de l ID -->
                        </tr>
                    <br><br>
                    </table></td>


                    <tr>
                    <td><a href="javascript:help()"><img src="buttons/need_help_button.png" WIDTH="100px" alt="help"></a></td>
                    <td><a href="javascript:submitIfLoaded(editor)"><img src="buttons/search_button.png" WIDTH="150px" alt="Search"/></a></td>
                    </tr>
                </form>
            </tr>
            </table>
            </table></div> 
    </div>
</div>
</body>
</html>