<?php
	// read entities
	$entGates = array();
	$entStations = array();
	$entOther = array();

	foreach (readEntitesFile($classified,$sector) as $line) {
		$entity = explode(',', $line);

		/* add type to entity line, if available */
		$checkExperimental = 0;
		if ($entity[1] == "W") {
			$entity[3] = "Weapon Platform";
		} else if ($entity[1] == "Y") {
			$entity[3] = "Shipyard";
		} else if ($entity[1] == "R") {
			$entity[3] = "Research";
		} else if ($entity[1] == "I") {
			$entity[3] = "Industry";
		} else if ($entity[1] == "M") {
			$entity[3] = "Mining";
		} else if ($entity[1] == "C") {
			$entity[3] = "Command Post";
		} else if ($entity[1] == "D") {
			$entity[3] = "Defence";
		} else if (startsWith($line, "BH")) {
			$entity[3] = "Gravitational Singularity";
		} else if (startsWith($line, "WP")) {
			$entity[3] = "Weapon Platform";
		} else if (startsWith($line, "DS-")) {
			$entity[3] = "Deep Space Station";
		} else if (startsWith($line, "SY-")) {
			$entity[3] = "Ship Yard";
			$checkExperimental = 1;
		} else if (startsWith($line, "RS-")) {
			$entity[3] = "Research Station";
			$checkExperimental = 1;
		} else if (startsWith($line, "I-")) {
			$entity[3] = "Industrial Station";
			$checkExperimental = 1;
		} else if (startsWith($line, "M-")) {
			$entity[3] = "Mining Station";
			$checkExperimental = 1;
		} else if (startsWith($line, "CP-")) {
			$entity[3] = "Command Post";
		} else if ($entity[1] == "O") {
			$entity[3] = "Independent Enterprise";
		} else if ($entity[1] == "P") {
			$entity[3] = "Planet";
		}
		if ($checkExperimental == 1 && endsWith($entity[0], "X")) {
			$entity[3] = $entity[3].", experimental";
		}
		if (startsWith($line, $sector." Command")) {
			$entity[3] = "Sector Command";
		}


		$entity[2] = trim($entity[2]);
		if (isEmpty($sub) || $entity[2] == $sub) {
			if ($entity[1] == "S") {
				$entStations[$entity[0]] = $entity;
			} else if ($entity[1] == "R") {
				$entStations[$entity[0]] = $entity;
			} else if ($entity[1] == "I") {
				$entStations[$entity[0]] = $entity;
			} else if ($entity[1] == "M") {
				$entStations[$entity[0]] = $entity;
			} else if ($entity[1] == "D") {
				$entStations[$entity[0]] = $entity;
			} else if ($entity[1] == "C") {
				$entStations[$entity[0]] = $entity;
			} else if ($entity[1] == "G") {
				$entGates[$entity[0]] = $entity;
			} else {
				$entOther[$entity[0]] = $entity;
			}
		}
	}

	// Sort by name
	ksort($entStations);
	ksort($entGates);
	ksort($entOther);

	// default enttyp to first non-empty set
	if (isEmpty($sub) && isEmpty($entType)) {
		if (!empty($entStations)) {
			$entType = "stations";
		} else if (!empty($entOther)) {
			$entType = "other";
		} else if (!empty($entGates)) {
			$entType = "gates";
		}
	}

	function createGateButton($classified, $target, $source, $classifiedHref) {
		// If target equals e.g. "Atlantis Gate", retrieve the "Atlantis" string
		$target = trim(explode('Gate', $target)[0]);
		
		// Convert double names
		if ($target == "Poseidon") $target = "Poseidon Rift";
		if ($source == "Poseidon Rift") $source = "Poseidon";
		if ($target == "Euphini") $target = "Euphini Expanse";
		if ($source == "Euphini Expanse") $source = "Euphini";
		
		// Get files
		$targetSize = lookupClassifiedFile($classified,"sectors/".$target."/sector.txt");
		
		// Read Sector Size
		if (file_exists($targetSize)) {
			$handle = fopen($targetSize, "r");
			if ($handle) {
				$line = fgets($handle);
				$sizeArray = explode(',', $line);
				
				$size = $sizeArray[0] * $sizeArray[1];
			} else {
				$size = 0;
			}
		} else {
			$size = 0;
		}
		
		// Read entities and create button
		if (!isEmpty($source)) {
			foreach (readEntitesFile($classified,$target) as $line) {
				$entity = explode(',', $line);

				if ($entity[1] == "G" && startsWith($entity[0], $source)) {
					$targetSub = trim($entity[2]);

					$onClick = "onclick=\"location.href='index.php?".$classifiedHref."sector=".$target."&sub=".$targetSub."'\"";
					$title = strtoupper($target);
					if ($size > 1) {
						$title = $title." - ".toRoman($targetSub);
					}
					return "<button ".$onClick." class=\"dropbtn\">".$title."</button>";
				}
			}
		}
		return "<button class=\"dropbtn disabled\">".strtoupper($target)."</button>";
	}
?>
<div class="data">
	<table class="data">
		<tr style="height: 50px;">
			<td colspan="2" style="color:#fc5555;"><?php
				echo strtoupper($sector);
				if (!isEmpty($sub) && ($sectorWidth * $sectorHeight > 1)) {
					echo " - ".toRoman($sub);
				}?>
			</td>
			<td style="text-align:right;"><?php
				if (!isEmpty($sub) && ($sectorWidth * $sectorHeight > 1)) {?>
					<button onclick="location.href='index.php?<?=$classifiedHref?>sector=<?=$sector?>'" class="dropbtn">TO SYSTEM</button><?php
				} else {
					echo " ";
				}?>
			</td>
		</tr><?php
		
		$workingEntType = "stations";
		include 'sectorEntitiesList.php';
		
		$workingEntType = "gates";
		include 'sectorEntitiesList.php';
		
		$workingEntType = "other";
		include 'sectorEntitiesList.php';?>
	</table>
</div>
<?php
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
		
		<div style="position:absolute;bottom:0px;right:0px;">
			<button <?=$onclickStations?> class="dropbtn <?=$classStations?>">STATIONS</button>
			<button <?=$onclickGates?> class="dropbtn <?=$classGates?>">GATES</button>
			<button <?=$onclickOther?> class="dropbtn <?=$classOther?>">OTHER</button>
		</div><?php
	}
?>
