{*
 * @package Simple Groupware
 * @link http://www.simple-groupware.de
 * @copyright Simple Groupware Solutions Thomas Bley 2002-2012
 * @license GPLv2
 *}
{if !$t.schema_mode}
{capture name=pages}
<table cellspacing="0" class="data {if !$sys.fixed_footer}data_page{/if}" style="border-top:0px; {if $sys.fixed_footer}margin-bottom:0px;{/if}">
  <tr class="summary">
	{if $t.datasets>0}
    <td style="width:10px;"><input type="checkbox" id="itemall" value="" class="checkbox" onmousedown="mselectall(this.checked);">
    <a onclick="mselectallkey(); return false;" href="#" accesskey="a"></a></td>
	{/if}
	{if $popup}
	  <td><input type="button" onclick="opener.commit_from_popup('{$t.lookup}',assets_get_selected(true)); window.close();" value="{t}Take selection{/t}"></td>
	{/if}
	{if !$popup && !$iframe}
    <td style="width:50px;"><form action="index.php?" method="post"><input onkeypress="if (getmykey(event)==13) this.form.submit();" type="text" maxlength="5" value="{$t.limit}" class="input" name="limit" style="text-align:center; width:45px;"></form></td>
	{/if}
	{if $t.lastpage neq 1}
    <td style="text-align:center; white-space:nowrap; width:90%;" class="noprint">
      {if $t.page neq 1}<input type="button" onclick="locate('index.php?page=1'); return false;" title="Alt-q" value="&lt;&lt;&lt;">&nbsp;{/if}{" "}
	  {if $t.page neq $t.prevpage}<input type="button" onclick="locate('index.php?page={$t.prevpage}'); return false;" title="Alt-'-'" value="&lt;&lt;">&nbsp;{/if}
	  {if $t.lastpage > 2}
	  <input type="text" onkeypress="if (getmykey(event)==13) locate('index.php?page='+this.value);" maxlength="5" value="{$t.page}" class="input" style="text-align:center; width:44px;">
	  {/if}
	  {if $t.page neq $t.nextpage}&nbsp;<input type="button" onclick="locate('index.php?page={$t.nextpage}'); return false;" title="Alt-'+'" value="&gt;&gt;">{/if}{" "}
	  {if $t.page neq $t.lastpage}&nbsp;<input type="button" onclick="locate('index.php?page={$t.lastpage}'); return false;" title="Alt-w" value="&gt;&gt;&gt;">{/if}
	</td>
	{else}<td style="width:90%;">&nbsp;</td>{/if}
	{if $t.maxdatasets>0}
	<td style="text-align:right; white-space:nowrap;">
      {if $t.lastpage>1}[{$t.page}/{$t.lastpage}]{/if}&nbsp;{if $t.datasets>0}[{$t.datasets}/{$t.maxdatasets}]{/if}
    </td>
	{/if}
  </tr>
</table>
{/capture}
{if !$sys.fixed_footer}{$smarty.capture.pages|no_check}{/if}
{/if}