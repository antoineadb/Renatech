<?php
if(!isset($cas)){
    session_start();
}
include_once 'decide-lang.php';
include_once 'class/email.php';
include_once 'class/Manager.php';
include_once 'class/Securite.php';
$db = BD::connecter(); //CONNEXION A LA BASE DE DONNEE
$manager = new Manager($db); //CREATION D'UNE INSTANCE DU MANAGER
include_once 'outils/toolBox.php';
showError($_SERVER['PHP_SELF']);
include_once 'outils/constantes.php';
if (isset($_SESSION['pseudo'])) {
    check_authent($_SESSION['pseudo']);
} else {
    header('Location: /' . REPERTOIRE . '/Login_Error/' . $lang);
}

if (isset($_GET['idprojet']) && !empty($_GET['idprojet'])) {
    $idprojet = $_GET['idprojet'];
    $numero = $manager->getSingle2("select numero from projet where idprojet=?", $idprojet);
}
//------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
//                                                                              DONNEE SUCCINTE            
//------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
if (!empty($_SESSION['titremodif'])) {
    $titreProjet =nl2br(htmlspecialchars(affiche('TXT_TITREPROJETEMAIL') . ': ' . $_SESSION['titremodif']));
} 
if (!empty($_SESSION['acronymemodif'])) {
    $acronyme =nl2br(htmlspecialchars(TXT_ACRONYME . ': ' . $_SESSION['acronymemodif']));
} else{
    $acronyme ='';
}
if (!empty($_SESSION['confidentielmodif'])) {
    if ($_SESSION['confidentielmodif'] == 'TRUE') {
        $confid = TXT_OUI;
    } else {
        $confid = TXT_NON;
    }
    $confidentiel = TXT_CONFIDPDF . ': ' . $confid ;
} else{
    $confidentiel = '';
}
if (!empty($_SESSION['contextemodif'])) {
    $contexte = TXT_CONTEXTESCIENTIFIQUE . ': ' .htmlspecialchars(str_replace("''","'",  strip_tags($_SESSION['contextemodif'])));
}else{
    $contexte='';
} 
if (!empty($_SESSION['descriptionmodif'])) {
    $descriptif = TXT_DESCRIPTIFTRAVAIL . ': ' .htmlspecialchars(str_replace("''","'",strip_tags($_SESSION['descriptionmodif'])));
}else{
    $descriptif ='';
}
if (!empty($_SESSION['attachementmodif'])) {
    $attachement = TXT_PIECEJOINTE . ': ' . $_SESSION['attachementmodif'];
}else{
    $attachement = '';
}
if (!empty($_SESSION['contactcentralaccueilmodif'])) {
    $contactcentralaccueilmodif = TXT_CONTACTCENTRALACCUEIL . ': ' . Securite::bdd($_SESSION['contactcentralaccueilmodif']);
}
//------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
//                                                                              DONNEE PHASE2               
//------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
if (!empty($_SESSION['contactcentralaccueilmodif'])) {
    $contactscentraleaccueil = TXT_CONTACTCENTRALACCUEIL . ': ' . htmlspecialchars(str_replace("''","'",$_SESSION['contactcentralaccueilmodif']));
}else{
    $contactscentraleaccueil ='';
}
if (!empty($_SESSION['idtypeprojetmodif'])) {
    $libelletype = $manager->getSingle2("select libelletype from typeprojet where idtypeprojet=? ", $_SESSION['idtypeprojetmodif']);
    $typeProjet = TXT_TYPEPROJET . ': ' . $libelletype;
    $typeprojet = htmlspecialchars(removeDoubleQuote( $typeProjet)) ;
}else{
    $typeProjet ='';
}
if (!empty($_SESSION['typeFormationmodif'])) {
    $libelleformation = $manager->getSingle2("select libelletypeformation from typeformation where idtypeformation=?", $_SESSION['typeFormationmodif']);
    $typeFormation = TXT_TYPEFORMATION . ': ' . Securite::bdd($libelleformation) ;
}else{
    $typeFormation='';
}
if (!empty($_SESSION['nbheuremodif'])) {
    $nbHeure = htmlspecialchars(removeDoubleQuote( TXT_NBHEURE)) . ': ' . $_SESSION['nbheuremodif'] ;
}else{
    $nbHeure ='';
}
if (!empty($_SESSION['nbelevemodif'])) {
    $nbEleve = htmlspecialchars(removeDoubleQuote( TXT_NBELEVE)) . ': ' . $_SESSION['nbelevemodif'] ;
}else{
    $nbEleve = '';
}
if (!empty($_SESSION['nomformateurmodif'])) {
    $nomFormateur = TXT_NOMFORMATEUR . ': ' . htmlspecialchars(str_replace("''","'",$_SESSION['nomformateurmodif'])) ;
}else{
    $nomFormateur ='';
}
if (!empty($_SESSION['dureeprojetmodif']) && empty($_SESSION['idperiodmodif'])) {
    $arrayperiod = $manager->getList2("SELECT libelleperiodicite,libelleperiodiciteen FROM  period,projet WHERE idperiodicite_periodicite = idperiodicite and idprojet=?", $idprojet);
    if ($lang == 'fr') {
        $dureeProjet = utf8_decode(TXT_DUREEPROJET) . ': ' . $_SESSION['dureeprojetmodif'] . ' ' . utf8_decode($arrayperiod[0]['libelleperiodicite']) ;
    } else {
        $dureeProjet = utf8_decode(TXT_DUREEPROJET) . ': ' . $_SESSION['dureeprojetmodif'] . ' ' . $arrayperiod[0]['libelleperiodiciteen'] ;
    }
} elseif (!empty($_SESSION['idperiodmodif']) && empty($_SESSION['dureeprojetmodif'])) {
    $arrayPeriod = $manager->getList2("select libelleperiodicite,libelleperiodiciteen from period where idperiodicite=?", (int) $_SESSION['idperiodmodif']);
    $duree = $manager->getSingle2("select dureeprojet from projet where idprojet=?", $idprojet);
    if ($lang == 'fr') {
        $dureeProjet = utf8_decode(TXT_DUREEPROJET) . ': ' . $duree . ' ' . utf8_decode($arrayPeriod[0]['libelleperiodicite']) ;
    } else {
        $dureeProjet = utf8_decode(TXT_DUREEPROJET) . ': ' . $duree . ' ' . $arrayPeriod[0]['libelleperiodiciteen'] ;
    }
} elseif (!empty($_SESSION['idperiodmodif']) && !empty($_SESSION['dureeprojetmodif'])) {
    $arrayPeriod = $manager->getList2("select libelleperiodicite,libelleperiodiciteen from period where idperiodicite=?", (int) $_SESSION['idperiodmodif']);
    if ($lang == 'fr') {
        $dureeProjet = utf8_decode(TXT_DUREEPROJET) . ': ' . $_SESSION['dureeprojetmodif'] . ' ' . utf8_decode($arrayPeriod[0]['libelleperiodicite']) ;
    } else {
        $dureeProjet = utf8_decode(TXT_DUREEPROJET) . ': ' . $_SESSION['dureeprojetmodif'] . ' ' . $arrayPeriod[0]['libelleperiodiciteen'] ;
    }
} else {
    $dureeProjet = '';
}
//----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
//
//----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
$s_sourcefinancement = '';

$arraysourcefinancement = $_SESSION['arraysfmodif'];
if (!empty($arraysourcefinancement)|| $arraysourcefinancement!='') {    
    for ($k = 0; $k < count($arraysourcefinancement); $k++) {
        if (!empty($arraysourcefinancement[$k])) {
            $s_sourcefinancement .= htmlentities(removeDoubleQuote( $arraysourcefinancement[$k]), ENT_QUOTES, 'UTF-8') . ' - ';
        }  
    }
    $S_SourceFinancement = htmlentities($s_sourcefinancement, ENT_QUOTES, 'UTF-8') ;
    $s_SourceFinancement = removeDoubleQuote($S_SourceFinancement) ;
    $s_Sourcefinancement = substr($s_SourceFinancement, 0, -2) ;
    $s_sourcefinancement = TXT_SOURCEFINANCEMENT . ': ' . $s_Sourcefinancement;
    
}

$s_acronymesf = '';
if (!empty($_SESSION['arrayacromodif'])) {
    $arrayacrosourcefinancement = $_SESSION['arrayacromodif'];
    foreach ($arrayacrosourcefinancement as $cle => $element) {
        $sfassocie = $manager->getSingle2("select libellesourcefinancement from sourcefinancement where idsourcefinancement=?", $cle);
        if (!empty($element)) {
            $s_acronymesf.=  $sfassocie . '  --> ' . $element.', ';
        } else {
            $s_acronymesf.='';
        }
    }
}
if (!empty($_SESSION['porteurprojetmodif'])) {
    $sporteurprojet = TXT_QUESTIONPORTEURPROJET . ': ' . $_SESSION['porteurprojetmodif'] ;
} else{
    $sporteurprojet ='';
}
if (!empty($_SESSION['centralepartenaireprojetmodif'])) {
    $scentralepartenaireprojet = TXT_NOMLABOENTREPRISE . '1' . ': ' . removeDoubleQuote( $_SESSION['centralepartenaireprojetmodif']) ;
} else{
    $scentralepartenaireprojet='';
}
if (!empty($_SESSION['partenaire1modif'])) {
    $spartenaire1 = TXT_NOMPARTENAIRE . '1' . ': ' . removeDoubleQuote( $_SESSION['partenaire1modif']) ;
}else{
    $spartenaire1='';
}
if (!empty($_SESSION['partenairesmodif'])) {
    $spartenaires = removeDoubleQuote( $_SESSION['partenairesmodif']);
}else{
    $spartenaires = '';
}
if (!empty($_SESSION['libellethematiquemodif'])) {
    $thematique = TXT_THEMATIQUE . ': ' .removeDoubleQuote( $_SESSION['libellethematiquemodif']) ;
}else{
    $thematique = '';
}
if (!empty($_SESSION['libelleautrethematiquemodif'])) {
    $autrethematique = TXT_AUTRETHEMATIQUE . ': ' .removeDoubleQuote( $_SESSION['libelleautrethematiquemodif']);
}else{
    $autrethematique='';
}
if (!empty($_SESSION['dateDebutTravauxmodif'])) {
    $dateDebutTravaux = TXT_DATEDEBUTTRAVAUX . ': ' . $_SESSION['dateDebutTravauxmodif'] ;
}else{
    $dateDebutTravaux = '';
}
if (!empty($_SESSION['dureeestimemodif']) && empty($_SESSION['periodestimemodif'])) {
    $arrayperiod = $manager->getList2("SELECT libelleperiodicite,libelleperiodiciteen FROM period,projet WHERE periodestime = idperiodicite and idprojet=?", $idprojet);
    if ($lang == 'fr') {
        $dureeEstime = utf8_decode(TXT_DUREETRAVAUXTECNO) . ': ' . $_SESSION['dureeestimemodif'] . ' ' . utf8_decode($arrayperiod[0]['libelleperiodicite']) ;
    } else {
        $dureeEstime = utf8_decode(TXT_DUREETRAVAUXTECNO) . ': ' . $_SESSION['dureeestimemodif'] . ' ' . $arrayperiod[0]['libelleperiodiciteen'] ;
    }
} elseif (!empty($_SESSION['periodestimemodif']) && empty($_SESSION['dureeestimemodif'])) {
    $arrayPeriod = $manager->getList2("select libelleperiodicite,libelleperiodiciteen from period where idperiodicite=?", $_SESSION['periodestimemodif']);
    $duree = $manager->getSingle2("select dureeestime from projet where idprojet=?", $idprojet);
    if ($lang == 'fr') {
        $dureeEstime = utf8_decode(TXT_DUREETRAVAUXTECNO) . ': ' . $duree . ' ' . utf8_decode($arrayPeriod[0]['libelleperiodicite']) ;
    } else {
        $dureeEstime = utf8_decode(TXT_DUREETRAVAUXTECNO) . ': ' . $duree . ' ' . $arrayPeriod[0]['libelleperiodiciteen'] ;
    }
} elseif (!empty($_SESSION['periodestimemodif']) && !empty($_SESSION['dureeestimemodif'])) {
    $arrayPeriod = $manager->getList2("select libelleperiodicite,libelleperiodiciteen from period where idperiodicite=?", $_SESSION['periodestimemodif']);
    if ($lang == 'fr') {
        $dureeEstime = utf8_decode(TXT_DUREETRAVAUXTECNO) . ': ' . $_SESSION['dureeestimemodif'] . ' ' . utf8_decode($arrayPeriod[0]['libelleperiodicite']) ;
    } else {
        $dureeEstime = utf8_decode(TXT_DUREETRAVAUXTECNO) . ': ' . $_SESSION['dureeestimemodif'] . ' ' . $arrayPeriod[0]['libelleperiodiciteen'] ;
    }
}else{
    $dureeEstime='';
}
if(!empty($_SESSION['personnecentralemodif'])){
    $personnecentrale =TXT_PERSONNEACCUEILCENTRALE.': '.substr(str_replace("''","'",$_SESSION['personnecentralemodif']),0,-1);
}else{
    $personnecentrale='';
}
if(!empty($_SESSION['ressourcesmodif'])){
    $ressources = TXT_RESSOURCES.': '.$_SESSION['ressourcesmodif'];
}else{
    $ressources='';
}
if(!empty($_SESSION['descriptifTechnologiquemodif'])){
    $descriptifTechno =TXT_DESCRIPTIFTECHNOLOGIQUE.':   '.str_replace("''","'", $_SESSION['descriptifTechnologiquemodif']);
}else{
    $descriptifTechno='';
}
if(!empty($_SESSION['attachementdescmodif'])){
    $attachementdesc = TXT_PIECEJOINTE . ': ' .$_SESSION['attachementdescmodif'];
}else{
    $attachementdesc = '';
}

if(!empty($_SESSION['verrouidentifiemodif'])){
    $verrouIdentifiee =  TXT_VERROUIDENTIFIEE.':  '. str_replace("''","'", $_SESSION['verrouidentifiemodif']);
}else{
    $verrouIdentifiee='';
}

//ON VERIFE AU PREALABLE QUE LES AUTRES CENTRALES SONT BIEN SELECTIONNEES
$Booletape = $manager->getSingle2("select etapeautrecentrale from projet where idprojet=?", $idprojet);

if($Booletape==FALSE){
    $_SESSION['descriptionautrecentralemodif']='';
    $_SESSION['autrecentralemodif'] ='';
}elseif(!empty($_SESSION['descriptionautrecentralemodif'])){
    $descriptionautrecentrale =   TXT_DESCRIPTETAPE .' '.str_replace("''","'", $_SESSION['descriptionautrecentralemodif']); 
}if(!empty($_SESSION['autrecentralemodif'])){
    $autrecentrale = TXT_AUTRESCENTRALES.': '. $_SESSION['autrecentralemodif'];
}else{
    $autrecentrale='';
    $descriptionautrecentrale='';
}
if(!empty($_SESSION['centraleproximitemodif'])){
    $sCentraleProximite = '';
    for ($i = 0; $i < count($_SESSION['centraleproximitemodif']); $i++) {
        if(strlen($_SESSION['centraleproximitemodif'][$i]>3)){// PLUS DE 9 CENTRALE DE POXIMITE
            $idcentrale=(int) substr($_SESSION['centraleproximitemodif'][$i],2,2);
        }else{// MOINS DE 9 CENTRALE DE POXIMITE
            $idcentrale=(int) substr($_SESSION['centraleproximitemodif'][$i],2,1);
        }
        $sCentraleProximite .= $manager->getSingle2("select libellecentraleproximite from centraleproximite where idcentraleproximite=?", $idcentrale).' - ';
    }
$scentraleProximite =  str_replace("''","'",TXT_CENTRALEPROXIMITE.':  '. substr($sCentraleProximite,0, -2));
}else{
    $scentraleProximite='';
}

if(!empty($_SESSION['descriptioncentraleproximitemodif'])){
    $descriptioncentraleproximite =   TXT_DESCRIPTCENTRALEROXIMITE .': ' .' '.str_replace("''","'", $_SESSION['descriptioncentraleproximitemodif']); 
}else{
    $descriptioncentraleproximite='';
}
if(!empty( $_SESSION['nbplaquemodif'])){
    $nbplaque = TXT_NBPLAQUE.':  '.$_SESSION['nbplaquemodif'];
}else{
    $nbplaque = '';
}
if(!empty( $_SESSION['nbRunmodif'])){
    $nbRun = TXT_NBRUN.':  '.$_SESSION['nbRunmodif'];
}else{
    $nbRun='';
}
    
if(!empty($_SESSION['emailrespdevismodif)'])){
    $emailrespdevis = TXT_EMAILRESPDEVIS.': '.' '.$_SESSION['emailrespdevismodif)'];
}else{
    $emailrespdevis='';
}
if(!empty($_SESSION['reussitemodif'])){
    $reussite = TXT_REUSSITEESCOMPTE.': :'.' '.str_replace("''","'", $_SESSION['reussitemodif']);
}else{
    $reussite ='';
}
if(!empty($_SESSION['refinternemodif'])){
    $refinterne =TXT_REFINTERNE.': '.' '.  str_replace("''","'",$_SESSION['refinternemodif']);
}else{
    $refinterne ='';
}
if(!empty($titreProjet)){
    $titreProjet = htmlentities($titreProjet, ENT_QUOTES, 'UTF-8').'<br>';
}else{
    $titreProjet = '';
}
if(!empty($acronyme)){
    $acronyme = htmlentities($acronyme, ENT_QUOTES, 'UTF-8').'<br>';
}else{
    $acronyme = '';
}
if(!empty($confidentiel)){
    $confidentiel = htmlentities($confidentiel, ENT_QUOTES, 'UTF-8').'<br>';
}else{
    $confidentiel = '';
}
if(!empty($contexte)){
    $contexte = htmlentities(removeBrEmail($contexte), ENT_QUOTES, 'UTF-8').'<br>';
}else{
    $contexte = '';
}
if(!empty($descriptif)){
    $descriptif = htmlentities(removeBrEmail($descriptif), ENT_QUOTES, 'UTF-8').'<br>';
}else{
    $descriptif = '';
}
if(!empty($attachement)){
    $attachement = htmlentities($attachement, ENT_QUOTES, 'UTF-8').'<br>';
}else{
    $attachement = '';
}
if(!empty($contactscentraleaccueil)){
    $contactscentraleaccueil = htmlentities($contactscentraleaccueil, ENT_QUOTES, 'UTF-8').'<br>';
}else{
    $contactscentraleaccueil = '';
}
if(!empty($typeprojet)){
    $typeprojet = htmlentities($typeprojet, ENT_QUOTES, 'UTF-8').'<br>';
}else{
    $typeprojet = '';
}
if(!empty($typeFormation)){
    $typeFormation = htmlentities($typeFormation, ENT_QUOTES, 'UTF-8').'<br>';
}else{
    $typeFormation = '';
}
if(!empty($nbHeure)){
    $nbHeure = htmlentities($nbHeure, ENT_QUOTES, 'UTF-8').'<br>';
}else{
    $nbHeure = '';
}
if(!empty($nbRun)){
    $nbRun = htmlentities($nbRun, ENT_QUOTES, 'UTF-8');
}else{
    $nbRun = '';
}
if(!empty($nbEleve)){    
    $nbEleve = htmlentities($nbEleve, ENT_QUOTES, 'UTF-8').'<br>';
}else{
    $nbEleve = '';
}
if(!empty($nomFormateur)){
    $nomFormateur = htmlentities($nomFormateur, ENT_QUOTES, 'UTF-8').'<br>';
}else{
    $nomFormateur = '';
}
if(!empty($dureeProjet)){
    $dureeProjet = $dureeProjet.'<br>';
}else{
    $dureeProjet = '';
}
if(!empty($s_Sourcefinancement)){
    $s_Sourcefinancement = htmlentities($s_Sourcefinancement, ENT_QUOTES, 'UTF-8').'<br>';
}else{
    $s_Sourcefinancement = '';
}
if(!empty($s_acronymesf)){
    $s_acronymesf =  substr(TXT_ACROSOURCECFINANCEMENT . ':<br> ' . htmlentities($s_acronymesf, ENT_QUOTES, 'UTF-8'),0,-2).'<br>';
}else{
    $s_acronymesf = '';
}
if(!empty($sporteurprojet)){
    $sporteurprojet = htmlentities($sporteurprojet, ENT_QUOTES, 'UTF-8').'<br>';
}else{
    $sporteurprojet = '';
}
if(!empty($scentralepartenaireprojet)){
    $scentralepartenaireprojet = htmlentities($scentralepartenaireprojet, ENT_QUOTES, 'UTF-8').'<br>';
}else{
    $scentralepartenaireprojet = '';
}
if(!empty($spartenaire1)){
    $spartenaire1 = htmlentities($spartenaire1, ENT_QUOTES, 'UTF-8').'<br>';
}else{
    $spartenaire1 = '';
}
if(!empty($spartenaires)){
    $spartenaires = htmlentities($spartenaires, ENT_QUOTES, 'UTF-8').'<br>';
}else{
    $spartenaires = '';
}
if(!empty($thematique)){
    $thematique = htmlentities($thematique, ENT_QUOTES, 'UTF-8').'<br>';
}else{
    $thematique = '';
}
if(!empty($autrethematique)){
    $autrethematique = htmlentities($autrethematique, ENT_QUOTES, 'UTF-8').'<br>';
}else{
    $autrethematique = '';
}
if(!empty($dateDebutTravaux)){
    $dateDebutTravaux = htmlentities($dateDebutTravaux, ENT_QUOTES, 'UTF-8').'<br>';
}else{
    $dateDebutTravaux = '';
}
if(!empty($dureeEstime)){
    $dureeEstime = $dureeEstime.'<br>';
}else{
    $dureeEstime = '';
}
if(!empty($personnecentrale)){
    $personnecentrale = htmlentities($personnecentrale, ENT_QUOTES, 'UTF-8').'<br>';
}else{
    $personnecentrale = '';
}
if(!empty($ressources)){
    $ressources = htmlentities($ressources, ENT_QUOTES, 'UTF-8').'<br>';
}else{
    $ressources = '';
}
if(!empty($descriptifTechno)){
    $descriptifTechno = htmlentities(removeBrEmail($descriptifTechno), ENT_QUOTES, 'UTF-8').'<br>';
}else{
    $descriptifTechno = '';
}
if(!empty($attachementdesc)){
    $attachementdesc = htmlentities($attachementdesc, ENT_QUOTES, 'UTF-8').'<br>';
}else{
    $attachementdesc = '';
}
if(!empty($verrouIdentifiee)){
    $verrouIdentifiee = htmlentities(removeBrEmail($verrouIdentifiee), ENT_QUOTES, 'UTF-8').'<br>';
}else{
    $verrouIdentifiee = '';
}
if(!empty($autrecentrale)){
    $autrecentrale = htmlentities($autrecentrale, ENT_QUOTES, 'UTF-8').'<br>';
}else{
    $autrecentrale = '';
}
if(!empty($descriptionautrecentrale)){
    $descriptionautrecentrale = htmlentities(removeBrEmail($descriptionautrecentrale), ENT_QUOTES, 'UTF-8').'<br>';
}else{
    $descriptionautrecentrale = '';
}
if(!empty($scentraleProximite)){
    $scentraleProximite = htmlentities($scentraleProximite, ENT_QUOTES, 'UTF-8').'<br>';
}else{
    $scentraleProximite = '';
}
if(!empty($descriptioncentraleproximite)){
    $descriptioncentraleproximite = htmlentities(removeBrEmail($descriptioncentraleproximite), ENT_QUOTES, 'UTF-8').'<br>';
}else{
    $descriptioncentraleproximite = '';
}
if(!empty($nbplaque)){
    $nbplaque = $nbplaque.'<br>';
}else{
    $nbplaque = '';
}
if(!empty($nbRun)){
    $nbRun = $nbRun.'<br>';
}else{
    $nbRun = '';
}
if(!empty($emailrespdevis)){
    $emailrespdevis = htmlentities($emailrespdevis, ENT_QUOTES, 'UTF-8').'<br>';
}else{
    $emailrespdevis = '';
}
if(!empty($reussite)){
    $reussite = htmlentities(strip_tags(removeBrEmail($reussite)), ENT_QUOTES, 'UTF-8').'<br>';
}else{
    $reussite = '';
}
if(!empty($refinterne)){
    $refinterne = htmlentities($refinterne, ENT_QUOTES, 'UTF-8').'<br>';
}else{
    $refinterne = '';
}
$tableauVariables = array($titreProjet,$acronyme,$confidentiel ,$contexte,$descriptif,$attachement, $contactscentraleaccueil ,$typeprojet,$typeFormation,$nbHeure ,$nbEleve,$nomFormateur,$dureeProjet ,$s_Sourcefinancement,$s_acronymesf,
        $sporteurprojet,$scentralepartenaireprojet,$spartenaire1,$spartenaires,$thematique,$autrethematique,$dateDebutTravaux ,$dureeEstime,$personnecentrale,$ressources,$descriptifTechno,$attachementdesc,
        $verrouIdentifiee,$autrecentrale,$descriptionautrecentrale,$scentraleProximite,$descriptioncentraleproximite,$nbplaque,$nbRun,$emailrespdevis,$reussite,$refinterne);
$arrayresult=array();
        for ($i = 0; $i < count($tableauVariables); $i++) {
            if(!empty($tableauVariables[$i])){
                array_push($arrayresult,$tableauVariables[$i]);  
            }
        }
       $nbarrayresult =  count($arrayresult);
if($nbarrayresult>0){        
$body = htmlentities(stripslashes(removeDoubleQuote( affiche('TXT_MRSMR5'))), ENT_QUOTES, 'UTF-8') . '<br><br>' .
        htmlentities(stripslashes(removeDoubleQuote( affiche('TXT_BODYEMAILMODIF0'))), ENT_QUOTES, 'UTF-8') . '<br><br>' .
        htmlentities(stripslashes(removeDoubleQuote( affiche('TXT_BODYEMAILMODIF1'))), ENT_QUOTES, 'UTF-8') . '<br><br>' .        
        $titreProjet.$acronyme.$confidentiel .$contexte.$descriptif.$attachement. $contactscentraleaccueil .$typeprojet.$typeFormation.$nbHeure .$nbEleve.$nomFormateur.$dureeProjet .$s_Sourcefinancement.$s_acronymesf.
        $sporteurprojet.$scentralepartenaireprojet.$spartenaire1.$spartenaires.$thematique.$autrethematique.$dateDebutTravaux .$dureeEstime.$personnecentrale.$ressources.$descriptifTechno.$attachementdesc.
        $verrouIdentifiee.$autrecentrale.$descriptionautrecentrale.$scentraleProximite.$descriptioncentraleproximite.$nbplaque.$nbRun.$emailrespdevis.$reussite.$refinterne.
        '<br><br>' . htmlentities(stripslashes(removeDoubleQuote( affiche('TXT_SINCERESALUTATION'))), ENT_QUOTES, 'UTF-8') .
        '<br><br>' . htmlentities(stripslashes(removeDoubleQuote( affiche('TXT_RESEAURENATECH'))), ENT_QUOTES, 'UTF-8') .
        "<a href=".ADRESSESITE." ><br><br>" . TXT_RETOUR . '</a><br>    ' .
        '<br><br>' . htmlentities(stripslashes(removeDoubleQuote( affiche('TXT_DONOTREPLY'))), ENT_QUOTES, 'UTF-8');
}else{
    $body = htmlentities(stripslashes(removeDoubleQuote( affiche('TXT_MRSMR5'))), ENT_QUOTES, 'UTF-8') . '<br><br>' .
        htmlentities(stripslashes(removeDoubleQuote( affiche('TXT_BODYEMAILMODIF0'))), ENT_QUOTES, 'UTF-8') . '<br><br>' .
        htmlentities(stripslashes(removeDoubleQuote( affiche('TXT_BODYEMAILMODIF1'))), ENT_QUOTES, 'UTF-8') . '<br><br>' .  utf8_decode(TXT_MESSAGEERREURENODATA).'<br><br>' . htmlentities(stripslashes(removeDoubleQuote( affiche('TXT_SINCERESALUTATION'))), ENT_QUOTES, 'UTF-8') .
        '<br><br>' . htmlentities(stripslashes(removeDoubleQuote( affiche('TXT_RESEAURENATECH'))), ENT_QUOTES, 'UTF-8') .
        "<a href=".ADRESSESITE." ><br><br>" . TXT_RETOUR . '</a><br>    ' .
        '<br><br>' . htmlentities(stripslashes(removeDoubleQuote( affiche('TXT_DONOTREPLY'))), ENT_QUOTES, 'UTF-8');
}
$sujet = utf8_decode(TXT_MODIFPROJETNUM) . $numero;
$emailcentrales = array();
$idcentraleAffectation = $manager->getSingle2("select idcentrale_centrale from concerne where idprojet_projet=?", $idprojet);

$mailcentrale = $manager->getList2("SELECT email1,email2,email3,email4,email5 FROM centrale WHERE idcentrale = ? ", $idcentraleAffectation);

for ($i = 0; $i <= 5; $i++) {
    if (!empty($mailcentrale[0][$i])) {
        array_push($emailcentrales, $mailcentrale[0][$i]); //construction d'un tableau d'email des responsable de la centrale
    }
}
$infodemandeur = array($manager->getList2('SELECT mail, mailresponsable FROM creer,loginpassword,utilisateur WHERE idutilisateur_utilisateur = idutilisateur
            AND idlogin_loginpassword = idlogin and idprojet_projet=?', $idprojet));
$maildemandeur = array($infodemandeur[0][0]['mail']); //EMAIL DU DEMANDEUR NE PEUT PAS NE PAS EXISTER

$arrayemailCC = array();
$mailresponsable = $manager->getSingle2("SELECT mailresponsable FROM loginpassword,utilisateur WHERE idlogin = idlogin_loginpassword and pseudo=?", $_SESSION['pseudo']);
if (!empty($mailresponsable)) {
    array_push($arrayemailCC, $mailresponsable); //EMAIL DU RESPONSABLE SI IL EXISTE
    $CC = $arrayemailCC;
} else {
    $CC = $arrayemailCC;
}
$emailcc = array_merge($emailcentrales, $CC);
$mailCC = array_unique($emailcc);

$sMailCc='';
for ($i = 0;$i < count($mailCC);$i++) {
    $sMailCc.=$mailCC[$i].',';
}
$statut = $manager->getSingle2("SELECT libellestatutprojet FROM concerne,statutprojet WHERE idstatutprojet = idstatutprojet_statutprojet and idprojet_projet = ?", $idprojet);
$sMailCC = substr($sMailCc,0,-1);
$centrale = $manager->getSingle2("SELECT libellecentrale FROM concerne,centrale WHERE idcentrale = idcentrale_centrale and idprojet_projet=?",$idprojet);
$nomPrenomDemandeur = $manager->getList2("SELECT nom, prenom FROM creer,utilisateur WHERE idutilisateur_utilisateur = idutilisateur and idprojet_projet = ?", $idprojet);
$idcentrales = $manager->getList2("select idcentrale_centrale from concerne where idprojet_projet=?", $idprojet);
    foreach ($idcentrales as $idcentrale) {
        createLogInfo(NOW, 'Projet mise à jour, centrale '.$centrale.' : E-mail demandeur: ' .$infodemandeur[0][0]['mail'].' : '.' copie E-mail à  : ' .$sMailCC.' : n°: '. $numero, 'Demandeur: '.$nomPrenomDemandeur[0]['nom'] .
        ' ' .$nomPrenomDemandeur[0]['prenom'] , removeDoubleQuote($statut), $manager,$idcentrale[0]);
    }
    


envoieEmail($body, $sujet, $maildemandeur, $mailCC); //envoie de l'email au responsable centrale et au copiste
if (!empty($_GET['nbpersonne'])) {
    $nbpersonne = $_GET['nbpersonne'];
} else {
    $nbpersonne = 0;
}
header('Location: /' . REPERTOIRE . '/update_project3/' . $lang . '/' . $_GET['idprojet'] . '/' . $numero . '/' . $nbpersonne);

