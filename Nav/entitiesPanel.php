<style>
    .ent-type {
        font-size: 20px;
    }
</style>
<div id="entityPane" class="data ent-type" style="background-color: #00274a;">
<!--    <select name="Entities" id="entity-selection" class="ent-type" style="background-color: <?=$color?>;">
        <option <?=$onclickStations?> class="ent-type <?=$classStations?>" style="color: <?=$color?>; ">Stations</option>
        <option <?=$onclickGates?> class="ent-type <?=$classGates?>" style="color: <?=$color?>;">Gates</option>
        <option <?=$onclickOther?> class="ent-type <?=$classOther?>" style="color: <?=$color?>;">Other</option>
        <option <?=$onclickIntel?> class="ent-type <?=$classOther?>" style="color: <?=$color?>;">Intel</option>
    </select>-->
    
    <?php
        $sysName = $sector;
        if (!isEmpty($sub) && ($sectorWidth * $sectorHeight > 1)) {
            $sysName .= " - " . toRoman($sub);
        }
    ?>
    <div id="entities-header" style="display: flex;">
    <h3 style="color: #fc5555; margin-left: 20px; margin-top: 14px; font-size: xx-large; margin-bottom: auto;"><?=$sysName?></h3>
    <?php

        //echo strtoupper($sysName);
//                                    if (!isEmpty($sub) && ($sectorWidth * $sectorHeight > 1)) {
//					echo " - ".toRoman($sub);
//                                    }
//        if ($classified) {
//            $edit_url = "index.php?Classified";
//        }
//        if ($sector != "") {
//            $edit_url .= "&sector=$sector";
//            if ($sub != "") {
//                $edit_url .= "&sub=$sub";
//            }
//        }
//        if ($mobile) {
//            $edit_url .= "&mobile=true";
//        }
        $edit_url = getUrlParams();
        
        //echo($edit_url);
        //echo($classified);
        if ($classified) {?>
            
            <form action="<?=$edit_url?>&edit" method="post" style="margin: 20px; margin-bottom: auto;">
                <button type="submit" class="edit-button" style="background-image: url('img/edit-icon.png'); background-size: 20px; height: 25px; width: 24px;"></button>
            </form>
            <?php
        }
        if ($showIntel) {
            //$edit_url .= "&Intel";
            str_replace("&Intel", "", $edit_url);
        }
        ?>
    </div>
    <div id="panel-buttons" style="display: flex;">
        <button class="dropbtn<?=$showIntel ? " active" : ""?>" id="toggle-intel" onclick="location.href='<?=$edit_url?>'">Intel</button>
    <?php
        

        if (!isEmpty($sub) && ($sectorWidth * $sectorHeight > 1)) {?>
                <button onclick="location.href='index.php?<?=$classifiedHref?>sector=<?=$sector?>'" class="dropbtn" style="margin-left: -8px;">TO SYSTEM</button><?php
        } else {
                echo " ";
        }
        ?>
    </div>
    <?php
        if ($showIntel || $edit) {
            include 'intelEditForm.php';
        } else {
            include 'sectorEntities.php'; 
        }?>
</div>