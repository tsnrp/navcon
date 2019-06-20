<?php	
	$tdClass = "width".$sectorWidth;
	
	// simulate at least $minWidth columns
	$minWidth = $sectorHeight + 1;
	$addColsNeeded = $sectorWidth < $minWidth;
	if ($addColsNeeded) {
		$tdClass = "width".$minWidth;
	}
	
	// If a sector is only 1x1, automatically redirect to detail sub sector page
	if ($sectorWidth * $sectorHeight == 1) {
		echo "<script type='text/javascript'>location.href='index.php?sector=".$sector."&sub=1';</script>";
	}
?>
<table class="sectors"><?php
	$continue = true;
	for ($row = 1; $row <= $sectorHeight; $row++) {?>
		<tr><?php
			for ($col = 1; $col <= $sectorWidth; $col++) {
				$sectorSubId = ($row - 1) * $sectorWidth + $col; 
				$imgPath = $sectorDir."/".$sectorSubId.".png";?>
				
				<td class="<?=$tdClass?>">
					<div class="content border" style="background-image:url('<?=$imgPath?>');" onClick="location.href='?sector=<?=$sector?>&sub=<?=$sectorSubId?>'"><?php
						if ($sectorWidth * $sectorHeight == 1) {
							echo " ";
						} else {
							echo toRoman($sectorSubId);
						}?>
					</div>
				</td><?php
			}
				
			// add columns, in case there are more rows than columns
			if ($addColsNeeded) {
				for ($addCol = 0; $addCol < $minWidth - $sectorWidth; $addCol++) {?>
					<td class="<?=$tdClass?>">
						<div class="content">
							&nbsp;
						</div>
					</td><?php
				}
			}?>
		</tr><?php
	}?>
</table>

<?php include 'sectorEntities.php'; ?>