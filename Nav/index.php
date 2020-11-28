<?php
	session_start();	
	//---- RELEASE VERSION ----//
        $version = "13.2";
        
        $battleNet = "https://cdn.discordapp.com/attachments/741744298072211537/782226715266383872/gateNetworkLowerBattleCurrent.png";
        
        //TODO: use filter_input() on these
        $sub = isset($_GET['sub']) ? trim($_GET['sub']) : "";
	$entType = isset($_GET['entType']) ? trim($_GET['entType']) : "";

	$gateNetwork= isset($_GET['gateNetwork']) ? trim($_GET['gateNetwork']) : "Lower";

	$classified = isset($_GET['Classified']);
	$classifiedHref = isset($_GET['Classified'])? "Classified&" : "" ;
        
        $systems = getAllSystems($classified);

        // TODO: Looks like issue here....
        
        //$uri = filter_input($_SERVER["QUERY STRING"], FILTER_SANITIZE_URL); // Doesn't work rn
        $t = parse_str($_SERVER["QUERY_STRING"], $params);
        $newQuery = http_build_query($params);
//        $fp = fopen("./../../log.txt",'a');
//        fwrite($fp, "\nQuery_String: ".$_SERVER["QUERY_STRING"]);
//        fwrite($fp, "\nNew Query index.php: ".$newQuery);
//        fclose($fp);
        

        // This bit (hopefully) forces the client to refresh their cache
        if (isset ($_GET["cc"])) {
            if (filter_input(INPUT_GET, "cc", FILTER_SANITIZE_STRING)) {
                session_cache_limiter('private');
                session_cache_expire(0);
            }
        }
        
        // Determine if this is master or TestNav branch based on directory.
        try {
            $u = dirname_r(__DIR__, 1);
            $v = strripos($u, "/") + 8;
            $update_type = substr($u,$v);
            //echo $update_type;
        } catch(Exception $e) {
            $update_type = "master";
            echo $e->getMessage();
        }
        //echo $update_type; // NavTest or master
	//$update_type = "master";
        $redirect = false;
	// Actually starts things
	if (sessionUpdate()) {
	    if (checkForUpdate()) {
	    //    echo "True";
		//echo "update needed";
		redirectWithQuery();
		exit();
	    } //else Continue with your book report

	}

	function dirname_r($path, $count=1){
	    if ($count > 1){
	       return dirname(dirname_r($path, --$count));
	    }else{
	       return dirname($path);
	    }
	}
        
        
        
	function checkForUpdate() {
            global $update_type;
	    $dir1 = dirname_r(__DIR__, 2);
	   
	    if (!file_exists($dir1."./".$update_type."saved.txt")) {
		    return true;
	    }
	    // else, continue
	    $saved = fopen($dir1."./".$update_type."saved.txt", "r") or die("Unable to open file");
	    fgets($saved);// Passoword Unused here, function called so update date can be read
	    $last_update = fgets($saved);
	    fclose($saved);

	    $commitDate = getLatestCommit();
	    return $commitDate != $last_update;
	}

	


	// returns true if an update check is needed.
	function sessionUpdate() {
	    $d = date("U");
            if(isset($_SESSION["lastUpdateCheck"])) {
                $luc = $_SESSION["lastUpdateCheck"];
            } else {
                $luc = 0;
            }
            
//	    echo "<br>luc = ";
//	    echo $luc;
//            echo "<br>";
	    if ($luc === false || $luc === null) {
		$_SESSION["lastUpdateCheck"] = $d;
		return true; // Cause updateCheck to happen
	    } else {
		$hour = 60*60;//seconds*minutes
		$luc += $hour;
		if ($luc < $d) {
		    $_SESSION["lastUpdateCheck"] = $d;
		    return true;
		} else {
		    return false;
		}
	    }
	}

	// Gets time of last commit to whichever branch
	// Default is master of course
	function getLatestCommit() {
            global $update_type;
            $fp = fopen('./../../log.txt','a');
            fwrite($fp,"\nupdate type (getLatestCommit(): ".$update_type);
            fclose($fp);
	    $context = stream_context_create(
		array(
		    "http" => array(
			"header" => "User-Agent: Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/50.0.2661.102 Safari/537.36"
		    )
		)
	    );
	    $url = "https://api.github.com/repos/tsnrp/navcon/commits/".$update_type;
	    $json = file_get_contents($url, false, $context);
	    $arr = json_decode($json, true);
	    $date = $arr["commit"]["committer"]["date"];
	    return $date;
	}
        
        // Redirects with the fancy extra stuff on the end of the url
	function redirectWithQuery() {
            global $update_type;
            $params;
            global $newQuery;
	    //$dir1 = dirname(__DIR__,2);
	    if (strlen($newQuery)>0) {
		$r = "./../../NavUpdate.php?".$newQuery."&update_type=".$update_type; // This may need changed someday
	    } else {
		$r = "./../../NavUpdate.php?update_type=".$update_type;
	    }
	//    echo "Redirecting to...".$r;
	//    exit();
	    header("Location: ".$r, TRUE, 303);
	    exit();
	}



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
				$line=str_replace("\r","",$line);
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
			$ret['x']=(int)$file_contents[1];
			$ret['y']=(int)$file_contents[2];
			return $ret;
		}
		return array();
	}

	

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
        <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
        <style>
            /*
            At some point should move this to sectorEntities.css, but I don't want people to have to deal with cache clearing atm.
            */
            table.data tr.entity:hover {
                background-color: #0033cc;
            }
            table.data tr.highlight:hover {
                background-color: #0033cc;
            }
        </style>
        <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
        <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
	<script>    
function toggleSystemView() {
    document.getElementById("search-bar").value = "";
    document.getElementById("system-menu").classList.toggle("show");
    try {
        document.getElementById("arc-map").classList.toggle("show");
    } catch (e) {
        console.log(e.toString());
    }
    var buttons = document.getElementsByClassName("systemButton");
    for (var i = 0; i < buttons.length; i++) {
        buttons[i].classList.add("show");
    }
    
}

function systemSearch() {
    //var systemList = <?php echo json_encode($systems);?>;
    //console.log(systemList);
    var searchBar = document.getElementById("search-bar");
    var input = searchBar.value;
    console.log(input);
    var buttonList = document.getElementsByClassName("systemButton");
    try {
        document.getElementById("gateNet").classList.remove("show");
    } catch (e) {
        console.log(e);
    }
    document.getElementById("system-menu").classList.add("show");
    for (var i = 0; i < buttonList.length; i++) {
        if (buttonList[i].innerHTML.toUpperCase().indexOf(input.toUpperCase()) !== -1) {
            buttonList[i].classList.add("show");
        } else if (input === "") {
            buttonList[i].classList.add("show");
        } else {
            buttonList[i].classList.remove("show");
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
        dropdowns[i].classList.remove('show');
    }
    
    document.getElementById("system-menu").classList.remove("show");
    try {
        document.getElementById("gateNet").classList.add("show");
    } catch (e) {
        console.log(e.toString());
    }
    // make all buttons visible (but not the div containing them, so they aren't actually visible)
    var buttons = document.getElementsByClassName("systemButton");
    for (var i = 0; i < buttons.length; i++) {
        buttons[i].classList.add("show");
    }
    // Return search parameter to placeholder text
    document.getElementById("search-bar").value = "";
  }
};

function buttonClick(system) {
    location.href = "index.php?sector=" + system;
}

function setupSystemMenu() {
    var div = document.getElementById("system-menu");
    var systemList = <?php echo json_encode($systems);?>;
    for (var i = 0; i < systemList.length; i++) {
        var but = document.createElement("button");
        but.innerHTML = systemList[i];
        but.className = "systemButton dropdown-entry show";
        but.setAttribute("onClick", "buttonClick('" + systemList[i] + "')");
        div.appendChild(but);
    }
}
// These set the height of #sys-dat, which contains the entities pane and map. It keeps them from covering the footer and accounts for wrapping of the buttons both on top and bototm.
function setMapHeight() {
    var h = window.innerHeight - 38 - $("#buttons").height() - $("#sector-menu").height();
    $("#sys-dat").css("height",h);
}
$(function() {
    setMapHeight();
    $(window).on("resize", function() {
        setMapHeight();
    });
});

	</script>
	<title>TSN Stellar Navigation Console</title>
</head>
<body style="overflow: hidden;">
	<?php
		//menu?>
    
                
                <span></span>
		<div id="sector-menu" class="dropdown" style="z-index:1;">
                    <button onclick="toggleSystemView()" id="systemButton" class="dropbtn">SYSTEMS</button>
                    <button onclick="location.href='index.php?<?=$classifiedHref?>gateNetwork=<?php printf($gateButtonDest) ?>'" class="dropbtn<?=isEmpty($sector) ? " active" : ""?>"><?php printf($gateNetText);?></button>
                    <?php
                    $intelButtonActiveText=$classified ? " active" : "" ;
                    $getString= $classified ? "?" : "?Classified&";
                    $getString.=isset($_GET['gateNetwork']) ? "gateNetwork=".$_GET['gateNetwork']."&" : "";
                    $getString.=isset($_GET['sector']) ? "sector=".$_GET['sector']."&" : "";
                    $getString.=isset($_GET['sub']) ? "sub=".$_GET['sub']."&" : "";
                    $getString.=isset($_GET['entType']) ? "entType=".$_GET['entType']."&" : "";
                    if ($getString=="?") {
                            $getString="";
                    } else {
                            $getString=substr($getString,0,-1);
                    }
                    echo("<button onclick=\"location.href='index.php$getString'\" class=\"dropbtn$intelButtonActiveText\">INTEL</button>");
                    ?>

                    <input type="text" name="search" id="search-bar" onkeyup="systemSearch()" placeholder="Search for system...">
                    <!--button class="dropbtn">Search</button-->
                    <?php
                    if (!isset($_GET['sector']) && !isset($_GET['sub']) && $gateNetwork === "Lower") {
                        ?><button id="publicIntelButton" class="dropbtn">BATTLE LINES</button><?php
                    }
                    ?>
		</div>
                <div id="system-menu" class="system-menu">
                                    <!--This is where the buttons will go-->
                </div>        
                <?php

		if ($requestPassword) {?>
			<br>Please enter ONI security clearance
			<form action="index.php?Classified" method="post">
			<input type="text" name="pass"><br>
			<input type="submit" value="authenticate me">
			</form>
			<br><?php
		} else {?>
                    <!--<div id="system-data" style="margin-bottom: 10px; /*overflow: auto;*/ display: flex; justify-content: flex-end; flex-direction: row">-->
                        <?php
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
                                    
                                    var isDefaultImage = true;
                                    
                                    $("#publicIntelButton").on("click", function(event){
                                        console.log("Trying to toggle..." + isDefaultImage);
                                        if (isDefaultImage) {
                                            console.log("trying to change to temp...");
                                            $("#gateNet").attr("src","<?=$battleNet?>");
                                        } else {
                                            <?php $gateImg="\img/gateNetwork".$gateNetwork.".png";
                                            $gateImg=lookupClassifiedFile($classified,$gateImg);?>
                                            $("#gateNet").attr("src","<?=$gateImg?>");
                                        }
                                        isDefaultImage = !isDefaultImage;
                                        event.preventDefault();
                                    });
                                    
                                var clickables=[<?php
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
                                    
				function systemClick(event) {
					var el=document.getElementById("gateNet");
					//we need to convert the information that we get in the event info how far into the image has been clicked
					//first up we are going to figure out how much the image has been scaled
					var imgOrigX=1654;
					var imgOrigY=1080;

					var scaleImgX=document.getElementById("gateNet").width/imgOrigX;
					var scaleImgY=document.getElementById("gateNet").height/imgOrigY;
					var imageScale=Math.min(scaleImgX,scaleImgY);

					//then we are going to calculate how far inside the window the image is
					//see https://stackoverflow.com/questions/8389156/what-substitute-should-we-use-for-layerx-layery-since-they-are-deprecated-in-web
					var x=0;
					var y=0;
                                        
					while (el && !isNaN(el.offsetLeft) && !isNaN(el.offsetTop)) {
						x += el.offsetLeft - el.scrollLeft;
						y += el.offsetTop - el.scrollTop;
						el = el.offsetParent;
					}
                                        
					x = event.clientX - x;
					y = event.clientY - y;
                                        
					//we compare the offset from the mid point of the image
					//and scale it back to original units used to make the clickables array
					var width=event.currentTarget.clientWidth;
					var clickFromMidX=x-(width/2);
					var clickX=(clickFromMidX*(1/imageScale))+(imgOrigX/2);

					var height=event.currentTarget.clientHeight;
					var clickFromMidY=y-(height/2);
					var clickY=(clickFromMidY*(1/imageScale))+(imgOrigY/2);

                                        for (i=0; i<clickables.length; i++) {
                                            var deltaX=clickX-clickables[i].x;
                                            var deltaY=clickY-clickables[i].y;
                                            var delta=Math.sqrt((deltaX*deltaX)+(deltaY*deltaY));
                                            if (delta<50) {
                                                    window.open(clickables[i].url,"_self");
                                            }
					}
				}
				</script>
                                    <div id="arc-map" class="show">
                                            <?php $gateImg="img/gateNetwork".$gateNetwork.".png";
                                            $gateImg=lookupClassifiedFile($classified,$gateImg);?>
                                            <img id="gateNet" class="show" src="<?=$gateImg?>"/>
                                            <div id="slider-vertical" style="height:200px;"></div>
                                    </div>
                                <script>
                                        var lastSliderValue = 100; // Global value
                                        $( function() {
                                            var mouseDown = false;
                                            var mouseCanClick = true;
                                            
                                            // The mouse events are used to determine if the user is dragging the map.
                                            // If so, it will not let the mouse "click" on a system when released.
                                            $("#gateNet").on("mousedown", function(event) {
                                                mouseDown = true;
                                            });
                                            $("#gateNet").on("mouseup", function(event){
                                                console.log(mouseCanClick);
                                                if (mouseCanClick) {
                                                    systemClick(event);
                                                }
                                                mouseCanClick = true;
                                                mouseDown = false;
                                            });
                                            $("#gateNet").on("mousemove", function(event){
                                                if (mouseDown) {
                                                    mouseCanClick = false;
                                                }
                                                // This can be used later to show the system map over top of the 
                                                // arc map when hovering over a system's location.
//                                                var el=document.getElementById("gateNet");
//                                                //we need to convert the information that we get in the event info how far into the image has been clicked
//                                                //first up we are going to figure out how much the image has been scaled
//                                                var imgOrigX=1654;
//                                                var imgOrigY=1080;
//
//                                                var scaleImgX=document.getElementById("gateNet").width/imgOrigX;
//                                                var scaleImgY=document.getElementById("gateNet").height/imgOrigY;
//                                                var imageScale=Math.min(scaleImgX,scaleImgY);
//
//                                                //then we are going to calculate how far inside the window the image is
//                                                //see https://stackoverflow.com/questions/8389156/what-substitute-should-we-use-for-layerx-layery-since-they-are-deprecated-in-web
//                                                var x=0;
//                                                var y=0;
//
//                                                while (el && !isNaN(el.offsetLeft) && !isNaN(el.offsetTop)) {
//                                                        x += el.offsetLeft - el.scrollLeft;
//                                                        y += el.offsetTop - el.scrollTop;
//                                                        el = el.offsetParent;
//                                                }
//
//                                                x = event.clientX - x;
//                                                y = event.clientY - y;
//
//                                                //we compare the offset from the mid point of the image
//                                                //and scale it back to original units used to make the clickables array
//                                                var width=event.currentTarget.clientWidth;
//                                                var clickFromMidX=x-(width/2);
//                                                var clickX=(clickFromMidX*(1/imageScale))+(imgOrigX/2);
//
//                                                var height=event.currentTarget.clientHeight;
//                                                var clickFromMidY=y-(height/2);
//                                                var clickY=(clickFromMidY*(1/imageScale))+(imgOrigY/2);
//                                                for (i=0; i<clickables.length; i++) {
//                                                    var deltaX=clickX-clickables[i].x;
//                                                    var deltaY=clickY-clickables[i].y;
//                                                    var delta=Math.sqrt((deltaX*deltaX)+(deltaY*deltaY));
//                                                    if (delta<50) {
//                                                            //window.open(clickables[i].url,"_self");
//                                                    }
//                                                }
                                            });
                                            
                                            $( "#slider-vertical" ).slider({
                                                orientation: "vertical",
                                                range: "min",
                                                min: 20,
                                                max: 200,
                                                value: 100,
                                                change: function( event, ui ) {
                                                    // image size - assumes the size of the image, which propably isn't the best
                                                    // practice, but we're going with it for now.
                                                    var imgOrigX=1654;
                                                    var imgOrigY=1080;
                                                    
                                                    // Old scale values
                                                    var scaleXConstOld = imgOrigX * lastSliderValue / 100;
                                                    var scaleYConstOld = imgOrigY * lastSliderValue / 100;
                                                    // New scale values
                                                    var scaleXConst = imgOrigX * ui.value / 100;
                                                    var scaleYConst = imgOrigY * ui.value / 100;
                                                    
                                                    // Get position of the image relative to the window (top left)
                                                    var oldX = $("#gateNet").offset().left;
                                                    var oldY = $("#gateNet").offset().top;
                                                    
                                                    // Calculate how much the image moves, assuming the center of the image
                                                    // is the point of zoom.
                                                    // TODO: Determine how to calculate movement assuming the point of zoom
                                                    // is at the center of the veiwport or at the location of the mouse.
                                                    // Due to the slider, location of the mouse isn't the ideal option imo.
                                                    var diffX = (scaleXConstOld - scaleXConst)/2;
                                                    var diffY = (scaleYConstOld - scaleYConst)/2;

                                                    // Effect changes based on above calculations
                                                    $("#gateNet").offset({left: oldX + diffX, top: oldY + diffY});
                                                    $("#gateNet").css("width", scaleXConst);
                                                    $("#gateNet").css("height", scaleYConst);
                                                    
                                                    // Set last value of ui.value for use later
                                                    lastSliderValue = ui.value;
                                                }
                                            });
                                            
                                            // Makes the map draggable using JQuery UI
                                            $( "#gateNet" ).draggable({
                                                start: function() {
                                                    $("#gateNet").css("cursor","grabbing");
                                                },
                                                stop: function() {
                                                    $("#gateNet").css("cursor","grab");
                                                },
                                                scroll: false
                                            });
                                            
                                            // Checks for wheel events. If detected, adjusts slider as necessary, which triggers the map to zoom.
                                            $("#gateNet").on('wheel', function(e) {
                                                    var delta = e.originalEvent.deltaY/10 * -1;
                                                    $("#slider-vertical").slider("value", $("#slider-vertical").slider("value") + delta);
                                            });
                                        });
                                </script>
                                
                                <span></span>
                                <?php
			}
		?>
                    <!--</div>-->
                        <?php
                        
                }
	?>
        
        
<?php
	if (isEmpty($sub) && !isEmpty($sector)) { 
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
  
		<div id="buttons" style="/*position:absolute;bottom:0px;right:0px;*/flex: 0 0 50px;">
                    <button id="toggle-button" class="dropbtn active">TOGGLE DATA</button>
			<button <?=$onclickStations?> class="dropbtn <?=$classStations?>">STATIONS</button>
			<button <?=$onclickGates?> class="dropbtn <?=$classGates?>">GATES</button>
			<button <?=$onclickOther?> class="dropbtn <?=$classOther?>">OTHER</button>
		</div><?php
	}
        if (isEmpty($sub) && isEmpty($sector)) {
            $versionStyle = "position: absolute; bottom: 0px; left: 0px; padding: 8px;";
        } else {
            $versionStyle = "flex: 0 0 20px";
        }
?>
        <div id="navcon-title" style="<?=$versionStyle?>">
            Stellar Cartography <?php if ($classified) {printf("ONI");} else {printf("TSN");}?> <?=$version?>
        </div>
        <script>
                var defaultMapOffset;
                var defaultMapHeight;
                var defaultMapWidth;
                window.onload = function(event) {
                    defaultMapHeight = $("gateNet").height();
                    defaultMapWidth = $("gateNet").width();
                    defaultMapOffset = $("gateNet").offset();
                    setupSystemMenu();
                };
        </script>
</body>
</html>
