<?php
include_once '../outils/constantes.php';
if (isset($_POST['centraletrs'])) {
    $libellecentraledestination = $manager->getSingle2("select libellecentrale from centrale where idcentrale=?", $_POST['centraletrs']);
    //CENTRALE DESTINATION
    $emailcentraledestinations = array();
    $mailcentrales = $manager->getList2("select email1,email2,email3,email4,email5 from centrale where idcentrale=?", $_POST['centraletrs']);
    $infocentraledest = $manager->getList2("select email1,email2,email3,email4,email5 from centrale where idcentrale=?", $_POST['centraletrs']);
    $emailCentrale = '';
    for ($i = 0; $i < count($infocentraledest); $i++) {
        for ($j = 0; $j < 4; $j++) {
            if (!empty($infocentraledest[$i]['email' . $j])) {
                $emailCentrale .=$infocentraledest[$i]['email' . $j] . '<br>';
            }
        }
    }
} elseif (isset($_POST['centrale'])) {
    //CENTRALE DESTINATION
    $emailcentraledestinations = array();
    $mailcentrales = $manager->getList2("select email1,email2,email3,email4,email5 from centrale where idcentrale=?", $_POST['centrale']);
    $libellecentraledestination = $manager->getSingle2("select libellecentrale from centrale where idcentrale=?", $_POST['centrale']);

    $infocentraledest = $manager->getList2("select email1,email2,email3,email4,email5 from centrale where idcentrale=?", $_POST['centrale']);
    $emailCentrale = '';
    for ($i = 0; $i < count($infocentraledest); $i++) {
        for ($j = 0; $j < 4; $j++) {
            if (!empty($infocentraledest[$i]['email' . $j])) {
                $emailCentrale .=$infocentraledest[$i]['email' . $j] . '<br>';
            }
        }
    }
}
for ($i = 0; $i <= 5; $i++) {
    if (!empty($mailcentrales[0][$i])) {
        array_push($emailcentraledestinations, $mailcentrales[0][$i]); //construction d'un tableau d'email des responsable de la centrale
    }
}
//DEMANDEUR
$infodemandeur = array($manager->getList2("SELECT mail,mailresponsable FROM creer,loginpassword,utilisateur WHERE  idlogin = idlogin_loginpassword
            AND  idutilisateur = idutilisateur_utilisateur and   idprojet_projet =?", $idprojet));
$maildemandeur = array($infodemandeur[0][0]['mail']); //EMAIL DU DEMANDEUR NE PEUT PAS NE PAS EXISTER
if (!empty($infodemandeur[0][0]['mailresponsable'])) {
    array_push($arrayemailCC, $infodemandeur[0][0]['mailresponsable']); //EMAIL DU RESPONSABLE SI IL EXISTE
    $CC = $arrayemailCC;
} else {
    $CC = $arrayemailCC;
}
$CC = array_merge($arrayemailCC, $emailcentraledestinations); //CENTRALE DESTINATION
$cc = array_merge($mailCC, $CC); //CENTRALE SOURCE
$mailCC = array_unique($cc);
$body = utf8_decode(removeDoubleQuote( stripslashes(affiche('TXT_MRSMR7')))) . '<br><br>' . utf8_decode(removeDoubleQuote( stripslashes(affiche('TXT_BODYEMAILTRSFPHASE20')))) . '<br><br>' .
        utf8_decode(removeDoubleQuote( stripslashes(affiche('TXT_BODYEMAILPHASE21')))) . " <a href=".ADRESSESITE." >" . utf8_decode(removeDoubleQuote( stripslashes(affiche('TXT_FINSUJETMAILRESPONSABLE')))) . "</a>" .
        '<br><br>' . htmlentities(removeDoubleQuote( stripslashes(affiche('TXT_BODYEMAILTRSFPHASE22')))) . '<br><br>' .
        utf8_decode(removeDoubleQuote( stripslashes(affiche('TXT_RAPPELINSERTLOGO1')))) . '<br><br>' . utf8_decode(removeDoubleQuote( stripslashes(affiche('TXT_SINCERESALUTATION')))) . '<br><br>' . utf8_decode(removeDoubleQuote( stripslashes(affiche('TXT_RESEAURENATECH')))) .
        '<br><br>' . utf8_decode(affiche('TXT_EMAILADDRESSCENTRAL')) . ' ' . utf8_decode($libellecentraledestination) . ' <br> ' . $emailCentrale . '<br><br>' .
        '<br><br><a href='.ADRESSESITE.'>' . utf8_decode(TXT_RETOUR) . '<a>' . '<br><br>' .
        utf8_decode(removeDoubleQuote( stripslashes(affiche('TXT_DONOTREPLY'))));
$EmailCC = array_values($mailCC); //Renumérotation des index
envoieEmail($body,  $sujet, $maildemandeur, $EmailCC); //envoie de l'email au responsable centrale et au copiste
$numero = $manager->getSingle2("select numero from projet where idprojet=?",$idprojet);
$sMailCc="";
for ($i = 0; $i < count($mailCC); $i++) {
    $sMailCc.=$mailCC[$i] . ',';
}
$sMailCC = substr($sMailCc, 0, -1);
$nomPrenomDemandeur = $manager->getList2("SELECT nom, prenom FROM creer,utilisateur WHERE idutilisateur_utilisateur = idutilisateur and idprojet_projet = ?", $idprojet);
$idcentrale = $manager->getSingle2("select idcentrale_centrale from concerne where idprojet_projet=?", $idprojet);
createLogInfo(NOW, "Projet transféré à la centrale " . $libellecentraledestination . ' : E-mail demandeur: ' . $maildemandeur[0]. ' : ' . ' copie E-mail à  : ' . $sMailCC . ' : n°: ' . $numero, 'Demandeur: ' . $nomPrenomDemandeur[0]['nom'] .
        ' ' . $nomPrenomDemandeur[0]['prenom'], removeDoubleQuote(TXT_ACCEPTE), $manager,$idcentrale);


header('Location: /' . REPERTOIRE . '/transfered_project/' . $lang . '/' . $idprojet . '/' . $numprojet . '/' . $idcentrale);
exit();
