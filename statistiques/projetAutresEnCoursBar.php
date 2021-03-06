<?php
include_once 'class/Manager.php';
$db = BD::connecter();
unset($_SESSION['anneeprojetautres']);
$manager = new Manager($db);
$libelleCentrale = $manager->getSingle2("select libellecentrale from centrale where idcentrale=?", IDCENTRALEUSER);
$centrales = $manager->getList2("select libellecentrale,idcentrale from centrale where idcentrale!=? and masquecentrale!=TRUE order by idcentrale asc", IDAUTRECENTRALE);
$years = $manager->getList("select distinct EXTRACT(YEAR from dateprojet)as year from projet where   EXTRACT(YEAR from dateprojet)>2012order by year asc");
if (IDTYPEUSER == ADMINNATIONNAL) {
    ?>
    <table>
        <tr>
            <td>
                <select  id="anneerepartitionProjetEncoursParType" data-dojo-type="dijit/form/FilteringSelect"  data-dojo-props="name: 'anneerepartitionProjetEncoursParType',value: '',required:false,placeHolder: '<?php echo TXT_SELECTYEAR; ?>'" 
                         style="width: 250px;margin-left:35px" 
                         onchange="window.location.replace('<?php echo "/" . REPERTOIRE; ?>/statProjetAutresEnCours/<?php echo $lang . '/'; ?>' + this.value)" >
                             <?php
                             for ($i = 0; $i < count($years); $i++) {
                                 echo '<option value="' . $years[$i]['year'] . '">' . $years[$i]['year'] . '</option>';
                             }
                             ?>
                </select>
            </td>
        </tr>
    </table>
    <?php
}
if (IDTYPEUSER == ADMINNATIONNAL && !isset($_GET['anneerepartitionProjetEncoursParType'])) {
    $title = TXT_REPARTITIONPROJETENCOURS;
    $subtitle = "";
    $xasisTitle = "";
$nbtotalprojet = $manager->getSinglebyArray("select count(idprojet_projet) from concerne,projet  where idprojet_projet=idprojet and idstatutprojet_statutprojet=? ", 
        array(ENCOURSREALISATION));
    $nbprojetExogeneExterne = $manager->getSingle2("SELECT count(distinct co.idprojet_projet) FROM creer cr,projet p,utilisateur u,concerne co WHERE  cr.idprojet_projet = p.idprojet 
        AND  u.idutilisateur = cr.idutilisateur_utilisateur AND  u.idcentrale_centrale is null and  co.idprojet_projet = p.idprojet and p.idprojet not in(select idprojet_projet 
        from projetpartenaire )and idstatutprojet_statutprojet=?  ", ENCOURSREALISATION);

    $nbprojetExogeneCollaboratif = $manager->getSingle2("SELECT count(distinct co.idprojet_projet) FROM  projet p, projetpartenaire pr, utilisateur u, creer c,concerne co WHERE p.idprojet = c.idprojet_projet 
        AND pr.idprojet_projet = p.idprojet AND c.idutilisateur_utilisateur = u.idutilisateur and  co.idprojet_projet = p.idprojet And u.idcentrale_centrale IS NOT NULL and idstatutprojet_statutprojet=? 
        ", ENCOURSREALISATION);
    $nbprojetInterne = $nbtotalprojet - $nbprojetExogeneExterne - $nbprojetExogeneCollaboratif;
    $serieX = '{name: "' . TXT_PROJETINTERNE . '", data: [{name: "' . TXT_DETAILS . '",y: ' . $nbprojetInterne . ',drilldown: "' . TXT_PROJETINTERNE . '"}]},';
    $serieX .= '{name: "' . TXT_PROJETEXOEXTERNE . '", data: [{name: "' . TXT_DETAILS . '",y: ' . $nbprojetExogeneExterne . ',drilldown: "' . "externe" . '"}]},';
    $serieX .= '{name: "' . TXT_PROJETEXOCOLLABORATIF . '", data: [{name: "' . TXT_DETAILS . '",y: ' . $nbprojetExogeneCollaboratif . ',drilldown: "' . 'collaboratif' . '"}]}';
//------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------    
    $serieExterne = "{id: '" . 'externe' . "',name: '" . TXT_PROJETEXOEXTERNE . "',data: [";
    $serieExterneCentrale = "";
    foreach ($years as $key => $year) {
        $nbprojetExogeneExterne = $manager->getSinglebyArray("SELECT count(distinct co.idprojet_projet) FROM creer cr,projet p,utilisateur u,concerne co WHERE cr.idprojet_projet = p.idprojet 
                    AND  u.idutilisateur = cr.idutilisateur_utilisateur  AND  u.idcentrale_centrale is null and  co.idprojet_projet = p.idprojet and idstatutprojet_statutprojet=? 
                    and p.idprojet not in(select idprojet_projet from projetpartenaire ) and extract(year from dateprojet)<=? ", array(ENCOURSREALISATION, $year[0]));
        if($year[0]==2013){
            $serieExterne .="{name: '" . TXT_INFERIEUR2013 . "', y: " . $nbprojetExogeneExterne . " , drilldown: '" . 'externe' . $year[0] . "'},";
        }else{
            $serieExterne .="{name: '" . $year[0] . "', y: " . $nbprojetExogeneExterne . " , drilldown: '" . 'externe' . $year[0] . "'},";
        }
        
    }$serieExterne .="]},";
    foreach ($years as $key => $year) {
        $serieExterneCentrale .= "{id: '" . 'externe' . $year[0] . "',name: '" . TXT_PROJETEXOEXTERNE . "',data: [";
        foreach ($centrales as $key => $centrale) {
            $nbprojetExogeneExterne = $manager->getSinglebyArray("SELECT count(distinct co.idprojet_projet) FROM creer cr,projet p,utilisateur u,concerne co WHERE cr.idprojet_projet = p.idprojet 
                        AND  u.idutilisateur = cr.idutilisateur_utilisateur  AND  u.idcentrale_centrale is null and  co.idprojet_projet = p.idprojet and p.idprojet not in(select idprojet_projet from projetpartenaire) 
                        and extract(year from dateprojet)<=?  and idstatutprojet_statutprojet=? and co.idcentrale_centrale=?", array($year[0], ENCOURSREALISATION, $centrale[1]));
            $serieExterneCentrale .="{name: '" . $centrale[0] . "',color:'". couleurGraphLib($centrale[0])."', y:" . $nbprojetExogeneExterne . " , drilldown: '" . 'externe' . $libelleCentrale . $year[0] . "'},";
        }$serieExterneCentrale .="]},";
    }
    //--------------------------------------------------------------------
    $serieCollaboratif = "{id: '" . 'collaboratif' . "',name: '" . TXT_PROJETEXOCOLLABORATIF . "',data: [";
    foreach ($years as $key => $year) {
        $nbprojetExogeneCollaboratif = $manager->getSinglebyArray("SELECT count(distinct co.idprojet_projet) FROM  projet p, projetpartenaire pr, utilisateur u, creer c,concerne co WHERE p.idprojet = c.idprojet_projet 
                    AND pr.idprojet_projet = p.idprojet AND c.idutilisateur_utilisateur = u.idutilisateur and  co.idprojet_projet = p.idprojet And u.idcentrale_centrale IS NOT NULL and extract(year from dateprojet)<=? 
                     and idstatutprojet_statutprojet=?", array($year[0], ENCOURSREALISATION));
         if($year[0]==2013){
            $serieCollaboratif .="{name: '" . TXT_INFERIEUR2013 . "', y: " . $nbprojetExogeneCollaboratif . " , drilldown: '" . 'collaboratif' . $year[0] . "'},";
        }else{
            $serieCollaboratif .="{name: '" . $year[0] . "', y: " . $nbprojetExogeneCollaboratif . " , drilldown: '" . 'collaboratif' . $year[0] . "'},";
        }        
    }$serieCollaboratif.="]},";
    $serieCollaboratifCentrale = "";
    foreach ($years as $key => $year) {
        $serieCollaboratifCentrale .= "{id: '" . 'collaboratif' . $year[0] . "',name: '" . TXT_PROJETEXOCOLLABORATIF . "',data: [";
        foreach ($centrales as $key => $centrale) {
            $nbprojetExogeneCollaboratif = $manager->getSinglebyArray("SELECT count(distinct co.idprojet_projet) FROM  projet p, projetpartenaire pr, utilisateur u, creer c,concerne co WHERE p.idprojet = c.idprojet_projet 
                    AND pr.idprojet_projet = p.idprojet AND c.idutilisateur_utilisateur = u.idutilisateur and  co.idprojet_projet = p.idprojet And u.idcentrale_centrale IS NOT NULL and extract(year from dateprojet)<=? 
                     and idstatutprojet_statutprojet=? and co.idcentrale_centrale=?", array($year[0], ENCOURSREALISATION, $centrale[1]));
            $serieCollaboratifCentrale .="{name: '" . $centrale[0] . "',color:'". couleurGraphLib($centrale[0])."', y: " . $nbprojetExogeneCollaboratif . " , drilldown: '" . 'collaboratif' . $centrale[0] . $year[0] . "'},";
        }$serieCollaboratifCentrale .="]},";
    }

    //--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    $serieInterne = "{id: '" . TXT_PROJETINTERNE . "',name: '" . TXT_PROJETINTERNE . "',data: [";
    foreach ($years as $key => $year) {
        $nbtotalprojet = $manager->getSinglebyArray("select count(distinct idprojet_projet) from concerne,projet where idprojet_projet=idprojet and extract(year from dateprojet)<=?  "
                . "and idstatutprojet_statutprojet=?",array($year[0], ENCOURSREALISATION));

        $nbprojetExogeneExterne = $manager->getSinglebyArray("SELECT count(distinct co.idprojet_projet) FROM creer cr,projet p,utilisateur u,concerne co WHERE cr.idprojet_projet = p.idprojet 
                    AND  u.idutilisateur = cr.idutilisateur_utilisateur  AND  u.idcentrale_centrale is null and  co.idprojet_projet = p.idprojet and p.idprojet not in(select idprojet_projet from projetpartenaire ) 
                    and extract(year from dateprojet)<=?  and idstatutprojet_statutprojet=?", array($year[0], ENCOURSREALISATION));

        $nbprojetExogeneCollaboratif = $manager->getSinglebyArray("SELECT count(distinct co.idprojet_projet) FROM  projet p, projetpartenaire pr, utilisateur u, creer c,concerne co WHERE p.idprojet = c.idprojet_projet 
                    AND pr.idprojet_projet = p.idprojet AND c.idutilisateur_utilisateur = u.idutilisateur and  co.idprojet_projet = p.idprojet And u.idcentrale_centrale IS NOT NULL and extract(year from dateprojet)<=? 
                     and idstatutprojet_statutprojet=?", array($year[0], ENCOURSREALISATION));

        $nbprojetInterne = $nbtotalprojet - ($nbprojetExogeneExterne + $nbprojetExogeneCollaboratif);
        if($year[0]==2013){
            $serieInterne .="{name: '" . TXT_INFERIEUR2013 . "', y: " . $nbprojetInterne . " , drilldown: '" . TXT_PROJETINTERNE . $year[0] . "'},";
        }else{
            $serieInterne .="{name: '" . $year[0] . "', y: " . $nbprojetInterne . " , drilldown: '" . TXT_PROJETINTERNE . $year[0] . "'},";
        }
        
    }$serieInterne.= "]},";
    //--------------------------------------------------------------------------------------------------------------------------------------------------------------------------    
    $serieInterneCentrale = "";
    foreach ($years as $key => $year) {
        $serieInterneCentrale .= "{id: '" . TXT_PROJETINTERNE . $year[0] . "',name: '" . TXT_PROJETINTERNE . "',data: [";
        foreach ($centrales as $key => $centrale) {
            $nbtotalprojet = $manager->getSinglebyArray("select count(distinct idprojet_projet) from concerne,projet where idprojet_projet=idprojet and extract(year from dateprojet)<=?  "
                    . "and idstatutprojet_statutprojet=? and idcentrale_centrale=?", array($year[0], ENCOURSREALISATION, $centrale[1]));
            $nbprojetExogeneExterne = $manager->getSinglebyArray("SELECT count(distinct co.idprojet_projet) FROM creer cr,projet p,utilisateur u,concerne co WHERE cr.idprojet_projet = p.idprojet 
                    AND  u.idutilisateur = cr.idutilisateur_utilisateur  AND  u.idcentrale_centrale is null and  co.idprojet_projet = p.idprojet and p.idprojet not in(select idprojet_projet from projetpartenaire ) 
                    and extract(year from dateprojet)<=?  and idstatutprojet_statutprojet=? and co.idcentrale_centrale=?", array($year[0], ENCOURSREALISATION, $centrale[1]));
            $nbprojetExogeneCollaboratif = $manager->getSinglebyArray("SELECT count(distinct co.idprojet_projet) FROM  projet p, projetpartenaire pr, utilisateur u, creer c,concerne co WHERE p.idprojet = c.idprojet_projet 
                    AND pr.idprojet_projet = p.idprojet AND c.idutilisateur_utilisateur = u.idutilisateur and  co.idprojet_projet = p.idprojet And u.idcentrale_centrale IS NOT NULL and extract(year from dateprojet)<=? 
                     and idstatutprojet_statutprojet=? and co.idcentrale_centrale=?", array($year[0], ENCOURSREALISATION, $centrale[1]));
            $nbprojetInterne = $nbtotalprojet - ($nbprojetExogeneExterne + $nbprojetExogeneCollaboratif);
            $serieInterneCentrale .="{name: '" . $centrale[0] . "',color:'". couleurGraphLib($centrale[0])."', y: " . $nbprojetInterne . " , drilldown: '" . TXT_PROJETINTERNE . $centrale[0] . $year[0] . "'},";
        }$serieInterneCentrale .="]},";
    }
    $serieY0 = $serieExterne . $serieCollaboratif . $serieInterne . $serieExterneCentrale . $serieCollaboratifCentrale . $serieInterneCentrale;
    $serieY = substr(str_replace("],]", "]]", $serieY0), 0, -1);
} elseif (IDTYPEUSER == ADMINNATIONNAL && isset($_GET['anneerepartitionProjetEncoursParType'])) {
    ?>
    <table>
        <tr>
            <td><input class="admin" type="button" onclick="window.location.replace('/<?php echo REPERTOIRE; ?>/chxStatistique/<?php echo $lang . '/' . IDREPARTIONPROJETENCOURSTYPE; ?>')"  value= "<?php echo TXT_EFFACESELECTION; ?>" >
            </td>
        </tr>
    </table> 
    <?php
    if($_GET['anneerepartitionProjetEncoursParType']==2013){
        $title = TXT_REPARTITIONPROJETENCOURSANNEE.' '.TXT_INFERIEUR2013;
    }else{
        $title = TXT_REPARTITIONPROJETENCOURSANNEE . $_GET['anneerepartitionProjetEncoursParType'];
    }
    $subtitle="";
    $xasisTitle = "";
    $nbtotalProjet = $manager->getSinglebyArray("select count(idprojet_projet) from concerne,projet  where idprojet_projet=idprojet and idstatutprojet_statutprojet=?  "
            . "and extract(year from dateprojet)<=?",array(ENCOURSREALISATION, $_GET['anneerepartitionProjetEncoursParType']));
    $nbProjetExogeneExterne = $manager->getSinglebyArray("SELECT count(distinct co.idprojet_projet) FROM creer cr,projet p,utilisateur u,concerne co WHERE  cr.idprojet_projet = p.idprojet 
        AND  u.idutilisateur = cr.idutilisateur_utilisateur AND  u.idcentrale_centrale is null and  co.idprojet_projet = p.idprojet and p.idprojet not in(select idprojet_projet from projetpartenaire )
        and idstatutprojet_statutprojet =?  and extract(year from dateprojet)<=? ",array(ENCOURSREALISATION,$_GET['anneerepartitionProjetEncoursParType']));

    $nbProjetExogeneCollaboratif = $manager->getSinglebyArray("SELECT count(distinct co.idprojet_projet) FROM  projet p, projetpartenaire pr, utilisateur u, creer c,concerne co WHERE p.idprojet = c.idprojet_projet AND pr.idprojet_projet = p.idprojet
        AND c.idutilisateur_utilisateur = u.idutilisateur and  co.idprojet_projet = p.idprojet And u.idcentrale_centrale IS NOT NULL and idstatutprojet_statutprojet=?  
        and extract(year from dateprojet)<=? ", array(ENCOURSREALISATION, $_GET['anneerepartitionProjetEncoursParType']));
    $nbProjetInterne = $nbtotalProjet - $nbProjetExogeneExterne - $nbProjetExogeneCollaboratif;
    $serieX = '{name: "' . TXT_PROJETINTERNE . '", data: [{name: "' . TXT_DETAILS . '",y: ' . $nbProjetInterne . ',drilldown: "' . TXT_PROJETINTERNE . '"}]},';
    $serieX .= '{name: "' . TXT_PROJETEXOEXTERNE . '", data: [{name: "' . TXT_DETAILS . '",y: ' . $nbProjetExogeneExterne . ',drilldown: "' . TXT_PROJETEXOEXTERNE . '"}]},';
    $serieX .= '{name: "' . TXT_PROJETEXOCOLLABORATIF . '", data: [{name: "' . TXT_DETAILS . '",y: ' . $nbProjetExogeneCollaboratif . ',drilldown: "' . TXT_PROJETEXOCOLLABORATIF . '"}]}';


//-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

    $serieInterneCentrale = "{id: '" . TXT_PROJETINTERNE . "',name: '" . TXT_PROJETINTERNE . "',data: [";
    $serieExogeneExterne = "{id: '" . TXT_PROJETEXOEXTERNE . "',name: '" . TXT_PROJETEXOEXTERNE . "',data: [";
    $serieExogeneCollaboratif = "{id: '" . TXT_PROJETEXOCOLLABORATIF . "',name: '" . TXT_PROJETEXOCOLLABORATIF . "',data: [";
    foreach ($centrales as $key => $centrale) {
        $nbtotalprojet = $manager->getSinglebyArray("select count(distinct idprojet_projet) from concerne,projet where idprojet_projet=idprojet and extract(year from dateprojet)<=?  "
                . "and idstatutprojet_statutprojet=? and idcentrale_centrale=?", array($_GET['anneerepartitionProjetEncoursParType'], ENCOURSREALISATION, $centrale[1]));
        $nbprojetExogeneExterne = $manager->getSinglebyArray("SELECT count(distinct co.idprojet_projet) FROM creer cr,projet p,utilisateur u,concerne co WHERE cr.idprojet_projet = p.idprojet 
                    AND  u.idutilisateur = cr.idutilisateur_utilisateur  AND  u.idcentrale_centrale is null and  co.idprojet_projet = p.idprojet and p.idprojet not in(select idprojet_projet from projetpartenaire )
                    and extract(year from dateprojet)<=?  and idstatutprojet_statutprojet=? and co.idcentrale_centrale=?",
                array($_GET['anneerepartitionProjetEncoursParType'], ENCOURSREALISATION, $centrale[1]));
        $nbprojetExogeneCollaboratif = $manager->getSinglebyArray("SELECT count(distinct co.idprojet_projet) FROM  projet p, projetpartenaire pr, utilisateur u, creer c,concerne co WHERE p.idprojet = c.idprojet_projet 
                    AND pr.idprojet_projet = p.idprojet AND c.idutilisateur_utilisateur = u.idutilisateur and  co.idprojet_projet = p.idprojet And u.idcentrale_centrale IS NOT NULL and extract(year from dateprojet)<=? 
                     and idstatutprojet_statutprojet=? and co.idcentrale_centrale=?", array($_GET['anneerepartitionProjetEncoursParType'], ENCOURSREALISATION, $centrale[1]));
        $nbprojetInterne = $nbtotalprojet - ($nbprojetExogeneExterne + $nbprojetExogeneCollaboratif);
        $serieInterneCentrale .="{name: '" . $centrale[0] . "',color:'". couleurGraphLib($centrale[0])."', y: " . $nbprojetInterne . " , drilldown: '" . TXT_PROJETINTERNE . $centrale[0] . $_GET['anneerepartitionProjetEncoursParType'] . "'},";
        $serieExogeneExterne .="{name: '" . $centrale[0] . "',color:'". couleurGraphLib($centrale[0])."', y: " . $nbprojetExogeneExterne . " , drilldown: '" . TXT_PROJETEXOEXTERNE . $centrale[0] . $_GET['anneerepartitionProjetEncoursParType'] . "'},";
        $serieExogeneCollaboratif .="{name: '" . $centrale[0] . "',color:'". couleurGraphLib($centrale[0])."', y: " . $nbprojetExogeneCollaboratif . " , drilldown: '" . TXT_PROJETEXOCOLLABORATIF . $centrale[0] . $_GET['anneerepartitionProjetEncoursParType'] . "'},";
//-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    }
    $serieInterneCentrale .="]},";
    $serieExogeneExterne .="]},";
    $serieExogeneCollaboratif .="]},";
    $serieY0 = str_replace("],]}", "]]}", $serieInterneCentrale);
    $serieY1 = str_replace("],]}", "]]}", $serieExogeneExterne);
    $serieY2 = str_replace("],]}", "]]}", $serieExogeneCollaboratif);
    $serieY00 = str_replace('},},', '}},', $serieY0 . $serieY1 . $serieY2);
    $serie001 = str_replace('},]}', '}]}', $serieY00);
    $serieY = substr($serie001, 0, -1);
}


if (IDTYPEUSER == ADMINLOCAL) {
    $nbtotalprojet = $manager->getSinglebyArray("select count(idprojet_projet) from concerne,projet  where idprojet_projet=idprojet and idstatutprojet_statutprojet=?  "
            . "and idcentrale_centrale=?", array(ENCOURSREALISATION,IDCENTRALEUSER));
    $title = TXT_REPARTITIONPROJETENCOURS;
    $subtitle = "";
    $xasisTitle = "";

    $nbprojetExogeneExterne = $manager->getSinglebyArray("SELECT count(distinct co.idprojet_projet) FROM creer cr,projet p,utilisateur u,concerne co WHERE  cr.idprojet_projet = p.idprojet 
        AND  u.idutilisateur = cr.idutilisateur_utilisateur AND  u.idcentrale_centrale is null and  co.idprojet_projet = p.idprojet and p.idprojet not in(select idprojet_projet from projetpartenaire )and idstatutprojet_statutprojet=? 
         and co.idcentrale_centrale=?",array(ENCOURSREALISATION,IDCENTRALEUSER));
    $nbprojetExogeneCollaboratif = $manager->getSinglebyArray("SELECT count(distinct co.idprojet_projet) FROM  projet p, projetpartenaire pr, utilisateur u, creer c,concerne co WHERE p.idprojet = c.idprojet_projet AND pr.idprojet_projet = p.idprojet
        AND c.idutilisateur_utilisateur = u.idutilisateur and  co.idprojet_projet = p.idprojet And u.idcentrale_centrale IS NOT NULL and idstatutprojet_statutprojet=?  
        and co.idcentrale_centrale=?",array(ENCOURSREALISATION,IDCENTRALEUSER));
    $nbprojetInterne = $nbtotalprojet - $nbprojetExogeneExterne - $nbprojetExogeneCollaboratif;    
    $serieX = '{name: "' . TXT_PROJETINTERNE . '", data: [{name: "' . TXT_DETAILS . '",y: ' . $nbprojetInterne . ',drilldown: "' . TXT_PROJETINTERNE . '"}]},';
    $serieX .= '{name: "' . TXT_PROJETEXOEXTERNE . '", data: [{name: "' . TXT_DETAILS . '",y: ' . $nbprojetExogeneExterne . ',drilldown: "' . "externe" . '"}]},';
    $serieX .= '{name: "' . TXT_PROJETEXOCOLLABORATIF . '", data: [{name: "' . TXT_DETAILS . '",y: ' . $nbprojetExogeneCollaboratif . ',drilldown: "' . 'collaboratif' . '"}]}';
//------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------    
    $serieExterne = "{id: '" . 'externe' . "',name: '" . TXT_PROJETEXOEXTERNE . "',data: [";    
    $serieExterneMois="";
    foreach ($years as $key => $year) {
        $nbprojetExogeneExterne = $manager->getSinglebyArray("SELECT count(distinct co.idprojet_projet) FROM creer cr,projet p,utilisateur u,concerne co WHERE cr.idprojet_projet = p.idprojet 
                    AND  u.idutilisateur = cr.idutilisateur_utilisateur  AND  u.idcentrale_centrale is null and  co.idprojet_projet = p.idprojet and p.idprojet not in(select idprojet_projet from projetpartenaire )
                    and extract(year from dateprojet)<=?  and idstatutprojet_statutprojet=? and co.idcentrale_centrale=?", array($year[0], ENCOURSREALISATION,IDCENTRALEUSER));
        if($year[0]==2013){
            $serieExterne .="{name: '" . TXT_INFERIEUR2013 . "', y: " . $nbprojetExogeneExterne . " , drilldown: '" . 'externe' . $year[0] . "'},";
        }else{
            $serieExterne .="{name: '" . $year[0] . "', y: " . $nbprojetExogeneExterne . " , drilldown: '" . 'externe' . $year[0] . "'},";
        }
        
        $serieExterneMois .="{id: '" . 'externe' . $year[0] . "',name: '" .'externe'. $year[0] . "'" . ',data: [';
        for ($mois = 1; $mois < 13; $mois++) {
            $nbprojetExogeneExterneMois = $manager->getSinglebyArray("SELECT count(distinct co.idprojet_projet) FROM creer cr,projet p,utilisateur u,concerne co WHERE cr.idprojet_projet = p.idprojet 
                    AND  u.idutilisateur = cr.idutilisateur_utilisateur  AND  u.idcentrale_centrale is null and  co.idprojet_projet = p.idprojet and p.idprojet not in(select idprojet_projet from projetpartenaire)
                    and extract(year from dateprojet)=?  and idstatutprojet_statutprojet=? and co.idcentrale_centrale=? and extract(month from dateprojet)=?", 
                    array($year[0], ENCOURSREALISATION,IDCENTRALEUSER,$mois));
            $serieExterneMois.= "['" . showMonth($mois,$lang) . "'," . $nbprojetExogeneExterneMois . "],";;
        }$serieExterneMois.=']},';
        
    }$serieExterne .="]},";
    //--------------------------------------------------------------------
    $serieCollaboratif = "{id: '" . 'collaboratif' . "',name: '" . TXT_PROJETEXOCOLLABORATIF . "',data: [";
    $serieCollaboratifMois="";
    foreach ($years as $key => $year) {
        $nbprojetExogeneCollaboratif = $manager->getSinglebyArray("SELECT count(distinct co.idprojet_projet) FROM  projet p, projetpartenaire pr, utilisateur u, creer c,concerne co WHERE p.idprojet = c.idprojet_projet 
                    AND pr.idprojet_projet = p.idprojet AND c.idutilisateur_utilisateur = u.idutilisateur and  co.idprojet_projet = p.idprojet And u.idcentrale_centrale IS NOT NULL and extract(year from dateprojet)<=? 
                     and idstatutprojet_statutprojet=? and co.idcentrale_centrale=?", array($year[0], ENCOURSREALISATION,IDCENTRALEUSER));
        if($year[0]==2013){
            $serieCollaboratif .="{name: '" . TXT_INFERIEUR2013 . "', y: " . $nbprojetExogeneCollaboratif . " , drilldown: '" . 'collaboratif' . $year[0] . "'},";
        }else{
            $serieCollaboratif .="{name: '" . $year[0] . "', y: " . $nbprojetExogeneCollaboratif . " , drilldown: '" . 'collaboratif' . $year[0] . "'},";
        }
        
        $serieCollaboratifMois .="{id: '" . 'collaboratif' . $year[0] . "',name: '" .'collaboratif'. $year[0] . "'" . ',data: [';
        for ($mois = 1; $mois < 13; $mois++) {
            $nbprojetCollaboratifMois = $manager->getSinglebyArray("SELECT count(distinct co.idprojet_projet) FROM  projet p, projetpartenaire pr, utilisateur u, creer c,concerne co WHERE p.idprojet = c.idprojet_projet 
                    AND pr.idprojet_projet = p.idprojet AND c.idutilisateur_utilisateur = u.idutilisateur and  co.idprojet_projet = p.idprojet And u.idcentrale_centrale IS NOT NULL and extract(year from dateprojet)=? 
                     and idstatutprojet_statutprojet=? and co.idcentrale_centrale=? and extract(month from dateprojet)=?", 
                    array($year[0], ENCOURSREALISATION,IDCENTRALEUSER,$mois));
            $serieCollaboratifMois.= "['" . showMonth($mois,$lang) . "'," . $nbprojetCollaboratifMois . "],";
        }$serieCollaboratifMois.=']},';
    }$serieCollaboratif.="]},";     
    //--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    $serieInterne = "{id: '" . TXT_PROJETINTERNE . "',name: '" . TXT_PROJETINTERNE . "',data: [";
    $serieInterneMois="";
    foreach ($years as $key => $year) {
        $nbtotalprojet = $manager->getSinglebyArray("select count(distinct idprojet_projet) from concerne,projet where idprojet_projet=idprojet and extract(year from dateprojet)<=?  
            and idstatutprojet_statutprojet=? and idcentrale_centrale=?", array($year[0], ENCOURSREALISATION,IDCENTRALEUSER));
        $nbprojetExogeneExterne = $manager->getSinglebyArray("SELECT count(distinct co.idprojet_projet) FROM creer cr,projet p,utilisateur u,concerne co WHERE cr.idprojet_projet = p.idprojet 
                    AND  u.idutilisateur = cr.idutilisateur_utilisateur  AND  u.idcentrale_centrale is null and  co.idprojet_projet = p.idprojet and p.idprojet not in(select idprojet_projet from projetpartenaire) 
                    and extract(year from dateprojet)<=?  and idstatutprojet_statutprojet=? and co.idcentrale_centrale=?", array($year[0], ENCOURSREALISATION,IDCENTRALEUSER));

        $nbprojetExogeneCollaboratif = $manager->getSinglebyArray("SELECT count(distinct co.idprojet_projet) FROM  projet p, projetpartenaire pr, utilisateur u, creer c,concerne co WHERE p.idprojet = c.idprojet_projet 
                    AND pr.idprojet_projet = p.idprojet AND c.idutilisateur_utilisateur = u.idutilisateur and  co.idprojet_projet = p.idprojet And u.idcentrale_centrale IS NOT NULL and extract(year from dateprojet)<=? 
                     and idstatutprojet_statutprojet=? and co.idcentrale_centrale=?", array($year[0], ENCOURSREALISATION,IDCENTRALEUSER));

        $nbprojetInterne = $nbtotalprojet - ($nbprojetExogeneExterne + $nbprojetExogeneCollaboratif);
        if($year[0]==2013){
            $serieInterne .="{name: '" . TXT_INFERIEUR2013 . "', y: " . $nbprojetInterne . " , drilldown: '" . TXT_PROJETINTERNE . $year[0] . "'},";
        }else{
            $serieInterne .="{name: '" . $year[0] . "', y: " . $nbprojetInterne . " , drilldown: '" . TXT_PROJETINTERNE . $year[0] . "'},";
        }
        
        $serieInterneMois .="{id: '" . TXT_PROJETINTERNE . $year[0] . "',name: '" .TXT_PROJETINTERNE. $year[0] . "'" . ',data: [';
        for ($mois = 1; $mois < 13; $mois++) {
            $nbtotalprojetmois = $manager->getSinglebyArray("select count(distinct idprojet_projet) from concerne,projet where idprojet_projet=idprojet and extract(year from dateprojet)=? "
                    . " and idstatutprojet_statutprojet=? and idcentrale_centrale=? and extract(month from dateprojet)=?", array($year[0], ENCOURSREALISATION, IDCENTRALEUSER, $mois));
            $nbprojetExogeneExterneMois = $manager->getSinglebyArray("SELECT count(distinct co.idprojet_projet) FROM creer cr,projet p,utilisateur u,concerne co WHERE cr.idprojet_projet = p.idprojet 
                    AND  u.idutilisateur = cr.idutilisateur_utilisateur  AND  u.idcentrale_centrale is null and  co.idprojet_projet = p.idprojet and p.idprojet not in(select idprojet_projet from projetpartenaire) 
                    and extract(year from dateprojet)=?  and idstatutprojet_statutprojet=? and co.idcentrale_centrale=? and extract(month from dateprojet)=? ",
                    array($year[0], ENCOURSREALISATION, IDCENTRALEUSER, $mois));
            $nbprojetExogeneCollaboratifMois = $manager->getSinglebyArray("SELECT count(distinct co.idprojet_projet) FROM  projet p, projetpartenaire pr, utilisateur u, creer c,concerne co WHERE p.idprojet = c.idprojet_projet 
                    AND pr.idprojet_projet = p.idprojet AND c.idutilisateur_utilisateur = u.idutilisateur and  co.idprojet_projet = p.idprojet And u.idcentrale_centrale IS NOT NULL and extract(year from dateprojet)=? 
                     and idstatutprojet_statutprojet=? and co.idcentrale_centrale=? and extract(month from dateprojet)=?", array($year[0], ENCOURSREALISATION, IDCENTRALEUSER, $mois));
            $nbprojetInterneMois = $nbtotalprojetmois - ($nbprojetExogeneExterneMois + $nbprojetExogeneCollaboratifMois);
            $serieInterneMois.= "['" . showMonth($mois,$lang) . "'," . $nbprojetInterneMois . "],";
        }$serieInterneMois.=']},';
    }$serieInterne.= "]},";
    //--------------------------------------------------------------------------------------------------------------------------------------------------------------------------    
  
    $serieY0 = $serieExterne . $serieCollaboratif . $serieInterne.$serieExterneMois.$serieCollaboratifMois. $serieInterneMois;
    $serieY = substr(str_replace("],]", "]]", $serieY0), 0, -1);

}
include_once 'commun/scriptBar.php';
