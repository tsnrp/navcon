
<style>
    table.data td.sub {
        width: auto;
        /*white-space: nowrap;*/
    }
    tbody {
        width: auto;
        /*white-space: nowrap;*/
    }
    
    .sub:hover {
        text-decoration: underline;
        cursor: pointer;
    }
    
</style>

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
			<td colspan="3" style="color:<?=$workingColor?>"><?=strtoupper($workingEntType)?></td>
		</tr>
                    
                    <?php
		$highlightRow = false;
                $link = $sub == "";
		foreach ($workingArray as $key => $value) {
			$desc = isset($value['description']) ? $value['description'] : "";
                        if ($link) {?>
                            <tr class="entity<?=$highlightRow ? " highlight" : ""?>" >
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
                                <?php
                                if (isEmpty($sub)) {?>
                                <td class="sub" style="" onclick="location.href='index.php?<?=$classifiedHref?>sector=<?=$sector?>&sub=<?=$value['loc']?>'">
					<?=isEmpty($sub) ? "&nbsp".toRoman($value['loc'])."&nbsp" : "&nbsp;"?>
				</td>
                                <?php
                                }
                                
                                ?>
				<td class="desc">
					<?=$desc?>
					<?php if ($workingEntType == "gates") {
						echo createGateButton($classified,$value['name'], $sector, $classifiedHref);
					}?>
				</td>
                                <?php
                                
                                    if (!$link) {?>
<!--                                        <td>
                                        </td>-->
                                        <?php
                                    }
                                ?>
			</tr><?php
			$highlightRow = !$highlightRow;
		}
		
		// in case array is empty, display "-"
		if (empty($workingArray)) {?>
			<tr class="entity">
				<td class="caption" >
					-
				</td>
<!--				<td class="sub">
					&nbsp;
				</td>
				<td class="desc">
					&nbsp;
				</td>-->
			</tr><?php
		}
	}
?>
