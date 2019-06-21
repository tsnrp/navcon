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
	<button onclick="location.href='http://www.1sws.com\\Intel\\NavClassified\\index.php'" class="dropbtn<?=isEmpty($sector) ? " active" : ""?>">INTEL</button><?php
		//split the menu so that it doesnt become too long
		$files = scandir("sectors", 0);
		$sectorList=array();
		foreach ($files as $name) {
			if ($name != "." && $name != ".." && $name != "desktop.ini") {
				array_push($sectorList,$name);
			}
		}
		asort($sectorList);
		//note array_chunk may turn out to be the wrong call
		//for instance if we decide 7 sectors need to be devided over 3 it will be
		//3,3,1 rather than the more logical 3,2,2
		//lets fix that when it becomes an issue
		$amountOfSystemMenus=2;
		$menus=array_chunk($sectorList,ceil(count($sectorList)/$amountOfSystemMenus));
		for ($i=0; $i!=$amountOfSystemMenus; $i++) {
			?><div id="menuSectorsPart<?php printf($i+1)?>" class="dropdown-content opaque">
			<?php foreach ($menus[$i] as $name) {?>
				<div class="dropdown-entry<?=(!isEmpty($sector) && $name == $sector) ? " selected" : ""?>">
					<a href="?sector=<?=$name?>"><?=strtoupper($name)?></a>
				</div>
				<?php }?>
			</div><?php
		}
		?>
</div>
