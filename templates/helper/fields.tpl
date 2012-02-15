{*
 * @package Simple Groupware
 * @link http://www.simple-groupware.de
 * @author Thomas Bley
 * @copyright Copyright (C) 2002-2012 by Thomas Bley
 * @license GPLv2
 *}
{if $iframe}
  <td title="{$curr_id}" style="{$fstyle}">{$t.fields.$curr_id.DISPLAYNAME|default:$curr_id|replace:"_":" "}</td>
{elseif $curr_id eq $t.orderby}
  <td title="{$curr_id}" class="cursor bold" style="white-space:nowrap; {$fstyle}" onclick="locate('index.php?orderby={$curr_id}&order={if $t.order eq "asc"}desc{else}asc{/if}')">{$t.fields.$curr_id.DISPLAYNAME|default:$curr_id}&nbsp;<img src="ext/icons/{$t.order}.gif" style="width:8px; height:6px; padding-bottom:2px;"></td>
{else}
  <td title="{$curr_id}" class="cursor hide_fields" style="{$fstyle}" onclick="locate('index.php?orderby={$curr_id}&order=asc')">{$t.fields.$curr_id.DISPLAYNAME|default:$curr_id}
  &nbsp;<a title="{t}Hide{/t}" class="hide_field" href="index.php?hide_fields={$t.hidden_fields|@implode:","},{$curr_id}">&ndash;</a>
  </td>
{/if}