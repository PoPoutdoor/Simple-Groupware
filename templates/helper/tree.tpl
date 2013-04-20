{*
 * @package Simple Groupware
 * @link http://www.simple-groupware.de
 * @copyright Simple Groupware Solutions Thomas Bley 2002-2012
 * @license GPLv2
 *}
{strip}
<div class="tree_caption" title="{$folder.type} / {$t.custom_name|default:$t.title}">{$t.views[$t.view].modulename} - {$sys.username}</div>
<a onclick="tree_showhide(); return false;" href="#" accesskey="t"></a>

{if $folder.description && !$sys.fdesc_in_content}
  <table border="0" cellpadding="0" cellspacing="0" class="tree_box">
    <tr><td>{$folder.description|modify::nl2br}</td></tr>
  </table>
{/if}

{if $datebox}
  <table border="0" cellpadding="0" cellspacing="0" class="tree_data">
    <tr class="fields" style="padding:0px;">
	  <td onclick="locate('index.php?today={$datebox.prev_date}')">&laquo;&laquo;</td>
  	  <td colspan="6" class="{if $datebox.mark eq "month"}datebox_head{else}datebox_headline{/if}" onclick="locate('index.php?markdate=month&today={$datebox.fstinmonths[$datebox.month]}')">
		{$datebox.months[$datebox.month]} {$datebox.year}
	  </td>
	  <td onclick="locate('index.php?today={$datebox.next_date}')">&raquo;&raquo;</td>
	</tr>
	<tr class="default">
	  <td class="datebox_days">&nbsp;</td>
      {foreach key=day_key item=day from=$datebox.dow}
	    <td class="datebox_days" onclick="locate('index.php?weekstart={$day.date_w}')">{$day.date_d}</td>
	  {/foreach}
	</tr>
    {foreach name=datebox key=week_key item=week from=$datebox.dates[$datebox.month]}
	  <tr class="datebox_row {if $datebox.mark eq "week" && $datebox.week eq $week_key}datebox_rowweek{elseif $smarty.foreach.datebox.first}datebox_rowfirst{elseif $smarty.foreach.datebox.last}datebox_rowlast{/if}">
	    <td class="datebox_week" onclick="locate('index.php?markdate=week&today={$week[0].timestamp}')">{$week_key}</td>
	    {foreach key=days_key item=days from=$week}
	      <td onclick="locate('index.php?markdate=day&today={$days.timestamp}')" {if $datebox.mark eq "day" && $datebox.today eq $days.timestamp}class="datebox_today"{elseif $datebox.realtoday eq $days.timestamp}class="datebox_realtoday"{elseif $datebox.month neq $days.n}class="datebox_disabled"{/if}>{$days.j}</td>
	    {/foreach}
	  </tr>
    {/foreach}
  </table>
  <table id="tree_bar" border="0" cellpadding="0" cellspacing="0">
  <tr>
	{if $datebox.mark neq "all"}
    <td style="width:45%;">
	  <input type="button" id="datebox_button" value="{$datebox.today|modify::localdateformat:"{t}M j, Y{/t}"|default:"..."}" onclick="calendar('datebox',false,true);" style="margin-right:2px; margin-bottom:2px;">
	  <input type="hidden" id="datebox" name="datebox" value="{$datebox.today|modify::dateformat:"{t}m/d/Y{/t}"}">
    </td>
	{/if}
	<td style="width:55%;">
	  <a href="#" onclick="locate('index.php?markdate=day'); return false;" {if $datebox.mark eq "day"}class="bold"{/if}>{t}Day{/t}</a> &nbsp;
	  <a href="#" onclick="locate('index.php?markdate=week'); return false;" {if $datebox.mark eq "week"}class="bold"{/if}>{t}Week{/t}</a> &nbsp;
	  <a href="#" onclick="locate('index.php?markdate=gantt'); return false;" {if $datebox.mark eq "gantt"}class="bold"{/if}>Gantt</a><br>
	  <a href="#" onclick="locate('index.php?markdate=month'); return false;" {if $datebox.mark eq "month"}class="bold"{/if}>{t}Month{/t}</a> &nbsp;
	  <a href="#" onclick="locate('index.php?markdate=year'); return false;" {if $datebox.mark eq "year"}class="bold"{/if}>{t}Year{/t}</a> &nbsp;
	  <a href="#" onclick="locate('index.php?markdate=all'); return false;" {if $datebox.mark eq "all"}class="bold"{/if}>{t}All{/t}</a> &nbsp;
	  {if $datebox.mark neq "all"}<a href="#" onclick="locate('index.php?today=now'); return false;"><img title="{t}Today{/t}" class="cursor" src="ext/icons/date.gif"></a>{/if}
	</td>
  </tr>
  </table>
{/if}

<div id="tree_def" onscroll="save_cookie();" class="overflow">
<table border="0" cellpadding="0" cellspacing="0" id="tree_frame" class="tree_frame"><tr><td valign="top">

<table border="0" cellpadding="0" cellspacing="0" id="tree" class="full_width">
<tr><td class="search_bar">
  <form onsubmit="search(); return false;">
  <input type="submit" class="hide"/>
  <table id="search_bar" class="tree2" border="0" cellpadding="0" cellspacing="0"><tr>
	<td style="width:100%; padding-left:3px;">
	  <input id="search_query" accesskey="f" onblur="searchbox2(this);" onfocus="searchbox(this);" style="width:100%;" type="text" value="{$t.search.query|default:"{t}Search{/t}"}">
	</td>
	<td style="padding-right:6px; white-space:nowrap;">
	  <img class="cursor" src="ext/icons/search.gif" onclick="search();" style="padding-top:1px; margin-left:4px;">
	  <img class="cursor" src="ext/icons/down.gif" onclick="showhide('tree_searchengines');">
	</td>
  </tr><tr>
	<td colspan="2">
	<div id="tree_searchengines" style="{if !$t.search}display:none;{/if}" class="tree_panes">
	{if $t.isdbfolder}
	  <table border="0" cellpadding="0" cellspacing="0" style="margin-left:4px;"><tr>
	  <td class="default">{t}Module{/t}:</td>
	  <td>&nbsp;</td>
	  <td colspan="2">
	  <select id="search_module" style="margin-bottom:1px; ">
	    <option value=""> {t}All{/t}
	    {foreach key=key item=item from=$sys_schemas}
		  {if $item[0] eq " "}<optgroup label="{$item}">{else}<option value="{$key}" {if $key eq $t.search.module}selected{/if}>{$item}{/if}
	    {/foreach}
	  </select>
	  </td>
	  </tr><tr>
	  <td class="default nowrap">{t}Modified{/t}:</td>
	  <td></td>
	  <td>
	  <select id="search_modified">
		<option value="gt" {if $t.search.modified.type eq "gt"}selected{/if}> {t}after{/t}
		<option value="lt" {if $t.search.modified.type eq "lt"}selected{/if}> {t}before{/t}
	  </select>
	  </td><td>&nbsp;
	  <input id="search_modified_value" type="text" value="{$t.search.modified.value}" style="width:70px;"/>
	  </td>
	  </tr><tr>
	  <td class="default">{t}User{/t}:</td>
	  <td></td>
	  <td colspan="2"><input id="search_user" type="text" value="{$t.search.user}"/></td>
	  </tr><tr>
	  <td class="default">{t}Similar{/t}:</td>
	  <td></td>
	  <td colspan="2"><input id="search_similar" class="checkbox" type="checkbox" value="1" onchange="search();" {if $t.search.similar}checked{/if}/></td>
	  </tr><tr>
	  <td class="default">{t}Subfolders{/t}:</td>
	  <td></td>
	  <td colspan="2"><input id="search_subfolders" class="checkbox" type="checkbox" value="1" onchange="search();" {if $t.search.subfolders || !$t.search}checked{/if}/></td>
	  </tr>	  
	  </table>
	{/if}
	</div>
	</td>
  </tr>
  </table>
  </form>
</td></tr>
<tr><td style="height:5px;"></td></tr>

{if count($t.folders)>1}
<tr><td>
  <div class="tree_folders">
  <table border="0" cellpadding="0" cellspacing="0" style="margin-left:4px;">
	{foreach key=key item=item from=$t.folders}
  	<tr>
	  <td class="default" valign="top"><img class="folder_block_image" src="ext/images/empty.gif" style="margin-right:6px; background-color: {$item[1]}"></td>
	  <td class="default"><a href="index.php?folder={$item[0]|escape:"url"}">{$item[0]|modify::getpath}</a></td>
	</tr>
	{/foreach}
  </table>
  </div>
</td></tr>
<tr><td style="height:5px;"></td></tr>
<tr><td><div style="border-top: {#border#};"><!--IE--></div></td></tr>
<tr><td style="height:5px;"></td></tr>
{/if}

<tr><td>
  <select id="tree_schema" style="width:150px; margin-left:3px; margin-top:3px; margin-bottom:3px;" onchange="tree_get_folders_by_type(this.value, 'tree_type');">
	{foreach key=skey item=sitem from=$sys_schemas}
	  {if $sitem[0] eq " "}<optgroup label="{$sitem}">{else}<option value="{$skey}" {if $skey eq $folder.type}selected{/if}>{$sitem}{/if}
	{/foreach}
  </select>
  {if $t.isdbfolder && $t.rights.write_folder}
	<a class="folder_buttons" onclick="folder_options(1);" title="{t}Merge folders permanently{/t}">+</a>
	<a class="folder_buttons" onclick="folder_options(0);" title="{t}Rename folder{/t}">-</a>
	<a class="folder_buttons" onclick="folder_categories();" title="{t}Create new folder{/t}">u</a>
  {/if}
  <img class="cursor" src="ext/icons/down.gif" onclick="tree_get_folders_by_type(val('tree_schema'), 'tree_type');">
  <div id="tree_type"></div>
</td></tr>
<tr><td style="height:5px;"></td></tr>
<tr><td><div style="border-top: {#border#};"><!--IE--></div></td></tr>
<tr><td style="height:5px;"></td></tr>

<tr><td>
<div>
  {foreach name=tree key=key item=item from=$tree.tree}
	{if $item.flevel <= $level}{repeat count=$level-$item.flevel+1}</div>{/repeat}{/if}
	<div class="drop_tree" rel="{$item.id}" title="{$item.id|sys_remove_handler} {$item.fdescription|replace:"\n":"\n "}" style="white-space:nowrap; {if $folder.id eq $item.id}font-weight:bold;{/if} {if $item.flevel eq 0}padding-bottom:1px;{/if}">
    {repeat count=$item.flevel}<img src="ext/icons/line.gif">{/repeat}
    {if $item.flevel neq 0}<a onclick="tree_open('{$item.id}');"><img id="{$item.id}_img" src="ext/icons/{$item.plus}.gif"></a>{/if}&nbsp;

	<a href="index.php?folder={$item.id|escape:"url"}">
	{if #tree_icons# || $item.icon}
	  <img src="ext/modules/{$item.icon|default:$item.tree_icon}"> {$item.title}&nbsp;
	{else}
	  <img src="ext/cache/folder{if $folder.id eq $item.id}2{else}1{/if}_{$sys_style}.gif"> {$item.title}&nbsp;
	{/if}
	{if $item.count neq ""}({$item.count}){/if}
	</a>
	</div>
	<div id="{$item.id}">
	{assign var="level" value=$item.flevel}
	{assign var="id" value=$item.id}
  {/foreach}
{repeat count=$level+1}</div>{/repeat}
</td></tr>

{if $tree.lastpage neq 1}
  <tr style="height:7px;"><td></td></tr>
  <tr><td>
    {if $tree.page neq 1}&nbsp; <a href="#" onclick="locate('index.php?treepage=1'); return false;">|&lt;&lt;</a>{/if}{" "}
	{if $tree.page neq $tree.prevpage}<a href="#" onclick="locate('index.php?treepage={$tree.prevpage}'); return false;">&lt;&lt;</a>{/if}
	&nbsp; {if $tree.page neq $tree.nextpage}<a href="#" onclick="locate('index.php?treepage={$tree.nextpage}'); return false;">&gt;&gt;</a>{/if}
	{if $tree.page neq $tree.lastpage}<a href="#" onclick="locate('index.php?treepage={$tree.lastpage}'); return false;">&gt;&gt;|</a>{/if}
  </td></tr>
{/if}
<tr><td style="height:7px;"></td></tr>
<tr><td><div style="border-top: {#border#};"><!--IE--></div></td></tr>
<tr><td style="height:7px;"></td></tr>
{foreach name=views key=key item=item from=$t.views}
  {if $item.VISIBILITY eq "bottom"}
  <tr><td class="tree_cpane">
	<a onmousedown="this.href=asset_form_link('index.php?view={$key}');" style="{if $key eq $t.view}font-weight:bold;{/if}">&nbsp;&gt; {$item.DISPLAYNAME|default:$key}</a>
  </td></tr>
  {/if}
{/foreach}
</table>
</td></tr>
</table>
</div>
{/strip}