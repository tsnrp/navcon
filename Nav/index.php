<?php
	session_start();	
	//---- RELEASE VERSION ----//
        $version = "13.3b3";
        
        $battleNet = "https://cdn.discordapp.com/attachments/354025103471673346/787400593886806026/gateNetworkLowerBattleCurrent.png";
        
        //TODO: use filter_input() on these
        $mobile = isset($_GET['mobile']);
        $sub = isset($_GET['sub']) ? trim($_GET['sub']) : "";
	$entType = isset($_GET['entType']) ? trim($_GET['entType']) : "";

	$gateNetwork= isset($_GET['gateNetwork']) ? trim($_GET['gateNetwork']) : "Lower";

	$classified = isset($_GET['Classified']);
	$classifiedHref = isset($_GET['Classified'])? "Classified&" : "" ;
        $intelDoc = false;
        
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
        
        function readIntelFile($classified,$sector) {
            $file=lookupClassifiedFile($classified,"sectors/".$sector."/intel.txt");
            if (file_exists($file)) {
                $intelDoc = file_get_contents($file);
            }
            return $intelDoc;
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
        <link rel="stylesheet" type="text/css" href="Utilities/spinner.css">
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
        <script src="https://unpkg.com/imagesloaded@4/imagesloaded.pkgd.min.js"></script>
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


// Check if mobile
$(function() {
    <?php
        $mobileQuery = "index.php?mobile=true";
        if (strlen($newQuery)>0) {
            $mobileQuery = "index.php?".$newQuery."&mobile=true";
        } else {
            $mobileQuery = "index.php?mobile=true";
        }
        if (!$mobile) {
    ?>
    // check if on mobile device. If so, redirect to mobile url
    (function(a,b){if(/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i.test(a)||/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(a.substr(0,4)))window.location=b})(navigator.userAgent||navigator.vendor||window.opera,"<?=$mobileQuery?>");
    <?php
    }?>
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
                <div id="system-menu" class="system-menu" style="z-index: 1">
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
                                            <?php $gateImg= ($gateNetwork=="Upper") ? "img/gateNetwork".$gateNetwork.".png" : "img/gateNetworkLowerTransparent.png";
                                            $gateImg=lookupClassifiedFile($classified,$gateImg);?>
                                            $("#gateNet").attr("src","<?=$gateImg?>");
                                        }
                                        var disp = !isDefaultImage ? "display: none;" : "display: block;";
                                        $("#battle-lines-legend").attr("style", disp);
                                        //document.getElementById("#battle-lines-legend").style = disp;
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
                                var imgOrigX=1654;
                                var imgOrigY=1080;    
				function systemClick(event) {
					var el=document.getElementById("gateNet");
					//we need to convert the information that we get in the event info how far into the image has been clicked
					//first up we are going to figure out how much the image has been scaled
					
                                        console.log(document.getElementById("gateNet").width);
                                        console.log(document.getElementById("gateNet").height);
					var scaleImgX=document.getElementById("gateNet").width/imgOrigX;
					var scaleImgY=document.getElementById("gateNet").height/imgOrigY;
					var imageScale=Math.min(scaleImgX,scaleImgY);
                                        console.log("Scale: " + imageScale);

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
                                        console.log("Click: ");
                                        console.log(x);
                                        console.log(y);
                                        
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
                                            if (clickables[i].url.indexOf("Reema") !== -1) {
                                                console.log("Reema Delta: " + delta);
                                            }
                                            //console.log(delta);
                                            if (delta<50) {
                                                    window.open(clickables[i].url,"_self");
                                            }
					}
				}
				</script>
                                    <?php 
                                    // Really only mobile devices need this
                                    if ($mobile) {
                                        include "Utilities/spinner.php";
                                    }?>
                                    <div id="arc-map" class="" style="height: 100%;">
                                            <?php $gateImg= ($gateNetwork=="Upper") ? "img/gateNetwork".$gateNetwork.".png" : "img/gateNetworkLowerTransparent.png";
                                            $gateImg=lookupClassifiedFile($classified,$gateImg);?>
                                            <img id="gateNet" class="show" src="<?=$gateImg?>"/>
                                            <?php if (!$mobile) {?>
                                            <div id="handle" style="position: fixed; width: 100vw; height: 100vh; top: 0px; left: 0px; /*z-index: 5;*/"></div>
                                            <?php
                                            }?>
                                    </div>
                                <script>
                                        var lastSliderValue = 100; // Global value
                                        var map = "#arc-map";
                                        $( function() {
                                            /// Show spinner while map loads (mostly for mobile, but...)
                                            $("#gateNet").imagesLoaded(function() {
                                                $("#arc-map").addClass("show");
                                                rescale(document.getElementById("gateNet").width/imgOrigX*100, false);
                                                <?php if ($mobile) {?>
                                                document.getElementById("loading").style = "display: none;";
                                                //$("#loading").addClass("hidden");
                                                <?php
                                                    }?>
                                            });
                                            
                                            /// Following code is for moving the map.
                                            var mouseDown = false;
                                            var mouseCanClick = true;
                                            
                                            var locX;
                                            var locY;
                                            locX = $("#arc-map").position().left;
                                            locY = $("#arc-map").position().top;
                                            
                                            // The mouse events are used to determine if the user is dragging the map.
                                            // If so, it will not let the mouse "click" on a system when released.
                                            //$("#handle").on("mousedown", function(event) {
                                            $(map).on("mousedown", function(event) {
                                                mouseDown = true;
                                                //locX = $("#arc-map").position().left;
                                                //locY = $("#arc-map").position().top;
                                            });
                                            //$("#handle").on("mouseup", function(event) {
                                            $(map).on("mouseup", function(event) { 
                                                console.log(mouseCanClick);
                                                if (mouseCanClick) {
                                                    systemClick(event);
                                                }
                                                mouseCanClick = true;
                                                mouseDown = false;
                                                
                                                
//                                                console.log("locX = " + locY);
//                                                $("#gateNet").css("top", $("#arc-map").position().top);
//                                                $("#gateNet").css("left", $("#arc-map").position().left);
//                                                $("#arc-map").css("top", locY);
//                                                $("#arc-map").css("left", locX);
//                                                console.log($("#arc-map").position().top);
                                                //offset({left: 0, top: 0});//
                                                $("handle").css("top", 0);
                                                $("handle").css("left", 0);
                                            });
                                            <?php if (!$mobile) {?>
                                            $(map).on("mousemove", function(event){
                                            //$("#handle").on("mousemove", function(event){
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
                                            <?php
                                            }
                                            // Again, slider is hidden for mobile.
                                            if (!$mobile) {?>
                                            $( "#slider-vertical" ).slider({
                                                orientation: "vertical",
                                                range: "min",
                                                min: 20,
                                                max: 200,
                                                value: 100,
                                                change: function( event, ui ) {
                                                    rescale(ui.value);
                                                }
                                            });
                                            
                                            
                                            //<?php 
                                            // only make draggable and zoomable on non mobile, since mobile has built-in things usually
                                            //if (!$mobile) {?>
                                            // Makes the map draggable using JQuery UI
                                            $(map).draggable({
                                            //$( "#arc-map" ).draggable({
                                                start: function() {
                                                    $("#handle").css("cursor","grabbing");
                                                    $(map).css("cursor","grabbing");
                                                },
                                                stop: function() {
                                                    $("#handle").css("cursor","grab");
                                                    $(map).css("cursor","grab");
                                                    
                                                },
                                                scroll: false,
                                                //cancel: "#legend",//,#system-menu,#slider-vertical,#navcon-title,#compass,#legend",
                                                handle: "#handle"
                                            }).css("cursor","grab");
                                            
                                            // Checks for wheel events. If detected, adjusts slider as necessary, which triggers the map to zoom.
                                            $(document).on('wheel', function(e) {
                                                    var delta = e.originalEvent.deltaY/10 * -1;
                                                    $("#slider-vertical").slider("value", $("#slider-vertical").slider("value") + delta);
                                            });
                                            <?php
                                            }?>
                                        });
                                        
                                        function rescale( uiValue = 100 , reposition = true) {
                                                    // image size - assumes the size of the image, which propably isn't the best
                                                    // practice, but we're going with it for now.
                                                    var imgOrigX=1654;
                                                    var imgOrigY=1080;
                                                    
                                                    // Old scale values
                                                    var scaleXConstOld = imgOrigX * lastSliderValue / 100;
                                                    var scaleYConstOld = imgOrigY * lastSliderValue / 100;
                                                    // New scale values
                                                    var scaleXConst = imgOrigX * uiValue / 100;
                                                    var scaleYConst = imgOrigY * uiValue / 100;
                                                    
                                                    // Get position of the image relative to the window (top left)
                                                    var oldX = $("#arc-map").offset().left;
                                                    var oldY = $("#arc-map").offset().top;
                                                    
                                                    // Calculate how much the image moves, assuming the center of the image
                                                    // is the point of zoom.
                                                    // TODO: Determine how to calculate movement assuming the point of zoom
                                                    // is at the center of the veiwport or at the location of the mouse.
                                                    // Due to the slider, location of the mouse isn't the ideal option imo.
                                                    var diffX = (scaleXConstOld - scaleXConst)/2;
                                                    var diffY = (scaleYConstOld - scaleYConst)/2;

                                                    // Effect changes based on above calculations
                                                    if (reposition) {
                                                        $("#arc-map").offset({left: oldX + diffX, top: oldY + diffY});
                                                    }
                                                    $("#arc-map").css("width", scaleXConst);
                                                    $("#arc-map").css("height", scaleYConst);
                                                    
                                                    // Set last value of ui.value for use later
                                                    lastSliderValue = uiValue;
                                        }
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
            <?php
                // Hide slider on mobile. Doesn't work and is unnecessary anyhow.
                    if (!$mobile) {?>
                        <div id="slider-vertical" style="position: absolute; height:200px; left: 20px;"></div>
            <?php
            }?>
        
        <div id="navcon-title" style="<?=$versionStyle?>">
            Stellar Cartography <?php if ($classified) {printf("ONI");} else {printf("TSN");}?> <?=$version?>
        </div>
        <?php 
        if ($sector == false && $gateNetwork === "Lower") {
        ?>
            <img id="compass" src="img/galactic-compass.png" class="show" style="position: absolute; top: 0px; right: 0px; z-index: -1; width: 30vw;"/>
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
