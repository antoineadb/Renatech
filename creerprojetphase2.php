<?php
session_start();
include('decide-lang.php');
include_once 'class/Manager.php';
include_once 'outils/constantes.php';
include_once 'outils/toolBox.php';
include_once 'class/Cache.php';

define('ROOT', dirname(__FILE__));
$Cache = new Cache(ROOT . '/cache', 60);
if (isset($_SESSION['pseudo'])) {
    check_authent($_SESSION['pseudo']);
} else {
    header('Location: /' . REPERTOIRE . '/Login_Error/' . $lang);
}
$db = BD::connecter();
$manager = new Manager($db);
include 'outils/phpPhase2.php';
for ($i = 0; $i < 10; $i++) {
    if (!empty($_SESSION['nomPartenaire' . $i . ''])) {
        unset($_SESSION['nomPartenaire' . $i . '']);
    }
    if (!empty($_SESSION['nomLaboEntreprise' . $i . ''])) {
        unset($_SESSION['nomLaboEntreprise' . $i . '']);
    }
}
$nb = $manager->getSingle("select count(idsourcefinancement) from sourcefinancement");
$idautre = $manager->getSingle2("select idsourcefinancement from sourcefinancement where libellesourcefinancement=? ", 'Autres');
unset($_SESSION['acronymesourcesf' . $idautre . '']);
unset($_SESSION['sf' . $idautre . '']);
for ($i = 0; $i < $nb; $i++) {
    if (!empty($_SESSION['sf' . $i . ''])) {
        unset($_SESSION['sf' . $i . '']);
    }
    if (!empty($_SESSION['acronymesourcesf' . $i . ''])) {
        unset($_SESSION['acronymesourcesf' . $i . '']);
    }
}
//}
if (empty($bool)) {
    $bool = '';
}
include 'html/header.html';
?>
<div id="global">
    <?php
    include 'html/entete.html';
    ?>
    <script src="<?php echo '/' . REPERTOIRE; ?>/js/ajaxefface.js"></script>
    <div style="margin-top: 100px;"></div>
    <form name="modifProjet" data-dojo-type="dijit/form/Form" id="modifProjet"  method="post" action="<?php echo '/' . REPERTOIRE; ?>/modifBase/insertProjetphase2.php?lang=<?php echo $lang; ?>" enctype="multipart/form-data" >
        <input name="page_precedente" type="hidden" value="<?php echo basename(__FILE__); ?>">	
        <script type="dojo/method" data-dojo-event="onSubmit">            
            var nbdescript=stripTags(trim(dojo.byId("descriptifValeur").value)).length;
            var nbcontexte=stripTags(trim(dojo.byId("contextValeur").value)).length;
            var nbtitre=stripTags(trim(dijit.byId("titreProjet").value)).length;
            var centrale=dijit.byId("centrale").value;alert(centrale);
            var nbdureeprojet=dojo.byId("dureeprojet").value.length;
            var nbdescriptTechno=stripTags(trim(dojo.byId("desTechno").value)).length;
            var nbverrou=stripTags(trim(dojo.byId("verrouide").value)).length;
            var nbreussite=stripTags(trim(dojo.byId("reussit").value)).length;
            var typeProjet = dijit.byId("typeProjet").value;
            var nbdureeestimeprojet = dojo.byId("dureeestimeprojet").value.length;            
            if(nbtitre==0){
                alert("<?php echo TXT_ERRTITREVIDE; ?>");
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
            }else if(!centrale){
                alert("<?php echo TXT_CHECKCENTRALE; ?>");
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
        <?php 
            if(isset($_SESSION['idTypeUser'])&& $_SESSION['idTypeUser']==ADMINLOCAL){
                $idcentrale=$manager->getIdCentraleFromLogin($_SESSION['pseudo']);
                $libellecentrale = str_replace("-","",$manager->getSingle2("select libellecentrale from centrale where idcentrale=?",$idcentrale));
         ?>
    
        var selectedCentrale = "<?php echo $libellecentrale; ?>";
        var libellecentrale = "<?php echo 'lbl'.$libellecentrale;?>";
        dijit.byId(selectedCentrale).set('disabled',true);
        document.getElementById(libellecentrale).style.textDecoration  ='line-through';
        <?php } ?>
            
        </script>
        <div data-dojo-type="dijit/layout/TabContainer" id="tabcont" doLayout="false">
            <div data-dojo-type="dijit/layout/ContentPane" id="succinct"  title="<?php echo TXT_DESCRIPTIONSUCCINTE ?>" >
                <?php include 'html/createProjetphase1.html'; ?>
            </div>
            <div data-dojo-type="dijit/layout/ContentPane"  id="detail" title="<?php echo TXT_DESCRIPTIONDETAILLE ?>" >
                <?php include 'html/createProjetphase2.html'; ?>
            </div>
        </div>
        <div>
            <?php include 'html/footer.html'; ?>
        </div>
    </form>
</div>
</body>
</html>