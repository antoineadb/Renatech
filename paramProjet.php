<?php
session_start();
include_once 'outils/toolBox.php';
include_once 'outils/constantes.php';
include 'decide-lang.php';
if (isset($_SESSION['pseudo'])) {
    check_authent($_SESSION['pseudo']);
} else {
    header('Location: /' . REPERTOIRE . '/Login_Error/' . $lang);
}
include 'html/header.html';
include_once 'class/Manager.php';
$db = BD::connecter();
$manager = new Manager($db);
?>
<script src="<?php echo '/'.REPERTOIRE ?>/js/ajax.js"></script>
<script src="<?php echo '/'.REPERTOIRE ?>/js/jquery.js"></script>
<script src="<?php echo '/'.REPERTOIRE ?>/js/script_autocomplete.js"></script>

<div id="global">
    <?php include 'html/entete.html'; ?>
    <input name="page_precedente" type="hidden" value="<?php echo basename(__FILE__); ?>">     
    <div style="margin-top: 70px;">
        <?php include_once 'outils/bandeaucentrale.php'; ?>
    </div>
    <?php
    $valueAcronyme = $manager->getList2("SELECT ref_interne,num_projet FROM config_acronyme WHERE idcentrale=? ", IDCENTRALEUSER);
    $numProjet = $valueAcronyme[0]['num_projet'];
    $ref_interne = $valueAcronyme[0]['ref_interne'];
     if($numProjet){
        $checkNumprojet = 'checked';
     }else{
        $checkNumprojet = '';
    }
     if($ref_interne){
        $checkRefInterne = 'checked';
     }else{
        $checkRefInterne = '';
    }
    ?>
    <form id="myform">
        <fieldset id="paramProjet">
            <legend style="color: #5D8BA2;font-size: 1.2em"><b><?php echo TXT_PARAM_ACRONYME; ?></b></legend>
            <table>
                <tr>
                    <td>
                        <label for="porteurProjet"  style="width: auto; margin-left: 20px;margin-right: 20px;font-size: 15px;"><?php echo TXT_CHX_ACRONYME . ' : *'; ?></label>
                    </td>
                    <td>	 
                    <input type="radio" data-dojo-type="dijit/form/RadioButton" name="configAcronyme" id="ref_interne" <?php echo $checkRefInterne; ?> /> 
                        <span style="font-size:15px"><?php echo TXT_REFINTERNE ?></span>
                    <input type="radio" data-dojo-type="dijit/form/RadioButton" name="configAcronyme" id="num_projet" <?php echo $checkNumprojet; ?>  /> 
                        <span style="font-size:15px"><?php echo TXT_NUMPROJECT ?></span>
                    </td>
                <td>&nbsp;&nbsp;&nbsp;</td>
                <td>
                    <button data-dojo-type="dijit/form/Button" type="button">
                        Valider le choix
                        <script type="dojo/on" data-dojo-event="click">                            
                            require(["dojo/dom"], function(dom){
                               adminAcronyme('/<?php echo REPERTOIRE; ?>/outils/adminConfiAcronyme.php?lang=<?php echo $lang;?>&id_centrale=' +<?php echo IDCENTRALEUSER ?> + '&ref_interne='+ dijit.byId('ref_interne').get('value')+'&num_projet='+ dijit.byId('num_projet').get('value')+'');
                            });
                        </script>
                    </button>
                </td>
                <td><span style="display: none;margin-left: 20px;" id="imgConfigAcronyme"><img src="/<?php echo REPERTOIRE; ?>/styles/img/valide.png" ></span></td></td>
                </tr>
                <tr><td></tr>
            </table>
          
        </fieldset>
    </form>
    <?php 
        $contactCentralAccueil = $manager->getList2("SELECT u.nom,u.prenom,cad.idutilisateur FROM config_accueil_defaut cad "
                . "LEFT JOIN utilisateur u ON u.idutilisateur = cad.idutilisateur "
                . "WHERE cad.idcentrale=?  ", IDCENTRALEUSER);
    ?>
    <form data-dojo-type="dijit/form/Form" name="paramContact"  method="post"  action="<?php echo '/' . REPERTOIRE; ?>/modifBase/updateParamProjet.php?lang=<?php echo $lang; ?>"  >
        <fieldset id="paramContact" >
            <legend style="color: #5D8BA2;font-size: 1.2em"><b><?php echo TXT_PARAM_CONTACT_CENTRALE_ACCUEIL; ?></b></legend>
            <table>
                <tr>
                    <td>
                        <input readonly  data-dojo-type='dijit/form/ValidationTextBox' name='nom' id='nom' style="width:350px;margin-left:20px"  
                               value='<?php echo ucfirst($contactCentralAccueil[0]['nom']) .' - ' .ucfirst($contactCentralAccueil[0]['prenom']) ;?>'   
                               placeholder="<?php echo "Saisir les 3 premiers caractères"; ?>"  onkeyup="autocomplet()">                                
                    </td>                    
                    <td>
                        <button data-dojo-type="dijit/form/Button" type="button">
                        Changer
                            <script type="dojo/on" data-dojo-event="click">                            
                                require(["dojo/dom"], function(dom){
                                   dijit.byId("nom").set('readOnly',false);
                                   dijit.byId("nom").set('value','');
                                });
                            </script>
                        </button>
                    </td>
                </tr>                
            </table>
            <ul id="nom_list_id"></ul>
        </fieldset>
    </form>
    <?php include 'html/footer.html'; ?>
</div>
</body>
</html>