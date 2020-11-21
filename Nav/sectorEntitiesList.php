<?php	
	if ($workingEntType == "stations") {
		$workingArray = $entStations;
		$workingColor = "yellow";
	} else if ($workingEntType == "gates") {
		$workingArray = $entGates;
		$workingColor = "#ff8000";
	} else if ($workingEntType == "other") {
		$workingArray = $entOther;
		$workingColor = "cyan";
	}
	
	if (isEmpty($entType) || $entType == $workingEntType) {?>
		<tr>
			<td colspan="3" style="color:<?=$workingColor?>"><br/><?=strtoupper($workingEntType)?></td>
		</tr><?php
		$highlightRow = false;
		foreach ($workingArray as $key => $value) {
			$desc = isset($value['description']) ? $value['description'] : "";
                        if (!isset($sub)) {?>
                            <tr class="entity<?=$highlightRow ? " highlight" : ""?>" onclick="location.href='index.php?<?=$classifiedHref?>sector=<?=$sector?>&sub=<?=$value['loc']?>'">
                        <?php    
                        } else {?>
                            <tr class="entity<?=$highlightRow ? " highlight" : ""?>">
                        <?php
                        }
                        ?>
				<td class="caption" >
					<!--?=str_replace(" ", "&nbsp;", $key)?-->
                                        <?=$key?>
				</td>
				<td class="sub">
					<?=isEmpty($sub) ? toRoman($value['loc']) : "&nbsp;"?>
				</td>
				<td class="desc">
					<?=$desc?>
					<?php if ($workingEntType == "gates") {
						echo createGateButton($classified,$value['name'], $sector, $classifiedHref);
					}?>
				</td>
			</tr><?php
			$highlightRow = !$highlightRow;
		}
		
		// in case array is empty, display "-"
		if (empty($workingArray)) {?>
			<tr class="entity">
				<td class="caption" >
					-
				</td>
				<td class="sub">
					&nbsp;
				</td>
				<td class="desc">
					&nbsp;
				</td>
			</tr><?php
		}
	}
?>
