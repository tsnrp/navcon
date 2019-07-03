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

	function getAllSystems($classified) {
		$files = scandir("sectors", 0);
		if ($classified) {
			$files = array_unique(array_merge($files,scandir("classified/sectors",0)));
		}
		$sectorList=array();
		foreach ($files as $name) {
			if ($name != "." && $name != ".." && $name != "desktop.ini") {
				array_push($sectorList,$name);
			}
		}
		asort($sectorList);
		return $sectorList;
	}

	//returns an array of arrays where each array is a menu to be shown
	function getSystemsMenus($classified) {
		$sectorList=getAllSystems($classified);
		// 12 is roughly right for a 768 height screen
		// however this was tested on a machine that had a signifcantly different display
		// (27 inch) 2560x1440, resized
		// its possible that if it was really on a machine with such a small display menus would of been resized
		// and/or the display scaling would be on
		// as such it may be worth trying to find someone with the worst display we want to support and check we cant raise it
		$maxMenuSize=12;
		$amountOfSystemMenus=ceil(count($sectorList)/$maxMenuSize);

		//note array_chunk may turn out to be the wrong call
		//for instance if we decide 7 sectors need to be devided over 3 it will be
		//3,3,1 rather than the more logical 3,2,2
		//lets fix that when it becomes an issue
		return array_chunk($sectorList,ceil(count($sectorList)/$amountOfSystemMenus));
	}

	function lookupClassifiedFile($classified,$file) {
		if ($classified) {
			$classifiedFile="classified/".$file;
			if (file_exists($classifiedFile)) {
				return $classifiedFile;
			} else {
				return $file;
			}
		} else {
			return $file;
		}
	}

	function readEntitesFile($classified,$sector) {
		$file=lookupClassifiedFile($classified,"sectors/".$sector."/entities.txt");
		if (file_exists($file)) {
			$entities=explode("\n",file_get_contents($file));
			$ret=array();
			foreach ($entities as $line) {
				$line=explode(",",$line);
				$entity=array();
				$entity['name']=$line[0];
				$entity['type']=$line[1];
				$entity['loc']=$line[2];
				$isClassified=(isset($line[3]) && $line[3]=="Classified");
				if (!$isClassified || $classified) {
					array_push($ret,$entity);
				}
			}
			return $ret;
		}
		return array();
	}

	function getSectorInfo($classified,$sector) {
		$file=lookupClassifiedFile($classified,"sectors/".$sector."/sector.txt");
		if (file_exists($file)) {
			$ret=array();
			$file_contents=explode(',',file_get_contents($file));
			$ret['network']=$file_contents[0];
			$ret['x']=$file_contents[1];
			$ret['y']=$file_contents[2];
			return $ret;
		}
		return array();
	}

	$sub = isset($_GET['sub']) ? trim($_GET['sub']) : "";
	$entType = isset($_GET['entType']) ? trim($_GET['entType']) : "";

	$gateNetwork= isset($_GET['gateNetwork']) ? trim($_GET['gateNetwork']) : "Upper";

	$classified = isset($_GET['Classified']);
	$classifiedHref = isset($_GET['Classified'])? "Classified&" : "" ;

	//if passwords are stored on disc it can be tricky (as an understatement) to do them securely
	//even if the password is unimportant (as it is in this case)
	//the ever present reuse of passwords means it probably needs to be done properly
	//to prevent people putting a important password in here then complaining when it gets leaked
	//we are only going to have a fixed password
	$requestPassword=false;
	if ($classified) {
		$requestPassword=true;
		if (isset($_COOKIE['passwordOK'])) {
			$requestPassword=false;
		} else if (isset($_POST['pass']) && $_POST['pass']=="ONI-2F4L") {
			setcookie('passwordOK',"true",time()+60*60*24*365*10);//expires 10 years into the future
			$requestPassword=false;
		} else {
			$classified=false;
			$classifiedHref="";
		}
	}

	$sector ="";
	if (isset($_GET['sector'])) {
		$sector=$_GET['sector'];
		$gateNetwork=getSectorInfo($classified,$sector)['network'];
		$gateButtonDest=$gateNetwork;
		$gateNetText = ($gateNetwork=='Upper') ? "VIEW UPPER ARC" : "VIEW LOWER ARC";
	} else {
		$gateButtonDest = $gateNetwork=="Upper" ? "Lower" : "Upper";
		$gateNetText = $gateNetwork=="Upper" ? "VIEW LOWER ARC" : "VIEW UPPER ARC";
	}

	$menus=getSystemsMenus($classified);
?>
<html>
<head>
	<link rel="stylesheet" type="text/css" href="style.css">
	<link rel="stylesheet" type="text/css" href="sectors.css">
	<link rel="stylesheet" type="text/css" href="sectorEntities.css">
	<link rel="stylesheet" type="text/css" href="sectorSubCross.css">
	<link rel="stylesheet" type="text/css" href="menu.css">
	<script>
function toggleSystemView() {
	var toggled=false;
		<?php	// the code here is ugly, and it generates ugly code
			// if I knew more javascript there probably is a nice soultion
			// however I dont and so you get ugly code
		for ($i=count($menus);$i!=0;$i--) {?>
			if (document.getElementById("menuSectorsPart<?php printf($i);?>").classList.contains("show")) {
				document.getElementById("menuSectorsPart<?php printf($i);?>").classList.toggle("show");
				<?php if ($i!=count($menus)) { ?>
				document.getElementById("menuSectorsPart<?php printf($i+1);?>").classList.toggle("show");
				<?php } ?>
				<?php if (($i+1)==count($menus)) {?>
					document.getElementById("systemButton").innerHTML = "CANCEL SYSTEMS";
				<?php } else if ($i==count($menus)) {?>
					document.getElementById("systemButton").innerHTML = "SYSTEMS";
				<?php }?>
				toggled=true;
			}
		<?php }?>
	if (toggled==false) {
		document.getElementById("menuSectorsPart1").classList.toggle("show");
		document.getElementById("systemButton").innerHTML = "MORE SYSTEMS";
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
		//menu?>
		<div class="dropdown">
		<button onclick="toggleSystemView()" id="systemButton" class="dropbtn">SYSTEMS</button>
		<button onclick="location.href='index.php?<?=$classifiedHref?>gateNetwork=<?php printf($gateButtonDest) ?>'" class="dropbtn<?=isEmpty($sector) ? " active" : ""?>"><?php printf($gateNetText);?></button>
		<?php
		//it would be kind of nice if this tracked what sub page you where on
		//but that seems like effort
		//effort that I suspect no one is too fussed by
		if ($classified) {?>
			<button onclick="location.href='index.php'" class="dropbtn active">INTEL</button>
		<?php } else { ?>
			<button onclick="location.href='index.php?Classified'" class="dropbtn">INTEL</button>
		<?php
		}
		for ($i=0; $i!=count($menus); $i++) {
			?><div id="menuSectorsPart<?php printf($i+1)?>" class="dropdown-content opaque">
			<?php foreach ($menus[$i] as $name) {?>
				<div class="dropdown-entry<?=(!isEmpty($sector) && $name == $sector) ? " selected" : ""?>">
					<a href="?<?=$classifiedHref?>sector=<?=$name?>"><?=strtoupper($name)?></a>
				</div>
				<?php }?>
			</div><?php
			}?>
		</div><?php

		if ($requestPassword) {?>
			<br>Please enter ONI security clearance
			<form action="index.php?Classified" method="post">
			<input type="text" name="pass"><br>
			<input type="submit" value="authenticate me">
			</form>
			<br><?php
		} else {
			if (!isEmpty($sector)) {
				$sectorSize = getSectorInfo($classified,$sector);
				$sectorWidth = $sectorSize['x'];
				$sectorHeight = $sectorSize['y'];
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
						$files=getAllSystems($classified);
						foreach ($files as $name) {
							if ($name != "." && $name != "..") {
								$mapPos = lookupClassifiedFile($classified,"sectors/".$name."/mainMapPos.txt");
								if (file_exists($mapPos)) {
									$handle = fopen(lookupClassifiedFile($classified,"sectors/".$name."/mainMapPos.txt"), "r");
									if ($handle) {
										if (getSectorInfo($classified,$name)['network']==$gateNetwork){
											$xy=explode(",",fgets($handle));
											if (count($xy)==2) {
												printf("{x:%d, y:%d, url:\"?%ssector=%s\"},",$xy[0],$xy[1],$classifiedHref,$name);
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
					<?php $gateImg="img/gateNetwork".$gateNetwork.".png";
					$gateImg=lookupClassifiedFile($classified,$gateImg);?>
					<img onClick="systemClick(event)" max-height="100%" max-width="100%" z-index="-1" position="absolute" bottom="0px" right="0px" src="<?=$gateImg?>"/>
				</div>
				<div style="position:absolute;top:10px;right:20px;">
					Stellar Cartography <?php if ($classified) {printf("ONI");} else {printf("TSN");}?> 11.0
				</div><?php
			}
		}
	?>
</body>
</html>
