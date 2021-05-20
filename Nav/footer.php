
<?php

    if (isEmpty($sub) && isEmpty($sector)) {
        $versionStyle = "position: absolute; bottom: 0px; left: 0px; padding: 8px;";
    } else {
        $versionStyle = "display: flex; padding-bottom: 15px; padding-left: 10px; flex-grow: 0; flex-shrink: 0;";
    }
    

?>
<style>
    .noShrink {
        flex-shrink: 0;
    }
</style>
<!--<div id="buttons" style="<?=$versionStyle?>/*position:absolute;bottom:0px;right:0px;flex: 0 0 50px;*/">-->
<div id="footer" style="<?=$versionStyle?>">
    <p style="margin: 0px; padding: 0px; margin-top: auto; padding-right: 10px;" class="noShrink">
    Stellar Cartography <?php if ($classified) {printf("ONI");} else {printf("TSN");}?> <?=$version?>
    </p>
    <p style="flex-grow: 1;"></p>
    <div class="" style="display: flex; flex-wrap: wrap-reverse; justify-content: flex-end;">
        
<?php
    if (!isEmpty($sector)) {
	if (isEmpty($sub)) { 
		$onclick = "onclick=\"location.href='index.php?".$classifiedHref."sector=".$sector."&entType=";
		$onclickStations = $onclick."stations'\"";
		$onclickGates = $onclick."gates'\"";
		$onclickOther = $onclick."other'\"";
		
		if (empty($entStations)) {
			$onclickStations = "";
			$classStations = " disabled";
		} else if ($entType == "stations") {
			$classStations = " active";
		} else {
			$classStations = "";
		}
		
		if (empty($entGates)) {
			$onclickGates = "";
			$classGates = " disabled";
		} else if ($entType == "gates") {
			$classGates = " active";
		} else {
			$classGates = "";
		}
		
		if (empty($entOther)) {
			$onclickOther = "";
			$classOther = " disabled";
		} else if ($entType == "other") {
			$classOther = " active";
		} else {
			$classOther = "";
		}?>
                    
			<button <?=$onclickStations?> class="dropbtn noShrink <?=$classStations?>">STATIONS</button>
			<button <?=$onclickGates?> class="dropbtn noShrink <?=$classGates?>">GATES</button>
			<button <?=$onclickOther?> class="dropbtn noShrink <?=$classOther?>">OTHER</button>
                    
		<?php
	} else {?>
                        <button onclick="location.href='index.php?<?=$classifiedHref?>sector=<?=$sector?>'" class="dropbtn noShrink">TO SYSTEM</button>
                <?php 
        }
        ?>
        <button id="toggle-button" class="dropbtn active noShrink">TOGGLE DATA</button>
    <?php
    }
        
?>
    </div>
</div>



<!--    <div id="navcon-title" style="<?=$versionStyle?>">
        
    </div>-->
    <?php 
        if ($sector == false && $gateNetwork === "Lower") {
    ?>
        <!--<img id="compass" src="img/galactic-compass.png" class="show" style="position: absolute; top: 0px; right: 0px; z-index: -1; width: 30vw;"/>-->
        <img id="compass" src="img/CompassSimple.png" class="show" style="position: absolute; top: 0px; right: 0px; z-index: -1; width: 25vw;"/>
        <img id="legend" src="img/legend.png" style="position: absolute; bottom: 0px; right: 0px; z-index: 0;/**Definitely leave this on top**/ width: 20vh;"/>

    <?php
        }?>
    <img id="battle-lines-legend" src ="img/BattleLinesLegend.png" style="display: none;"/>
    <style>
        #battle-lines-legend {
                position: absolute; 
                bottom: 30px; 
                left: 0px; 
                z-index: 0; 
                width: 35vh;
        }
    </style>