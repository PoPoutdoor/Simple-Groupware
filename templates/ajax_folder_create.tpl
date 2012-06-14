{*
 * @package Simple Groupware
 * @link http://www.simple-groupware.de
 * @author Thomas Bley
 * @copyright Copyright (C) 2002-2012 by Thomas Bley
 * @license GPLv2
 *}
{config_load file="core_css.conf" section=$style}

<form onsubmit="ajax('folder_create',[val('cmultiple')?val('fmultiple'):tfolder,val('ftitle'),val('ftype_new'),val('fdescription_new'),val('ficon_new'),val('ffirst')], locate_folder); return false;">
  <div class="tree_subpane">{t}New folder{/t}</div>
  <table class="tree2" border="0" cellpadding="0" cellspacing="2">
	<tr>
	<td><label for="ftitle">{t}Name{/t}</label></td>
	<td><input id="ftitle" type="Text" maxlength="40" style="width:100%;" value="" required="true"></td>
	</tr>
	{if $isdbfolder}
	<tr>
	<td><label for="ftype_new">{t}Module{/t}</label></td>
	<td>
	  <select id="ftype_new" style="width:100%;" required="true">
	  {foreach key=key item=item from=$sys_schemas}
		{if $item[0] eq " "}<optgroup label="{$item}"/>{else}<option value="{$key}" {if $key eq $folder.type}selected{/if}>{$item}{/if}
	  {/foreach}
	  </select>
	</td>
	</tr>
	<tr>
	<td><label for="ficon_new">{t}Icon{/t}</label> (<a href="#" onclick="nWin('ext/modules/folder_icons.php?obj=ficon_new'); return false;">?</a>)</td>
	<td>
	  <select id="ficon_new" style="width:100%;">
	  <option value=""> {t}Default{/t}
	  {foreach key=key item=item from=$sys_icons}
		<option value="{$key}" {if $key eq $folder.icon}selected{/if}> {$item}
	  {/foreach}
	  </select>
	</td>
	</tr>
	<tr>
	<td style="white-space:nowrap;"><label for="fdescription_new">{t}Description{/t}&nbsp;</label></td>
	<td style="width:100%;"><textarea id="fdescription_new" rows="4" style="width:100%; height:65px;"></textarea></td>
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
	<td><input id="fmultiple" type="Text" style="width:100%;" value="{$folder.id|modify::getpathfull:false:"/"}/*/">
	</td>
	</tr>
	{/if}
	<tr><td></td><td>
	  <input type="submit" value="{t}Ok{/t}" style="width:50px;">&nbsp;
  	  <input type="button" value="{t}Cancel{/t}" style="width:50px;" onclick="hide('folder_info');">
	</td></tr>
  </table>
</form>
<div style="border-top: {#border#}; margin-top:5px; margin-bottom:5px;"></div>