<?php
session_start();
include_once 'class/Manager.php';
include_once 'outils/toolBox.php';
$db = BD::connecter(); //CONNEXION A LA BASE DE DONNEE
$manager = new Manager($db); //CREATION D'UNE INSTANCE DU MANAGER
include 'decide-lang.php';
include 'outils/constantes.php';

if (isset($_SESSION['pseudo'])) {
    check_authent($_SESSION['pseudo']);
} else {
    header('Location: /' . REPERTOIRE . '/Login_Error/' . $lang);
}
include 'html/header.html';

$arraycentrale= $manager->getList2("SELECT ce.libellecentrale,co.idstatutprojet_statutprojet,co.idcentrale_centrale FROM loginpassword l,concerne co,centrale ce,utilisateur u
WHERE l.idlogin = u.idlogin_loginpassword AND ce.idcentrale = co.idcentrale_centrale AND u.idcentrale_centrale = co.idcentrale_centrale and l.pseudo =? ", $_SESSION['pseudo']);

$arrayprojetnodev = $manager->getListbyArray("SELECT p.idautrethematique_autrethematique,p.idprojet,p.titre,t.libellethematiqueen,u.prenom,u.idutilisateur,u.nom,p.numero FROM projet p,thematique t,creer cr,utilisateur u,concerne co
WHERE t.idthematique = p.idthematique_thematique AND cr.idprojet_projet = p.idprojet AND cr.idutilisateur_utilisateur = u.idutilisateur AND  co.idprojet_projet = p.idprojet and co.idcentrale_centrale =? AND trashed =FALSE
and co.idstatutprojet_statutprojet =? AND p.idprojet not in (select idprojet from rapport)
union
SELECT p.idautrethematique_autrethematique,p.idprojet,p.titre,t.libellethematiqueen,u.prenom,u.idutilisateur,u.nom,p.numero FROM projet p,thematique t,creer cr,utilisateur u,concerne co
WHERE t.idthematique = p.idthematique_thematique AND cr.idprojet_projet = p.idprojet AND cr.idutilisateur_utilisateur = u.idutilisateur AND  co.idprojet_projet = p.idprojet and co.idcentrale_centrale =? AND trashed =FALSE
and co.idstatutprojet_statutprojet =? AND p.idprojet not in (select idprojet from rapport)
union
SELECT p.idautrethematique_autrethematique,p.idprojet,p.titre,t.libellethematiqueen,u.prenom,u.idutilisateur,u.nom,p.numero FROM projet p,thematique t,creer cr,utilisateur u,concerne co
WHERE t.idthematique = p.idthematique_thematique AND cr.idprojet_projet = p.idprojet AND cr.idutilisateur_utilisateur = u.idutilisateur AND  co.idprojet_projet = p.idprojet and co.idcentrale_centrale =? AND trashed =FALSE
and co.idstatutprojet_statutprojet =? AND p.idprojet not in (select idprojet from rapport)
order by libellethematiqueen asc", array($arraycentrale[0]['idcentrale_centrale'],ENCOURSREALISATION,$arraycentrale[0]['idcentrale_centrale'],FINI,$arraycentrale[0]['idcentrale_centrale'],CLOTURE));

$fprow = fopen('tmp/projetNoDev.json', 'w');
$projetnodev = "";
$nbarrayprojetnodev = count($arrayprojetnodev);
fwrite($fprow, '{"items": [');
$ressources = "";
for ($i = 0; $i < $nbarrayprojetnodev; $i++) {
    $arrayRessources = $manager->getList2("SELECT  libelleressourceen FROM ressource,ressourceprojet WHERE idressource = idressource_ressource and idprojet_projet =?", $arrayprojetnodev[$i]['idprojet']);
    for ($j = 0; $j < count($arrayRessources); $j++) {
        $ressources.=$arrayRessources[$j]['libelleressourceen'].' / ';
    }
    $ressources = substr($ressources,0,-2);
    //Entity
    $arrayEntity = $manager->getList2("SELECT codeunite,libelleautrecodeunite FROM utilisateur,centrale,autrecodeunite WHERE idcentrale_centrale = idcentrale AND idautrecodeunite = idautrecodeunite_autrecodeunite "
            . "and idutilisateur=?", $arrayprojetnodev[$i]['idutilisateur']);
       
    if(isset($arrayEntity[0]['libelleautrecodeunite']) && $arrayEntity[0]['libelleautrecodeunite'] != 'n/a' ){
        $libelleEntity = ' / '.$arrayEntity[0]['libelleautrecodeunite'];
    }elseif(isset($arrayEntity[0]['codeunite'])&&!empty($arrayEntity[0]['codeunite'])){
        $libelleEntity = ' / '.$arrayEntity[0]['codeunite'];
    }else{
        $libelleEntity = ' / '."No entity";
    }
    
    $arraySf = $manager->getList2("SELECT libellesourcefinancementen,acronymesource FROM sourcefinancement,projetsourcefinancement WHERE idsourcefinancement_sourcefinancement = idsourcefinancement  and idprojet_projet=?", $arrayprojetnodev[$i]['idprojet']);
    if(!empty($arraySf[0]['libellesourcefinancementen'])&& $arraySf[0]['libellesourcefinancementen']!='Others'){
        $sf = ' / '.$arraySf[0]['libellesourcefinancementen'];
    }elseif(!empty($arraySf[0]['libellesourcefinancementen'])&& $arraySf[0]['libellesourcefinancementen']=='Others'){
        $sf = ' / '.$arraySf[0]['acronymesource'];        
    }else{
        $sf ='';
    }
    if($arrayprojetnodev[$i]['libellethematiqueen']!='Others'){
        $thematic = $arrayprojetnodev[$i]['libellethematiqueen'];
    }else{
        $thematic =$manager->getSingle2("select libelleautrethematique from autrethematique where idautrethematique=?", $arrayprojetnodev[$i]['idautrethematique_autrethematique']);
    }
    
    $projetnodev = "" . '{'                      
            . '"titre":' . '"' . filtredonnee($arrayprojetnodev[$i]['titre']) . '"' . ","
            . '"numero":' . '"' . $arrayprojetnodev[$i]['numero'] . '"' . ","
            . '"projectleader":' . '"' . $arrayprojetnodev[$i]['nom'] .' - '.$arrayprojetnodev[$i]['prenom'] .'  '.$libelleEntity.' '.$sf. '"' . ","            
            . '"centrale":' . '"' . $arraycentrale[0]['libellecentrale'] . '"' . ","
            . '"thematique":' . '"' . filtredonnee($thematic)  . '"' . ","
            . '"idstatutprojet_statutprojet":' . '"' . $arraycentrale[0]['idstatutprojet_statutprojet'] . '"' . ","
            . '"ressources":' . '"' . $ressources . '"' . "},";

    fputs($fprow, $projetnodev);
    fwrite($fprow, '');
    $ressources = "";
}
fwrite($fprow, ']}');
$json_fileuserProjet = "tmp/projetNoDev.json";
$jsonusercompte1 = file_get_contents($json_fileuserProjet);
$jsonUsercompte = str_replace('},]}', '}]}', $jsonusercompte1);
file_put_contents($json_fileuserProjet, $jsonUsercompte);
fclose($fprow);
chmod('tmp/projetNoDev.json', 0777);
?>
<div id="global">
    <?php include 'html/entete.html'; ?>
    <div style="margin-top: 70px;">
        <?php include_once 'outils/bandeaucentrale.php'; ?>
    </div>
    <form data-dojo-type="dijit/form/Form" name="exportProjetnoDEV" style="font-size:1.0em;" id="exportProjetnoDEV" method="post" action="<?php echo '/'.REPERTOIRE ?>/controler/controleTypeFile.php?lang=<?php echo $lang; ?>"  >        
    <fieldset id="projetNoDev" style="">
        <legend style="color: #5D8BA2;"><b><?php echo TXT_PROJETSANSDEV; ?></b></legend>
        <table>
                <tr>                   
                    <td>
                        
                        <select  id="anneerapport" data-dojo-type="dijit/form/FilteringSelect"  data-dojo-props="name: 'annee',value: '',required:false,placeHolder: '<?php echo TXT_SELECTYEAR; ?>'" 
                                 style="width: 220px; margin-left: 35px; font-size: 1.0em;margin-top: 10px;margin-right: 25px" >
                                     <?php
                                     $row = $manager->getList("select distinct  EXTRACT(YEAR from datecreation) as anneeCreation  from rapport where EXTRACT(YEAR from datecreation)>2012");
                                     for ($i = 0; $i < count($row); $i++) {
                                         echo '<option value="' . $row[$i]['anneecreation'] . '">' . $row[$i]['anneecreation'] . '</option>';
                                     }echo '<option value=1>' . TXT_TOUS . '</option>';
                                     ?>
                        </select>         
                    </td>                    
                    <td valign="middle" style="width: 150px; margin-left: 15px; height: 24px; font-size: 1.0em;padding-top: 20px;;padding-top: 1px">
                        <input type="radio" data-dojo-type="dijit/form/RadioButton" name="typefile" id="radioOne" checked  value="word" > <img src='<?php echo '/' . REPERTOIRE ?>/styles/img/word.png' width="25" height="25"  />
                        <input type="radio" data-dojo-type="dijit/form/RadioButton" name="typefile" id="radioTwo" value="excel" style="margin-left: 15px;"/> <img src='<?php echo '/' . REPERTOIRE ?>/styles/img/excel.png'  width="25" height="25"  />
                    </td>
                    <td>
                        <input type="submit"   label="<?php echo TXT_EXPORT; ?>" data-dojo-Type="dijit.form.Button" data-dojo-type="dijit/form/Button" style="text-align: center; font-size: 1.0em;" />
                    </td>
                </tr>
            </table>

        <div id="gridDiv"></div>
       </fieldset>
<style type="text/css">
    #grid {
        width: 78em;
        height: 38em;
        margin-top: 25px;
    }
</style>
<script>
        function word(){
            document.getElementById('word').value='oui';
            document.getElementById('excel').value='non';
            this.form.submit();
        }
        function excel(){
            document.getElementById('excel').value='oui';
            document.getElementById('word').value='non';
            console.log('excel = '+document.getElementById('excel').value);
            //this.form.submit();
        }
        </script> 
<script>
    dojo.require("dojox.grid.EnhancedGrid");
    dojo.require("dojox.data.CsvStore");
    dojo.require("dojox.grid.enhanced.plugins.Menu");
    dojo.require("dojo.data.ItemFileWriteStore");
    dojo.require("dijit.Menu");
    dojo.require("dijit.MenuItem");
    dojo.require("dojo.domReady!");
    dojo.require("dojo.request");
    dojo.require("dojox.grid.enhanced.plugins.Pagination");
dojo.ready(function(){
    var store = new dojo.data.ItemFileWriteStore({url: "<?php echo '/'.REPERTOIRE;?>/tmp/projetNoDev.json"});
    var layout = [[
        {name: "<?php echo 'Project'; ?>", field: "titre", width: "auto"},
        {name: "<?php echo 'BTR Platform'; ?>", field: "centrale", width: "75px"},
        {name: "<?php echo 'Project leader / Entity / Financial support'; ?>", field: "projectleader", width: "250px"},
        {name: "<?php echo 'Activities supported '; ?>", field: "ressources", width: "250px"},
        {name: "<?php echo 'Project number'; ?>", field: "numero", width: "100px",formatter: hrefFormatter},
        {name: "<?php echo 'Thematics'; ?>", field: "thematique", width: "130px"},
    ]];
    var grid = new dojox.grid.EnhancedGrid({
        id: 'grid',
        store: store,
        structure: layout,
        //autoHeight:true,
        rowSelector: '20px',
        plugins: {menus:{headerMenu:"headerMenu", rowMenu:"rowMenu", cellMenu:"cellMenu", selectedRegionMenu:"selectedRegionMenu"},
            pagination: {
            pageSizes: [ "10", "50","100","200", "All"],
            description: true,
            sizeSwitch: true,
            pageStepper: true,
            gotoButton: true,
            maxPageStep: 5,
            position: "top"
        }}
    },
      document.createElement('div'));
//----------------------------------------------------------------------------------------------------------------------
//                                              FONCTIONS DE FORMATAGE
//----------------------------------------------------------------------------------------------------------------------
        function hrefFormatter(numero, idx) {
            var item = grid.getItem(idx);//idx = index 
            var centrale = item.centrale;//libelle centrale
            var idstatut= item.idstatutprojet_statutprojet;
            return "<a  href=\"<?php echo '/'.REPERTOIRE; ?>/controler/controlestatutprojet.php?lang=<?php echo $lang;?>&statut="+idstatut+"&numProjet=" + numero + "&centrale=" + centrale + "\">"+ numero + "</a>";
        } 
       dojo.byId("gridDiv").appendChild(grid.domNode);
    grid.startup();
    });
    </script>   
    </form>
<?php include 'html/footer.html'; ?>
</div>

