<fieldset id='stat'><legend><?php echo TXT_STATISTIQUE; ?></legend>
    <select  id="choixstat" data-dojo-type="dijit/form/FilteringSelect"  data-dojo-props="name: 'libellechoix',value: '',required:false,placeHolder: '<?php echo 'Choisir un item'; ?>'"
             style="width: 300px;margin-top:25px;" onchange="window.location.replace('<?php echo "/" . REPERTOIRE; ?>/chxStatistique/<?php echo $lang ?>/' + this.value)" >
        <?php
        if ($lang == 'fr') {
            for ($i = 0; $i < $nbstat; $i++) {
                echo '<option value=' . $arraystat[$i]['idstatistique'] . '>' . $arraystat[$i]['libellestatistique'] . '</option>';
            }
        } elseif ($lang == 'en') {
            for ($i = 0; $i < $nbstat; $i++) {
                echo '<option value=' . $arraystat[$i]['idstatistique'] . '>' . $arraystat[$i]['libellestatistiqueen'] . '</option>';
            }
        }
        ?>
    </select>
</fieldset>