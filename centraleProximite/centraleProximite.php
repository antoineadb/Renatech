<?php

include_once 'class/Securite.php';
$db = BD::connecter();
$manager = new Manager($db);
?>
<!----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
CENTRALES DE PROXIMITES
------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------>                                               
<script>
    function affichecentraleproximite() {
        document.getElementById('etapecentraleproximite').style.display = 'block';
        document.getElementById('centraleProximite').style.display = 'block';
        document.getElementById('cptcentraleproximite').style.display = 'block';
        document.getElementById('chckcentraleproximite').style.display = 'block';
        document.getElementById('descriptioncentraleproximite').style.display = 'block';
        document.getElementById('affiche_centrale_proximite').value = 'oui';
    }
    function masquecentraleproximite() {
        document.getElementById('etapecentraleproximite').style.display = 'none';
        document.getElementById('centraleProximite').style.display = 'none';
        document.getElementById('cptcentraleproximite').style.display = 'none';
        document.getElementById('chckcentraleproximite').style.display = 'none';
        document.getElementById('descriptioncentraleproximite').style.display = 'none';
        document.getElementById('affiche_centrale_proximite').value = 'non';
        
    }
</script>
<input type='text' id='affiche_centrale_proximite' style='display: none' value='non'>
<fieldset style="border: 1px solid #5d8ba2;border-radius: 5px;color: midnightblue;font-family: verdana;margin-top: 20px;padding: 10px 15px 15px;width: 949px;" >
    <legend><?php echo TXT_CENTRALEPART; ?>&nbsp;<a class="infoBulle" href="#"><img src='<?php echo "/" . REPERTOIRE; ?>/styles/img/help.gif' height="13px" width="13px"/><span style="width: auto;min-width: 700px" >
                <?php echo removeDoubleQuote(affiche('TXT_AIDECENTRALEPROXIMITE')); ?></span></a></legend>
    <?php 
    if (isset($_GET['numProjet'])) {
        $idprojet = $manager->getSingle2('select idprojet from projet where numero=?', $_GET['numProjet']);
        $centraleselectionne = $manager->getSingle2("select libellecentrale from centrale,concerne  where idcentrale_centrale=idcentrale and idprojet_projet=?", $idprojet);
    } elseif (isset($_GET['idprojet'])) {
        $idprojet = $_GET['idprojet'];
        $centraleselectionne = $manager->getSingle2("select libellecentrale from centrale,concerne  where idcentrale_centrale=idcentrale and idprojet_projet=?", $idprojet);
    }
    if (isset($libelleCentrale)) {
        $question = TXT_QUESTIONCPARTCONNU . $libelleCentrale;
    }elseif (isset($centraleselectionne)) {
        $question = TXT_QUESTIONCPARTCONNU . $centraleselectionne;
    } else {
        $question = TXT_QUESTIONCENTRALEPARTENAIRE;
    }
    if (isset($_GET['idprojet'])) {
        $centraleproximite = $manager->getSingle2("select centraleproximite from projet where idprojet=?", $_GET['idprojet']);
        
    } elseif (isset($_GET['numProjet'])) {
        $idprojet = $manager->getSingle2('select idprojet from projet where numero=?', $_GET['numProjet']);
        $centraleproximite = $manager->getSingle2("select centraleproximite from projet where idprojet=?", $idprojet);
    } else {
        $centraleproximite = 'FALSE';
    }
?>
    <input type="hidden" id="centraleproximitevaleur" name="centraleproximitevaleur" >
    <input type="hidden" id="commentairephase2Valeur" name="commentairephase2Valeur" >
<?php
    if ($centraleproximite == 'TRUE' ) {
        ?>
    
        <table>
            <tr>
                <td>
                    <label for="centraleproximite" id="default" style="width:918px;margin-left: 20px;;margin-top: 10px"><?php echo $question; ?></label>                                    
                    <input type= "radio" data-dojo-type="dijit/form/RadioButton" name="centrale_proximite"   value="TRUE" checked="checked" class="btRadio" style="margin-left: 20px;" onclick="affichecentraleproximite()" ><?php echo TXT_OUI; ?>
                    <input type= "radio" data-dojo-type="dijit/form/RadioButton" name="centrale_proximite"  value="FALSE" class="btRadio" style="margin-left: 20px;" onclick="masquecentraleproximite()"><?php echo TXT_NON; ?>
                </td>
            </tr>
        </table>
        <table>
            <tr>
                <td style="float: left;" >                  
                    <fieldset style="font-size: 1.1em;font-style: italic;width: 520px;display: block" id="centraleProximite" >
                        <legend style="margin-left:20px;margin-top: 10px"><?php echo TXT_SELECTCENTRALES; ?></legend>
                        <?php $idcentraleProjet =(int) $manager->getSingle2("select idcentrale_centrale from concerne where idprojet_projet=?", $idprojet);?>
                        <?php if (!empty($idcentraleProjet)) {?>
                            <div id='chckcentraleproximite' style=" margin-bottom: 15px; margin-left: 25px; margin-top: 10px; display: inline-block;">
                                <?php
                                $arrayCentraleRegion = $manager->getListbyArray("SELECT r.libelleregion, cp.libellecentraleproximite, cp.idcentraleproximite FROM centraleproximite cp,region r,centraleregion cr "
                                        . " WHERE cp.idregion = r.idregion AND cr.idregion = r.idregion and cr.idcentrale=? and masquecentraleproximite !=?", array($idcentraleProjet, TRUE));                                                                
                                for ($i = 0; $i < count($arrayCentraleRegion); $i++) {
                                    $nomcentraleproximite = $arrayCentraleRegion[$i]['libellecentraleproximite'];
                                    $idcentraleproximite = $arrayCentraleRegion[$i]['idcentraleproximite'];
                                    echo "<input    type='checkbox' data-dojo-type='dijit/form/CheckBox' class='centPrx' id='" . 'cp' . $idcentraleproximite . "' " . "name='centrale_Proximite[]' value='" . 'cp' . $idcentraleproximite . "'  >
                                                        <label for = '" . 'cp' . $idcentraleproximite . "' class='centraleP' > " . $nomcentraleproximite . "</label>";
                                    echo '<br>';
                                }
                                echo "<p><u>".TXT_CENTRALESPECIFIQUES."</u></p>";
                                $rowspecifique = $manager->getListbyArray("SELECT cp.libellecentraleproximite, cp.idcentraleproximite FROM region r,centraleproximite cp WHERE cp.idregion = r.idregion  "
                                        . "AND r.libelleregion =? and masquecentraleproximite !=? order by idcentraleproximite asc", array(TXT_CENTRALESPECIFIQUES, TRUE));
                                for ($i = 0; $i < count($rowspecifique); $i++) {
                                    $nomcentraleproximite = $rowspecifique[$i]['libellecentraleproximite'];
                                    $idcentraleproximite = $rowspecifique[$i]['idcentraleproximite'];
                                    echo "<input    type='checkbox' data-dojo-type='dijit/form/CheckBox' id='" . 'cp' . $idcentraleproximite . "' " . "name='centrale_Proximite[]' value='" . 'cp' . $idcentraleproximite . "'  >
                                        <label for = '" . 'cp' . $idcentraleproximite . "' class='opt' > " . $nomcentraleproximite . "</label>";
                                    echo '<br>';
                                }
                                ?>
                            </div>
                        <?php } else { ?>
                            <div id='chckcentraleproximite' style=" margin-bottom: 15px; margin-left: 20px; margin-top: 10px; display: inline-block;" ></div>
                        <?php } ?>
                    </fieldset>
                </td>
                <?php $descriptionCentraleProximite = removeDoubleQuote(clean($manager->getSingle2('select descriptioncentraleproximite from projet where idprojet=?', $idprojet))); ?>
                <td  id="etapecentraleproximite" style="float: left">                        
                    <label for="descriptioncentraleproximite" style="width: auto;margin-top:15px;">
                        <?php echo TXT_DESCRIPTCENTRALEROXIMITE; ?>
                    </label>
                    <div id="descriptioncentraleproximite"  name="descriptioncentraleproximite" disabled="<?php echo $bool; ?>" 
                         style="margin-right: 0px;color:midnightblue;font-family:verdana;margin-left:0px;width:960px;;margin-bottom: 20px" >
                             <?php
                             if (isset($descriptionCentraleProximite)) {
                                 echo $descriptionCentraleProximite;
                             }
                             ?>
                    </div>
                </td>
            <tr>                
                <td><label id="cptcentraleproximite" style="width:450px;color:#B71701;margin-bottom: 10px;"></label></td>
            </tr>
            <tr style="display: none" id="msgerrcentraleproximite">
                <td>
                    <fieldset class='msgerrverrou' ><legend><?php echo TXT_INFO; ?></legend>
                        <?php echo stripslashes(TXT_LIMITEDITEURVERROU) . '<br>'; ?>
                    </fieldset>
                </td>                            
            </tr>      
        </table>
<?php }else {?>
        <table>
            <tr>
                <td>
                    <div id="selection" style="display: none"></div>
                    <label for="centraleproximite" id="default" style="width:918px;margin-left: 20px;;margin-top: 10px"><?php echo $question; ?></label>                                    
                    <input type= "radio" data-dojo-type="dijit/form/RadioButton" name="centrale_proximite"   value="TRUE"  class="btRadio" style="margin-left: 20px;" onclick="affichecentraleproximite()">
                    <?php echo TXT_OUI; ?>
                        <input type= "radio" data-dojo-type="dijit/form/RadioButton" name="centrale_proximite"  value="FALSE" checked="checked" class="btRadio" style="margin-left: 20px;" onclick="masquecentraleproximite()">
                    <?php echo TXT_NON; ?>
                </td>                                
            </tr>
</table>
        <table>
            <tr>
                <td style="float: left;" >                  
                    <fieldset style="font-size: 1.1em;font-style: italic;width: 520px;display: none" id="centraleProximite" >
                        <legend style="margin-left:20px;margin-top: 10px"><?php echo TXT_SELECTCENTRALES; ?></legend>
                        <?php 
                            if(isset($idprojet)){
                                $idcentraleProjet =(int) $manager->getSingle2("select idcentrale_centrale from concerne where idprojet_projet=?", $idprojet);
                            }else{
                                $idcentraleProjet='';
                            }
                            if(empty($idcentraleProjet)){
                                $idcentraleUser =  IDCENTRALEUSER;
                               if (!empty($idcentraleUser)){
                                   $centraleSelectionne = IDCENTRALEUSER;
                               }else{
                                   $centraleSelectionne='';
                               }
                            }else{
                                 $centraleSelectionne = $idcentraleProjet;
                            }
                        ?>
                        
                        
                            <?php if (!empty($centraleSelectionne) && $centraleSelectionne != IDCENTRALEAUTRE) {?>
                            <div id='chckcentraleproximite' style=" margin-bottom: 15px; margin-left: 25px; margin-top: 10px; display: inline-block;">
                                 <?php                                 
                                $arrayCentraleRegion = $manager->getListbyArray("SELECT r.libelleregion, cp.libellecentraleproximite, cp.idcentraleproximite FROM centraleproximite cp,region r,centraleregion cr "
                                        . " WHERE cp.idregion = r.idregion AND cr.idregion = r.idregion and cr.idcentrale=? and masquecentraleproximite !=?", array($centraleSelectionne, TRUE));                                
                                $rowspecifique = $manager->getListbyArray("SELECT cp.libellecentraleproximite, cp.idcentraleproximite FROM region r,centraleproximite cp WHERE cp.idregion = r.idregion  "
                                        . "AND r.libelleregion =? and masquecentraleproximite !=? order by idcentraleproximite asc", array(TXT_CENTRALESPECIFIQUES, TRUE));
                             
                                for ($i = 0; $i < count($arrayCentraleRegion); $i++) {
                                    $nomcentraleproximite = $arrayCentraleRegion[$i]['libellecentraleproximite'];
                                    $idcentraleproximite = $arrayCentraleRegion[$i]['idcentraleproximite'];
                                    echo "<input    type='checkbox' data-dojo-type='dijit/form/CheckBox' class='centPrx' id='" . 'cp' . $idcentraleproximite . "' " . "name='centrale_Proximite[]' value='" . 'cp' . $idcentraleproximite . "'  >
                                                        <label for = '" . 'cp' . $idcentraleproximite . "' class='centraleP' > " . $nomcentraleproximite . "</label>";
                                    echo '<br>';
                                }
                                echo "<p><u>".TXT_CENTRALESPECIFIQUES."</u></p>";                                
                                for ($i = 0; $i < count($rowspecifique); $i++) {
                                    $nomcentraleproximite = $rowspecifique[$i]['libellecentraleproximite'];
                                    $idcentraleproximite = $rowspecifique[$i]['idcentraleproximite'];
                                    echo "<input    type='checkbox' data-dojo-type='dijit/form/CheckBox' id='" . 'cp' . $idcentraleproximite . "' " . "name='centrale_Proximite[]' value='" . 'cp' . $idcentraleproximite . "'  >
                                        <label for = '" . 'cp' . $idcentraleproximite . "' class='opt' > " . $nomcentraleproximite . "</label>";
                                    echo '<br>';
                                }
                                ?>
                            </div>
                        <?php } else {?>
                            <div id='chckcentraleproximite' style=" margin-bottom: 15px; margin-left: 25px; margin-top: 10px; display: inline-block;">
                                 <?php 
                                $rowspecifique = $manager->getListbyArray("SELECT cp.libellecentraleproximite, cp.idcentraleproximite FROM region r,centraleproximite cp WHERE cp.idregion = r.idregion  "
                                        . "AND r.libelleregion =? and masquecentraleproximite !=? order by idcentraleproximite asc", array(TXT_CENTRALESPECIFIQUES, TRUE));
                                echo "<p><u>".TXT_CENTRALESPECIFIQUES."</u></p>";                                
                                for ($i = 0; $i < count($rowspecifique); $i++) {
                                    $nomcentraleproximite = $rowspecifique[$i]['libellecentraleproximite'];
                                    $idcentraleproximite = $rowspecifique[$i]['idcentraleproximite'];
                                    echo "<input    type='checkbox' data-dojo-type='dijit/form/CheckBox' id='" . 'cp' . $idcentraleproximite . "' " . "name='centrale_Proximite[]' value='" . 'cp' . $idcentraleproximite . "'  >
                                        <label for = '" . 'cp' . $idcentraleproximite . "' class='opt' > " . $nomcentraleproximite . "</label>";
                                    echo '<br>';
                                }
                                ?>
    <?php } ?>
                    </fieldset>
                </td>

                <?php
            if (isset($idprojet)) {
                $descriptionCentraleProximite = removeDoubleQuote(clean($manager->getSingle2('select descriptioncentraleproximite from projet where idprojet=?', $idprojet)));
                } else {
                    $descriptionCentraleProximite = '';
                }
                ?>
            <td  id="etapecentraleproximite" style="display:none;float: left">                        
                    <label for="descriptioncentraleproximite" style="width: auto;margin-top:15px;">
                             <?php echo TXT_DESCRIPTCENTRALEROXIMITE; ?>
                    </label>
                <div id="descriptioncentraleproximite"  name="descriptioncentraleproximite" disabled="<?php echo $bool; ?>" 
                style="margin-right: 0px;color:midnightblue;font-family:verdana;margin-left:0px;width:960px;;margin-bottom: 20px" >
                <?php
                if (isset($descriptionCentraleProximite)) {
                    echo $descriptionCentraleProximite;
                }
                ?></div>
            </td>
            </tr>
            <tr>
                <td><label id="cptcentraleproximite" style="width:450px;color:#B71701;margin-bottom: 10px;"></label></td>
            </tr>
            <tr style="display: none" id="msgerrcentraleproximite">
                <td>
                    <fieldset class='msgerrverrou' ><legend><?php echo TXT_INFO; ?></legend>dfsdfsdfsdf
                            <?php echo stripslashes(TXT_LIMITEDITEURVERROU) . '<br>'; ?>
                    </fieldset>
                </td>                            
            </tr>      
        </table>
<?php } ?>

</fieldset>

<?php
if (isset($idprojet)) {
//RECUPERATION DES CENTRALE DE PROXIMITE SELECTIONNEES
    $row = $manager->getList2("select cp.idcentraleproximite,libellecentraleproximite from centraleproximite cp,centraleproximiteprojet cpp where cp.idcentraleproximite=cpp.idcentraleproximite and idprojet =?", $idprojet);
    $nbrow = count($row);
    if ($nbrow != 0) {
        for ($i = 0; $i < $nbrow; $i++) {
            $centraleproximite = 'cp' . $row[$i]['idcentraleproximite'];
        }
        $centraleproximite = count($row);
    } else {
        $nbressource = 0;
    }

//RECUPERATION DES CENTRALES DE PROXIMITE SELECTIONNEES
    
        $row = $manager->getListbyArray("SELECT cp.idcentraleproximite FROM centraleproximiteprojet cpp,centraleproximite cp   WHERE cp.idcentraleproximite=cpp.idcentraleproximite and masquecentraleproximite!=? and idprojet =?", array(TRUE, $idprojet));
        $nbcentraleproximite = count($row);
        for ($i = 0; $i < $nbcentraleproximite; $i++) {
            $centrale_Proximite[] = 'cp' . $row[$i]['idcentraleproximite'];
        }
    

BD::deconnecter();
?>

<?php if (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== FALSE) { ?>
    <script>
        require(["dojo/ready"], function (ready) {
            ready(function () {//MSIE
                '<?php for ($i = 0; $i < $nbcentraleproximite; $i++) { ?>'
                    var centralep = '<?php echo $centrale_Proximite[$i] ?>';
                    dijit.byId(centralep).set('checked', true);
                    '<?php } ?>'
            });
        });
    </script>

<?php } else { ?>
    <script>
        require(["dojo/ready"], function (ready) {
            ready(function () {//FIREFOX                                    
                '<?php for ($i = 0; $i < $nbcentraleproximite; $i++) { ?>'
                    var centralep = '<?php echo $centrale_Proximite[$i] ?>';                    
                    dijit.byId(document.getElementById(centralep).id).setValue(true);
                    '<?php } ?>'

            });
        });
    </script>

<?php }
}?>
<script>        
    require(["dojo/parser", "dijit/Editor"]);
    require(["dijit/Editor", "dojo/ready"], function(Editor, ready) {
        ready(function() {
            new Editor({
                plugins: ["undo", "redo", "|"],
                height: "150px"
            },"descriptioncentraleproximite");
        });
    
        dojo.addOnLoad(function() {
            var editor11 = dijit.byId("descriptioncentraleproximite");
            dojo.connect(editor11, "onChange", this, function(event) {
                dojo.byId("centraleproximitevaleur").value = editor11.get("value");
        });
    
        //CAS POUR CHROME                
                dojo.byId("centraleproximitevaleur").value = editor11.get("value");
                document.getElementById('cptcentraleproximite').innerHTML = "<?php echo TXT_NBCARACT.' : '; ?>"+ strip_tags(trim(dojo.byId("centraleproximitevaleur").value),'').length; 
        //----------------------------------------------------------------------------------------------------------------------------------------------------
        //                                                                              CENTRALE PROXIMITE
        //----------------------------------------------------------------------------------------------------------------------------------------------------
            dojo.connect(editor11, "onChange", this, function(event) {
                dojo.byId("centraleproximitevaleur").value = editor11.get("value");                
                document.getElementById('cptcentraleproximite').innerHTML = "<?php echo TXT_NBCARACT.' : '; ?>"+ strip_tags(trim(dojo.byId("centraleproximitevaleur").value),'').length;
                if (trim(strip_tags(editor11.get("value")),'').length > 3000) {
                    document.getElementById('msgerrcentraleproximite').style.display = 'block';
                } else {
                    document.getElementById('msgerrcentraleproximite').style.display = 'none';
                }
            });
            dojo.connect(editor11, "onMouseOut", this, function(event) {
                dojo.byId("centraleproximitevaleur").value = editor11.get("value");                
                document.getElementById('cptcentraleproximite').innerHTML = "<?php echo TXT_NBCARACT.' : '; ?>"+ strip_tags(trim(dojo.byId("centraleproximitevaleur").value),'').length;
                if (trim(strip_tags(editor11.get("value")),'').length > 3000) {
                    document.getElementById('msgerrcentraleproximite').style.display = 'block';
                } else {
                    document.getElementById('msgerrcentraleproximite').style.display = 'none';
                }
            });
            dojo.connect(editor11, "onLoad", this, function(event) {
                dojo.byId("centraleproximitevaleur").value = editor11.get("value");                
                document.getElementById('cptcentraleproximite').innerHTML = "<?php echo TXT_NBCARACT.' : '; ?>"+ strip_tags(trim(dojo.byId("centraleproximitevaleur").value),'').length;
                if (trim(strip_tags(editor11.get("value")),'').length > 3000) {
                    document.getElementById('msgerrcentraleproximite').style.display = 'block';
                } else {
                    document.getElementById('msgerrcentraleproximite').style.display = 'none';
                }
            });
            dojo.connect(editor11, "onBlur", this, function(event) {
            dojo.byId("centraleproximitevaleur").value = editor11.get("value");            
                document.getElementById('cptcentraleproximite').innerHTML = "<?php echo TXT_NBCARACT.' : '; ?>"+ strip_tags(trim(dojo.byId("centraleproximitevaleur").value),'').length;
                if (trim(strip_tags(editor11.get("value")),'').length > 3000) {
                    document.getElementById('msgerrcentraleproximite').style.display = 'block';
                } else {
                    document.getElementById('msgerrcentraleproximite').style.display = 'none';
                }
            });
            dojo.connect(editor11, "onKeyPress", this, function(event) {
            dojo.byId("centraleproximitevaleur").value = editor11.get("value");            
                document.getElementById('cptcentraleproximite').innerHTML = "<?php echo TXT_NBCARACT.' : '; ?>"+ strip_tags(trim(dojo.byId("centraleproximitevaleur").value),'').length;
                if (trim(strip_tags(editor11.get("value")),'').length > 3000) {
                    document.getElementById('msgerrcentraleproximite').style.display = 'block';
                } else {
                    document.getElementById('msgerrcentraleproximite').style.display = 'none';
                }
            });
            dojo.connect(editor11, "onClick", this, function(event) {
                dojo.byId("centraleproximitevaleur").value = editor11.get("value");                
                document.getElementById('cptcentraleproximite').innerHTML = "<?php echo TXT_NBCARACT.' : '; ?>"+ strip_tags(trim(dojo.byId("centraleproximitevaleur").value),'').length;
                if (trim(strip_tags(editor11.get("value")),'').length > 3000) {
                    document.getElementById('msgerrcentraleproximite').style.display = 'block';
                } else {
                    document.getElementById('msgerrcentraleproximite').style.display = 'none';
                }
            });            
        })}
    );
</script>
<!----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
FIN CENTRALES DE PROXIMITES
------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------>                                               
