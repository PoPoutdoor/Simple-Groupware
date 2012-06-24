<?
/**
 * @package Simple Groupware
 * @link http://www.simple-groupware.de
 * @copyright Simple Groupware Solutions Thomas Bley 2002-2012
 * @license GPLv2
 */
?>
<form id="tcategories" action="index.php" method="get">
  <input type="hidden" name="tpreview" value="1"/>
  <input type="hidden" name="folders[]" value="<?= q($t.folder) ?>"/>
  <div class="tree_subpane">{t}Merge folders permanently{/t}</div>
  <table border="0" cellpadding="0" cellspacing="0" style="margin:3px;">
	<tr>
	  <td style="padding:2px; padding-right:6px;">
		<input type="checkbox" class="checkbox" onclick="folder_selectall(this.checked);">
	  </td>
      <td style="width:100%; height:22px;">
		<input type="submit" value="{t}S a v e{/t}" onclick="return folder_categories_save();">&nbsp;
		<input type="button" value="{t}Preview{/t}" onclick="this.form.submit();">&nbsp;
		<input type="button" value="{t}Cancel{/t}" onclick="hide('folder_info');">
	  </td>
	</tr>
  </table>
  <table border="0" cellpadding="0" cellspacing="0" style="margin-left:3px;">  
	<? foreach ($this->items as $item) { ?>
	  <tr style="padding:0px; margin:0px;">
	    <td valign="top" style="padding-right:3px; padding-left:2px;">
	    <input style="margin-top:3px;" type="checkbox" class="checkbox" id="tcat_<?= q($item["id"]) ?>" name="folders[]"
			value="<?= q($item["id"]) ?>" <?= in_array($item["id"],$this->folders)?"checked":"" ?>>
		</td><td valign="top">
		  <img style="margin:3px; margin-bottom:0px; vertical-align:top;" src="ext/cache/folder1_<?= $this->style ?>.gif">
		</td><td class="default"><label for="tcat_<?= q($item["id"]) ?>"><?= q($item["path"]) ?></label></td></tr>
	<? } if (!$this->items) { ?>
	  <tr><td class="default"><div style="margin-top:3px;">&nbsp;{t}No entries found.{/t}</div></td></tr>	
	<? } ?>
  </table>
  <div style="border-top: <?= $this->c("border") ?>; margin-top:5px; margin-bottom:5px;"></div>
</form>