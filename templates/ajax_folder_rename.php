<?
/**
 * @package Simple Groupware
 * @link http://www.simple-groupware.de
 * @copyright Simple Groupware Solutions Thomas Bley 2002-2012
 * @license GPLv2
 */
?>
<form onsubmit="ajax('folder_rename',[tfolder,val('ftitle'),val('ftype'),val('fdescription'),val('ficon'),val('fnotification')], locate_folder); return false;">
  <div class="tree_subpane">{t}Rename folder{/t}</div>
  <table class="tree2" border="0" cellpadding="0" cellspacing="2">
	<tr>
	<td><label for="ftitle">{t}Name{/t}</label></td>
	<td><input id="ftitle" name="first" type="Text" maxlength="40" style="width:100%;" value="<?= q($this->folder["name"]) ?>" required="true"></td>
	</tr>
	<? if ($this->isdbfolder and $this->folder["assets"]==0) { ?>
	<tr>
	<td><label for="ftype">{t}Module{/t}</label></td>
	<td>
	  <select id="ftype" style="width:100%;" required="true">
	  <? foreach ($this->schemas as $key=>$item) { ?>
		<? if ($item[0]==" ") { ?>
		<optgroup label="<?= q($item) ?>"/>{else}<option value="<?= q($key) ?>" <? if ($key==$this->folder["type"]) echo "selected" ?>><?= q($item) ?>
		<? } ?>
	  <? } ?>
	  </select>
	</td>
	</tr>
	<? } ?>
	<? if ($this->isdbfolder) { ?>
	<tr>
	<td><label for="ficon">{t}Icon{/t}</label> (<a href="#" onclick="nWin('ext/modules/folder_icons.php?obj=ficon'); return false;">?</a>)</td>
	<td>
	  <select id="ficon" style="width:100%;">
	  <option value=""> {t}Default{/t}
	  <? foreach ($this->icons as $key=>$item) { ?>
		<option value="<?= q($key) ?>" <? if ($key!=$this->folder["icon"]) echo "selected" ?>><?= q($item) ?>
	  <? } ?>
	  </select>
	</td>
	</tr>
	<tr>
	<td style="white-space:nowrap;"><label for="fdescription">{t}Description{/t}&nbsp;</label></td>
	<td><textarea id="fdescription" rows="4" style="width:100%; height:65px;"><?= q($this->folder["description"]) ?></textarea></td>
	</tr>
	<tr>
	<td style="white-space:nowrap;">
	  <label for="fnotification">{t}Notification{/t}&nbsp;({t}E-mail{/t})
	  <a href="#" onclick="sys_alert('{t}Syntax{/t}:\nabc@doecorp.com, cc:abcd@doecorp.com, bcc:abcde@diecorp.com,\n@{t}Group{/t}, cc:@{t}Group{/t}1, bcc:@{t}Group{/t}2');">(?)</a></label>
	</td>
	<td><textarea id="fnotification" rows="2" style="width:100%; height:30px;"><?= q($this->folder["notification"]) ?></textarea></td>
	</tr>
	<? } ?>
	<tr><td></td><td>
	  <input type="submit" value="{t}Ok{/t}" style="width:50px;">&nbsp;
	  <input type="button" value="{t}Cancel{/t}" onclick="hide('folder_info');">
	</td></tr>
  </table>
</form>
<div style="border-top: <?= $this->c("border") ?>; margin-top:5px; margin-bottom:5px;"></div>