<div class="system" style="display: inline-block;">
	<img src="<?=lookupClassifiedFile($classified,"sectors/$sector/$sub.png");?>" style="height:100%;"/>
</div>
<?php 
	include 'sectorEntities.php'; 
	
	$subRow = ceil($sub / $sectorWidth);
	$subCol = $sub % $sectorWidth;
	
	if ($sectorHeight * $sectorHeight > 1) {?>
		<table class="navCross">
			<tr class="vertical">
				<td>
					&nbsp;
				</td>
				<td class="middle"><?php
					if ($subRow > 1 && $sectorHeight > 1) {
						$target = $sub - $sectorWidth;?>
						<div class="up" onclick="location.href='index.php?<?=$classifiedHref?>sector=<?=$sector?>&sub=<?=$target?>'">
							<span><?=toRoman($target)?></span>
						</div><?php
					} else {?>
						<div class="up disabled">&nbsp;<div><?php
					}?>
				</td>
				<td>
					&nbsp;
				</td>
			</tr>
			<tr class="horizontal">
				<td align="right"><?php
					if ($subCol != 1 && $sectorWidth > 1) {?>
						<div class="left" onclick="location.href='index.php?<?=$classifiedHref?>sector=<?=$sector?>&sub=<?=$sub - 1?>'">
							<span><?=toRoman($sub - 1)?></span>
						</div><?php
					} else {?>
						<div class="left disabled">&nbsp;</div><?php
					}?>
				</td>
				<!-- mini map -->
				<td class="minimap">
					<table><?php
						for ($y = 1; $y <= $sectorHeight; $y++) {?>
							<tr>
								<td>&nbsp;</td><?php
								for ($x = 1; $x <= $sectorWidth; $x++) {
									if ($y == $subRow && ($x % $sectorWidth == $subCol)) {
										$style = "background-color:blue;";
									} else {
										$style = "";
									}?>
									<td style="border:1px solid blue;width:14px;min-width:14px;<?=$style?>">
										&nbsp;
									</td><?php
								}?>
								<td>&nbsp;</td>
							</tr><?php
						}?>
					</table>
				</td>
				<td><?php
					if ($subCol > 0 && $sectorWidth > 1) {?>
						<div class="right" onclick="location.href='index.php?<?=$classifiedHref?>sector=<?=$sector?>&sub=<?=$sub + 1?>'">
							<span><?=toRoman($sub + 1)?></span>
						</div><?php
					} else {?>
						<div class="right disabled">&nbsp;<div><?php
					}?>
				</td>
			</tr>
			<tr class="vertical">
				<td>
					&nbsp;
				</td>
				<td class="middle"><?php
					if ($subRow < $sectorHeight && $sectorHeight > 1) {
						$target = $sub + $sectorWidth;?>
						<div class="down" onclick="location.href='index.php?<?=$classifiedHref?>sector=<?=$sector?>&sub=<?=$target?>'">
							<span><?=toRoman($target)?></span>
						</div><?php
					} else {?>
						<div class="down disabled">&nbsp;<div><?php
					}?>
				</td>
				<td>
					&nbsp;
				</td>
			</tr>
		</table><?php
	}
?>
