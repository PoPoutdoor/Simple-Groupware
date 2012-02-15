{*
 * @package Simple Groupware
 * @link http://www.simple-groupware.de
 * @author Thomas Bley
 * @copyright Copyright (C) 2002-2012 by Thomas Bley
 * @license GPLv2
 *}
<html>
<head>
  <title>{$sys.app_title} - {t}Offline folders{/t}</title>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <meta name="generator" content="Simple Groupware" />
  <link media="all" href="images_php?css_style={$sys.style}&browser={$sys.browser.name}&{$smarty.const.CORE_VERSION}" rel="stylesheet" type="text/css" />
  <script>
    {literal}
	function update_status() {
	  if (navigator.onLine) {
		show("back");
		show("online");
	  } else {
		hide("back");
		hide("online");
	  }
	  set_html("status", "{t}Status{/t}: " + (navigator.onLine ? "<b>{t}online{/t}</b>" : "<b>{t}offline{/t}</b>"));
	}
	function change_size(id, size) {	
	  var obj = getObj(id);
  	  if (obj.offsetHeight + size > 0) obj.style.height = (obj.offsetHeight + size) + "px";
	  if (!obj.contentWindow) return;
	  try {
		if (size<0) {
		  obj.contentWindow.hide(".tfields");
		} else {
		  obj.contentWindow.show2(".tfields");
		}
	  } catch (e) {}
	}
    {/literal}
  </script>
  <script type="text/javascript" src="ext/js/functions.js?{$smarty.const.CORE_VERSION}"></script>
  <style>body {ldelim}margin:10px;{rdelim}</style>
</head>
<body onload="update_status();">
  <div style="white-space:nowrap; border-bottom: 1px solid black; letter-spacing: 2px; font-size: 18px; font-weight: bold;">{$sys.app_title} - {t}Offline folders{/t}</div>
  <br>
  <div id="back"><a href="index.php">Back</a> | <a href="index.php?folder=^offline_{$sys.username}">{t}Manage offline folders{/t}</a><br><br></div>
  <div id="status"></div>
  <script>
	document.body.addEventListener("offline", update_status, false);
	document.body.addEventListener("online", update_status, false);
  </script>
  <div>&nbsp;</div>
  <div style="margin-bottom:10px;"><b>{t}Folders{/t}:</b></div>
  {foreach key=key item=row from=$rows}
	{$row.path}
	{if $row.view neq "display"}<small>({$row.view})</small>{/if} &nbsp;
	<span>
	  <a href="#" onclick="change_size('iframe_{$key}',60); return false;"> + </a>/
	  <a href="#" onclick="change_size('iframe_{$key}',-60); return false;"> &ndash;&nbsp;</a>
	</span><br>
	<table cellpadding="0" cellspacing="0" style="width:100%;"><tr><td style="padding-bottom:4px;">
	  <iframe src="{$row.url}" id="iframe_{$key}" name="iframe_{$key}" style="width:100%; height:350px; border:0px; margin-top:5px; margin-bottom:9px;"></iframe>
	</td></tr></table>
  {foreachelse}
    {t}No entries found.{/t} ({t}Offline folders{/t})<br>
  {/foreach}
  <br>
  <div id="online">
	{t}Read this page offline:{/t}<br/>
	<ul>
	  <li>Android: Context menu -> Save for offline reading</li>
	  <li>Safari: Reading List -> Add page</li>
	  <li>Firefox: Install the "Readability" or "Read it later" extension</li>
	</ul>
  </div>
  <span style="float:right;">{"{t}m/d/y g:i:s a{/t}"|sys_date}</span>
  <div style="white-space:nowrap; border-top: 1px solid black;">  
    <a href="http://www.simple-groupware.de" class="lnotice" target="_blank" onmouseover="set_html(this,'Powered by Simple Groupware, Copyright (C) 2002-2012 by Thomas Bley.');" onmouseout="set_html(this,'Powered by Simple Groupware.');">Powered by Simple Groupware.</a>
  </div>
</body>
</html>