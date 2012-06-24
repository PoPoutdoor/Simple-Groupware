<?
/**
 * @package Simple Groupware
 * @link http://www.simple-groupware.de
 * @copyright Simple Groupware Solutions Thomas Bley 2002-2012
 * @license GPLv2
 */
?>
<form onsubmit="ajax('folder_create',[val('cmultiple')?val('fmultiple'):tfolder,val('ftitle'),val('ftype_new'),val('fdescription_new'),val('ficon_new'),val('ffirst')], locate_folder); return false;">
  <div class="tree_subpane">{t}New folder{/t}</div>
  <table class="tree2" border="0" cellpadding="0" cellspacing="2">
	<tr>
	<td><label for="ftitle">{t}Name{/t}</label></td>
	<td><input id="ftitle" type="Text" maxlength="40" style="width:100%;" value="" required="true"></td>
	</tr>
	<? if ($this->isdbfolder) { ?>
	<tr>
	<td><label for="ftype_new">{t}Module{/t}</label></td>
	<td>
	  <select id="ftype_new" style="width:100%;" required="true">
	  <? foreach ($this->schemas as $key=>$item) { ?>
		<? if ($item[0]==" ") { ?>
		<optgroup label="<?= q($item) ?>"/>
		<? } else { ?>
		<option value="<?= q($key) ?>" <? if ($key==$this->folder["type"]) echo "selected" ?>><?= q($item) ?>
		<? } ?>
	  <? } ?>
	  </select>
	</td>
	</tr>
	<tr>
	<td><label for="ficon_new">{t}Icon{/t}</label> (<a href="#" onclick="nWin('ext/modules/folder_icons.php?obj=ficon_new'); return false;">?</a>)</td>
	<td>
	  <select id="ficon_new" style="width:100%;">
	  <option value=""> {t}Default{/t}
	  <? foreach ($this->icons as $key=>$item) { ?>
		<option value="<?= q($key) ?>" <? if ($key==$this->folder["icon"]) echo "selected" ?>><?= q($item) ?>
	  <? } ?>
	  </select>
	</td>
	</tr>
	<tr>
	<td style="white-space:nowrap;"><label for="fdescription_new">{t}Description{/t}&nbsp;</label></td>
	<td><textarea id="fdescription_new" rows="4" style="width:100%; height:65px;"></textarea></td>
	</tr>
	<tr>
	<td><label for="ffirst">{t}First in list{/t}</label></td>
	<td><input id="ffirst" type="checkbox" value="1" checked class="checkbox"></td>
	</tr>
	<tr>
	<td><label for="cmultiple">{t}Multiple{/t}</label></td>
	<td><input id="cmultiple" type="checkbox" value="1" class="checkbox" onchange="showhide('fmultiple_line');">
	</td>
	</tr>
	<tr id="fmultiple_line" style="display:none;">
	<td><label for="fmultiple">{t}Parent folder{/t}</label></td>
	<td><input id="fmultiple" type="Text" style="width:100%;" value="<?= q(modify::getpathfull($this->folder["id"],false,"/")) ?>/*/">
	</td>
	</tr>
	<? } ?>
	<tr><td></td><td style="padding-top:4px;">
	  <input type="submit" value="{t}Ok{/t}" style="width:50px;">&nbsp;
  	  <input type="button" value="{t}Cancel{/t}" onclick="hide('folder_info');">
	</td></tr>
  </table>
</form>
<div style="border-top: <?= $this->c("border") ?>; margin-top:5px; margin-bottom:5px;"></div>