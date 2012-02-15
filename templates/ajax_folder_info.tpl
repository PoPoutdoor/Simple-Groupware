{*
 * @package Simple Groupware
 * @link http://www.simple-groupware.de
 * @author Thomas Bley
 * @copyright Copyright (C) 2002-2012 by Thomas Bley
 * @license GPLv2
 *}
{config_load file="core_css.conf" section=$style}

<a style="float:right;" onclick="hide('tree_info');">X</a>
<div class="tree_subpane">{t}Info{/t}</div>
<table class="tree2" border="0" cellpadding="0" cellspacing="2" style="margin-left:4px;">
  {foreach key=key item=item from=$info}
	<tr><td>{$key}</td><td>{$item}</td></tr>
  {/foreach}
</table>
<div style="border-top: {#border#}; margin-top:5px; margin-bottom:5px;"></div>