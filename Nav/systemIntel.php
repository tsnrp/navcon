<?php

// from index.php
// $sub
// $sector
    $intel;
    $oni_intel;
    if (isset($sub)) {
        $intel = './../../SystemIntel/'.$sector.'/'.$sub.'.txt';
        $oni_intel = './../../SystemIntel/'.$sector.'/'.$sub.'-ONI'.'.txt';
    } else if (isset($sector)) {
        $intel = './../../SystemIntel/'.$sector.'system.txt';
        $oni_intel = './../../SystemIntel/'.$sector.'-ONI'.'system.txt';
    }
    if (file_exists($intel)) {
        //fwrite($fp,"\nupdate type (getLatestCommit(): ".$update_type);
        $data = file_get_contents($intel);
        fclose($intel);
    } else {
        $data="No information known.";
    }
    if (file_exists($oni_intel)) {
        $oni_data = file_get_contents($oni_intel);
        fclose($oni_intel);
    } else {
        $oni_data = "No infomration known.";
    }
    


?>

<div class="data">
    <tr>
        <td colspan="1" style="color:blue;"><br/>System Intelligence</td>
        <tr>
        <textarea id="intel-textarea">
            <?php 
                echo($data); 
            ?>
        </textarea>
        </tr>
        <tr>
        <textarea id="intel-textarea-oni">
            <?php
                echo($oni_data);
            ?>
        </textarea>
        </tr>
    </tr>
</div>