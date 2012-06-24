{*
 * @package Simple Groupware
 * @link http://www.simple-groupware.de
 * @copyright Simple Groupware Solutions Thomas Bley 2002-2012
 * @license GPLv2
 *}
{if $t.groupby neq "" && $data_item[$t.groupby].data[0] neq $last_groupitem && ($data_item[$t.groupby].filter[0] || $last_groupitem neq "_")}
  {counter assign="group_counter"}
  {if $table}<table border="0" cellspacing="0" cellpadding="0" class="data" style="border:0px; margin-top:6px; margin-bottom:8px;">{/if}
  <tr>
	<td class="item_groupby" colspan="20">
      {if $t.links[$t.groupby][1]}<a target="{$t.linkstext[$t.groupby][0]|modify::target}" href="{$t.links[$t.groupby][1]|modify::link:$data_item:0:$urladdon}">@</a>&nbsp;{/if}
      {if $t.linkstext[$t.groupby][1]}<a target="{$t.linkstext[$t.groupby][0]|modify::target}" id="linktext" href="{$t.linkstext[$t.groupby][1]|modify::link:$data_item:0:$urladdon}">{/if}
	  {$t.fields[$t.groupby].DISPLAYNAME|default:$t.groupby|replace:"_":" "}: {$data_item[$t.groupby].filter|@implode:$t.fields[$t.groupby].SEPARATOR|default:"{t}none{/t}"}
	  {if $t.linkstext[$t.groupby][1]}</a>{/if}
	</td>
  </tr>
  {if $table}</table>{/if}
  {assign var="last_groupitem" value=$data_item[$t.groupby].data[0]}
  {if $cycle_dataitem neq "items_even"}{cycle assign="cycle_dataitem" values="items_even,items_odd"}{/if}
{/if}
