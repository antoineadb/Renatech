<?php

session_start();

include '../outils/toolBox.php';
include_once '../class/Manager.php';
include_once '../outils/constantes.php';
include '../class/Securite.php';
include_once '../decide-lang.php';
$datejour = date('Y-m-d');
if (!empty($_POST['page_precedente']) && $_POST['page_precedente'] == 'modifProjetRespCentrale.html') {
    $db = BD::connecter(); //CONNEXION A LA BASE DE DONNEE
    $manager = new Manager($db); //CREATION D'UNE INSTANCE DU MANAGER
    $idprojet = $_GET['idprojet'];
    $numprojet = $manager->getSingle2("select numero from projet where idprojet=?", $idprojet);
    $idutilisateur = $_SESSION['idutilisateur'];
    $idcentrale = $manager->getSingle2("select idcentrale_centrale from utilisateur where idutilisateur =?", $idutilisateur);
    if (empty($_POST['comment']) && $_POST['statutProjet'] == REFUSE) {
        header('Location: /' . REPERTOIRE . '/refused_project2/' . $lang . '/' . $numprojet . '/' . $idcentrale . '/' . $_POST['statutProjet'].'/true');      
        exit();
    } else {
        $commentaire = $_POST['comment'];
    }
//Récupération de l'idcentrale --> L'utilisateur qui peut changer le statut est forcément un responsable de centrale
    $_SESSION['idcentrale'] = $idcentrale;
    $idstatutprojet = (int) $_POST['statutProjet'];
    $_SESSION['idstatutprojet'] = $idstatutprojet;
    $concerne = new Concerne($idcentrale, $idprojet, $idstatutprojet, $commentaire);
    $manager->addConcerne($concerne);
				checkConcerne($idprojet,	$idcentrale,	$idstatutprojet);
//------------------------------------------------------------------------------------------------------------------------------------------------------
//			ACCEPTE
//------------------------------------------------------------------------------------------------------------------------------------------------------
//On efface l'affectation de toutes les centrales pour le projet en cours
    if ($idstatutprojet == ACCEPTE) {
        $manager->updateConcerne($concerne, $idprojet); //mise à jour du statut du projet
								checkConcerne($idprojet,	$idcentrale,	ACCEPTE);echo 'mldfmkl';
        ////PROJET ACCEPTE POUR EXPERTISE DANS LA CENTRALE
        //SI LE PROJET CONCERNE PLUS DE UNE CENTRALE
        $nbcentrale = $manager->getSingle2("select count(idcentrale_centrale) from concerne where idprojet_projet=?", $idprojet);
        if ($nbcentrale > 1) {
            include_once '../EmailProjetInfosautrescentrale.php'; //ENVOIE D'UN EMAIL DANS LES AUTRES CENTRALES
        } else {
            include '../EmailProjetphase2.php'; //ENVOIE D'UN EMAIL AU DEMANDEUR AVEC COPIE DU CHAMP COMMENTAIRE
            header('Location: /' . REPERTOIRE . '/accepted_project/' . $lang . '/' . $idprojet . '/' .ACCEPTE.'/'. $numprojet );
        }


//------------------------------------------------------------------------------------------------------------------------------------------------------
//                      REFUSE
//------------------------------------------------------------------------------------------------------------------------------------------------------
    } elseif ($idstatutprojet == REFUSE) {
        $manager->updateConcerne($concerne, $idprojet); //mise à jour du statut du projet
        $daterefus = new DateStatutRefusProjet($idprojet, $datejour);
        $manager->updateDateStatutRefuser($daterefus, $idprojet, $idcentrale);
        include '../EmailProjetphase2.php'; //ENVOIE D'UN EMAIL AU DEMANDEUR AVEC COPIE DU CHAMP COMMENTAIRE
//------------------------------------------------------------------------------------------------------------------------------------------------------
//                      TRANSFERER DANS UNE AUTRE CENTRALE
//------------------------------------------------------------------------------------------------------------------------------------------------------
    } elseif ($idstatutprojet == TRANSFERERCENTRALE) {
        if (!empty($_POST['centrale'])) {
            $idcentrale = $_POST['centrale'];
        }
//------------------------------------------------------------------------------------------------------------------------------------------------------
        //MISE A JOUR DES COMMENTAIRES
        //MISE AU STATUT REFUSE DANS LES PRECEDENTES AFFECTATIONS DES CENTRALES
//------------------------------------------------------------------------------------------------------------------------------------------------------
        $rowidcentrale = $manager->getListbyArray("select idcentrale_centrale from concerne where idprojet_projet =? and idcentrale_centrale <>?  ", array($idprojet, $idcentrale));
        $libellecentrale = $manager->getSingle2("select libellecentrale from centrale where idcentrale=?", $idcentrale); //NOM DE LA CENTRALE
        foreach ($rowidcentrale as $key => $value) {
            $concerne = new Concerne($value[0], $idprojet, REFUSE, TXT_COMMENTTRS . ' ' . $libellecentrale);
            $manager->updateConcerne($concerne, $idprojet);
            $daterefus = new DateStatutRefusProjet($idprojet, $datejour);
            $manager->updateDateStatutRefuser($daterefus, $idprojet, $value[0]);
        }
        //MISE A JOUR DE LA CENTRALE AFFECTE
        $centraleaffecte = new Concerne($idcentrale, $idprojet, ENCOURSANALYSE, TXT_PROJETTRANSFERT . '<br>' . $commentaire);
        $manager->addConcerne($centraleaffecte);
        include '../EmailProjetphase2.php';
//------------------------------------------------------------------------------------------------------------------------------------------------------
//			EN ATTENTE
//------------------------------------------------------------------------------------------------------------------------------------------------------
    } elseif ($idstatutprojet == ENATTENTE) {
        if (isset($_POST['comment'])) {
            $comment = $_POST['comment'];
            $_SESSION['comment'] = $comment;
        } else {
            $comment = '';
        }
        $rowidcentrale = $manager->getList2("select idcentrale_centrale from concerne where idprojet_projet =?  ", $idprojet);
        foreach ($rowidcentrale as $key => $value) {
            $centraleenattente = new Concerne($value[0], $idprojet, ENATTENTE, $comment);
            $manager->updateConcerne($centraleenattente, $idprojet);
        }
        include '../EmailProjetphase2.php';
        BD::deconnecter();
    }
} else {
    header('Location: /' . REPERTOIRE . '/Login_Error/' . $lang);
}