<?php

// from index.php
// $sub
// $sector
    $intel;
    if (isset($sub)) {
        $intel = './../../SystemIntel/'.$sector.'/'.$sub.'.txt';
    } else if (isset($sector)) {
        $intel = './../../SystemIntel/'.$sector.'system.txt';
    }
    if (file_exists($intel)) {
        //fwrite($fp,"\nupdate type (getLatestCommit(): ".$update_type);
        $data = file_get_contents($intel);
        fclose($intel);
    } else {
        $data="No information known.";
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
    </tr>
</div>