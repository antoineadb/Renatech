<?php

session_start();

include '../class/Manager.php';
include_once '../decide-lang.php';
include_once '../outils/constantes.php';
include_once '../outils/toolBox.php';
include_once '../class/Cache.php';

showError($_SERVER['PHP_SELF']);
if (isset($_SESSION['pseudo'])) {
    check_authent($_SESSION['pseudo']);

    if (!is_dir(REP_ROOT . '/cache' . LIBELLECENTRALEUSER . '/')) {      
        mkdir(REP_ROOT . '/cache' . LIBELLECENTRALEUSER . '/');
        chmod(REP_ROOT . '/cache' . LIBELLECENTRALEUSER . '/', 0777);
    }
    
    
    $cache = new Cache(REP_ROOT . '/cache' . LIBELLECENTRALEUSER , 60); //1 heure
    
    $db = BD::connecter();
    $manager = new Manager($db);
    if (isset($_SESSION['passe'])) {
        unset($_SESSION['passe']);
    } else {
        unset($_SESSION['mot_de_passe_1']);
    }
    if (isset($_POST['email'])) {
        $mail = pg_escape_string($_POST['email']);
        $_SESSION['mail'] = $mail;
    } elseif (isset($_SESSION['email'])) {
        $mail = $_SESSION['email'];
        $_SESSION['mail'] = $mail;
    } else {
        $mail = $_SESSION['mail'];
        $_SESSION['mail'] = $mail;
    }
    if (isset($_POST['pseudo'])) {
        $pseudo = pg_escape_string($_POST['pseudo']);
        $_SESSION['pseudo'] = $pseudo;
    } elseif (isset($_SESSION['pseudo'])) {
        $pseudo = $_SESSION['pseudo'];
    }
//------------------------------------------------------
//-----AFFICHAGE DU NOM EN HAUT A DROITE DE LA PAGE ---
//------------------------------------------------------
    nomEntete($mail, $pseudo);

//------------------------------------------------------------------------
//-----RECUPERATION DU LIBELLE DE LA CENTRALE DU RESPONSABLE CENTRALE-----
//------------------------------------------------------------------------
 
 
    $idtypeuser = $manager->getSingle2("SELECT idtypeutilisateur_typeutilisateur FROM loginpassword,utilisateur WHERE idlogin = idlogin_loginpassword and pseudo =?", $pseudo);
    if ($idtypeuser == ADMINLOCAL) {
        $libellecentrale = $manager->getSingle2("SELECT libellecentrale FROM centrale , utilisateur ,loginpassword  WHERE idcentrale_centrale = idcentrale AND idlogin_loginpassword = idlogin AND pseudo=?", $pseudo);
        $idcentrale = $manager->getSingle2("SELECT idcentrale FROM centrale , utilisateur ,loginpassword  WHERE idcentrale_centrale = idcentrale AND idlogin_loginpassword = idlogin AND pseudo=?", $pseudo);
    } elseif ($idtypeuser == ADMINNATIONNAL) {
        if (!empty($_POST['centrale'])) {
            $idcentrale = (int) $_POST['centrale'];
            $libellecentrale = $manager->getSingle2("SELECT libellecentrale FROM centrale WHERE idcentrale =?", $idcentrale);
        }
    }

//-----------------------------------------------------------------------------------------------------------------------------------------------
//                                                                  PROJET CENTRALE TOUS
//-----------------------------------------------------------------------------------------------------------------------------------------------
   // if($idtypeuser != ADMINNATIONNAL){
        if (!$cache->read('tous_'.LIBELLECENTRALEUSER)) {
    //SUPPRESSION DE LA TABLE TEMPORAIRE SI ELLE EXISTE
            $manager->exeRequete("drop table if exists tmptous;");
    //CREATION DE LA TABLE TEMPORAIRE
            $manager->getRequete("CREATE TABLE tmptous AS (
    SELECT p.titre,p.idprojet,p.datemaj,p.idperiodicite_periodicite,p.acronyme,p.datedebutprojet,p.numero,p.dureeprojet ,u.idutilisateur,p.refinterneprojet,p.dateprojet,ce.libellecentrale,s.idstatutprojet,s.libellestatutprojet,s.libellestatutprojeten,u.nom||' -  '|| u.prenom as demandeur,null as porteur
    FROM projet p,creer c,utilisateur u,concerne,loginpassword l,centrale ce,statutprojet s
    WHERE p.idprojet = c.idprojet_projet AND c.idutilisateur_utilisateur = u.idutilisateur AND concerne.idprojet_projet = p.idprojet AND
    concerne.idcentrale_centrale = ce.idcentrale AND l.idlogin = u.idlogin_loginpassword AND s.idstatutprojet = concerne.idstatutprojet_statutprojet
    AND ce.libellecentrale =? and idstatutprojet_statutprojet!=? and idstatutprojet_statutprojet!=? and idstatutprojet_statutprojet!=? AND trashed =FALSE
    union
    SELECT p.titre,p.idprojet,p.datemaj,p.idperiodicite_periodicite,p.acronyme,p.datedebutprojet,p.numero,p.dureeprojet ,u.idutilisateur,p.refinterneprojet,p.dateprojet,ce.libellecentrale,s.idstatutprojet,s.libellestatutprojet,s.libellestatutprojeten,null as demandeur,u.nom||' -  '|| u.prenom as porteur
    FROM projet p ,utilisateur u,concerne c,loginpassword l ,centrale ce,statutprojet s,utilisateurporteurprojet up
    WHERE c.idprojet_projet = p.idprojet AND c.idcentrale_centrale = ce.idcentrale AND l.idlogin = u.idlogin_loginpassword
    AND s.idstatutprojet = c.idstatutprojet_statutprojet AND up.idprojet_projet = p.idprojet AND up.idutilisateur_utilisateur = u.idutilisateur
    AND ce.libellecentrale =? and idstatutprojet_statutprojet!=? and idstatutprojet_statutprojet!=? and idstatutprojet_statutprojet!=? AND trashed =FALSE
    union
    SELECT p.titre,p.idprojet,p.datemaj,p.idperiodicite_periodicite,p.acronyme,p.datedebutprojet,p.numero,p.dureeprojet ,u.idutilisateur,p.refinterneprojet,p.dateprojet,ce.libellecentrale,s.idstatutprojet,s.libellestatutprojet,s.libellestatutprojeten,u.nom||' -  '|| u.prenom as demandeur,null as porteur
    FROM projet p,creer c,utilisateur u,concerne,loginpassword l,centrale ce,statutprojet s
    WHERE p.idprojet = c.idprojet_projet AND c.idutilisateur_utilisateur = u.idutilisateur AND concerne.idprojet_projet = p.idprojet AND
    concerne.idcentrale_centrale = ce.idcentrale AND l.idlogin = u.idlogin_loginpassword AND s.idstatutprojet = concerne.idstatutprojet_statutprojet
    AND ce.libellecentrale =? and idstatutprojet_statutprojet!=? and idstatutprojet_statutprojet!=? and idstatutprojet_statutprojet!=? AND trashed =FALSE
    union
    SELECT p.titre,p.idprojet,p.datemaj,p.idperiodicite_periodicite,p.acronyme,p.datedebutprojet,p.numero,p.dureeprojet ,u.idutilisateur,p.refinterneprojet,p.dateprojet,ce.libellecentrale,s.idstatutprojet,s.libellestatutprojet,s.libellestatutprojeten,null as demandeur,u.nom||' -  '|| u.prenom as porteur
    FROM projet p ,utilisateur u,concerne c,loginpassword l ,centrale ce,statutprojet s,utilisateurporteurprojet up
    WHERE c.idprojet_projet = p.idprojet AND c.idcentrale_centrale = ce.idcentrale AND l.idlogin = u.idlogin_loginpassword
    AND s.idstatutprojet = c.idstatutprojet_statutprojet AND up.idprojet_projet = p.idprojet AND up.idutilisateur_utilisateur = u.idutilisateur
    AND ce.libellecentrale =? and idstatutprojet_statutprojet!=? and idstatutprojet_statutprojet!=? and idstatutprojet_statutprojet!=? AND trashed =FALSE
    union
    SELECT p.titre,p.idprojet,p.datemaj,p.idperiodicite_periodicite,p.acronyme,p.datedebutprojet,p.numero,p.dureeprojet ,u.idutilisateur,p.refinterneprojet,p.dateprojet,ce.libellecentrale,s.idstatutprojet,s.libellestatutprojet,s.libellestatutprojeten,u.nom||' -  '|| u.prenom as demandeur,null as porteur
    FROM projet p,creer c,utilisateur u,concerne,loginpassword l,centrale ce,statutprojet s
    WHERE p.idprojet = c.idprojet_projet AND c.idutilisateur_utilisateur = u.idutilisateur AND concerne.idprojet_projet = p.idprojet AND
    concerne.idcentrale_centrale = ce.idcentrale AND l.idlogin = u.idlogin_loginpassword AND s.idstatutprojet = concerne.idstatutprojet_statutprojet
    AND ce.libellecentrale =? and idstatutprojet_statutprojet!=? and idstatutprojet_statutprojet!=? and idstatutprojet_statutprojet!=? AND trashed =FALSE
    union
    SELECT p.titre,p.idprojet,p.datemaj,p.idperiodicite_periodicite,p.acronyme,p.datedebutprojet,p.numero,p.dureeprojet ,u.idutilisateur,p.refinterneprojet,p.dateprojet,ce.libellecentrale,s.idstatutprojet,s.libellestatutprojet,s.libellestatutprojeten,null as demandeur,u.nom||' -  '|| u.prenom as porteur
    FROM projet p ,utilisateur u,concerne c,loginpassword l ,centrale ce,statutprojet s,utilisateurporteurprojet up
    WHERE c.idprojet_projet = p.idprojet AND c.idcentrale_centrale = ce.idcentrale AND l.idlogin = u.idlogin_loginpassword
    AND s.idstatutprojet = c.idstatutprojet_statutprojet AND up.idprojet_projet = p.idprojet AND up.idutilisateur_utilisateur = u.idutilisateur
    AND ce.libellecentrale =? and idstatutprojet_statutprojet!=? and idstatutprojet_statutprojet!=? and idstatutprojet_statutprojet!=? AND trashed =FALSE
    union
    SELECT p.titre,p.idprojet,p.datemaj,p.idperiodicite_periodicite,p.acronyme,p.datedebutprojet,p.numero,p.dureeprojet ,u.idutilisateur,p.refinterneprojet,p.dateprojet,ce.libellecentrale,s.idstatutprojet,s.libellestatutprojet,s.libellestatutprojeten,u.nom||' -  '|| u.prenom as demandeur, null as porteur
    FROM projet p,creer c,utilisateur u,concerne,loginpassword l,centrale ce,statutprojet s
    WHERE p.idprojet = c.idprojet_projet AND c.idutilisateur_utilisateur = u.idutilisateur AND concerne.idprojet_projet = p.idprojet AND
    concerne.idcentrale_centrale = ce.idcentrale AND l.idlogin = u.idlogin_loginpassword AND s.idstatutprojet = concerne.idstatutprojet_statutprojet
    AND ce.libellecentrale =? and idstatutprojet_statutprojet!=? and idstatutprojet_statutprojet!=? and idstatutprojet_statutprojet!=? AND trashed =FALSE
    union
    SELECT p.titre,p.idprojet,p.datemaj,p.idperiodicite_periodicite,p.acronyme,p.datedebutprojet,p.numero,p.dureeprojet ,u.idutilisateur,p.refinterneprojet,p.dateprojet,ce.libellecentrale,s.idstatutprojet,s.libellestatutprojet,s.libellestatutprojeten,null as demandeur,u.nom||' -  '|| u.prenom as porteur
    FROM projet p ,utilisateur u,concerne c,loginpassword l ,centrale ce,statutprojet s,utilisateurporteurprojet up
    WHERE c.idprojet_projet = p.idprojet AND c.idcentrale_centrale = ce.idcentrale AND l.idlogin = u.idlogin_loginpassword
    AND s.idstatutprojet = c.idstatutprojet_statutprojet AND up.idprojet_projet = p.idprojet AND up.idutilisateur_utilisateur = u.idutilisateur
    AND ce.libellecentrale =? and idstatutprojet_statutprojet!=? and idstatutprojet_statutprojet!=? and idstatutprojet_statutprojet!=? AND trashed =FALSE
    union
    SELECT p.titre,p.idprojet,p.datemaj,p.idperiodicite_periodicite,p.acronyme,p.datedebutprojet,p.numero,p.dureeprojet ,u.idutilisateur,p.refinterneprojet,p.dateprojet,ce.libellecentrale,s.idstatutprojet,s.libellestatutprojet,s.libellestatutprojeten,u.nom||' -  '|| u.prenom as demandeur, null as porteur
    FROM projet p,creer c,utilisateur u,concerne,loginpassword l,centrale ce,statutprojet s
    WHERE p.idprojet = c.idprojet_projet AND c.idutilisateur_utilisateur = u.idutilisateur AND concerne.idprojet_projet = p.idprojet AND
    concerne.idcentrale_centrale = ce.idcentrale AND l.idlogin = u.idlogin_loginpassword AND s.idstatutprojet = concerne.idstatutprojet_statutprojet
    AND ce.libellecentrale =? and idstatutprojet_statutprojet!=? and idstatutprojet_statutprojet!=? and idstatutprojet_statutprojet!=? AND trashed =FALSE
    union
    SELECT p.titre,p.idprojet,p.datemaj,p.idperiodicite_periodicite,p.acronyme,p.datedebutprojet,p.numero,p.dureeprojet ,u.idutilisateur,p.refinterneprojet,p.dateprojet,ce.libellecentrale,s.idstatutprojet,s.libellestatutprojet,s.libellestatutprojeten,null as demandeur,u.nom||' -  '|| u.prenom as porteur
    FROM projet p ,utilisateur u,concerne c,loginpassword l ,centrale ce,statutprojet s,utilisateurporteurprojet up
    WHERE c.idprojet_projet = p.idprojet AND c.idcentrale_centrale = ce.idcentrale AND l.idlogin = u.idlogin_loginpassword
    AND s.idstatutprojet = c.idstatutprojet_statutprojet AND up.idprojet_projet = p.idprojet AND up.idutilisateur_utilisateur = u.idutilisateur
    AND ce.libellecentrale =? and idstatutprojet_statutprojet!=? and idstatutprojet_statutprojet!=? and idstatutprojet_statutprojet!=? AND trashed =FALSE
    )", array($libellecentrale, FINI, REFUSE, CLOTURE, $libellecentrale, FINI, REFUSE, CLOTURE, $libellecentrale, FINI, REFUSE, CLOTURE, $libellecentrale, FINI, REFUSE, CLOTURE, $libellecentrale, FINI, REFUSE, CLOTURE, $libellecentrale,
                FINI, REFUSE, CLOTURE, $libellecentrale, FINI, REFUSE, CLOTURE, $libellecentrale, FINI, REFUSE, CLOTURE, $libellecentrale, FINI, REFUSE, CLOTURE, $libellecentrale, FINI, REFUSE, CLOTURE));
            $arrayprojet = $manager->getList("select * from tmptous order by idprojet desc");
            $nbProjet = count($arrayprojet);
            $manager->exeRequete("ALTER TABLE tmptous ADD COLUMN calcfinprojet date;");
            $manager->exeRequete("ALTER TABLE tmptous ADD COLUMN finprojetproche date;");
            for ($i = 0; $i < $nbProjet; $i++) {
                if ($arrayprojet[$i]['idstatutprojet'] != REFUSE && $arrayprojet[$i]['idstatutprojet'] != FINI && $arrayprojet[$i]['idstatutprojet'] != CLOTURE && $arrayprojet[$i]['idstatutprojet'] != ACCEPTE && $arrayprojet[$i]['idstatutprojet'] != ENATTENTEPHASE2) {
                    if ($arrayprojet[$i]['idperiodicite_periodicite'] == JOUR) {
                        $datedepart = strtotime($arrayprojet[$i]['datedebutprojet']);
                        $duree = ($arrayprojet[$i]['dureeprojet']);
                        $dateFin = date('Y-m-d', strtotime('+' . $duree . 'day', $datedepart));
                        $dureeproche = $duree - 15;
                        $dateFinproche = date('Y-m-d', strtotime('+' . $dureeproche . 'day', $datedepart));
                        $annee = (int) date('Y', strtotime($dateFinproche));
                        if ($annee > 1970) {
                            $manager->getRequete("update tmptous set calcfinprojet=?,finprojetproche=? where idprojet=? ", array($dateFin, $dateFinproche, $arrayprojet[$i]['idprojet']));
                        }
                    } elseif ($arrayprojet[$i]['idperiodicite_periodicite'] == MOIS) {
                        $datedepart = strtotime($arrayprojet[$i]['datedebutprojet']);
                        $duree = ($arrayprojet[$i]['dureeprojet']);
                        $dateFin = date('Y-m-d', strtotime('+' . $duree . 'month', $datedepart));
                        $dureeproche = ($duree * 30) - 15;
                        $dateFinproche = date('Y-m-d', strtotime('+' . $dureeproche . 'day', $datedepart));
                        $annee = (int) date('Y', strtotime($dateFinproche));
                        if ($annee > 1970) {
                            $manager->getRequete("update tmptous set calcfinprojet=?,finprojetproche=? where idprojet=? ", array($dateFin, $dateFinproche, $arrayprojet[$i]['idprojet']));
                        }
                    } elseif ($arrayprojet[$i]['idperiodicite_periodicite'] == ANNEE) {
                        $datedepart = strtotime($arrayprojet[$i]['datedebutprojet']);
                        $duree = ($arrayprojet[$i]['dureeprojet']);
                        $dateFin = date('Y-m-d', strtotime('+' . $duree . 'year', $datedepart));
                        $dureeproche = ($duree * 365) - 15;
                        $dateFinproche = date('Y-m-d', strtotime('+' . $dureeproche . 'day', $datedepart));
                        $annee = (int) date('Y', strtotime($dateFinproche));
                        if ($annee > 1970) {
                            $manager->getRequete("update tmptous set calcfinprojet=?,finprojetproche=? where idprojet=? ", array($dateFin, $dateFinproche, $arrayprojet[$i]['idprojet']));
                        }
                    }
                }
            }
            $_SESSION['nbprojet'] = $manager->getSingle("select count(distinct idprojet) from tmptous");
            $porteur = '';
            $arrayporteur1 = $manager->getList("select distinct numero from tmptous");
            $arrayporteur = array();

            foreach ($arrayporteur1 as $key => $value) {
                $arrayporteur = $manager->getList2("select distinct porteur from tmptous where  numero=?", $value[0]);
                foreach ($arrayporteur as $key1 => $value1) {
                    if (!empty($value1[0])) {
                        $porteur.= $value1[0] . '  / ';
                    }
                    $valeur = $value[0];
                    $porteur = substr(trim($porteur), 0, -1);
                    if (!empty($porteur)) {
                        $tmptous = new Tmprecherche($porteur, $valeur);
                        $manager->updateProjetcentrale($tmptous, $value[0]);
                    }
                }
                $porteur = '';
            }
            $row = $manager->getList("select * from (select distinct on(numero) *from tmptous where demandeur is not null)p order by idprojet desc");
            $fprow = fopen("../tmp/projetCentrale".IDCENTRALEUSER.".json", 'w');
            $datausercompte = "";
            fwrite($fprow, '{"items": [');
            $nbrow = count($row);

            for ($i = 0; $i < $nbrow; $i++) {
                if (!empty($row[$i]['datemaj'])) {
                    $datemaj = $row[$i]['datemaj'];
                } else {
                    $datemaj = '';
                }
                if ($lang == 'fr') {
                    $datausercompte = "" . '{"numero":' . '"' . $row[$i]['numero'] . '"' . "," .
                            '"dateprojet":' . '"' . $row[$i]['dateprojet'] . '"' . ","
                            . '"idprojet":' . '"' . $row[$i]['idprojet'] . '"' . ","
                            . '"datemaj":' . '"' . $datemaj . '"' . ","
                            . '"titre":' . '"' . filtredonnee($row[$i]['titre']) . '"' . ","
                            . '"refinterneprojet":' . '"' . filtredonnee($row[$i]['refinterneprojet']) . ' - ' . filtredonnee($row[$i]['acronyme']) . '"' . ","
                            . '"libellestatutprojet":' . '"' . str_replace("''", "'", $row[$i]['libellestatutprojet']) . '"' . ","
                            . '"idstatutprojet":' . '"' . str_replace("''", "'", $row[$i]['idstatutprojet']) . '"' . ","
                            . '"idutilisateur":' . '"' . $row[$i]['idutilisateur'] . '"' . ","
                            . '"demandeur":' . '"' . filtredonnee(ucfirst($row[$i]['demandeur'])) . '"' . ","
                            . '"porteur":' . '"' . filtredonnee(ucfirst($row[$i]['porteur'])) . '"' . ","
                            . '"acronyme":' . '"' . filtredonnee($row[$i]['acronyme']) . '"' . ","
                            . '"libellecentrale":' . '"' . $row[$i]['libellecentrale'] . '"' . ","
                            . '"calcfinproche":' . '"' . $row[$i]['finprojetproche'] . '"' . ","
                            . '"calcfinprojet":' . '"' . $row[$i]['calcfinprojet'] . '"' . ","
                            . '"imprime":' . '"' . TXT_PDF . '"' . "},";
                    fputs($fprow, $datausercompte);
                    fwrite($fprow, '');
                } elseif ($lang == 'en') {
                    $datausercompte = "" . '{"numero":' . '"' . $row[$i]['numero'] . '"' . "," .
                            '"dateprojet":' . '"' . $row[$i]['dateprojet'] . '"' . ","
                            . '"idprojet":' . '"' . $row[$i]['idprojet'] . '"' . ","
                            . '"datemaj":' . '"' . $datemaj . '"' . ","
                            . '"titre":' . '"' . filtredonnee($row[$i]['titre']) . '"' . ","
                            . '"refinterneprojet":' . '"' . filtredonnee($row[$i]['refinterneprojet']) . ' - ' . filtredonnee($row[$i]['acronyme']) . '"' . ","
                            . '"libellestatutprojet":' . '"' . str_replace("''", "'", $row[$i]['libellestatutprojeten']) . '"' . ","
                            . '"idstatutprojet":' . '"' . str_replace("''", "'", $row[$i]['idstatutprojet']) . '"' . ","
                            . '"idutilisateur":' . '"' . $row[$i]['idutilisateur'] . '"' . ","
                            . '"demandeur":' . '"' . filtredonnee(ucfirst($row[$i]['demandeur'])) . '"' . ","
                            . '"porteur":' . '"' . filtredonnee(ucfirst($row[$i]['porteur'])) . '"' . ","
                            . '"acronyme":' . '"' . filtredonnee($row[$i]['acronyme']) . '"' . ","
                            . '"libellecentrale":' . '"' . $row[$i]['libellecentrale'] . '"' . ","
                            . '"calcfinproche":' . '"' . $row[$i]['finprojetproche'] . '"' . ","
                            . '"calcfinprojet":' . '"' . $row[$i]['calcfinprojet'] . '"' . ","
                            . '"imprime":' . '"' . TXT_PDF . '"' . "},";
                    fputs($fprow, $datausercompte);
                    fwrite($fprow, '');
                }
            }
            fwrite($fprow, ']}');
            $json_fileprojet = "../tmp/projetCentrale".IDCENTRALEUSER.".json";
            $json_fileRecherche1 = file_get_contents($json_fileprojet);
            $json_fileRecherche = str_replace('},]}', '}]}', $json_fileRecherche1);
            file_put_contents($json_fileprojet, $json_fileRecherche);

            $cache->write('tous_'.LIBELLECENTRALEUSER,$json_fileRecherche);

            fclose($fprow);
            chmod("../tmp/projetCentrale".IDCENTRALEUSER.".json", 0777);
        }
   // }
    $_SESSION['email'] = $mail;
    $_SESSION['pseudo'] = $pseudo;
//-----------------------------------------------------------------------------------------------------------------------------------------------
//                                                                  PROJET CENTRALE EN COURS DE REALISATION
//-----------------------------------------------------------------------------------------------------------------------------------------------

        if (!$cache->read('encours_'.LIBELLECENTRALEUSER)) {
//SUPPRESSION DE LA TABLE TEMPORAIRE SI ELLE EXISTE
        $manager->exeRequete("drop table if exists tmpencoursrealisation;");
//CREATION DE LA TABLE TEMPORAIRE
        $manager->getRequete("create table tmpencoursrealisation as
(SELECT p.idprojet,p.datemaj,p.acronyme,p.idperiodicite_periodicite,p.dureeprojet,p.refinterneprojet,s.libellestatutprojet,libellestatutprojeten,s.idstatutprojet,p.numero,p.titre,p.datedebutprojet,p.dateprojet,u.nom,u.nomentreprise,u.entrepriselaboratoire,null as nomAffecte,null as prenomAffecte,ce.libellecentrale
  FROM  projet p,utilisateur u,creer cr,centrale ce,concerne co,typeprojet t,statutprojet s
  WHERE cr.idprojet_projet = p.idprojet AND cr.idutilisateur_utilisateur = u.idutilisateur AND co.idcentrale_centrale = ce.idcentrale AND
  co.idprojet_projet = p.idprojet AND  t.idtypeprojet = p.idtypeprojet_typeprojet
  AND s.idstatutprojet = co.idstatutprojet_statutprojet and ce.libellecentrale = ? and s.idstatutprojet=? AND trashed =FALSE
union
SELECT idprojet,p.datemaj,acronyme,p.idperiodicite_periodicite,p.dureeprojet,p.refinterneprojet,s.libellestatutprojet,libellestatutprojeten,s.idstatutprojet,numero,titre,p.datedebutprojet,p.dateprojet,u.nom,u.nomentreprise,u.entrepriselaboratoire,u1.nom,u1.prenom,ce.libellecentrale
FROM projet p,utilisateur u,creer,centrale ce,concerne,typeprojet,statutprojet s,utilisateurporteurprojet,utilisateur u1
WHERE idtypeprojet_typeprojet = typeprojet.idtypeprojet AND u.idutilisateur = creer.idutilisateur_utilisateur AND
  creer.idprojet_projet = idprojet AND concerne.idcentrale_centrale = idcentrale AND concerne.idprojet_projet = idprojet AND
  concerne.idstatutprojet_statutprojet = s.idstatutprojet AND utilisateurporteurprojet.idprojet_projet = idprojet AND
  utilisateurporteurprojet.idutilisateur_utilisateur = u1.idutilisateur AND ce.libellecentrale = ? and s.idstatutprojet=? AND trashed =FALSE
order by idprojet asc);", array($libellecentrale, ENCOURSREALISATION, $libellecentrale, ENCOURSREALISATION));

        $manager->exeRequete("ALTER TABLE tmpencoursrealisation ADD COLUMN calcfinprojet date;");
        $manager->exeRequete("ALTER TABLE tmpencoursrealisation ADD COLUMN finprojetproche date;");
        $arrayprojetrealisation = $manager->getList("select distinct on(idprojet) idprojet,datemaj,acronyme,idperiodicite_periodicite,dureeprojet,refinterneprojet,libellestatutprojet,libellestatutprojeten,idstatutprojet,
            numero,titre,datedebutprojet,dateprojet,nom,nomentreprise,entrepriselaboratoire,nomaffecte,prenomaffecte,libellecentrale,calcfinprojet,finprojetproche from tmpencoursrealisation");
        $nbProjetreal = count($arrayprojetrealisation);
        for ($i = 0; $i < $nbProjetreal; $i++) {
            if ($arrayprojetrealisation[$i]['idperiodicite_periodicite'] == JOUR) {
                $datedepart = strtotime($arrayprojetrealisation[$i]['datedebutprojet']);
                $duree = ($arrayprojetrealisation[$i]['dureeprojet']);
                $dateFin = date('Y-m-d', strtotime('+' . $duree . 'day', $datedepart));
                $dureeproche = $duree - 15;
                $dateFinproche = date('Y-m-d', strtotime('+' . $dureeproche . 'day', $datedepart));
                $annee = (int) date('Y', strtotime($dateFinproche));
                $manager->getRequete("update tmpencoursrealisation set calcfinprojet=?,finprojetproche=? where idprojet=? ", array($dateFin, $dateFinproche, $arrayprojetrealisation[$i]['idprojet']));
            } elseif ($arrayprojetrealisation[$i]['idperiodicite_periodicite'] == MOIS) {
                $datedepart = strtotime($arrayprojetrealisation[$i]['datedebutprojet']);
                $duree = ($arrayprojetrealisation[$i]['dureeprojet']);
                $dateFin = date('Y-m-d', strtotime('+' . $duree . 'month', $datedepart));
                $dureeproche = ($duree * 30) - 15;
                $dateFinproche = date('Y-m-d', strtotime('+' . $dureeproche . 'day', $datedepart));
                $annee = (int) date('Y', strtotime($dateFinproche));
                $manager->getRequete("update tmpencoursrealisation set calcfinprojet=?,finprojetproche=? where idprojet=? ", array($dateFin, $dateFinproche, $arrayprojetrealisation[$i]['idprojet']));
            } elseif ($arrayprojetrealisation[$i]['idperiodicite_periodicite'] == ANNEE) {
                $datedepart = strtotime($arrayprojetrealisation[$i]['datedebutprojet']);
                $duree = ($arrayprojetrealisation[$i]['dureeprojet']);
                $dateFin = date('Y-m-d', strtotime('+' . $duree . 'year', $datedepart));
                $dureeproche = ($duree * 365) - 15;
                $dateFinproche = date('Y-m-d', strtotime('+' . $dureeproche . 'day', $datedepart));
                $annee = (int) date('Y', strtotime($dateFinproche));
                $manager->getRequete("update tmpencoursrealisation set calcfinprojet=?,finprojetproche=? where idprojet=? ", array($dateFin, $dateFinproche, $arrayprojetrealisation[$i]['idprojet']));
            }
        }
        $rowProjetEncoursRealisation = $manager->getList("
    select  finprojetproche,datemaj,calcfinprojet,idprojet,acronyme,refinterneprojet,libellestatutprojet,libellestatutprojeten,idstatutprojet,numero,titre,datedebutprojet,dateprojet,nom,nomentreprise,entrepriselaboratoire,nomaffecte,prenomaffecte,libellecentrale from tmpencoursrealisation
    where idprojet not in (select idprojet from tmpencoursrealisation where nomaffecte is not null)
    union
    select finprojetproche,datemaj,calcfinprojet,idprojet,acronyme,refinterneprojet,libellestatutprojet,libellestatutprojeten,idstatutprojet,numero,titre,datedebutprojet,dateprojet,nom,nomentreprise,entrepriselaboratoire,nomaffecte,prenomaffecte,libellecentrale from tmpencoursrealisation where nomaffecte is not null
    order by idprojet asc");
        $fpProjetEncoursRealisation = fopen("../tmp/ProjetEncoursRealisationcentrale".IDCENTRALEUSER.".json", 'w');
        $dataProjetEncoursRealisation = "";
        fwrite($fpProjetEncoursRealisation, '{"items": [');
        $nbrowProjetEncoursRealisation = count($rowProjetEncoursRealisation);
        for ($i = 0; $i < $nbrowProjetEncoursRealisation; $i++) {
            if (!empty($rowProjetEncoursRealisation[$i]['datemaj'])) {
                $datemaj = $rowProjetEncoursRealisation[$i]['datemaj'];
            } else {
                $datemaj = '';
            }
            if ($lang == 'fr') {
                $dataProjetEncoursRealisation = "" . '{"dateprojet":' . '"' . $rowProjetEncoursRealisation[$i]['dateprojet'] . '"' . ","
                        . '"datedebutprojet":' . '"' . $rowProjetEncoursRealisation[$i]['datedebutprojet'] . '"' . ","
                        . '"idstatutprojet":' . '"' . $rowProjetEncoursRealisation[$i]['idstatutprojet'] . '"' . ","
                        . '"libellecentrale":' . '"' . $rowProjetEncoursRealisation[$i]['libellecentrale'] . '"' . ","
                        . '"datemaj":' . '"' . $datemaj . '"' . ","
                        . '"numero":' . '"' . $rowProjetEncoursRealisation[$i]['numero'] . '"' . ","
                        . '"idprojet":' . '"' . $rowProjetEncoursRealisation[$i]['idprojet'] . '"' . ","
                        . '"titre":' . '"' . filtredonnee($rowProjetEncoursRealisation[$i]['titre']) . '"' . ","
                        . '"libellestatutprojet":' . '"' . $rowProjetEncoursRealisation[$i]['libellestatutprojet'] . '"' . ","
                        . '"nom":' . '"' . filtredonnee($rowProjetEncoursRealisation[$i]['nom']) . '"' . ","
                        . '"nomentreprise":' . '"' . filtredonnee($rowProjetEncoursRealisation[$i]['nomentreprise']) . '"' . ","
                        . '"calcfinproche":' . '"' . $rowProjetEncoursRealisation[$i]['finprojetproche'] . '"' . ","
                        . '"calcfinprojet":' . '"' . $rowProjetEncoursRealisation[$i]['calcfinprojet'] . '"' . ","
                        . '"entrepriselaboratoire":' . '"' . filtredonnee($rowProjetEncoursRealisation[$i]['entrepriselaboratoire']) . '"' . ","
                        . '"acronyme":' . '"' . filtredonnee($rowProjetEncoursRealisation[$i]['acronyme']) . ' - ' . filtredonnee($rowProjetEncoursRealisation[$i]['refinterneprojet']) . '"' . "},";
                fputs($fpProjetEncoursRealisation, $dataProjetEncoursRealisation);
                fwrite($fpProjetEncoursRealisation, '');
            } elseif ($lang == 'en') {
                for ($i = 0; $i < $nbrowProjetEncoursRealisation; $i++) {
                    $dataProjetEncoursRealisation = "" . '{"dateprojet":' . '"' . $rowProjetEncoursRealisation[$i]['dateprojet'] . '"' . ","
                            . '"datedebutprojet":' . '"' . $rowProjetEncoursRealisation[$i]['datedebutprojet'] . '"' . ","
                            . '"idstatutprojet":' . '"' . $rowProjetEncoursRealisation[$i]['idstatutprojet'] . '"' . ","
                            . '"libellecentrale":' . '"' . $rowProjetEncoursRealisation[$i]['libellecentrale'] . '"' . ","
                            . '"datemaj":' . '"' . $datemaj . '"' . ","
                            . '"numero":' . '"' . $rowProjetEncoursRealisation[$i]['numero'] . '"' . ","
                            . '"titre":' . '"' . filtredonnee($rowProjetEncoursRealisation[$i]['titre']) . '"' . ","
                            . '"libellestatutprojet":' . '"' . $rowProjetEncoursRealisation[$i]['libellestatutprojeten'] . '"' . ","
                            . '"nom":' . '"' . filtredonnee($rowProjetEncoursRealisation[$i]['nom']) . '"' . ","
                            . '"nomentreprise":' . '"' . filtredonnee($rowProjetEncoursRealisation[$i]['nomentreprise']) . '"' . ","
                            . '"calcfinproche":' . '"' . $rowProjetEncoursRealisation[$i]['finprojetproche'] . '"' . ","
                            . '"calcfinprojet":' . '"' . $rowProjetEncoursRealisation[$i]['calcfinprojet'] . '"' . ","
                            . '"entrepriselaboratoire":' . '"' . filtredonnee($rowProjetEncoursRealisation[$i]['entrepriselaboratoire']) . '"' . ","
                            . '"acronyme":' . '"' . filtredonnee($rowProjetEncoursRealisation[$i]['acronyme']) . ' - ' . filtredonnee($rowProjetEncoursRealisation[$i]['refinterneprojet']) . '"' . "},";
                    fputs($fpProjetEncoursRealisation, $dataProjetEncoursRealisation);
                    fwrite($fpProjetEncoursRealisation, '');
                }
            }
        }
        fwrite($fpProjetEncoursRealisation, ']}');
        $json_fileEncoursRealisation = "../tmp/ProjetEncoursRealisationcentrale".IDCENTRALEUSER.".json";
        $jsonEncoursRealisation = file_get_contents($json_fileEncoursRealisation);
        $jsonEncoursRealisation1 = str_replace('},]}', '}]}', $jsonEncoursRealisation);
        file_put_contents($json_fileEncoursRealisation, $jsonEncoursRealisation1);
        $cache->write('encours_'.LIBELLECENTRALEUSER,$jsonEncoursRealisation1);
        fclose($fpProjetEncoursRealisation);
        chmod("../tmp/ProjetEncoursRealisationcentrale".IDCENTRALEUSER.".json", 0777);
        $_SESSION['nbprojetencoursrealisation'] = $nbProjetreal;
        }    
        if (!$cache->read('accepte_'.LIBELLECENTRALEUSER)) {
    //-----------------------------------------------------------------------------------------------------------------------------------------------
    //                                                                  PROJET CENTRALE ACCEPTE
    //-----------------------------------------------------------------------------------------------------------------------------------------------
    //SUPPRESSION DE LA TABLE TEMPORAIRE SI ELLE EXISTE
            $manager->exeRequete("drop table if exists tmpaccepte;");
    //CREATION DE LA TABLE TEMPORAIRE
            $manager->getRequete("create table tmpaccepte as
        (
    SELECT p.idprojet,p.acronyme,s.libellestatutprojet,s.idstatutprojet,p.numero,p.titre,p.dateprojet,u.nom,u.nomentreprise,u.entrepriselaboratoire,null as nomAffecte,null as prenomAffecte,ce.libellecentrale,p.refinterneprojet
      FROM  projet p,utilisateur u,creer cr,centrale ce,concerne co,typeprojet t,statutprojet s
      WHERE cr.idprojet_projet = p.idprojet AND cr.idutilisateur_utilisateur = u.idutilisateur AND co.idcentrale_centrale = ce.idcentrale AND
      co.idprojet_projet = p.idprojet AND  t.idtypeprojet = p.idtypeprojet_typeprojet
      AND s.idstatutprojet = co.idstatutprojet_statutprojet and ce.libellecentrale =? and s.idstatutprojet=? AND trashed =FALSE
    union
    SELECT idprojet,acronyme,s.libellestatutprojet,s.idstatutprojet,numero,titre,dateprojet,u.nom,u.nomentreprise,u.entrepriselaboratoire,u1.nom,u1.prenom,ce.libellecentrale,refinterneprojet
    FROM projet,utilisateur u,creer,centrale ce,concerne,typeprojet,statutprojet s,utilisateurporteurprojet,utilisateur u1
    WHERE idtypeprojet_typeprojet = typeprojet.idtypeprojet AND u.idutilisateur = creer.idutilisateur_utilisateur AND
      creer.idprojet_projet = idprojet AND concerne.idcentrale_centrale = idcentrale AND concerne.idprojet_projet = idprojet AND
      concerne.idstatutprojet_statutprojet = s.idstatutprojet AND utilisateurporteurprojet.idprojet_projet = idprojet AND
      utilisateurporteurprojet.idutilisateur_utilisateur = u1.idutilisateur AND ce.libellecentrale =? and s.idstatutprojet=? AND trashed =FALSE order by idprojet asc
    );", array($libellecentrale, ACCEPTE, $libellecentrale, ACCEPTE));
            $rowProjetAcceptee = $manager->getList("
            select  idprojet,acronyme,libellestatutprojet,idstatutprojet,numero,titre,dateprojet,nom,nomentreprise,entrepriselaboratoire,nomaffecte,prenomaffecte,libellecentrale,refinterneprojet from tmpaccepte
            where idprojet not in (select idprojet from tmpaccepte where nomaffecte is not null)
            union
            select idprojet,acronyme,libellestatutprojet,idstatutprojet,numero,titre,dateprojet,nom,nomentreprise,entrepriselaboratoire,nomaffecte,prenomaffecte,libellecentrale,refinterneprojet from tmpaccepte where nomaffecte is not null order by idprojet asc");
            $fpProjetAcceptee = fopen("../tmp/ProjetAccepteecentrale".IDCENTRALEUSER.".json", 'w');
            $dataProjetAcceptee = "";
            fwrite($fpProjetAcceptee, '{"items": [');
            $nbrowProjetAcceptee = count($rowProjetAcceptee);
            for ($i = 0; $i < $nbrowProjetAcceptee; $i++) {
                $dataProjetAcceptee = "" . '{"dateProjet":' . '"' . $rowProjetAcceptee[$i]['dateprojet'] . '"' . "," . '"numero":' . '"' . $rowProjetAcceptee[$i]['numero'] . '"' . "," . '"titre":' . '"' .
                        filtredonnee($rowProjetAcceptee[$i]['titre']) . '"' . ","
                        . '"libellestatutProjet":' . '"' . str_replace("''", "'", $rowProjetAcceptee[$i]['libellestatutprojet']) . '"' . ","
                        . '"idstatutprojet":' . '"' . $rowProjetAcceptee[$i]['idstatutprojet'] . '"' . ","
                        . '"libellecentrale":' . '"' . $rowProjetAcceptee[$i]['libellecentrale'] . '"' . ","
                        . '"refinterneprojet":' . '"' . filtredonnee($rowProjetAcceptee[$i]['refinterneprojet']) . '"' . ","
                        . '"nom":' . '"' . filtredonnee($rowProjetAcceptee[$i]['nom']) . '"' . ","
                        . '"nomentreprise":' . '"' . filtredonnee($rowProjetAcceptee[$i]['nomentreprise']) . '"' . "," . '"entrepriselaboratoire":' . '"' . filtredonnee($rowProjetAcceptee[$i]['entrepriselaboratoire']) . '"'
                        . "," . '"acronyme":' . '"' . filtredonnee($rowProjetAcceptee[$i]['acronyme']) . '"' . "},";
                fputs($fpProjetAcceptee, $dataProjetAcceptee);
                fwrite($fpProjetAcceptee, '');
            }
            fwrite($fpProjetAcceptee, ']}');
            $json_fileAccepteeCentrale = "../tmp/ProjetAccepteecentrale".IDCENTRALEUSER.".json";
            $jsonAccepteeCentrale = file_get_contents($json_fileAccepteeCentrale);
            $jsonAccepteeCentrale1 = str_replace('},]}', '}]}', $jsonAccepteeCentrale);
            file_put_contents($json_fileAccepteeCentrale, $jsonAccepteeCentrale1);
            $cache->write('accepte_'.LIBELLECENTRALEUSER,$jsonAccepteeCentrale1);
            fclose($fpProjetAcceptee);
            chmod("../tmp/ProjetAccepteecentrale".IDCENTRALEUSER.".json", 0777);
            $_SESSION['nbprojetaccepte'] = $nbrowProjetAcceptee;
        }
        if (!$cache->read('attente_'.LIBELLECENTRALEUSER)) {
        //-----------------------------------------------------------------------------------------------------------------------------------------------
        //                                                                  PROJET CENTRALE EN ATTENTE
        //-----------------------------------------------------------------------------------------------------------------------------------------------
                $rowProjetAttente = $manager->getListbyArray("
        SELECT p.numero,p.acronyme,p.titre,p.dateprojet,ce.libellecentrale,s.libellestatutprojet,s.idstatutprojet,p.idprojet,u.nom,u.nomentreprise,u.entrepriselaboratoire
        FROM  projet p,utilisateur u,creer cr,centrale ce,concerne co,typeprojet t,statutprojet s
        WHERE cr.idprojet_projet = p.idprojet AND cr.idutilisateur_utilisateur = u.idutilisateur AND co.idcentrale_centrale = ce.idcentrale AND
          co.idprojet_projet = p.idprojet AND  t.idtypeprojet = p.idtypeprojet_typeprojet AND s.idstatutprojet=? AND trashed =FALSE
          AND s.idstatutprojet = co.idstatutprojet_statutprojet and ce.libellecentrale =?", array(ENATTENTEPHASE2, $libellecentrale));
                $fpProjetAttente = fopen("../tmp/ProjetEnAttenteCentrale".IDCENTRALEUSER.".json", 'w');
                $dataProjetAttente = "";
                fwrite($fpProjetAttente, '{"items": [');
                $nbrowProjetAttente = count($rowProjetAttente);
                for ($i = 0; $i < $nbrowProjetAttente; $i++) {
                    $dataProjetAttente = "" . '{"dateProjet":' . '"' . $rowProjetAttente[$i]['dateprojet'] . '"' . ","
                            . '"numero":' . '"' . $rowProjetAttente[$i]['numero'] . '"' . ","
                            . '"libellecentrale":' . '"' . $rowProjetAttente[$i]['libellecentrale'] . '"' . ","
                            . '"idstatutprojet":' . '"' . $rowProjetAttente[$i]['idstatutprojet'] . '"' . ","
                            . '"titre":' . '"' . filtredonnee($rowProjetAttente[$i]['titre']) . '"' . ","
                            . '"libellestatutProjet":' . '"' . $rowProjetAttente[$i]['libellestatutprojet'] . '"' . "," . '"nom":' . '"' . filtredonnee($rowProjetAttente[$i]['nom']) .
                            '"' . "," . '"nomentreprise":' . '"' . filtredonnee($rowProjetAttente[$i]['nomentreprise']) . '"' . "," . '"entrepriselaboratoire":' . '"' . filtredonnee($rowProjetAttente[$i]['entrepriselaboratoire']) . '"'
                            . "," . '"acronyme":' . '"' . $rowProjetAttente[$i]['acronyme'] . '"' . "},";
                    fputs($fpProjetAttente, $dataProjetAttente);
                    fwrite($fpProjetAttente, '');
                }
                fwrite($fpProjetAttente, ']}');
                $json_fileAttenteCentrale = "../tmp/ProjetEnAttenteCentrale".IDCENTRALEUSER.".json";
                $jsonAttenteCentrale = file_get_contents($json_fileAttenteCentrale);
                $jsonAttenteCentrale1 = str_replace('},]}', '}]}', $jsonAttenteCentrale);
                file_put_contents($json_fileAttenteCentrale, $jsonAttenteCentrale1);
                $cache->write('attente_'.LIBELLECENTRALEUSER,$jsonAttenteCentrale1);
                fclose($fpProjetAttente);
                chmod("../tmp/ProjetEnAttenteCentrale".IDCENTRALEUSER.".json", 0777);                
                $_SESSION['nbprojetattente'] = $nbrowProjetAttente;
            }
        if (!$cache->read('refuse_'.LIBELLECENTRALEUSER)) {
    //-----------------------------------------------------------------------------------------------------------------------------------------------
    //                                                                  PROJET CENTRALE REFUSEE
    //-----------------------------------------------------------------------------------------------------------------------------------------------
            $row = $manager->getListbyArray("SELECT p.idprojet,s.idstatutprojet
    FROM projet p,utilisateur u,creer cr,centrale ce,concerne co,typeprojet t,statutprojet s
    WHERE p.idtypeprojet_typeprojet = t.idtypeprojet AND u.idutilisateur = cr.idutilisateur_utilisateur AND cr.idprojet_projet = p.idprojet AND
      ce.idcentrale = co.idcentrale_centrale AND co.idprojet_projet = p.idprojet AND co.idstatutprojet_statutprojet = s.idstatutprojet
    AND s.idstatutprojet=? and ce.libellecentrale =? AND trashed =FALSE", array(REFUSE, $libellecentrale));

            $arrayIdProjet = array();
            for ($i = 0; $i < count($row); $i++) {
                array_push($arrayIdProjet, $row[$i]['idprojet']);
            }
            $test = array();
            foreach ($arrayIdProjet as $idprojet) {
                array_push($test, $manager->getSinglebyArray("select idprojet_projet from concerne where idprojet_projet=? and idcentrale_centrale!=? and idstatutprojet_statutprojet!=4", array($idprojet, IDCENTRALEUSER)));
            }
            $array = array_values(array_filter($test));
            $arrayIdprojetTousCentrale = array_diff($arrayIdProjet, $array);

            $rowProjetRefusee = array();
            foreach ($arrayIdprojetTousCentrale as $idprojet) {
                array_push($rowProjetRefusee, $manager->getListbyArray("SELECT p.numero,co.commentaireprojet,p.acronyme,p.titre,p.idprojet,p.dateprojet,co.datestatutrefuser,ce.libellecentrale,s.libellestatutprojet,p.idprojet,u.nom,u.nomentreprise,u.entrepriselaboratoire,p.refinterneprojet
    FROM projet p,utilisateur u,creer cr,centrale ce,concerne co,typeprojet t,statutprojet s
    WHERE p.idtypeprojet_typeprojet = t.idtypeprojet AND u.idutilisateur = cr.idutilisateur_utilisateur AND cr.idprojet_projet = p.idprojet AND
      ce.idcentrale = co.idcentrale_centrale AND co.idprojet_projet = p.idprojet AND co.idstatutprojet_statutprojet = s.idstatutprojet
    and p.idprojet=? and co.idcentrale_centrale=?", array($idprojet, IDCENTRALEUSER)));
            }            
            $fpProjetRefusee = fopen("../tmp/ProjetRefuseecentrale".IDCENTRALEUSER.".json", 'w');
            $dataProjetRefusee = "";
            fwrite($fpProjetRefusee, '{"items": [');
            $nbrowProjetRefusee = count($rowProjetRefusee);
            for ($i = 0; $i < $nbrowProjetRefusee; $i++) {
                if (!empty($rowProjetRefusee[$i][0]['numero'])) {
                    $dataProjetRefusee = "" . '{"dateProjet":' . '"' . $rowProjetRefusee[$i][0]['dateprojet'] . '"' . ","
                            . '"datestatutrefuser":' . '"' . $rowProjetRefusee[$i][0]['datestatutrefuser'] . '"' . ","
                            . '"libellecentrale":' . '"' . $rowProjetRefusee[$i][0]['libellecentrale'] . '"' . ","
                            . '"numero":' . '"' . $rowProjetRefusee[$i][0]['numero'] . '"'
                            . "," . '"refinterneprojet":' . '"' . filtredonnee($rowProjetRefusee[$i][0]['refinterneprojet']) . '"'
                            . "," . '"titre":' . '"' . filtredonnee($rowProjetRefusee[$i][0]['titre']) . '"'
                            . "," . '"nom":' . '"' . filtredonnee($rowProjetRefusee[$i][0]['nom']) . '"'
                            . "," . '"commentaire":' . '"' . strip_tags(filtredonnee($rowProjetRefusee[$i][0]['commentaireprojet'])) . '"'
                            . "," . '"nomentreprise":' . '"' . filtredonnee($rowProjetRefusee[$i][0]['nomentreprise']) . '"'
                            . "," . '"entrepriselaboratoire":' . '"' . filtredonnee($rowProjetRefusee[$i][0]['entrepriselaboratoire']) . '"'
                            . "," . '"acronyme":' . '"' . $rowProjetRefusee[$i][0]['acronyme'] . '"' . "},";
                }
                fputs($fpProjetRefusee, $dataProjetRefusee);
                fwrite($fpProjetRefusee, '');
            }
            fwrite($fpProjetRefusee, ']}');
            $json_fileRefuseeCentrale = "../tmp/ProjetRefuseecentrale".IDCENTRALEUSER.".json";
            $jsonRefuseeCentrale = file_get_contents($json_fileRefuseeCentrale);
            $jsonRefuseeCentrale1 = str_replace('},]}', '}]}', $jsonRefuseeCentrale);
            file_put_contents($json_fileRefuseeCentrale, $jsonRefuseeCentrale1);
            $cache->write('refuse_'.LIBELLECENTRALEUSER,$jsonRefuseeCentrale1);
            fclose($fpProjetRefusee);
            chmod("../tmp/ProjetRefuseecentrale".IDCENTRALEUSER.".json", 0777);
            $_SESSION['nbProjetRefusee'] = $nbrowProjetRefusee;
        }

        if (!$cache->read('refuseTransfert_'.LIBELLECENTRALEUSER)) {
    //-----------------------------------------------------------------------------------------------------------------------------------------------
    //                                                                  PROJET REFUSE ET TRANSFERE DANS UNE AUTRE CENTRALE
    //-----------------------------------------------------------------------------------------------------------------------------------------------
            $row0 = $manager->getListbyArray("SELECT p.idprojet FROM projet p,utilisateur u,creer cr,centrale ce,concerne co,typeprojet t,statutprojet s
                WHERE p.idtypeprojet_typeprojet = t.idtypeprojet AND u.idutilisateur = cr.idutilisateur_utilisateur AND cr.idprojet_projet = p.idprojet AND
                ce.idcentrale = co.idcentrale_centrale AND co.idprojet_projet = p.idprojet AND co.idstatutprojet_statutprojet = s.idstatutprojet AND s.idstatutprojet=? and ce.libellecentrale =?",array(REFUSE, $libellecentrale));
            
            $arrayidprojet=array();
            $sarrayidprojet="(";
            foreach ($row0 as $key => $value) {
                array_push($arrayidprojet, $value[0]);
                $sarrayidprojet.=$value[0].",";
            }
            $s_arrayidprojet = substr($sarrayidprojet,0, -1);
            $s_arrayidprojet.=")";
            
            $row =$manager->getListbyArray("SELECT p.numero,co.commentaireprojet,p.titre,p.idprojet,ce.libellecentrale,s.libellestatutprojet,p.idprojet,u.nom,
            u.nomentreprise,u.entrepriselaboratoire,p.refinterneprojet FROM projet p,utilisateur u,creer cr,centrale ce,concerne co,typeprojet t,statutprojet s
            WHERE p.idtypeprojet_typeprojet = t.idtypeprojet AND u.idutilisateur = cr.idutilisateur_utilisateur AND cr.idprojet_projet = p.idprojet AND
            ce.idcentrale = co.idcentrale_centrale AND co.idprojet_projet = p.idprojet AND co.idstatutprojet_statutprojet = s.idstatutprojet AND s.idstatutprojet<>?
            AND idprojet in ".$s_arrayidprojet."", array(REFUSE));
                       
            $fpProjetRefuseTransfert = fopen("../tmp/ProjetRefuseTransfert".IDCENTRALEUSER.".json", 'w');
            $dataProjetRefuseTransfert = "";
            fwrite($fpProjetRefuseTransfert, '{"items": [');
            $nbrowProjetRefuseTransfert = count($row);
            for ($i = 0; $i < $nbrowProjetRefuseTransfert; $i++) {
                $datestaturefuser = $manager->getSinglebyArray("SELECT datestatutrefuser from concerne WHERE idprojet_projet=? AND idcentrale_centrale=? ", array($row[$i]['idprojet'],IDCENTRALEUSER));
                $centraleAcceptation = $manager->getSinglebyArray("SELECT libellecentrale FROM concerne LEFT JOIN centrale on idcentrale= idcentrale_centrale where idprojet_projet=? "
                        . "and idstatutprojet_statutprojet<>?",array($row[$i]['idprojet'],REFUSE));
                $s_Centrales = '';
                $centraleRefus = $manager->getListbyArray("SELECT commentaireprojet FROM concerne where idprojet_projet=? and idstatutprojet_statutprojet=?", array($row[$i]['idprojet'],REFUSE));
                foreach ($centraleRefus as $key => $value) {
                    $s_Centrales .= strip_tags($value[0]).",";
                }
                $sCentrale = substr($s_Centrales, 0,-1);
                
                if (!empty($row[$i]['numero'])) {
                    $dataProjetRefuseTransfert = "" . '{"datestatutrefuser":' . '"' . $datestaturefuser . '"' . ","                            
                            . '"libellecentrale":' . '"' . $row[$i]['libellecentrale'] . '"' . ","
                            . '"numero":' . '"' . $row[$i]['numero'] . '"'                            
                            . "," . '"titre":' . '"' . filtredonnee($row[$i]['titre']) . '"'
                            . "," . '"nom":' . '"' . filtredonnee($row[$i]['nom']) . '"'
                            . "," . '"centraleAcceptation":' . '"' .$centraleAcceptation . '"'
                            . "," . '"centraleRefus":' . '"' .$sCentrale . '"'
                            . "," . '"commentaire":' . '"' . strip_tags(filtredonnee($row[$i]['commentaireprojet'])) . '"'
                            . "},";
                }
                fputs($fpProjetRefuseTransfert, $dataProjetRefuseTransfert);
                fwrite($fpProjetRefuseTransfert, '');
            }
            fwrite($fpProjetRefuseTransfert, ']}');
            $json_fileRefuseTransfert = "../tmp/ProjetRefuseTransfert".IDCENTRALEUSER.".json";
            $jsonRefuseTransfertCentrale = file_get_contents($json_fileRefuseTransfert);
            $jsonRefuseTransfertCentrale1 = str_replace('},]}', '}]}', $jsonRefuseTransfertCentrale);
            file_put_contents($json_fileRefuseTransfert, $jsonRefuseTransfertCentrale1);
            $cache->write('refuse_'.LIBELLECENTRALEUSER,$jsonRefuseTransfertCentrale1);
            fclose($fpProjetRefuseTransfert);
            chmod("../tmp/ProjetRefuseTransfert".IDCENTRALEUSER.".json", 0777);
            $_SESSION['nbProjetRefuseTransfert'] = $nbrowProjetRefuseTransfert;
        }

        if (!$cache->read('finis_'.LIBELLECENTRALEUSER)) {
    ////---------------------------------------------------------------------------------------------------------------------------------------------------
    //                              CREATION DU PROJET JSON FINI
    //---------------------------------------------------------------------------------------------------------------------------------------------------
            $manager->exeRequete("drop table if exists tmpfini;");
    //CREATION DE LA TABLE TEMPORAIRE
            $manager->getRequete("create table tmpfini as
    (SELECT p.idprojet,p.acronyme,s.libellestatutprojet,s.idstatutprojet,p.numero,p.titre,p.datedebutprojet,p.datestatutfini,u.nom,u.nomentreprise,u.entrepriselaboratoire,null as nomAffecte,null as prenomAffecte,ce.libellecentrale,p.refinterneprojet
      FROM  projet p,utilisateur u,creer cr,centrale ce,concerne co,typeprojet t,statutprojet s
      WHERE cr.idprojet_projet = p.idprojet AND cr.idutilisateur_utilisateur = u.idutilisateur AND co.idcentrale_centrale = ce.idcentrale AND
      co.idprojet_projet = p.idprojet AND  t.idtypeprojet = p.idtypeprojet_typeprojet
      AND s.idstatutprojet = co.idstatutprojet_statutprojet and ce.libellecentrale = ? and s.idstatutprojet=? AND trashed =FALSE
    union
    SELECT idprojet,acronyme,s.libellestatutprojet,s.idstatutprojet,numero,titre,p.datedebutprojet,p.datestatutfini,u.nom,u.nomentreprise,u.entrepriselaboratoire,u1.nom,u1.prenom,ce.libellecentrale,p.refinterneprojet
    FROM projet p,utilisateur u,creer,centrale ce,concerne,typeprojet,statutprojet s,utilisateurporteurprojet,utilisateur u1
    WHERE idtypeprojet_typeprojet = typeprojet.idtypeprojet AND u.idutilisateur = creer.idutilisateur_utilisateur AND
      creer.idprojet_projet = idprojet AND concerne.idcentrale_centrale = idcentrale AND concerne.idprojet_projet = idprojet AND
      concerne.idstatutprojet_statutprojet = s.idstatutprojet AND utilisateurporteurprojet.idprojet_projet = idprojet AND
      utilisateurporteurprojet.idutilisateur_utilisateur = u1.idutilisateur AND ce.libellecentrale =? and s.idstatutprojet=? AND trashed =FALSE
    order by idprojet asc);", array($libellecentrale, FINI, $libellecentrale, FINI));
            $rowFini = $manager->getList("
        select  idprojet,acronyme,libellestatutprojet,idstatutprojet,numero,titre,datedebutprojet,datestatutfini,nom,nomentreprise,entrepriselaboratoire,nomaffecte,prenomaffecte,libellecentrale,refinterneprojet from tmpfini
        where idprojet not in (select idprojet from tmpfini where nomaffecte is not null)
        union
        select idprojet,acronyme,libellestatutprojet,idstatutprojet,numero,titre,datedebutprojet,datestatutfini,nom,nomentreprise,entrepriselaboratoire,nomaffecte,prenomaffecte,libellecentrale,refinterneprojet from tmpfini where nomaffecte is not null
        order by idprojet asc");
            $fpFini = fopen("../tmp/projetFiniCentrale".IDCENTRALEUSER.".json", 'w');
            $dataFini = "";
            fwrite($fpFini, '{"items": [');
            $nbrowFini = count($rowFini);
            for ($i = 0; $i < $nbrowFini; $i++) {
                $dataFini = "" . '{"datedebutprojet":' . '"' . $rowFini[$i]['datedebutprojet'] . '"' . ","
                        . '"datestatutfini":' . '"' . $rowFini[$i]['datestatutfini'] . '"' . ","
                        . '"libellecentrale":' . '"' . $rowFini[$i]['libellecentrale'] . '"' . ","
                        . '"idprojet":' . '"' . $rowFini[$i]['idprojet'] . '"' . ","
                        . '"idstatutprojet":' . '"' . $rowFini[$i]['idstatutprojet'] . '"' . ","
                        . '"refinterneprojet":' . '"' . filtredonnee($rowFini[$i]['refinterneprojet']) . '"' . ","
                        . '"numero":' . '"' . $rowFini[$i]['numero'] . '"' . "," . '"titre":' . '"' .
                        filtredonnee($rowFini[$i]['titre']) . '"' . "," . '"libellestatutprojet":' . '"' . $rowFini[$i]['libellestatutprojet'] . '"' . ","
                        . '"acronyme":' . '"' . $rowFini[$i]['acronyme'] . '"' . "},";
                fputs($fpFini, $dataFini);
                fwrite($fpFini, '');
            }
            fwrite($fpFini, ']}');
            $json_fileFini = "../tmp/projetFiniCentrale".IDCENTRALEUSER.".json";
            $jsonfini = file_get_contents($json_fileFini);
            $jsonFini = str_replace('},]}', '}]}', $jsonfini);
            file_put_contents($json_fileFini, $jsonFini);
            $cache->write('finis_'.LIBELLECENTRALEUSER,$jsonFini);
            fclose($fpFini);
            chmod("../tmp/projetFiniCentrale".IDCENTRALEUSER.".json", 0777);
            $_SESSION['nbFini'] = $nbrowFini;
        }
        if (!$cache->read('cloture_'.LIBELLECENTRALEUSER)) {
    //---------------------------------------------------------------------------------------------------------------------------------------------------
    //										CREATION DU PROJET JSON CLOTURE
    //---------------------------------------------------------------------------------------------------------------------------------------------------
            $manager->exeRequete("drop table if exists tmpcloturer;");
    //CREATION DE LA TABLE TEMPORAIRE
            $manager->getRequete("create table tmpcloturer as
    (SELECT p.idprojet,p.acronyme,s.libellestatutprojet,p.numero,p.titre,p.datedebutprojet,p.datestatutcloturer,u.nom,u.nomentreprise,u.entrepriselaboratoire,null as nomAffecte,null as prenomAffecte,ce.libellecentrale,p.refinterneprojet
      FROM  projet p,utilisateur u,creer cr,centrale ce,concerne co,typeprojet t,statutprojet s
      WHERE cr.idprojet_projet = p.idprojet AND cr.idutilisateur_utilisateur = u.idutilisateur AND co.idcentrale_centrale = ce.idcentrale AND
      co.idprojet_projet = p.idprojet AND  t.idtypeprojet = p.idtypeprojet_typeprojet
      AND s.idstatutprojet = co.idstatutprojet_statutprojet and ce.libellecentrale = ? and s.idstatutprojet=? AND trashed =FALSE
    union
    SELECT idprojet,acronyme,s.libellestatutprojet,numero,titre,p.datedebutprojet,p.datestatutcloturer,u.nom,u.nomentreprise,u.entrepriselaboratoire,u1.nom,u1.prenom,ce.libellecentrale,p.refinterneprojet
    FROM projet p,utilisateur u,creer,centrale ce,concerne,typeprojet,statutprojet s,utilisateurporteurprojet,utilisateur u1
    WHERE idtypeprojet_typeprojet = typeprojet.idtypeprojet AND u.idutilisateur = creer.idutilisateur_utilisateur AND
      creer.idprojet_projet = idprojet AND concerne.idcentrale_centrale = idcentrale AND concerne.idprojet_projet = idprojet AND
      concerne.idstatutprojet_statutprojet = s.idstatutprojet AND utilisateurporteurprojet.idprojet_projet = idprojet AND
      utilisateurporteurprojet.idutilisateur_utilisateur = u1.idutilisateur AND ce.libellecentrale =? and s.idstatutprojet=? AND trashed =FALSE
    order by idprojet asc);", array($libellecentrale, CLOTURE, $libellecentrale, CLOTURE));
            $rowCloturer = $manager->getList("
        select  idprojet,acronyme,libellestatutprojet,numero,titre,datedebutprojet,datestatutcloturer,nom,nomentreprise,entrepriselaboratoire,nomaffecte,prenomaffecte,libellecentrale,refinterneprojet from tmpcloturer
        where idprojet not in (select idprojet from tmpcloturer where nomaffecte is not null)
        union
        select idprojet,acronyme,libellestatutprojet,numero,titre,datedebutprojet,datestatutcloturer,nom,nomentreprise,entrepriselaboratoire,nomaffecte,prenomaffecte,libellecentrale,refinterneprojet from tmpcloturer where nomaffecte is not null
        order by idprojet asc");            
            $fpCloturer = fopen("../tmp/projetCloturerCentrale".IDCENTRALEUSER.".json", 'w');
            $dataCloturer = "";
            fwrite($fpCloturer, '{"items": [');
            $nbrowCloturer = count($rowCloturer);
            for ($i = 0; $i < $nbrowCloturer; $i++) {
                $dataCloturer = "" . '{"datedebutprojet":' . '"' . $rowCloturer[$i]['datedebutprojet'] . '"' . ","
                        . '"datestatutcloturer":' . '"' . $rowCloturer[$i]['datestatutcloturer'] . '"' . ","
                        . '"libellecentrale":' . '"' . $rowCloturer[$i]['libellecentrale'] . '"' . ","
                        . '"numero":' . '"' . $rowCloturer[$i]['numero'] . '"' . ","
                        . '"refinterneprojet":' . '"' . filtredonnee($rowCloturer[$i]['refinterneprojet']) . '"' . ","
                        . '"titre":' . '"' . filtredonnee($rowCloturer[$i]['titre']) . '"' . ","
                        . '"libellestatutprojet":' . '"' . $rowCloturer[$i]['libellestatutprojet'] . '"' . ","
                        . '"acronyme":' . '"' . $rowCloturer[$i]['acronyme'] . '"' . "},";
                fputs($fpCloturer, $dataCloturer);
                fwrite($fpCloturer, '');
            }
            fwrite($fpCloturer, ']}');            
            $json_fileCloturer = "../tmp/projetCloturerCentrale".IDCENTRALEUSER.".json";
            $jsoncloturer = file_get_contents($json_fileCloturer);
            $jsonCloturer = str_replace('},]}', '}]}', $jsoncloturer);
            file_put_contents($json_fileCloturer, $jsonCloturer);
            $cache->write('finis_'.LIBELLECENTRALEUSER,$jsonCloturer);
            fclose($fpCloturer);
            chmod("../tmp/projetCloturerCentrale".IDCENTRALEUSER.".json", 0777);
            $_SESSION['nbprojetCloturer'] = $nbrowCloturer;
        }
//-----------------------------------------------------------------------------------------------------------------------------------------------
//                                                                  PROJET EN SOUS-TRAITANCE
//-----------------------------------------------------------------------------------------------------------------------------------------------
        if (!$cache->read('soustraitance_'.LIBELLECENTRALEUSER)) {
            $rowProjetsoustraitance = $manager->getListbyArray("SELECT p.idprojet,s.idstatutprojet,p.titre,p.acronyme,p.refinterneprojet,s.libellestatutprojet,s.libellestatutprojeten,u.nom,u.prenom,p.numero,p.dateprojet FROM projet p,projetautrecentrale pa,statutprojet s,concerne co,creer cr,utilisateur u
    WHERE p.idprojet = pa.idprojet AND co.idprojet_projet = p.idprojet AND co.idstatutprojet_statutprojet = s.idstatutprojet AND cr.idprojet_projet = p.idprojet AND  cr.idutilisateur_utilisateur = u.idutilisateur
    and  pa.idcentrale=? AND trashed =FALSE and s.idstatutprojet <> ? and s.idstatutprojet <> ?",array($idcentrale,FINI, CLOTURE));
            $fpProjetSoustraitance = fopen("../tmp/Projetsoustraitance".IDCENTRALEUSER.".json", 'w');
            $dataProjetSoustraitance = "";
            fwrite($fpProjetSoustraitance, '{"items": [');
            $nbrowProjetSoustraitance = count($rowProjetsoustraitance);
            for ($i = 0; $i < $nbrowProjetSoustraitance; $i++) {
                $centrale = $manager->getSingle2("SELECT  c.libellecentrale FROM centrale c,concerne co WHERE  co.idcentrale_centrale = c.idcentrale and co.idprojet_projet=?", $rowProjetsoustraitance[$i]['idprojet']);
                $dataProjetSoustraitance = ""
                        . '{"dateProjet":' . '"' . $rowProjetsoustraitance[$i]['dateprojet'] . '"' . ","
                        . '"numero":' . '"' . $rowProjetsoustraitance[$i]['numero'] . '"' . ","
                        . '"titre":' . '"' . filtredonnee($rowProjetsoustraitance[$i]['titre']) . '"' . ","
                        . '"idprojet":' . '"' . $rowProjetsoustraitance[$i]['idprojet'] . '"' . ","
                        . '"libellestatutProjet":' . '"' . str_replace("''", "'", $rowProjetsoustraitance[$i]['libellestatutprojet']) . '"' . ","
                        . '"idstatutprojet":' . '"' . $rowProjetsoustraitance[$i]['idstatutprojet'] . '"' . ","
                        . '"refinterneprojet":' . '"' . filtredonnee($rowProjetsoustraitance[$i]['refinterneprojet']) . '"' . ","
                        . '"nom":' . '"' . filtredonnee($rowProjetsoustraitance[$i]['nom']) . '"' . ","
                        . '"centrale":' . '"' . $centrale . '"' . ","
                        . '"prenom":' . '"' . filtredonnee($rowProjetsoustraitance[$i]['prenom']) . '"' . ","
                        . '"acronyme":' . '"' . filtredonnee($rowProjetsoustraitance[$i]['acronyme']) . '"' . "},";
                fputs($fpProjetSoustraitance, $dataProjetSoustraitance);
                fwrite($fpProjetSoustraitance, '');
            }
            fwrite($fpProjetSoustraitance, ']}');
            $json_fileSoustraitance = "../tmp/Projetsoustraitance".IDCENTRALEUSER.".json";
            $jsonSoustraitance = file_get_contents($json_fileSoustraitance);
            $jsonSoustraitance1 = str_replace('},]}', '}]}', $jsonSoustraitance);
            file_put_contents($json_fileSoustraitance, $jsonSoustraitance1);
            $cache->write('soustraitance_'.LIBELLECENTRALEUSER,$jsonSoustraitance1);
            fclose($fpProjetSoustraitance);
            chmod("../tmp/Projetsoustraitance".IDCENTRALEUSER.".json", 0777);


            $_SESSION['nbProjetSoustraitance'] = $nbrowProjetSoustraitance;
            if (!$cache->read('rapportEnCours_'.LIBELLECENTRALEUSER)) {
        //-----------------------------------------------------------------------------------------------------------------------------------------------
        //                                                                  RAPPORTS PROJETS ANNEE EN COURS
        //-----------------------------------------------------------------------------------------------------------------------------------------------
                $anneeEnCours= date('Y');
                $rowProjetRapport = $manager->getListbyArray("select c.idstatutprojet_statutprojet,r.idprojet,cr.idutilisateur_utilisateur,p.numero,r.title, r.datecreation,r.datemiseajour,p.refinterneprojet "
                        . "from projet p,rapport r, concerne c,creer cr where p.idprojet=r.idprojet and c.idprojet_projet=r.idprojet and cr.idprojet_projet=r.idprojet and c.idcentrale_centrale=? "
                        . "and c.idstatutprojet_statutprojet!=? and (EXTRACT(YEAR from datecreation)>=? or EXTRACT(YEAR from datemiseajour)>=?)", 
                        array($idcentrale, REFUSE,$anneeEnCours,$anneeEnCours));                
                $fpProjetRapport = fopen("../tmp/ProjetRapportEnCours".IDCENTRALEUSER.".json", 'w');
                $dataProjetRapport = "";
                fwrite($fpProjetRapport, '{"items": [');

                $nbrowProjetRapportEnCours = count($rowProjetRapport);
                for ($i = 0; $i < $nbrowProjetRapportEnCours; $i++) {
                    $idutilisateur = $rowProjetRapport[$i]['idutilisateur_utilisateur'];
                    $arraycreateur = $manager->getList2("select nom,prenom from utilisateur where idutilisateur=?", $idutilisateur);
                    $dataProjetRapport = ""
                            . '{"numero":' . '"' . $rowProjetRapport[$i]['numero'] . '"' . ","
                            . '"datecreation":' . '"' . $rowProjetRapport[$i]['datecreation'] . '"' . ","
                            . '"identite":' . '"' . $arraycreateur[0]['nom'] . ' - ' . $arraycreateur[0]['prenom'] . '"' . ","
                            . '"datemiseajour":' . '"' . $rowProjetRapport[$i]['datemiseajour'] . '"' . ","
                            . '"title":' . '"' . filtredonnee($rowProjetRapport[$i]['title']) . '"' . ","
                            . '"idprojet":' . '"' . $rowProjetRapport[$i]['idprojet'] . '"' . ","
                            . '"idstatutprojet":' . '"' . $rowProjetRapport[$i]['idstatutprojet_statutprojet'] . '"' . ","
                            . '"refinterneprojet":' . '"' . filtredonnee($rowProjetRapport[$i]['refinterneprojet']) . '"' . ","
                            . '"imprime":' . '"' . TXT_PDF . '"' . "},";
                    fputs($fpProjetRapport, $dataProjetRapport);
                    fwrite($fpProjetRapport, '');
                }

                fwrite($fpProjetRapport, ']}');
                $json_fileRapport = "../tmp/ProjetRapportEnCours".IDCENTRALEUSER.".json";

                $jsonRapport = file_get_contents($json_fileRapport);


                $jsonRapport1 = str_replace('},]}', '}]}', $jsonRapport);
                file_put_contents($json_fileRapport, $jsonRapport1);
                $cache->write('rapportEnCours_'.LIBELLECENTRALEUSER,$jsonRapport1);
                fclose($fpProjetRapport);
                chmod("../tmp/ProjetRapportEnCours".IDCENTRALEUSER.".json", 0777);
                $_SESSION['nbProjetRapportEncours'] = $nbrowProjetRapportEnCours;
            }
           if (!$cache->read('rapportAutres_'.LIBELLECENTRALEUSER)) {
        //-----------------------------------------------------------------------------------------------------------------------------------------------
        //                                                                  RAPPORTS PROJETS AUTRES
        //-----------------------------------------------------------------------------------------------------------------------------------------------
                $anneeEnCours= date('Y');
                $rowProjetRapportAutres = $manager->getListbyArray("select c.idstatutprojet_statutprojet,r.idprojet,cr.idutilisateur_utilisateur,p.numero,r.title, r.datecreation,
                    r.datemiseajour,p.refinterneprojet from projet p,rapport r, concerne c,creer cr where p.idprojet=r.idprojet and c.idprojet_projet=r.idprojet 
                    and cr.idprojet_projet=r.idprojet and c.idcentrale_centrale=? and c.idstatutprojet_statutprojet!=? and (EXTRACT(YEAR from datecreation)!=? and 
                    EXTRACT(YEAR from datemiseajour)!=?) 
                    union 
                    select c.idstatutprojet_statutprojet,r.idprojet,cr.idutilisateur_utilisateur,p.numero,r.title, r.datecreation,r.datemiseajour,p.refinterneprojet 
                    from projet p,rapport r, concerne c,creer cr where p.idprojet=r.idprojet and c.idprojet_projet=r.idprojet and cr.idprojet_projet=r.idprojet 
                    and c.idcentrale_centrale=? and c.idstatutprojet_statutprojet!=? and datemiseajour isnull and EXTRACT(YEAR from datecreation)!=?", 
                        array($idcentrale, REFUSE,$anneeEnCours,$anneeEnCours,$idcentrale,REFUSE,$anneeEnCours));
                $fpProjetRapport = fopen("../tmp/ProjetRapportAutres".IDCENTRALEUSER.".json", 'w');
                $dataProjetRapportAutres = "";
                fwrite($fpProjetRapport, '{"items": [');

                $nbrowProjetRapportAutres = count($rowProjetRapportAutres);
                for ($i = 0; $i < $nbrowProjetRapportAutres; $i++) {
                    $idutilisateur = $rowProjetRapportAutres[$i]['idutilisateur_utilisateur'];
                    $arraycreateur = $manager->getList2("select nom,prenom from utilisateur where idutilisateur=?", $idutilisateur);
                    $dataProjetRapportAutres = ""
                            . '{"numero":' . '"' . $rowProjetRapportAutres[$i]['numero'] . '"' . ","
                            . '"datecreation":' . '"' . $rowProjetRapportAutres[$i]['datecreation'] . '"' . ","
                            . '"identite":' . '"' . $arraycreateur[0]['nom'] . ' - ' . $arraycreateur[0]['prenom'] . '"' . ","
                            . '"datemiseajour":' . '"' . $rowProjetRapportAutres[$i]['datemiseajour'] . '"' . ","
                            . '"title":' . '"' . filtredonnee($rowProjetRapportAutres[$i]['title']) . '"' . ","
                            . '"idprojet":' . '"' . $rowProjetRapportAutres[$i]['idprojet'] . '"' . ","
                            . '"idstatutprojet":' . '"' . $rowProjetRapportAutres[$i]['idstatutprojet_statutprojet'] . '"' . ","
                            . '"refinterneprojet":' . '"' . filtredonnee($rowProjetRapportAutres[$i]['refinterneprojet']) . '"' . ","
                            . '"imprime":' . '"' . TXT_PDF . '"' . "},";
                    fputs($fpProjetRapport, $dataProjetRapportAutres);
                    fwrite($fpProjetRapport, '');
                }

                fwrite($fpProjetRapport, ']}');
                $json_fileRapport = "../tmp/ProjetRapportAutres".IDCENTRALEUSER.".json";

                $jsonRapport = file_get_contents($json_fileRapport);


                $jsonRapport1 = str_replace('},]}', '}]}', $jsonRapport);
                file_put_contents($json_fileRapport, $jsonRapport1);
                $cache->write('rapportAutres_'.LIBELLECENTRALEUSER,$jsonRapport1);
                fclose($fpProjetRapport);
                chmod("../tmp/ProjetRapportAutres".IDCENTRALEUSER.".json", 0777);
                $_SESSION['nbProjetRapportAutres'] = $nbrowProjetRapportAutres;
            }            
            
        }

//-----------------------------------------------------------------------------------------------------------------------------------------------
    $_SESSION['email'] = $mail;
    $_SESSION['pseudo'] = $pseudo;
    if(isset($_GET['changeApplicant'])){
        header('location:/' . REPERTOIRE . '/projetCentrale/' . $lang . '/' . $libellecentrale.'/'.$_GET['idprojet'].'/'.$_GET['idutilisateur'].'/ok' ); 
    }elseif (isset($_GET['porteur'])) {
        header('Location:/'.REPERTOIRE.'/projet_centrale_affect/' . $lang . '/' . $_GET['idprojet'].'/'.$_GET['idutilisateur'].'/ok' ); 
    }elseif(isset($_GET['administrateur'])){
        header('Location:/'.REPERTOIRE.'/projetCentraleAdmin/' . $lang . '/' . $_GET['idprojet'].'/'.$_GET['idutilisateur'].'/ok' ); 
    }else{
        header('location:/' . REPERTOIRE . '/projet_centrale/' . $lang . '/' . $libellecentrale);
    }
    BD::deconnecter();
} else {
    header('Location: /' . REPERTOIRE . '/Login_Error/' . $lang);
}