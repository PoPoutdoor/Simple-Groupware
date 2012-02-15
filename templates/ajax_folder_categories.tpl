{*
 * @package Simple Groupware
 * @link http://www.simple-groupware.de
 * @author Thomas Bley
 * @copyright Copyright (C) 2002-2012 by Thomas Bley
 * @license GPLv2
 *}
{config_load file="core_css.conf" section=$style}

<form id="tcategories" action="index.php" method="get">
  <input type="hidden" name="tpreview" value="1"/>
  <input type="hidden" name="folders[]" value="{$t.folder}"/>
  <a style="float:right;" onclick="hide('tree_info');">X</a>
  <div class="tree_subpane">{t}Merge folders permanently{/t}</div>
  <table border="0" cellpadding="0" cellspacing="0" style="margin:3px;">
	<tr>
	  <td style="padding:2px; padding-right:6px;">
		<input type="checkbox" class="checkbox" onclick="tree_selectall(this.checked);">
	  </td>
      <td style="width:100%; height:22px;">
		<input type="submit" value="{t}S a v e{/t}" onclick="return tree_categories_save();">&nbsp;
		<input type="button" value="{t}Preview{/t}" onclick="this.form.submit();">
	  </td>
	</tr>
  </table>
  <table border="0" cellpadding="0" cellspacing="0" style="margin-left:3px;">  
	{foreach key=key item=item from=$items}
	  <tr style="padding:0px; margin:0px;">
	    <td valign="top" style="padding-right:3px; padding-left:2px;">
	    <input style="margin-top:3px;" type="checkbox" class="checkbox" id="tcat_{$item.id}" name="folders[]" value="{$item.id}" {if in_array($item.id,$folders)}checked{/if}>
		</td><td valign="top">
		  {if #bg_light_blue# eq "#B6BDD2"}
			<img style="margin:3px; margin-bottom:0px; vertical-align:top;" src="ext/icons/folder1.gif">
		  {else}
			<img style="margin:3px; margin-bottom:0px; vertical-align:top;" src="images.php?image=folder1&color={#bg_light_blue#|replace:"#":""}">
		  {/if}
		</td><td class="default"><label for="tcat_{$item.id}">{$item.path}</label></td></tr>
	{foreachelse}
	  <tr><td class="default"><div style="margin-top:3px;">&nbsp;{t}No entries found.{/t}</div></td></tr>	
    {/foreach}
  </table>
  <div style="border-top: {#border#}; margin-top:5px; margin-bottom:5px;"></div>
</form>