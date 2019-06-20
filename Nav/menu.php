<?php
	if (!isEmpty($sector)) {
		if ($gateNetwork=="Upper") {
			$gateButtonDest="Upper";
			$gateNetText="VIEW UPPER ARC";
		} else {
			$gateButtonDest="Lower";
			$gateNetText="VIEW LOWER ARC";
		}
	} else {
		if ($gateNetwork=="Upper") {
			$gateButtonDest="Lower";
			$gateNetText="VIEW LOWER ARC";
		} else {
			$gateButtonDest="Upper";
			$gateNetText="VIEW UPPER ARC";
		}
	}
?>
<div class="dropdown">
	<button onclick="toggleSystemView()" id="systemButton" class="dropbtn">SYSTEMS</button>
	<button onclick="location.href='index.php?gateNetwork=<?php printf($gateButtonDest) ?>'" class="dropbtn<?=isEmpty($sector) ? " active" : ""?>"><?php printf($gateNetText);?></button>
  <button onclick="location.href='http://www.1sws.com\\Intel\\NavClassified\\index.php'" class="dropbtn<?=isEmpty($sector) ? " active" : ""?>">INTEL</button>
	<div id="menuSectorsPart1" class="dropdown-content opaque"><?php
		$files = scandir("sectors", 0);
		// we split the files into two lists so as to make it not too long
		// if this needs in the future to be split into 3+ it may make sense to
		// make an array of lists and just loop over that
		$sectorList=array();
		foreach ($files as $name) {
			if ($name != "." && $name != ".." && $name != "desktop.ini") {
				array_push($sectorList,$name);
			}
		}
		//if editing - ***NOTE*** - the js/html is duplicated on the second loop - edit both
		foreach (array_slice($sectorList,count($sectorList)/2) as $name) {?>
			<div class="dropdown-entry<?=(!isEmpty($sector) && $name == $sector) ? " selected" : ""?>">
				<a href="?sector=<?=$name?>"><?=strtoupper($name)?></a>
			</div><?php
		}?>
		</div>
		<div id="menuSectorsPart2" class="dropdown-content opaque"><?php
		foreach (array_slice($sectorList,0,count($sectorList)/2) as $name) {?>
			<div class="dropdown-entry<?=(!isEmpty($sector) && $name == $sector) ? " selected" : ""?>">
				<a href="?sector=<?=$name?>"><?=strtoupper($name)?></a>
			</div><?php
		}?>
	</div>
</div>
