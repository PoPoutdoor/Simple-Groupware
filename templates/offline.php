<?
/**
 * @package Simple Groupware
 * @link http://www.simple-groupware.de
 * @author Thomas Bley
 * @copyright Copyright (C) 2002-2012 by Thomas Bley
 * @license GPLv2
 */
?>
<html>
<head>
  <title><?= $this->q(APP_TITLE) ?> - {t}Offline folders{/t}</title>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <meta name="generator" content="Simple Groupware" />
  <link media="all" href="ext/cache/core_<?= $this->style ?>_<?= $this->browser["name"] ?>.css?<?= CORE_VERSION ?>" rel="stylesheet" type="text/css" />
  <script>
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
  </script>
  <script type="text/javascript" src="ext/cache/functions_<?= LANG ?>.js?<?= CORE_VERSION ?>"></script>
  <style>body { margin:10px; }</style>
</head>
<body onload="update_status();">
  <div style="white-space:nowrap; border-bottom: 1px solid black; letter-spacing: 2px; font-size: 18px; font-weight: bold;"><?= $this->q(APP_TITLE) ?> - {t}Offline folders{/t}</div>
  <br>
  <div id="back"><a href="index.php">Back</a> | <a href="index.php?folder=^offline_<?= $this->q($this->username) ?>">{t}Manage offline folders{/t}</a><br><br></div>
  <div id="status"></div>
  <script>
	document.body.addEventListener("offline", update_status, false);
	document.body.addEventListener("online", update_status, false);
  </script>
  <div>&nbsp;</div>
  <div style="margin-bottom:10px;"><b>{t}Folders{/t}:</b></div>
  <? foreach ($this->rows as $key=>$row) { ?>
	<?= $this->q($row["path"]) ?>
	<? if ($row["view"]!="display") echo "<small>(".$this->q($row["view"]).")</small>" ?> &nbsp;
	<span>
	  <a href="#" onclick="change_size('iframe_<?= $this->q($key) ?>',60); return false;"> + </a>/
	  <a href="#" onclick="change_size('iframe_<?= $this->q($key) ?>',-60); return false;"> &ndash;&nbsp;</a>
	</span><br>
	<table cellpadding="0" cellspacing="0" style="width:100%;"><tr><td style="padding-bottom:4px;">
	  <iframe src="<?= $this->q($this->row["url"]) ?>" id="iframe_<?= $this->q($key) ?>" name="iframe_<?= $this->q($key) ?>" style="width:100%; height:350px; border:0px; margin-top:5px; margin-bottom:9px;"></iframe>
	</td></tr></table>
  <? } ?>
  <? if (!$this->rows) { ?>
	{t}No entries found.{/t} ({t}Offline folders{/t})<br>
  <? } ?>
  <br>
  <div id="online">
	{t}Read this page offline:{/t}<br/>
	<ul>
	  <li>Android: Context menu -> Save for offline reading</li>
	  <li>Safari: Reading List -> Add page</li>
	  <li>Firefox: Install the "Readability" or "Read it later" extension</li>
	</ul>
  </div>
  <span style="float:right;"><?= sys_date("{t}m/d/y g:i:s a{/t}") ?></span>
  <div style="white-space:nowrap; border-top: 1px solid black;">  
    <a href="http://www.simple-groupware.de" class="lnotice" target="_blank" onmouseover="set_html(this,'Powered by Simple Groupware, Copyright (C) 2002-2012 by Thomas Bley.');" onmouseout="set_html(this,'Powered by Simple Groupware.');">Powered by Simple Groupware.</a>
  </div>
</body>
</html>