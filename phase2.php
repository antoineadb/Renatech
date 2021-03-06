<?php

session_start();
include 'outils/phpPhase2.php'; 
include('decide-lang.php');
include_once 'outils/constantes.php';
include_once 'outils/toolBox.php';
include_once 'class/Cache.php';
define('ROOT',  dirname(__FILE__));
$Cache = new Cache(ROOT.'/cache', 60);

if (isset($_SESSION['pseudo'])) {
        check_authent($_SESSION['pseudo']);
    } else {
         header('Location: /' . REPERTOIRE . '/Login_Error/' . $lang);
}

/* VERIFICATION EN CAS DE MODIFICATION MANUEL D'URL */
include_once 'class/Manager.php';
$db = BD::connecter();
$manager = new Manager($db); 
if(isset($_GET['numProjet'])){
    $idprojet=$manager->getSingle2('select idprojet from projet where numero =?',$_GET['numProjet']);
}elseif (isset ($_SESSION['numProjet'])) {
    $idprojet=$manager->getSingle2('select idprojet from projet where numero =?',$_SESSION['numProjet']);
}
if(!check_URL($_SESSION['pseudo'], $idprojet)){
         header('Location: /' . REPERTOIRE . '/Login_Error/' . $lang);
}
BD::deconnecter();
/* FIN DE LA VERIFICATION EN CAS DE MODIFICATION MANUEL D'URL */

if(isset($_SESSION['idTypeUser'])){
    $typeUser =$_SESSION['idTypeUser']; 
}elseif(isset($_SESSION['typeutilisateur'])){
    $typeUser =$_SESSION['typeutilisateur'];
}
include 'html/header.html';
?>
<script src="<?php echo '/'.REPERTOIRE ?>/js/ajaxefface.js"></script>
<script src="<?php echo '/'.REPERTOIRE ?>/js/ajax.js"></script>
        <div id="global">
            <?php include 'html/entete.html';
             $nbcentraleselectionnees = count($centraleselectionnees);
            ?>
            <div name="modifProjet" data-dojo-type="dijit/form/Form" id="modifProjet" data-dojo-id="modifProjet"  method="post" 
                  action="<?php echo '/'.REPERTOIRE; ?>/updateprojet/<?php echo $lang; ?>/<?php echo $idprojet ;?>/<?php echo $numProjet;?>" enctype="multipart/form-data" >
                <input name="page_precedente" type="hidden" value="<?php echo basename(__FILE__); ?>">
                <script type="dojo/on" data-dojo-event="submit">
                var nbdescript=stripTags(trim(dojo.byId("descriptifValeur").value)).length;
                var nbcontexte=stripTags(trim(dojo.byId("contextValeur").value)).length;
                var nbtitre=stripTags(trim(dijit.byId("titreProjet").value)).length;                
                var centrale;
                if(dijit.byId("centrale")){
                    centrale=dijit.byId("centrale").value;
                }else if(document.getElementById('centraleselectionne').value>0){
                    centrale='ok';
                }                
                var nbdureeprojet=dojo.byId("dureeprojet").value.length;
                var nbdescriptTechno=stripTags(trim(dojo.byId("desTechno").value)).length;
                var nbverrou=stripTags(trim(dojo.byId("verrouide").value)).length;
                var nbreussite=stripTags(trim(dojo.byId("reussit").value)).length;
                var typeProjet = dijit.byId("typeProjet").value;
                var nbdureeestimeprojet = dojo.byId("dureeestimeprojet").value.length;
                if(dijit.byId("statutProjet")){
                    var statutProjet = dijit.byId("statutProjet").value;
                    
                    var commentaireprojet = document.getElementById("commentaireRefus").value;
                    if(statutProjet==='st4' && commentaireprojet ==''){
                        alert("<?php echo TXT_COMMENTREFUS; ?>");
                        return false;    
                        exit(); 
                    }
                }               
                
                if(document.getElementById('save').value=='oui'){//DANS LE CAS D'UNE SAUVEGARDE ON NE CONTROLE PAS LES CHAMPS                      
                    if(nbdescript>800){
                        alert("<?php echo TXT_LIMITEDITEURDESCRIPTION; ?>");
                        return false;    
                        exit();                        
                    }else if(nbcontexte>5000){
                        alert("<?php echo TXT_LIMITEDITEURCONTEXTE; ?>");
                        return false;    
                        exit();                        
                    }else if(nbdescriptTechno>3000){
                        alert("<?php echo TXT_LIMITEDITEURDESCTECHNO; ?>");
                        return false;    
                        exit();                        
                    }else if(nbverrou>3000){
                        alert("<?php echo TXT_LIMITEDITEURVERROU; ?>");
                        return false;    
                        exit();                        
                    }else if(nbreussite>3000){
                        alert("<?php echo TXT_LIMITEDITEURREUSSITE; ?>");
                        return false;    
                        exit();                        
                    }else{
                        return true;
                    }
                }else if(nbtitre==0){
                    alert("<?php echo TXT_ERRTITREVIDE; ?>");
                    return false;
                    exit();
                }else if(!centrale){
                    alert("<?php echo TXT_CHECKCENTRALE; ?>");
                    return false;
                    exit();
                }else if(nbcontexte==0){
                    alert("<?php echo TXT_ERRCONTEXTE; ?>");
                    return false;
                    exit();
                }else if(nbcontexte>5000){
                    alert("<?php echo TXT_LIMITEDITEURCONTEXTE; ?>");
                    return false;                    
                    exit();
                }else if(nbdescript==0){
                    alert("<?php echo TXT_ERRDESCRIPTIF; ?>");
                    return false;                    
                    exit();
                }else if(nbdescript>800){
                    alert("<?php echo TXT_LIMITEDITEURDESCRIPTION; ?>");
                    return false;
                    exit();
                }else if(!typeProjet){                    
                    alert("<?php echo TXT_ERRTYPEPROJETVIDE; ?>");
                    dijit.byId('typeProjet').focus();
                    return false;
                    exit();
                }else if(nbdureeprojet==0){
                    alert("<?php echo TXT_ERRDUREEVIDE; ?>");
                    dojo.byId('dureeprojet').focus();
                    return false;
                    exit();
                }else if(nbdureeestimeprojet==0){
                    alert("<?php echo TXT_ERRDUREEESTIMEVIDE; ?>");
                    dojo.byId('dureeestimeprojet').focus();
                    return false;
                    exit();
                }else if(nbdescriptTechno>3000){
                    alert("<?php echo TXT_LIMITEDITEURDESCTECHNO; ?>");
                    dojo.byId('descriptifTechnologique').focus();
                    return false;
                    exit();
                }else if(nbverrou>3000){
                    alert("<?php echo TXT_LIMITEDITEURVERROU; ?>");
                    dojo.byId('verrouidentifie').focus();
                    return false;
                    exit();
                }else if(nbreussite>3000){
                    alert("<?php echo TXT_LIMITEDITEURREUSSITE; ?>");
                    dojo.byId('reussite').focus();
                    return false;
                    exit();
                }else if(document.getElementById('affichautrecentrale').value == 'oui'){
                    if(!checkcentrale("chckautrecentrale")){
                        alert("<?php echo TXT_CHECKAUTRECENTRALE; ?>");
                        return false;
                        exit();
                    }
                }else if(document.getElementById('affiche_centrale_proximite').value == 'oui'){
                    if(!checkcentrale("chckcentraleproximite")){
                        alert("<?php echo TXT_CHECKCENTRALEPROXIMITE; ?>");
                        return false;
                        exit();
                    }
                }else if (this.validate()){
                    return true;
                }else{
                    alert("<?php echo TXT_ERR; ?>");
                    return false;
                }
        </script>
        <input type="text" id='centraleselectionne' style="display: none" value="<?php if($nbcentraleselectionnees > 0){ echo $nbcentraleselectionnees;}else{echo 0;} ?>" >
            <div style="margin-top: 100px;color:red;font-size: 1.2em;width:1030px;text-align: center" >
                <?php 
                  if(isset($_GET['erreuruploadphase1'])){
                      echo TXT_ERREURUPLOADSUCCINCTE.TXT_ERREURUPLOAD;
                  } elseif(isset($_GET['erreuruploadsizephase1'])){
                      echo TXT_ERREURUPLOADSUCCINCTE.TXT_ERREURTAILLEFICHIER;
                  }elseif(isset($_GET['erreurupload'])){
                      echo TXT_ERREURUPLOADDETAILLE.TXT_ERREURUPLOAD;
                  } elseif(isset($_GET['erreuruploadsize'])){
                      echo TXT_ERREURUPLOADDETAILLE.TXT_ERREURTAILLEFICHIER;
                  }
                ?>
            </div>         
             <div style="width: 1050px; height: auto">
                <!-- SI ON A UN RAPPORT OU UNE ERREUR SUR UN RAPPORT-->
                 <?php if(isset($_GET['report'])){?>
                        <div data-dojo-type="dijit/layout/TabContainer" style="margin-top:30px;width: 1050px;font-size: 1.2em;" doLayout="false" id="MyTabContainer" >
                            <div data-dojo-type="dijit/layout/ContentPane" title="<?php echo TXT_DESCRIPTIONSUCCINTE ?>" style="width: auto; height: auto;overflow: hidden" ><?php include 'html/vueModifProjet.html'; ?></div>
                            <div data-dojo-type="dijit/layout/ContentPane" title="<?php echo TXT_DESCRIPTIONDETAILLE ?>" style="width: auto; height: auto;overflow:hidden;"  ><?php  include 'html/phase2.html'; ?></div>                        
                            <div data-dojo-type="dijit/layout/ContentPane" title="rapport"   id="reportDiv"   style="width: auto; height: 2900px;overflow:hidden;" selected="true" ><?php  include  'html/rapport.html'; ?></div>
                <?php }else{//-- DANS LE CAS NORMAL SANS UN RAPPORT OU UNE ERREUR SUR UN RAPPORT -- ?>
                <div data-dojo-type="dijit/layout/TabContainer" style="margin-top:30px;width: 1050px;font-size: 1.2em;" doLayout="false" id="MyTabContainer" >
                    <div data-dojo-type="dijit/layout/ContentPane" title="<?php echo TXT_DESCRIPTIONSUCCINTE ?>" style="width: auto; height: auto;overflow: hidden" id='vue_modif_projet'><?php include 'html/vueModifProjet.html'; ?></div>
                    <div data-dojo-type="dijit/layout/ContentPane" title="<?php echo TXT_DESCRIPTIONDETAILLE ?>" style="width: auto; height: auto;overflow:hidden;" selected="true" id='vuePhase2'  ><?php  include 'html/phase2.html'; ?></div>               
                </div>
                </div> <?php } ?>
                <div style="margin-left: 20px"><?php include 'html/footer.html'; ?></div>
        </div>
        </div>
    </body>  
</html>