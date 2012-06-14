{*
 * @package Simple Groupware
 * @link http://www.simple-groupware.de
 * @author Thomas Bley
 * @copyright Copyright (C) 2002-2012 by Thomas Bley
 * @license GPLv2
 *}
{config_load file="core_css.conf" section=$style}

<div class="tree_subpane">{t}Info{/t}</div>
<table class="tree2" border="0" cellpadding="0" cellspacing="2" style="margin-left:4px;">
  {foreach key=key item=item from=$info}
	<tr><td>{$key}</td><td>{$item}</td></tr>
  {/foreach}
</table>
<input type="button" value="{t}Ok{/t}" style="width:50px;" onclick="hide('folder_info');">
<div style="border-top: {#border#}; margin-top:5px; margin-bottom:5px;"></div>