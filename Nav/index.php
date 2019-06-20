<?php
	function isEmpty($var) {
		return (!isset($var) || empty($var) || trim($var)==='');
	}
	
	function toRoman($integer) {
		// Convert the integer into an integer (just to make sure)
		$integer = intval($integer);
		$result = '';

		// Create a lookup array that contains all of the Roman numerals.
		$lookup = array('M' => 1000,
		'CM' => 900,
		'D' => 500,
		'CD' => 400,
		'C' => 100,
		'XC' => 90,
		'L' => 50,
		'XL' => 40,
		'X' => 10,
		'IX' => 9,
		'V' => 5,
		'IV' => 4,
		'I' => 1);

		foreach ($lookup as $roman => $value) {
			// Determine the number of matches
			$matches = intval($integer/$value);

			// Add the same number of characters to the string
			$result .= str_repeat($roman,$matches);

			// Set the integer to be the remainder of the integer and the value
			$integer = $integer % $value;
		}

		// The Roman numeral should be built, return it
		return $result;
	}

	/** check if a string starts with another string */
	function startsWith($haystack, $needle) {
		return (substr($haystack, 0, strlen($needle)) == $needle);
	}
	
	/** check if a string ends with another string */
	function endsWith($haystack, $needle) {
		$length = strlen($needle);
		if ($length == 0) {
			return true;
		}
		return (substr($haystack, -$length) == $needle);
	}
?>
<html>
<head>
	<link rel="stylesheet" type="text/css" href="style.css">
	<link rel="stylesheet" type="text/css" href="sectors.css">
	<link rel="stylesheet" type="text/css" href="sectorEntities.css">
	<link rel="stylesheet" type="text/css" href="sectorSubCross.css">
	<link rel="stylesheet" type="text/css" href="menu.css">
	<script>
/* When the user clicks on the SYSTEM button,
 * cycle which of the menu sectors are viewed
 * there probably is a cleverer way to do this
 * but it (probably) wont be as simple I advise
 * if this needs to go to toggling more elements
 * that a cleaner soultion is looked into*/
function toggleSystemView() {
    if (document.getElementById("menuSectorsPart1").classList.contains("show")) {
      document.getElementById("menuSectorsPart1").classList.toggle("show");
      document.getElementById("systemButton").innerHTML = "SYSTEMS";
    } else {
      if (document.getElementById("menuSectorsPart2").classList.contains("show")) {
        document.getElementById("menuSectorsPart1").classList.toggle("show");
        document.getElementById("menuSectorsPart2").classList.toggle("show");
        document.getElementById("systemButton").innerHTML = "CANCEL SYSTEMS";
      } else {
        document.getElementById("menuSectorsPart2").classList.toggle("show");
        document.getElementById("systemButton").innerHTML = "MORE SYSTEMS";
      }
    }
}

// Close the dropdown if the user clicks outside of it
window.onclick = function(event) {
  if (!event.target.matches('.dropbtn')) {
    document.getElementById("systemButton").innerHTML = "SYSTEMS";

    var dropdowns = document.getElementsByClassName("dropdown-content");
    var i;
    for (i = 0; i < dropdowns.length; i++) {
      var openDropdown = dropdowns[i];
      if (openDropdown.classList.contains('show')) {
        openDropdown.classList.remove('show');
      }
    }
  }
}
	</script>
	<title>TSN Stellar Navigation Console</title>
</head>
<body style="overflow: hidden;">
	<?php
		function getGateNetworkFromSector ($sector) {
			$ret="";
			$handle=fopen("sectors/".$sector."/gateNetwork.txt","r");
			if ($handle) {
				$ret = trim(fgets($handle));
				fclose($handle);
			}
			return $ret;
		}
		if (!isEmpty($_GET['sector'])) {$sector = trim($_GET['sector']);}
		if (!isEmpty($_GET['sub'])) {$sub = trim($_GET['sub']);}
		if (!isEmpty($_GET['entType'])) {$entType = trim($_GET['entType']);}
		$sectorDir = "sectors/".$sector;

		$gateNetwork="Upper";
		if (isEmpty($_GET['gateNetwork'])) {
			if (!isEmpty($sector)) {
				$gateNetwork=getGateNetworkFromSector($sector);
			}
		} else {
			$gateNetwork = trim($_GET['gateNetwork']);
		}
		
		include 'menu.php';
		
		if (!isEmpty($sector)) {
			// read sector size from sector directory
			$sectorSize = explode(',', file_get_contents($sectorDir."/sector.txt"));
			$sectorWidth = $sectorSize[0];
			$sectorHeight = $sectorSize[1];
			if (isEmpty($sub)) {
				include 'sectorMap.php';
			} else {
				include 'sectorSubMap.php';
			}
		} else {?>
			<script>
			function systemClick(event) {
				width=event.currentTarget.clientWidth;
				origX=1654/width*event.offsetX;
				width=event.currentTarget.clientHeight;
				origY=1080/width*event.offsetY;
				clickables=[<?php
					//build the clickables array
					//there probably is a nice way to do this with JSON
					//however I do not know it
					//logic is simliar to menu.php - if that needs duplication again
					//it probably should be moved into a function
					$files= scandir("sectors", 0);
					foreach ($files as $name) {
						if ($name != "." && $name != "..") {
							if (file_exists("sectors/".$name."/mainMapPos.txt")) {
								$handle = fopen("sectors/".$name."/mainMapPos.txt", "r");
								if ($handle) {
									if (getGateNetworkFromSector($name)==$gateNetwork){
										$xy=explode(",",fgets($handle));
										if (count($xy)==2) {
											printf("{x:%d, y:%d, url:\"?sector=%s\"},",$xy[0],$xy[1],$name);
										}
									}
									fclose($handle);
								}
							}
						}
					}
					?>];
					for (i=0; i<clickables.length; i++) {
					deltaX=origX-clickables[i].x;
					deltaY=origY-clickables[i].y;
					delta=Math.sqrt((deltaX*deltaX)+(deltaY*deltaY));
					if (delta<50) {
						window.open(clickables[i].url,"_self");
					}
				}
			}
			</script>
			<div>
				<?php if ($gateNetwork=="Lower") {?>
					<img onClick="systemClick(event)" max-height="100%" max-width="100%" z-index="-1" position="absolute" bottom="0px" right="0px" src="img/gateNetworkLower.png"/>
				<?php } else {?>
					<img onClick="systemClick(event)" max-height="100%" max-width="100%" z-index="-1" position="absolute" bottom="0px" right="0px" src="img/gateNetworkUpper.png"/>
				<?php }?>
			</div>
			<div style="position:absolute;top:10px;right:20px;">
				Stellar Cartography TSN 11.0
			</div><?php
		}
	?>
</body>
</html>
