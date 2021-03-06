<?php

session_start();
include_once '../decide-lang.php';
include '../outils/toolBox.php';
include '../class/Manager.php';
include '../outils/constantes.php';
include '../outils/donneeSession.php';
exit();
$db = BD::connecter(); //CONNEXION A LA BASE DE DONNEE
$manager = new Manager($db); //CREATION D'UNE INSTANCE DU MANAGER
include '../class/Securite.php';
if (isset($_SESSION['pseudo'])) {
    check_authent($_SESSION['pseudo']);
} else {
    header('Location: /' . REPERTOIRE . '/Login_Error/' . $lang);
}
if (isset($_POST['page_precedente']) && $_POST['page_precedente'] == 'createProjetphase2.html') {
//------------------------------------------------------------------------------------------------------------
//										INSERT PHASE 1
//------------------------------------------------------------------------------------------------------------

    if (!empty($_POST['titreProjet'])) {
        $titreProjet = stripslashes(Securite::bdd($_POST['titreProjet']));
    }
    
    if(!empty($_POST['ph1'])){
        if (!empty($_POST['contextValeur'])) {
                $contexte = filterEditor(Securite::bdd(trim($_POST['contextValeur'])));
        }else{
            $contexte=null;
        }
        if (!empty($_POST['descriptifValeur'])) {
                $descriptif = filterEditor(Securite::bdd(trim($_POST['descriptifValeur'])));
        }else{
            $descriptif=null;
        }
    }else{
        if (!empty($_POST['contextValeur'])) {
                $contexte = filterEditor(Securite::bdd(trim($_POST['contextValeur'])));
        }
        if (!empty($_POST['descriptifValeur'])) {
                $descriptif = filterEditor(Securite::bdd(trim($_POST['descriptifValeur'])));
        }
    }
    
    
    if (!empty($_POST['acronyme'])) {
        $acronyme = stripslashes(Securite::bdd($_POST['acronyme']));
    } else {
        $acronyme = "";
    }
    if (!empty($_FILES['fichierProjet']['name'])) {
        $attachement = stripslashes(Securite::bdd($_FILES['fichierProjet']['name']));
    } else {
        $attachement = "";
    }
    if (empty($_POST['confid'])) {
        $confidentiel = "FALSE";
    } else {
        $confidentiel = $_POST['confid'];
    }    

        date_default_timezone_set('Europe/London');
        $dateprojet = date("m,d,Y");
//RECUPERATION DE L'IDUTILISATEUR
        if (!empty($_SESSION['idutilisateur'])) {
            $idutilisateur_utilisateur = $_SESSION['idutilisateur'];
        } else {
            $idutilisateur_utilisateur = $manager->getSingle2("SELECT idutilisateur FROM loginpassword, utilisateur WHERE
	  idlogin_loginpassword = idlogin and pseudo =?", $_SESSION['pseudo']);
        }
        $idtypeprojet_typeprojet = $manager->getSingle2("select idtypeprojet from typeprojet where libelletype =?", 'n/a'); //"n/a" par défaut
// NUMERO DE PROJET
        $numero = $manager->getSingle("select max(numero) from projet");
        if (!empty($numero)) {
            $numProjet = createNumProjet($numero);
        } else {
            $numProjet = 'P-' . date("y") . '-' . '00001'; //CAS DU 1ER PROJET
        }
        $_SESSION['numprojet'] = $numProjet; //A GARDER BESOIN SUR L'EMAIL
//INSERSION EN BASE DE DONNEE
        $idprojet = $manager->getSingle("select max(idprojet) from projet") + 1;
        $idcentrale = (int) substr($_POST['centrale'], -1);
        $projet = new Projetphase1($idprojet, $titreProjet, $numProjet, $confidentiel, $descriptif, $dateprojet, $contexte, $idtypeprojet_typeprojet, $attachement, $acronyme);
        $manager->addProjetphase1($projet);
//RECUPERATION DE L'ID DU PROJET CREE
        $idProjet = $manager->getSingle("SELECT max(idprojet)FROM projet;");
        $_SESSION['idprojet'] = $idProjet; //A GARDER
        //MISE A JOUR DE LA TABLE CREER
        $creer = new Creer($idutilisateur_utilisateur, $idProjet);
        $manager->addCreer($creer);
        //MISE A JOUR DE LA TABLE CONCERNE
        $concerne = new Concerne($idcentrale, $idProjet, ACCEPTE, '');
        $manager->addConcerne($concerne);
        checkConcerne($idprojet, $idcentrale, ACCEPTE);

//------------------------------------------------------------------------------------------------------------
//										INSERT PHASE 2
//------------------------------------------------------------------------------------------------------------
//------------------------------------------------------------------------------------------------------------
//                          CHAMPS  SANS TRAITEMENT
//------------------------------------------------------------------------------------------------------------   
        if (!empty($_POST['contactCentralAccueil'])) {
            $contactCentralAccueil = stripslashes(Securite::bdd($_POST['contactCentralAccueil']));
        } else {
            $contactCentralAccueil = '';
        }
        if (!empty($_POST['typeProjet'])) {
            $idtypeprojet_typeprojet = substr($_POST['typeProjet'], 2);
            $idlibelleFormation = $manager->getSingle2("select idtypeprojet from typeprojet where libelletype=?", "Formation");
            if ($idtypeprojet_typeprojet == $idlibelleFormation) {// ON A AFFAIRE A UN TYPE DE PROJET FORMATION
                if (!empty($_POST['typeFormation'])) {
                    $idtypeformation = (int) substr($_POST['typeFormation'], 2);
                    //INSERTION DANS LA TABLE PROJETTYPEPROJET
                    $projettypeprojet = new Projettypeprojet($idtypeformation, $idprojet);
                    $manager->addprojettypeprojet($projettypeprojet, $idprojet);
                    if (!empty($_POST['nbHeure'])) {
                        $nbHeure = Securite::bdd($_POST['nbHeure']);
                    } else {
                        $nbHeure = 0;
                    }
                    if (!empty($_POST['nbeleve'])) {
                        $nbeleve = Securite::bdd($_POST['nbeleve']);
                    } else {
                        $nbeleve = 0;
                    }
                } else {
                    $idtypeformation = 1;
                }
            } else {
                $nbeleve = 0;
                $nbHeure = 0;
                $idtypeformation = 1;
            }
        } else {
            $idtypeprojet_typeprojet = 1;
            $nbeleve = 0;
            $nbHeure = 0;
            $idtypeformation = 1;
        }
        if (!empty($_POST['nomformateur'])) {
            $nomformateur = stripslashes(Securite::bdd($_POST['nomformateur']));
        } else {
            $nomformateur = '';
        }
        if (!empty($_POST['dateDebutTravaux'])) {
            $dateDebutTravaux = pg_escape_string($_POST['dateDebutTravaux']);
        } else {
            $dateDebutTravaux = date('Y-m-d');
        }
        if (!empty($_POST['dureeprojet'])) {
            $dureeprojet = stripslashes(Securite::bdd($_POST['dureeprojet']));
        } else {
            $dureeprojet = '';
        }
        if (!empty($_POST['choix'])) {
            $idperiodicite = substr($_POST['choix'], 2);
        }
        if (!empty($_POST['centralepartenaireprojet'])) {
            $centralepartenaireprojet = stripslashes(Securite::bdd($_POST['centralepartenaireprojet']));
        } else {
            $centralepartenaireprojet = '';
        }

        if (!empty($_POST['desTechno'])) {
            $descriptifTechnologique = filterEditor(Securite::bdd(trim($_POST['desTechno'])));
        } else {
            $descriptifTechnologique = '';
        }
        if (!empty($_POST['verrouide'])) {
            $verrouidentifie = filterEditor(Securite::bdd(trim($_POST['verrouide'])));
        } else {
            $verrouidentifie = '';
        }
        if (!empty($_POST['reussit'])) {
            $reussite = filterEditor(Securite::bdd(trim($_POST['reussit'])));
        } else {
            $reussite = '';
        }
        if (!empty($_POST['nbPlaque']) || $_POST['nbPlaque'] != 0) {
            $nbPlaque = Securite::bdd($_POST['nbPlaque']);
        } else {
            $nbPlaque = 0;
        }
        if (!empty($_POST['nbRun']) || $_POST['nbRun'] != 0) {
            $nbRun = Securite::bdd($_POST['nbRun']);
        } else {
            $nbRun = 0;
        }
        if (!empty($_POST['devis'])) {
            $devis = Securite::bdd($_POST['devis']);
        } else {
            $devis = '';
        }
        if (!empty($_POST['mailresp'])) {
            $mailresp = Securite::bdd(($_POST['mailresp']));
        } else {
            $mailresp = '';
        }
        if (!empty($_POST['refinterne'])) {
            $refinterne = stripslashes(Securite::bdd(($_POST['refinterne'])));
        } else {
            $refinterne = '';
        }
        if (!empty($_FILES['fichierphase2']['name']) && !empty($_FILES['fichierphase2']['name'])) {
            $attachementdesc = $_FILES['fichierphase2']['name'];
        } else {
            $attachementdesc = $manager->getSingle2("select attachementdesc from projet where idprojet =?", $idprojet);
            if (empty($attachementdesc)) {
                $attachementdesc = '';
            }
        }
//------------------------------------------------------------------------------------------------------------------------
//			 MISE A JOUR DES FICHIERS UPLOADES ON VERIFIE L'ECART ENTRE LES NOMS INSCRIT DANS LA TABLE PROJET
//			 ET LES FICHIERS PRESENTS SUR LE SERVEUR, ON EFFACE CEUX QUI NE SONT PAS REFERENCES DANS LA TABLE
//			 PROJET
//------------------------------------------------------------------------------------------------------------------------
        $uploadProjet = $manager->getdataArray("select attachementdesc from projet where attachementdesc !=''");
        $listerepertoire = getDirectoryList("../uploaddesc");
//$listerepertoire = tableau contenant la liste des fichiers dans le répertoire upload
        $resultEcart = array_diff($listerepertoire, $uploadProjet);
//$resultEcart = tableau contenant l'écart entre les fichiers du répertoire distant et le noms des fichier contenu dans la table projet
        for ($i = 0; $i < count($listerepertoire); $i++) {
            if (in_array($listerepertoire[$i], $resultEcart)) { //Vérification si l'
                unlink('../uploaddesc/' . $listerepertoire[$i]); //Suppression du fichier non référencés dans la table projet
            }
        }
//---------------------------------------------------------------------------------------------------------------------------
//---------------------------------------------------------------------------------------------------------------------------
        $devtechnologique = stripslashes(Securite::bdd($_POST['devTechnologique']));
        if ($devtechnologique == FALSE) {
            $verrouidentifie = '';
        }
//------------------------------------------------------------------------------------------------------------
//                              TRAITEMENT DES SOURCES DE FINANCEMENT (CADRE INSTITUTIONNEL)
//------------------------------------------------------------------------------------------------------------

        $nbsource = $manager->getSingle("select count(idsourcefinancement) from sourcefinancement");
        $arraylibellesourcefinancement = array();
        for ($i = 0; $i < $nbsource; $i++) {
            $sf = 'sf' . ($i + 1);
            if (!empty($_POST['' . $sf . ''])) {
                if (strlen($sf) < 4) {
                    array_push($arraylibellesourcefinancement, $manager->getSingle2("select libellesourcefinancement from sourcefinancement where idsourcefinancement=? ", substr($sf, -1)));
                } else {
                    array_push($arraylibellesourcefinancement, $manager->getSingle2("select libellesourcefinancement from sourcefinancement where idsourcefinancement=? ", substr($sf, -2)));
                }
            }
        }
        for ($i = 0; $i < count($arraylibellesourcefinancement); $i++) {
            if (!empty($arraylibellesourcefinancement[$i])) {
                $idsourcefinancement = $manager->getSingle2("select idsourcefinancement from sourcefinancement where libellesourcefinancement=? ", $arraylibellesourcefinancement[$i]);
                $projetSF = new Projetsourcefinancement($idprojet, $idsourcefinancement);
                $manager->insertProjetSF($projetSF); //AJOUT DES SOURCE DE FINANCEMENT DANS LA TABLE PROJETSOURCEFINANCEMENT AVEC L'IDPROJET
            }
        }
        for ($i = 1; $i <= $nbsource; $i++) {
            $champpost = 'acronymesourcesf' . $i;
            if (!empty($_POST['' . $champpost . ''])) {
                $idsourcefinancementacro = substr($champpost, -1); //RECUPERATION DU DERNIER CARACTERE acronymesourcef1-9
                $projetacro = new ProjetAcrosourcefinancement($idprojet, $_POST['' . $champpost . ''], $idsourcefinancementacro);
                $manager->updateProjetacrosourcefinancement($projetacro, $idprojet);
            }
        }
//------------------------------------------------------------------------------------------------------------
//                              TRAITEMENT DES THEMATIQUE
//------------------------------------------------------------------------------------------------------------
        if (!empty($_POST['thematique'])) {
            $idthematique = substr($_POST['thematique'], 2);
            $libellethematique = $manager->getSingle2("select libellethematique from thematique where idthematique =?", $idthematique);
            if ($libellethematique != TXT_AUTRES) {//VALEUR DIFFERENTE DE "Autres"
                $idthematique_thematique = $manager->getSingle2("select idthematique from thematique where libellethematique =?", $libellethematique);
                $idautrethematique_autrethematique = 1; //valeur n/a
            } else {
                //------------------------------------------------------------------------------------------------------------
                //                                       TRAITEMENT DES AUTRES THEMATIQUE
                //------------------------------------------------------------------------------------------------------------
                $autreThematique = stripslashes(Securite::bdd($_POST['autreThematique']));
                $idautrethematique_autrethematique = $manager->getSingle("select max(idautrethematique) from autrethematique") + 1;
                $newautrethematique = new autrethematique($idautrethematique_autrethematique, $autreThematique);
                $manager->addautrethematique($newautrethematique);
                $idthematique_thematique = $manager->getSingle("select idthematique from thematique where libellethematique='Autres'");
            }
        } else {
            $idthematique_thematique = null;
            $idautrethematique_autrethematique = null;
        }
        if (!empty($_POST['nomPartenaire01'])) {
            $partenaire1 = stripslashes(Securite::bdd(($_POST['nomPartenaire01'])));
        } else {
            $partenaire1 = '';
        }
        $porteurprojet = $_POST['porteurprojet'];
        //$descriptionautrecentrale, $etapeautrecentrale, $centraleproximite
        if (!empty($_POST['dureeestimeprojet'])) {
            $dureeestime = stripslashes(Securite::bdd($_POST['dureeestimeprojet']));
        } elseif (!empty($rowprojet[0]['dureeestimeprojet']) || $rowprojet[0]['dureeestimeprojet'] != 0) {
            $dureeestime = $rowprojet[0]['dureeestimeprojet'];
        } else {
            $dureeestime = '';
        }

        if (!empty($_POST['choix2'])) {
            $periodestime = substr($_POST['choix2'], 2);
        } else {
            $periodestime = $manager->getSingle2("select periodestime from projet where idprojet = ?", $idprojet);
        }
        /*  ------------------------------------------------------------------------------------  AUTRES CENTRALE ------------------------------------------------------------------------------------ */
        //centrale de proximite 
        $centraleproximite = stripslashes(Securite::bdd($_POST['centraleproximite']));
        if ($centraleproximite == FALSE) {
            $centraleproximite = '';
        }

        if ($_POST['etapeautrecentrale'] == 'TRUE') {
            $etapeautrecentrale = TRUE;
            if (!empty($_POST['autrecentrale'])) {//CENTRALE SELECTIONNE
                $manager->deleteprojetautrecentrale($idprojet);
                for ($i = 0; $i < count($_POST['autrecentrale']); $i++) {
                    $idcentrale = $manager->getSingle2("select idcentrale from centrale where libellecentrale=?", $_POST['autrecentrale'][$i]);
                    $projetautrecentrale = new Projetautrecentrale($idcentrale, $idprojet);
                    $manager->addprojetautrescentrale($projetautrecentrale);
                }
            }
            if (!empty($_POST['etautrecentrale'])) {//DESCRIPTION DE L'ETAPE
                $descriptionautrecentrale = filterEditor(Securite::bdd(trim($_POST['etautrecentrale'])));
            } else {
                $descriptionautrecentrale = strip_tags(trim($manager->getSingle2("select descriptionautrecentrale from projet where idprojet = ?", $idprojet)));
                if (empty($descriptionautrecentrale)) {
                    $descriptionautrecentrale = '';
                }
                //ENVOI DE L'EMAIL            
            }
        } elseif ($_POST['etapeautrecentrale'] == 'FALSE') {
            $etapeautrecentrale = 'FALSE';
            $manager->deleteprojetautrecentrale($idprojet);
            $descriptionautrecentrale = '';
        } else {
            $etapeautrecentrale = 'FALSE';
            $manager->deleteprojetautrecentrale($idprojet);
            $descriptionautrecentrale = '';
        }
//------------------------------------------------------------------------------------------------------------
//                              TRAITEMENT DU PROJETPHASE2
//------------------------------------------------------------------------------------------------------------    
        $projetphase2 = new Projetphase2($contactCentralAccueil, $idtypeprojet_typeprojet, $nbHeure, $dateDebutTravaux, $dureeprojet, $idperiodicite, $centralepartenaireprojet, $idthematique_thematique, $idautrethematique_autrethematique, $descriptifTechnologique, $attachementdesc, $verrouidentifie, $nbPlaque, $nbRun, $devis, $mailresp, $reussite, $refinterne, $devtechnologique, $nbeleve, $nomformateur, $partenaire1, $porteurprojet, $dureeestime, $periodestime, $descriptionautrecentrale, $etapeautrecentrale, $centraleproximite);
        $manager->updateProjetphase2($projetphase2, $idprojet);
        //MISE A JOUR DATE DEBUT DU PROJET
        $datedebutprojet = new DateDebutProjet($idprojet, date('Y-m-d'));
        $manager->updateDateDebutProjet($datedebutprojet, $idprojet);
//------------------------------------------------------------------------------
//                          PARTENAIRE PROJET
//------------------------------------------------------------------------------    
        if (!empty($_POST['nombrePartenaire']) && $_POST['nombrePartenaire'] != 0) {//SI LE NOMBRE DE PARTENAIRE EST >0
            //SUPPPRESSION DES PARTENAIRES DANS LA TABLE PROJETPARTENAIRE
            $nombrePartenaire = $_POST['nombrePartenaire'];
            $manager->deleteprojetpartenaire($idprojet);
            //RECUPERATION DU PROJET DANS LA TABLE PARTENAIREPROJET QUI N'AS PAS DE REFERENCE DANS LA TABLE PROJETPARTENAIRE-->SUPPRESSION ENREGISTEMENT VIDE
            $idpartenaire = $manager->getList("SELECT idpartenaire FROM  partenaireprojet where idpartenaire not in (select idpartenaire_partenaireprojet from projetpartenaire)");
            //SUPPRESSION DES LIGNES CORRESPONDANTES
            if (count($idpartenaire) > 0) {
                for ($i = 0; $i < count($idpartenaire); $i++) {
                    $manager->deletepartenaireprojet($idpartenaire[$i]['idpartenaire']);
                }
            }
            //$partenaires = '';        
            for ($i = 0; $i < $nombrePartenaire - 1; $i++) {
                $nomPartenaire = 'nomPartenaire' . $i;
                $nomLaboEntreprise = 'nomLaboEntreprise' . $i;
                if (!empty($_POST['' . $nomPartenaire . ''])) {
                    $nomPartenaire = stripslashes(Securite::bdd(($_POST['' . $nomPartenaire . ''])));
                }
                if (!empty($_POST['' . $nomLaboEntreprise . ''])) {
                    $nomLaboEntreprise = stripslashes(Securite::bdd(($_POST['' . $nomLaboEntreprise . ''])));
                }
                //TRAITEMENT AJOUT DANS LA TABLE PARTENAIREPROJET
                $idpartenaire = $manager->getSingle("select max (idpartenaire) from partenaireprojet") + 1;
                $newpartenaireprojet = new Partenaireprojet($idpartenaire, $nomPartenaire, $nomLaboEntreprise);
                $manager->addpartenaireprojet($newpartenaireprojet);
                //$partenaires .=$nomPartenaire . ' - ' . $nomLaboEntreprise . ' - ';
                //TRAITEMENT AJOUT DANS LA TABLE PROJETPARTENAIRE
                $newprojetpartenaire = new Projetpartenaire($idpartenaire, $idprojet);
                $manager->addprojetpartenaire($newprojetpartenaire);
            }
        } else {
            //IL N'A PAS DE PARTENAIRE SELECTIONNE IL FAUT SUPPRIMER LES PARTENAIRES PROJET DANS LES TABLE PARTENAIREPROJET ET PROJETPARTENAIRE
            //------------------------------------------------------------------------------------------------------------------------------------------------------
            $nombrePartenaire = 0;
            $manager->deleteprojetpartenaire($idprojet);
            //RECUPERATION DU PROJET DANS LA TABLE PARTENAIREPROJET QUI N'AS PAS DE REFERENCE DANS LA TABLE PROJETPARTENAIRE-->SUPPRESSION ENREGISTEMENT VIDE
            $idpartenaire = $manager->getList("SELECT idpartenaire FROM  partenaireprojet where idpartenaire not in (select idpartenaire_partenaireprojet from projetpartenaire)");
            //SUPPRESSION DES LIGNES CORRESPONDANTES
            if (count($idpartenaire) > 0) {
                for ($i = 0; $i < count($idpartenaire); $i++) {
                    $manager->deletepartenaireprojet($idpartenaire[$i]['idpartenaire']);
                }
            }
            //EFFACAGE DU ER PARTENAIRE DANS LA TABLE PROJET
            $centralepartenaireprojet = $manager->getSingle2("select centralepartenaireprojet from projet where idprojet=?", $idprojet);
            $partenaire1 = $manager->getSingle2("select partenaire1 from projet where idprojet=?", $idprojet);
            $partenairefromprojet = new Partenairefromprojet($centralepartenaireprojet, $partenaire1);
            $manager->updatepartenairefromprojet($partenairefromprojet, $idprojet);
            //------------------------------------------------------------------------------------------------------------------------------------------------------
            //------------------------------------------------------------------------------------------------------------------------------------------------------					}
        }
//------------------------------------------------------------------------------------------------------------------------
//			 MISE A JOUR DE LA TABLE RESSOURCEPROJET  ON EFFACE TOUTES LES RESSOURCES SELECTIONNEES
//------------------------------------------------------------------------------------------------------------------------
        $ressources = '';
        if (!empty($_POST['ressource'])) {
            $ressource = $_POST['ressource'];
            foreach ($ressource as $chkbx) {
                $arrayressource = $manager->getListbyArray("SELECT idressource FROM ressource where libelleressource =?", array($chkbx));
                $ressources .=$chkbx . ',';
                for ($i = 0; $i < count($arrayressource); $i++) {
                    $idressource_ressource = $arrayressource[$i]['idressource'];
                    $ressourceprojet = new Ressourceprojet($idprojet, $idressource_ressource);
                    $manager->addressourceprojet($ressourceprojet);
                }
            }
        }
//---------------------------------------------------------------------------------------------------------------------------------------------------
//																																																							ACCUEIL CENTRALE
//---------------------------------------------------------------------------------------------------------------------------------------------------
//SUPPPRESSION DES PERSONNES DANS LA TABLE PROJETPERSONNEACCUEILCENTRALE
        $manager->deleteprojetpersonneaccueilcentrale($idprojet);
        //RECUPERATION DU PROJET DANS LA TABLE PERSONNEACCUEILCENTRALE QUI N'AS PAS DE REFERENCE DANS LA TABLE PROJETPERSONNEACCUEILCENTRALE
        $idpersonnecentrale = $manager->getList("SELECT idpersonneaccueilcentrale FROM  personneaccueilcentrale where idpersonneaccueilcentrale not in
	 (select idpersonneaccueilcentrale_personneaccueilcentrale from projetpersonneaccueilcentrale)");
        //SUPPRESSION DES LIGNES CORRESPONDANTES
        if (count($idpersonnecentrale) > 0) {
            for ($i = 0; $i < count($idpersonnecentrale); $i++) {
                $manager->deletepersonneaccueilcentrale($idpersonnecentrale[$i]['idpersonneaccueilcentrale']);
            }
        }
        $nombrePersonneCentrale = +($_POST['integerspinner']);
        for ($i = 0; $i < $nombrePersonneCentrale; $i++) {
            if (!empty($_POST['nomaccueilcentrale' . $i . ''])) {
                $nomAccueilcentrale = $_POST['nomaccueilcentrale' . $i . ''];
                $nomaccueilcentrale = stripslashes(Securite::bdd($nomAccueilcentrale));
            }
            if (!empty($_POST['prenomaccueilcentrale' . $i . ''])) {
                $prenomAccueilcentrale = $_POST['prenomaccueilcentrale' . $i . ''];
                $prenomaccueilcentrale = stripslashes(Securite::bdd($prenomAccueilcentrale));
            }
            if (!empty($_POST['qualiteaccueilcentrale' . $i . ''])) {
                $idqualitedemandeurAca = $_POST['qualiteaccueilcentrale' . $i . ''];
                $idqualitedemandeuraca = (int) substr($idqualitedemandeurAca, -1);
                if ($lang == 'fr') {
                    $libellequalite = $manager->getSingle2("select libellequalitedemandeuraca from qualitedemandeuraca where idqualitedemandeuraca =?", $idqualitedemandeuraca);
                } elseif ($lang == 'en') {
                    $libellequalite = $manager->getSingle2("select libellequalitedemandeuracaen from qualitedemandeuraca where idqualitedemandeuraca =?", $idqualitedemandeuraca);
                }
            }
            if (!empty($_POST['mailaccueilcentrale' . $i . ''])) {
                $mailaccueilcentrale = $_POST['mailaccueilcentrale' . $i . ''];
            }
            if (!empty($_POST['telaccueilcentrale' . $i . ''])) {
                $telaccueilcentrale = $_POST['telaccueilcentrale' . $i . ''];
            } else {
                $telaccueilcentrale = '';
            }
            if (!empty($_POST['connaissancetechnologiqueaccueil' . $i . ''])) {
                $connaissancetechnologiqueAccueil = $_POST['connaissancetechnologiqueaccueil' . $i . ''];
                $connaissancetechnologiqueaccueil = Securite::bdd($connaissancetechnologiqueAccueil);
            } else {
                $connaissancetechnologiqueaccueil = '';
            }
            //TRAITEMENT AJOUT DANS LA TABLE PERSONNEACCUEILCENTRALE
            $idpersonneaccueilcentrale = $manager->getSingle("select max(idpersonneaccueilcentrale) from Personneaccueilcentrale") + 1;
            $personne = new Personneaccueilcentrale($idpersonneaccueilcentrale, $nomaccueilcentrale, $prenomaccueilcentrale, $idqualitedemandeuraca, $mailaccueilcentrale, $telaccueilcentrale, trim($connaissancetechnologiqueAccueil));
            $manager->addPersonneaccueilcentrale($personne);
            //TRAITEMENT AJOUT DANS LA TABLE PROJETPERSONNEACCUEILCENTRALE
            $projetpersonneaccueilcentrale = new Projetpersonneaccueilcentrale($idprojet, $idpersonneaccueilcentrale);
            $manager->addprojetpersonneaccueilcentrale($projetpersonneaccueilcentrale);
        }
        if (empty($centralepartenaireprojet)) {
            $centralepartenaireprojet = false;
        }
        if (empty($centralepartenaireprojet)) {
            $centralepartenaireprojet = false;
        }
        include '../uploadprojetphase2.php';
        BD::deconnecter();
} else {
    include_once '../decide-lang.php';
    header('Location:/' . REPERTOIRE . '/Login_Error/' . $lang);
}
