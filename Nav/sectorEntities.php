<?php
	// read entities
	$entGates = array();
	$entStations = array();
	$entOther = array();

	foreach (readEntitesFile($classified,$sector) as $entity) {
		/* add type to entity line, if available */
		$checkExperimental = 0;
		if ($entity['type'] == "W") {
			$entity['description'] = "Weapon Platform";
		} else if ($entity['type'] == "Y") {
			$entity['description'] = "Shipyard";
		} else if ($entity['type'] == "R") {
			$entity['description'] = "Research";
		} else if ($entity['type'] == "I") {
			$entity['description'] = "Industry";
		} else if ($entity['type'] == "M") {
			$entity['description'] = "Mining";
		} else if ($entity['type'] == "C") {
			$entity['description'] = "Command Post";
		} else if ($entity['type'] == "D") {
			$entity['description'] = "Defense";
                } else if ($entity['type'] == "DP") {
                        $entity['description'] = "Defense Platform";
		} else if ($entity['type'] == "V") {
			$entity['description'] = "Civilan Station";
		} else if ($entity['type'] == "X") {
			$entity['description'] = "Science Station Post";
		} else if ($entity['type'] == "F") {
			$entity['description'] = "Refinery";
		} else if ($entity['type'] == "B" || $entity['type'] == "SB") {
			$entity['description'] = "Sensor Buoy";
                } else if ($entity['type'] == "CR") {
                        $entity['description'] = "Comms Relay";
		} else if ($entity['type'] == "H") {
			$entity['description'] = "Gravitational Singularity";
                } else if ($entity['type'] == "SKN-B") {
                        $entity["description"] = "Skaraan Base";
                } else if ($entity['type'] == "SHIP") {
                        $entity['description'] = "Ship";
		} else if (startsWith($entity['name'], "BH")) {
			$entity['description'] = "Gravitational Singularity";
		} else if (startsWith($entity['name'], "WP")) {
			$entity['description'] = "Weapon Platform";
		} else if (startsWith($entity['name'], "DS-") || $entity['type'] == "DS") {
			$entity['description'] = "Deep Space Station";
		} else if (startsWith($entity['name'], "SY-")) {
			$entity['description'] = "Ship Yard";
			$checkExperimental = 1;
		} else if (startsWith($entity['name'], "BY-")) {
			$entity['description'] = "Sensor Buoy";
		} else if (startsWith($entity['name'], "CR-") || startsWith($entity['name'], "LCR-")) {
			$entity['description'] = "Comms Relay";
		} else if (startsWith($entity['name'], "RS-")) {
			$entity['description'] = "Research Station";
			$checkExperimental = 1;
		} else if (startsWith($entity['name'], "I-")) {
			$entity['description'] = "Industrial Station";
			$checkExperimental = 1;
		} else if (startsWith($entity['name'], "M-")) {
			$entity['description'] = "Mining Station";
			$checkExperimental = 1;
		} else if (startsWith($entity['name'], "CP-")) {
			$entity['description'] = "Command Post";
		} else if ($entity['type'] == "O") {
			$entity['description'] = "Independent Enterprise";
		} else if ($entity['type'] == "P") {
			$entity['description'] = "Planet";
                } else if ($entity['type'] == "S") {
                        $entity['description'] = "Station";
		} else if ($entity['type'] == "MK") {
                        $entity['description'] = "Marker Buoy";
                }
		if ($checkExperimental == 1 && endsWith($entity['name'], "X")) {
			$entity['description'] = $entity['description'].", experimental";
		}
		if (startsWith($entity['name'], $sector." Command")) {
			$entity['description'] = "Sector Command";
		}

                $stationTypes = array(
                    "S",
                    "R",
                    "I",
                    "M",
                    "D",
                    "V",
                    "C",
                    "DS"
                );
		$entity['loc'] = trim($entity['loc']);
		if (isEmpty($sub) || $entity['loc'] == $sub) {
                        $found = false;
                        foreach ($stationTypes as $ent) {
                            if ($entity['type'] == $ent) {
                                $entStations[$entity['name']] = $entity;
                                $found = true;
                                break;
                            }
                        }
                        if ($found) {
                            // do nothing
                        } else if ($entity['type'] == "G") {
				$entGates[$entity['name']] = $entity;
			} else {
				$entOther[$entity['name']] = $entity;
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

		// Read entities and create button
		if (!isEmpty($source)) {
			foreach (readEntitesFile($classified,$target) as $entity) {
				if ($entity['type'] == "G" && startsWith($entity['name'], $source)) {
					$targetSub = trim($entity['loc']);

					$onClick = "onclick=\"location.href='index.php?".$classifiedHref."sector=".$target."&sub=".$targetSub."'\"";
					$title = strtoupper($target);
					if (file_exists(lookupClassifiedFile($classified,"sectors/".$target."/sector.txt"))) {
						$title = $title." - ".toRoman($targetSub);
					}
					return "<button ".$onClick." class=\"dropbtn\">".$title."</button>";
				}
			}
		}
		return "<button class=\"dropbtn disabled\" style=\"margin-left: 25px;\">".strtoupper($target)."</button>";
	}
?>




    <table class="data" style="white-space: nowrap;">
    <?php
        $workingEntType = "stations";
        include 'sectorEntitiesList.php';

        $workingEntType = "gates";
        include 'sectorEntitiesList.php';

        $workingEntType = "other";
        include 'sectorEntitiesList.php';?>
    </table>
    <?php
        $dat = readIntelFile($classified, $sector);
        if ($classified && $intelDoc) {?>
            <button onclick="<?=$dat?>" class=\"dropbtn\">"Intel Doc"</button>
        <?php
        }
    ?>

            