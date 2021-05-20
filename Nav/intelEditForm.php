<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
    $edit_url = "index.php?Classified";
    if ($sector != "") {
        $edit_url .= "&sector=$sector";
        if ($sub != "") {
            $edit_url .= "&sub=$sub";
        }
    }
    if ($mobile) {
        $edit_url .= "&mobile=true";
    }
    $data = readIntelFile($sector, $sub);

?>
<br style="color: blue; font-size: 20;">Please enter ONI security clearance
<form action=<?=$edit_url?> method="post">
    <!--<input type="textarea" name="intel"><br>-->
    <textarea name="intel" style="width: 80vh; height: 30vh; background-color: black; color: white;"><?=$data?></textarea>
    <input type="submit" value="Submit Updates" style=" background-color: black; color: white;">
</form>
<br>