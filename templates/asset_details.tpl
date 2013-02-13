{*
 * @package Simple Groupware
 * @link http://www.simple-groupware.de
 * @copyright Simple Groupware Solutions Thomas Bley 2002-2012
 * @license GPLv2
 *}
{strip}
{foreach name=outer key=data_key item=data_item from=$t.data}
{foreach key=tab_key item=tab_item from=$t.tabs}
{if ($print neq 1 && !$t.disable_tabs) || $tab_key eq "general"}
{if $print eq 1 || $t.disable_tabs}{assign var="tab_key" value=false}{/if}

<div class="tab2 tab2{$tab_key}" {if $tab_key neq "general" && $tab_key}style="display:none;"{/if}>
{if $cycle_dataitem neq "items_even"}{cycle assign="cycle_dataitem" values="items_even,items_odd"}{/if}
<div id="div_{$tab_key}_{$data_item._id}" {if !is_array($data_item.tlevel)}style="margin-left:{$data_item.tlevel*25}px;"{/if}>
<table cellspacing="0" class="data" style="margin:0px;" title="{$data_item._id}">
  <tr rel="{$data_item._id}" class="mdown id_header asset_{$data_item._id}">
	<td id="pane_close" style="padding:0px; white-space:nowrap; cursor:pointer; display:none;" onclick="top.hidepane(window.name);">&nbsp;x&nbsp;|</td>
    {include file="helper/selitem.tpl"}
	<td class="cursor bold" style="width:70%;">
	  {if is_array($data_item[$t.field_1])}{$data_item[$t.field_1].filter[0]|modify::field} {/if}
	  {if is_array($data_item[$t.field_2])}{$data_item[$t.field_2].filter[0]|modify::field} {/if}
	  {if $t.hidden_fields}<a class="noprint" href="index.php?reset_view=true" title="{t}Reset view{/t}">+</a>{/if}
	</td>
	<td style="text-align:right; width:30%; white-space:nowrap;">
	  {if $data_item.lastmodifiedby}
	    <a target="_top" href="index.php?find=asset|simple_sys_users|1|username={$data_item.lastmodifiedby}&view=details">
	    {$data_item.lastmodifiedby}</a> |&nbsp;
	  {/if}
	  <a target="_top" href="index.php?orderby=lastmodified&order=desc">
	  {if !is_array($data_item.lastmodified)}
	    {$data_item.lastmodified|modify::shortdatetimeformat}
	  {else}{$data_item.lastmodified.filter[0]}{/if}
	  </a>
	  {if is_numeric($data_item._id)} |&nbsp;#{$data_item._id}{/if}
	</td>
	{if !$iframe && !$popup && !$t.nosinglebuttons && !$sys.is_mobile}{include file="helper/buttons.tpl" style=""}{/if}
  </tr>
</table>

<div><table cellspacing="0" class="data" style="border-top:0px; {if $iframe}margin-bottom:{if !$smarty.foreach.outer.last}4{else}0{/if}px;{/if}">
  {foreach name=fields key=curr_id item=item from=$data_item}
	{if $t.fields.$curr_id && (!$tab_key || in_array($tab_key,$t.fields.$curr_id.SIMPLE_TAB)) && !$t.fields.$curr_id.HIDDENIN[$t.view] && !$t.fields.$curr_id.HIDDENIN.all}
	  {if $sys.is_mobile}
	    {cycle assign="cycle_dataitem" values="items_even,items_odd"}
		<tr class="{$cycle_dataitem}" style="{$data_item._fgstyle}">
          {include file="helper/fields.tpl" fstyle="width:40%;"}
          {include file="helper/data.tpl"}
		  <td {if $data_item._bgstyle}style="width:35px; {$data_item._bgstyle}"{/if}>&nbsp;</td>
        </tr>
	  {elseif !$t.template_mode}
	    {cycle assign="cycle_dataitem" values="items_even,items_odd"}
		<tr class="{$cycle_dataitem}" style="{$data_item._fgstyle}">
          {include file="helper/fields.tpl" fstyle="width:20%;"}
          {include file="helper/data.tpl"}
		  <td {if $data_item._bgstyle}style="width:35px; {$data_item._bgstyle}"{/if}>&nbsp;</td>
        </tr>
	  {elseif ($t.template_mode eq "noheader") && $item.filter[0] neq "" && $curr_id neq $t.field_1 && $curr_id neq $t.field_2}
	    {cycle assign="cycle_dataitem" values="items_even,items_odd"}
        <tr class="{$cycle_dataitem}" style="{$data_item._fgstyle}">
		  {include file="helper/data.tpl"}
		  <td {if $data_item._bgstyle}style="width:35px; {$data_item._bgstyle}"{/if}>&nbsp;</td>
		</tr>
	  {elseif $t.template_mode eq "small" && $item.filter[0] neq ""}
	    {cycle assign="cycle_dataitem" values="items_even,items_odd"}
	    <tr class="{$cycle_dataitem}" style="{$data_item._fgstyle}">
          {include file="helper/fields.tpl" fstyle="width:20%;"}
          {include file="helper/data.tpl"}
		  <td {if $data_item._bgstyle}rowspan="2" style="width:35px; {$data_item._bgstyle}"{/if}>&nbsp;</td>
        </tr>
		<tr><td><div style="height:0px;"></div></td></tr>
	  {elseif $t.template_mode eq "flat" && $item.filter[0] neq ""}
	    {cycle assign="cycle_dataitem" values="items_even,items_odd"}
        <tr class="{$cycle_dataitem}" style="{$data_item._fgstyle}">
		  {include file="helper/fields.tpl"}
		  <td {if $data_item._bgstyle}rowspan="3" style="width:35px; {$data_item._bgstyle}"{/if}>&nbsp;</td>
		</tr>
	    {cycle assign="cycle_dataitem" values="items_even,items_odd"}
        <tr class="{$cycle_dataitem}" style="{$data_item._fgstyle}">
		  {include file="helper/data.tpl"}
		</tr>
		<tr><td><div style="height:3px;"></div></td></tr>
	  {/if}
    {/if}
  {/foreach}
  {if $sys.is_mobile && !$iframe && !$popup && !$t.nosinglebuttons && ($tab_key eq "general" || $t.disable_tabs)}
	<tr>{if !$iframe && !$popup && !$t.nosinglebuttons}{include file="helper/buttons.tpl" style="" colspan="3"}{/if}</tr>
  {/if}
</table></div>
</div>
</div>
{/if}
{/foreach}
{/foreach}
{/strip}