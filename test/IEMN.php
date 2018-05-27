<?php

include_once '../class/Manager.php';
$db = BD::connecter(); //CONNEXION A LA BASE DE DONNEE
$manager = new Manager($db); //CREATION D'UNE INSTANCE DU MANAGER




$test =array(
array("mail"=>'Didier.Theron@iemn.univ-lille1.fr'),
array("mail"=>'Jean-Francois.Lampin@isen.fr'),
array("mail"=>'Philippe.Menini@laas.fr'),
array("mail"=>'Rita.Meunier-Prest@u-bourgogne.fr'),
array("mail"=>'Temple@laas.fr'),
array("mail"=>'V-PAVEAU@innopsys.fr'),
array("mail"=>'Vincent.Senez@isen.iemn.univ-lille1.fr'),
array("mail"=>'Vivek.Pachauri@hs-kl.de'),
array("mail"=>'a.vicet@univ-montp2.fr'),
array("mail"=>'abancaud@laas.fr'),
array("mail"=>'abdallah.ougazzaden@georgiatech-metz.fr'),
array("mail"=>'abdelhakim.bourennane@laas.fr'),
array("mail"=>'abdelkader.souifi@insa-lyon.fr'),
array("mail"=>'abdelkrim.khelif@femto-st.fr'),
array("mail"=>'abourenn@laas.fr'),
array("mail"=>'acerf@laas.fr'),
array("mail"=>'ahmad.bsiesy@cea.fr'),
array("mail"=>'alain.bosseboeuf@u-psud.fr'),
array("mail"=>'alain.lecorre@insa-rennes.fr'),
array("mail"=>'aline.cerf@laas.fr'),
array("mail"=>'almuneau@laas.fr'),
array("mail"=>'amy.n-hari@cea.fr'),
array("mail"=>'anne-marie.gue@laas.fr'),
array("mail"=>'annie.pradel@univ-montp2.fr'),
array("mail"=>'annie.viallat@inserm.fr'),
array("mail"=>'antoineadb@gmail.com'),
array("mail"=>'arguel@laas.fr'),
array("mail"=>'aurelie.tauzin@cea.fr'),
array("mail"=>'badhise.ben-bakir@cea.fr'),
array("mail"=>'bancaud@laas.fr'),
array("mail"=>'bardinal@laas.fr'),
array("mail"=>'bassetp@esiee.fr'),
array("mail"=>'bergaud@laas.fr'),
array("mail"=>'bernard.ducommun@itav-recherche.fr'),
array("mail"=>'bernard.legrand@laas.fr'),
array("mail"=>'blaise.yvert@inserm.fr'),
array("mail"=>'breil@laas.fr'),
array("mail"=>'bruno.daudin@cea.fr'),
array("mail"=>'calmon@laas.fr'),
array("mail"=>'camon@laas.fr'),
array("mail"=>'cardinal@icmcb-bordeaux.cnrs.fr'),
array("mail"=>'celine.chevalier@cea.fr'),
array("mail"=>'cerf@laas.fr'),
array("mail"=>'cfuccio@laas.fr'),
array("mail"=>'chantal.fontaine@laas.fr'),
array("mail"=>'charvin@igbmc.fr'),
array("mail"=>'chierold@ethz.ch'),
array("mail"=>'christophe.gorecki@femto-st.fr'),
array("mail"=>'christophe.verinaud@obs.ujf-grenoble.fr'),
array("mail"=>'christophe.vieu@laas.fr'),
array("mail"=>'constantin.vahlas@ensiacet.fr'),
array("mail"=>'cvieu@laas.fr'),
array("mail"=>'damien.ducatteau@iemn.univ-lille1.fr'),
array("mail"=>'daniela@laas.fr'),
array("mail"=>'daran@laas.fr'),
array("mail"=>'dflwhchs@naver.com'),
array("mail"=>'didier.decoster@iemn.univ-lille1.fr'),
array("mail"=>'didier.rouxel@univ-lorraine.fr'),
array("mail"=>'didier.theron@IEMN.univ-lille1.fr'),
array("mail"=>'didier.theron@iemn.univ-lille1.fr'),
array("mail"=>'dinu@lal.in2p3.fr'),
array("mail"=>'dominique.deresmes@isen.iemn.univ-lille1.fr'),
array("mail"=>'dpech@laas.fr'),
array("mail"=>'drambaud@irap.omp.eu'),
array("mail"=>'dries.vanthourhout@intec.UGent.be'),
array("mail"=>'edescamp@laas.fr'),
array("mail"=>'elena@laas.fr'),
array("mail"=>'elie.lefeuvre@u-psud.fr'),
array("mail"=>'emeline.descamps@laas.fr'),
array("mail"=>'emmanuel.dubois@isen.iemn.univ-lille1.fr'),
array("mail"=>'emmanuelle.pauliac-vaujour@cea.fr'),
array("mail"=>'eric.charkaluk@ec-lille.fr'),
array("mail"=>'erik.dujardin@cemes.fr'),
array("mail"=>'esteve@laas.fr'),
array("mail"=>'fabien.malbet@ujf-grenoble.fr'),
array("mail"=>'faouzy.soilihi@nanomade-concept.com'),
array("mail"=>'farid.medjdoub@iemn.univ-lille1.fr'),
array("mail"=>'fontaine@laas.fr'),
array("mail"=>'fourniol@laas.fr'),
array("mail"=>'fourniols@laas.fr'),
array("mail"=>'francois.mear@univ-lille1.fr'),
array("mail"=>'francois.neuilly@iemn.univ-lille1.fr'),
array("mail"=>'gabor.molnar@lcc-toulouse.fr'),
array("mail"=>'gilles.dambrine@iemn.univ-lille1.fr'),
array("mail"=>'giuseppe.leo@univ-paris-diderot.fr'),
array("mail"=>'glarrieu@laas.fr'),
array("mail"=>'granier@laas.fr'),
array("mail"=>'gue@laas.fr'),
array("mail"=>'guilhem.almuneau@laas.fr'),
array("mail"=>'guilhem.larrieu@laas.fr'),
array("mail"=>'guillaume.herlem@univ-fcomte.fr'),
array("mail"=>'guy.monteil@femto-st.fr'),
array("mail"=>'guy.schlatter@unistra.fr'),
array("mail"=>'han-cheng.seat@enseeiht.fr'),
array("mail"=>'helene.bouchiat@u-psud.fr'),
array("mail"=>'helene.tap@enseeiht.fr'),
array("mail"=>'henri.happy@iemn.univ-lille1.fr'),
array("mail"=>'herve.maillotte@univ-fcomte.fr'),
array("mail"=>'hubert.renevier@grenoble-inp.fr'),
array("mail"=>'hugues.granier@laas.fr'),
array("mail"=>'hugues.leroux@univ-lille1.fr'),
array("mail"=>'iseguy@laas.fr'),
array("mail"=>'j.lang@cbmn.u-bordeaux.fr'),
array("mail"=>'jean-baptiste.doucet@laas.fr'),
array("mail"=>'jean-charles.souriau@cea.fr'),
array("mail"=>'jean-francois.lampin@isen.iemn.univ-lille1.fr'),
array("mail"=>'jean-francois.robillard@isen.iemn.univ-lille1.fr'),
array("mail"=>'jean-marie.george@thalesgroup.com'),
array("mail"=>'jean-pierre.vilcot@iemn.univ-lille1.fr'),
array("mail"=>'jean-yves.hihn@univ-fcomte.fr'),
array("mail"=>'jlaunay@laas.fr'),
array("mail"=>'joel.cibert@neel.cnrs.fr'),
array("mail"=>'julien.barjon@uvsq.fr'),
array("mail"=>'julien.sebilleau@imft.fr'),
array("mail"=>'julygaleano@itm.edu.co'),
array("mail"=>'karim.haouchine@ens2m.fr'),
array("mail"=>'karine.blary@iemn.univ-lille1.fr'),
array("mail"=>'karine.isoird@laas.fr'),
array("mail"=>'katia.grenier@laas.fr'),
array("mail"=>'kenneth.a.dawson@cbni.ucd.ie'),
array("mail"=>'kgrenier@laas.fr'),
array("mail"=>'kisoird@laas.fr'),
array("mail"=>'kreisel@lippmann.lu'),
array("mail"=>'laurent.bigot@univ-lille1.fr'),
array("mail"=>'laurent.cormier@impmc.upmc.fr'),
array("mail"=>'lbocquet@MIT.EDU'),
array("mail"=>'lorenzo.ghiberti@yahoo.fr'),
array("mail"=>'marc.duquennoy@univ-valenciennes.fr'),
array("mail"=>'marc.faucher@isen.iemn.univ-lille1.fr'),
array("mail"=>'marceline.bonvalot@cea.fr'),
array("mail"=>'marie-charlotte.deschamps@univ-lyon1.fr'),
array("mail"=>'marie-christine.hugon@u-psud.fr'),
array("mail"=>'marie-laure.locatelli@laplace.univ-tlse.fr'),
array("mail"=>'marie-paule.besland@cnrs-imn.fr'),
array("mail"=>'marie.lesecq@iemn.univ-lille1.fr'),
array("mail"=>'maryline.rochery@ensait.fr'),
array("mail"=>'maud.vinet@cea.fr'),
array("mail"=>'mbrunet@laas.fr'),
array("mail"=>'menini@laas.fr'),
array("mail"=>'michael.fontaine@ens2m.fr'),
array("mail"=>'michael.fontaine@femto-st.fr'),
array("mail"=>'mnertz@wynsep.com'),
array("mail"=>'morancho@laas.fr'),
array("mail"=>'mrakoton@femto-st.fr'),
array("mail"=>'msaleh@chu-besancon.fr'),
array("mail"=>'nicolas.andreff@femto-st.fr'),
array("mail"=>'nicolas.bertru@insa-rennes.fr'),
array("mail"=>'nicolas.dietrich@insa-toulouse.fr'),
array("mail"=>'nicolas.rougemaille@neel.crns.fr'),
array("mail"=>'nicu@laas.fr'),
array("mail"=>'ogauthie@laas.fr'),
array("mail"=>'olivier.fabbri@univ-fcomte.fr'),
array("mail"=>'olivier.gauthier.lafaye@laas.fr'),
array("mail"=>'patrice.camy@ensicaen.fr'),
array("mail"=>'patrice.raynaud@laplace.univ-tlse.fr'),
array("mail"=>'patrick.fievet@univ-fcomte.fr'),
array("mail"=>'patrick.tabeling@espci.fr'),
array("mail"=>'pere.roca@polytechnique.edu'),
array("mail"=>'philippe.pernod@iemn.univ-lille1.fr'),
array("mail"=>'pierre.joseph@laas.fr'),
array("mail"=>'pierre.temple-boyer@laas.fr'),
array("mail"=>'pignard@lma.in2p3.fr'),
array("mail"=>'pjoeseph@laas.fr'),
array("mail"=>'pjoseph@laas.fr'),
array("mail"=>'pons@laas.fr'),
array("mail"=>'ppons@laas.Fr'),
array("mail"=>'ppons@laas.fr'),
array("mail"=>'romain.quidant@icfo.es'),
array("mail"=>'rossi@laas.fr'),
array("mail"=>'samuel.saada@cea.fr'),
array("mail"=>'sbernard@mnhn.fr'),
array("mail"=>'scalvez@laas.fr'),
array("mail"=>'sclavez@laas.fr'),
array("mail"=>'seat@laas.fr'),
array("mail"=>'sebastian.david@freescale.com'),
array("mail"=>'sebastien.dubois@cea.fr'),
array("mail"=>'sebastien.thibaud@univ-fcomte.fr'),
array("mail"=>'sergio.ciliberto@ens-lyon.fr'),
array("mail"=>'severine.cheramy@cea.fr'),
array("mail"=>'skandar.basrour@imag.fr'),
array("mail"=>'stephane.fanget@cea.fr'),
array("mail"=>'sylvain.bollaert@iemn.univ-lille1.fr'),
array("mail"=>'sylvie.lorthois@imft.fr'),
array("mail"=>'temple@laas.fr'),
array("mail"=>'thierry.melin@isen.iemn.univ-lille1.fr'),
array("mail"=>'thierry.parra@laas.fr'),
array("mail"=>'thomas.pardoen@uclouvain.be'),
array("mail"=>'tleichle@laas.fr'),
array("mail"=>'tobias.schulli@esrf.fr'),
array("mail"=>'tony.maindron@cea.fr'),
array("mail"=>'ulrich.maschke@univ-lille1.fr'),
array("mail"=>'valerie.taly@parisdescartes.fr'),
array("mail"=>'veronique.bardinal@laas.fr'),
array("mail"=>'vgiordano@femto-st.fr'),
array("mail"=>'vincent.cros@thalesgroup.com'),
array("mail"=>'vincent.jourdain@univ-montp2.fr'),
array("mail"=>'vincent.senez@isen.fr'),
array("mail"=>'vincent.senez@isen.iemn.univ-lille1.fr'),
array("mail"=>'wboireau@femto-st.fr'),
array("mail"=>'wilfrid.boireau@femto-st.fr'),
array("mail"=>'yanxia.hou-broutin@cea.fr'),
array("mail"=>'yvan.avenas@g2elab.grenoble-inp.fr')
);	
		


for ($i = 0; $i < count($test); $i++) {
    $idresponsable = $manager->getSingle2("select idutilisateur from utilisateur,loginpassword  where idlogin=idlogin_loginpassword and  lower(mail) like  lower(?)", $test[$i]['mail']);
    if(!empty($idresponsable)){
        echo $test[$i]['mail'].' ; '.$idresponsable.';<br>';
    }
       
}