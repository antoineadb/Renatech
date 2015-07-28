<?php
session_start();

include_once 'class/Cache.php';
include_once 'decide-lang.php';
include_once 'outils/toolBox.php';
include_once 'outils/constantes.php';
include_once 'class/Manager.php';
$db = BD::connecter(); //CONNEXION A LA BASE DE DONNEE
$manager = new Manager($db); //CREATION D'UNE INSTANCE DU MANAGER
define('ROOT',  dirname(__FILE__));
$Cache = new Cache(ROOT.'/cache', 60);
$cacheLongindexchoix = new Cache(ROOT . '/cache', 24*60*15);//15 jours
$cacheLongfooter = new Cache(ROOT . '/cache', 24*60*15);//15 jours
if (isset($_SESSION['pseudo'])) {
    check_authent($_SESSION['pseudo']);
} else {
    header('Location: /' . REPERTOIRE . '/Login_Error/' . $lang);
}
supprDouble();//VERIFICATION A CHAQUE CONNEXION QU'IL N'Y A PAS DE DOUBLONS DANS LA BDD ET SUPPRESSION DANS LE CAS CONTRAIRE

include 'html/header.html';
?>
<div id="global">
    <?php include "html/entete.html";?>
    <div id="Contenuindexchoix">
        <div style="padding-top: 75px;">
    <?php 
    if(internetExplorer()=='false'){
        $Cache->inc(ROOT.'/outils/bandeaucentrale.php'); //RECUPERATION DU BANDEAU DEFILANT DANS LE CACHE CACHE
    }else{
        include_once 'outils/bandeaucentrale.php'; //RECUPERATION DU BANDEAU DEFILANT DANS LE CAS D'INTERNET EXPLORER
    }    
    ?>
           
</div>
<br>
    <?php
            $cacheLongindexchoix->inc(ROOT.'/html/indexchoix.html');
            $cacheLongfooter->inc(ROOT.'/html/footer.html');
    ?>
    </div>
</div>
</body>
</html>
<?php $db = BD::deconnecter();?>