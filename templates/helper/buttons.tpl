{*
 * @package Simple Groupware
 * @link http://www.simple-groupware.de
 * @author Thomas Bley
 * @copyright Copyright (C) 2002-2012 by Thomas Bley
 * @license GPLv2
 *}
<td valign="top" style="padding-right:0px; text-align:right; {$style}" colspan="{$colspan}">
<div style="padding-right:4px; {if !$sys.browser.is_mobile}white-space:nowrap;{/if}">
  {if $t.nosinglebuttons || $popup || $data_item.issum || $iframe || $print eq 1}
    &nbsp;
  {else}
    {if count($t.singlebuttons) neq 0 && !$t.nosinglebuttons}
      {foreach key=key item=item from=$t.singlebuttons}
	    {if (!$item.RIGHT || $t.rights[$item.RIGHT]) && (!$item.CONDITION || modify("match", $data_item, $item.CONDITION, $t.fields))}
		  &nbsp;<a href="#" onclick="{$item.ONCLICK|modify::link:$data_item} return false;" style="white-space:nowrap;">
	      {if $item.ICON}<img src="ext/icons/{$item.ICON}" title="{$item.DISPLAYNAME|default:$item.NAME}">{/if}
		  {if !$item.ICON || $sys.browser.is_mobile}&nbsp;{$item.DISPLAYNAME|default:$item.NAME}{/if}
		  </a>
		{/if}
      {/foreach}
    {/if}

    {foreach key=key item=item from=$t.views}
      {if $item.SHOWINSINGLEVIEW eq "true" && $item.VISIBILITY neq "hidden" && $item.VISIBILITY neq "active" && $key neq $t.view && (!$item.RIGHT || $t.rights[$item.RIGHT])}
		&nbsp;<a href="index.php?item[]={$data_item._id|escape:"url"}&view={$key}" style="white-space:nowrap;">
	    {if $item.ICON}<img src="ext/icons/{$item.ICON}" title="{$item.DISPLAYNAME|default:$item.NAME}">{/if}
		{if !$item.ICON || $sys.browser.is_mobile}&nbsp;{$item.DISPLAYNAME|default:$item.NAME}{/if}
		</a>
      {/if}
    {/foreach}
  {/if}
</div>
</td>