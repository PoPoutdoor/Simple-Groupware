{*
 * @package Simple Groupware
 * @link http://www.simple-groupware.de
 * @copyright Simple Groupware Solutions Thomas Bley 2002-2012
 * @license GPLv2
 *}
{if $t.links[$curr_id][1] && !$t.links[$curr_id][3]}
  {assign var="item_data" value=$t.links[$curr_id][1]|modify::link:$data_item:$key_filter:$urladdon}
  <a target="{$t.links[$curr_id][0]|modify::target}" href="{$item_data}">
  {if $t.links[$curr_id][0] neq "_top"}
    <img src="ext/icons/{$t.links[$curr_id][2]|default:"link_ext.gif"}" style="vertical-align:top;">
  {else}
    <img src="ext/icons/{$t.links[$curr_id][2]|default:"link.gif"}" style="vertical-align:top;">
  {/if}
  </a>&nbsp;
{/if}
		
{if $t.linkstext[$curr_id][1]}
  {assign var="link_data" value=$t.linkstext[$curr_id][1]|modify::link:$data_item:$key_filter:$urladdon}
  {if $link_data neq ""}<a target="{$t.linkstext[$curr_id][0]|modify::target}" id="linktext" href="{$link_data}" onmousedown="check_bold(this);">{/if}
{/if}

{if $t.fields.$curr_id.SIMPLE_TYPE eq "textarea"}
  {$item.filter[$key_filter]|modify::nl2br}
{elseif is_call_type($t.fields.$curr_id.SIMPLE_TYPE)}
  {assign var="field" value=$t.fields.$curr_id}
  {assign var="view" value=$t.views[$t.view]}
  {assign var="id" value=$data_item._id}
  {types type=$t.fields.$curr_id.SIMPLE_TYPE func="render_value" value=$item.filter[$key_filter] value_raw=$item.data[$key_filter] preview=$t.views[$t.view].SHOW_PREVIEW}
{elseif $t.fields.$curr_id.NO_CHECKS neq ""}
  {$item.filter[$key_filter]|no_check}
{else}
  {$item.filter[$key_filter]|modify::field}
{/if}

{if $t.linkstext[$curr_id] && $link_data neq ""}</a>{/if}

{if $t.links[$curr_id][1] && $t.links[$curr_id][3]}
  &nbsp;<a target="{$t.links[$curr_id][0]|modify::target}" href="{$t.links[$curr_id][1]|modify::link:$data_item:$key_filter:$urladdon}">
  {if $t.links[$curr_id][0] neq "_top"}
    <img src="ext/icons/{$t.links[$curr_id][2]|default:"link_ext.gif"}" style="vertical-align:top;">
  {else}
    <img src="ext/icons/{$t.links[$curr_id][2]|default:"link.gif"}" style="vertical-align:top;">
  {/if}
  </a>
{/if}