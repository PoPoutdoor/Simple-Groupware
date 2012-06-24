<?
/**
 * @package Simple Groupware
 * @link http://www.simple-groupware.de
 * @copyright Simple Groupware Solutions Thomas Bley 2002-2012
 * @license GPLv2
 */
?>
<div class="tree_subpane">{t}Info{/t}</div>
<table class="tree2" border="0" cellpadding="0" cellspacing="2" style="margin-left:4px;">
<? foreach ($this->info as $key=>$item) { ?>
	<tr><td><?= q($key) ?></td><td><?= q($item) ?></td></tr>
<? } ?>
</table>
<input type="button" value="{t}Ok{/t}" style="width:50px; margin-top:4px;" onclick="hide('folder_info');">
<div style="border-top: <?= $this->c("border") ?>; margin-top:5px; margin-bottom:5px;"></div>