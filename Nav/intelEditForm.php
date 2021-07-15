<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


    $data = readIntelFile($sector, $sub);
    $readonly = $edit ? "" : "readonly";
    $edit_url = getUrlParams();
    if (!$intel){
        $edit_url .= "&Intel";
    }
    if ($edit) {
        $edit_url .= "&edit";
    }
    str_replace("&&", "&", $$edit_url);
    str_replace("&&", "&", $$edit_url);
?>
<br style="color: blue; font-size: 20px;">
<form id="intel-form" action=<?=$edit_url?> method="post">
    <!--<input type="textarea" name="intel"><br>-->
    <textarea <?=$readonly?> form="intel-form" name="intel-data" style="width: 90%; height: auto; background-color: black; color: white; margin-left: 5%; max-width: 90%;"><?=$data?></textarea>
    <?php
    //echo($edit);
        if ($edit) {?>
        <!--<input type="submit" value="Submit Updates" style=" background-color: black; color: white; margin-left: 5%; margin-top: 10px;">-->
        <button type="submit" class="dropbtn" style="margin-top: 15px;">Submit Updates</button>
    <?php 
        }?>
</form>
<br>