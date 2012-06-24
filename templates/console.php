<?
/**
 * @package Simple Groupware
 * @link http://www.simple-groupware.de
 * @copyright Simple Groupware Solutions Thomas Bley 2002-2012
 * @license GPLv2
 */
?>
<html>
<head>
  <link media="all" href="ext/cache/core_core_firefox.css?<?= CORE_VERSION ?>" rel="stylesheet" type="text/css" />
  <script type="text/javascript" src="ext/lib/json/json.js"></script>
  <script type="text/javascript" src="ext/cache/functions_sql_<?= LANG ?>.js?<?= CORE_VERSION ?>"></script>
  <style>
	body {
	  overflow-y:hidden;
	}
	#output {
	  position:absolute;
	  width:60%;
	  height:100%;
	  top:0px;
	  bottom:0px;
	  overflow:auto;
	}
	#code {
	  float:right;
	  width:40%;
	  margin-top:2px;
	  z-index:1;
	}
	.codebox {
	  width:100%;
	  margin-bottom:2px;
	  font-size:13px;
	}
	#selectbox {
	  clear:both;
	  width:100%;
	  margin-bottom:2px;
	}
	input {
	  margin-bottom:4px;
	}
	table td {
	  font-size:12px;
	  padding:2px;
	}
	#showbody {
	  position:absolute;
	  width:24px;
	  right:16px;
	  background-color:inherit;
	  border-bottom:1px solid #C0C0C0;
	  border-left:1px solid #C0C0C0;
	  z-index:1;
	}
  </style>
  <title><?= $this->title ?></title>
</head>
<body onload="start(); resizeit();" onresize="resizeit();">
  <? if ($this->content!="") { ?><div id="showbody" style="display:none;">&nbsp;<a href="#" onclick="show('code'); hide('showbody'); resizeit();">&lt;=</a></div><? } ?>
  <div id="code" style="<? if ($this->content=="") echo "width:100%" ?>">
	<? if ($this->content!="") { ?><div style="position:absolute;">&nbsp;<a href="#" onclick="hide('code'); show('showbody'); resizeit();">=&gt;</a></div><? } ?>
	<div style="text-align:center;">
	  <a href="?console=sql" <?= $this->console=="sql" ? "class='bold'":"" ?>>SQL</a> - 
	  <a href="?console=php" <?= $this->console=="php" ? "class='bold'":"" ?>>PHP</a> - 
	  <a href="?console=sys" <?= $this->console=="sys" ? "class='bold'":"" ?>>SYS</a>
	</div>
	<form method="post" action="console.php">
	<input type="hidden" name="token" value="<?= modify::get_form_token() ?>">
	<? if ($this->console=="sql" and $this->auto_complete) { ?>
	  <input type="hidden" id="database" value="<?= SETUP_DB_NAME ?>" />
	  <textarea name="code" id="codebox" class="codebox" spellcheck="false"><? q($this->code) ?></textarea><br>
	  <select size="2" id="selectbox" ondblclick="select_insert(obj('codebox'),obj(this.id));"></select>
	<? } else { ?>
	  <textarea name="code" id="codebox" class="codebox" spellcheck="false"><?= q($this->code) ?></textarea>
	<? } ?>
	<div style="text-align:center;" id="buttons">
	  <input type="submit" value="    {t}Execute{/t}  [ Alt+e ]    " accesskey="e">&nbsp;
	  <? if ($this->console=="sql") { ?>
		<input type="submit" name="full_texts" value=" {t}Execute{/t} {t}Full texts{/t}  [ Alt+f ] " accesskey="f">&nbsp;
		<input type="submit" name="vertical" value=" {t}Execute{/t} {t}vertical{/t}  [ Alt+v ] " accesskey="v">&nbsp;
	  <? } ?>
	  <input type="button" value=" {t}Clear{/t} " onclick="obj('codebox').value=''; obj('codebox').focus();">
	  <input type="hidden" name="console" value="<?= q($this->console) ?>">&nbsp;
	  <? if ($this->console!="sql") { ?>{t}Time limit{/t} ({t}seconds{/t}): <input type="text" name="tlimit" value="<?= q($this->tlimit) ?>" style="width:40px;" />&nbsp;<? } ?>
	  {t}Memory limit{/t} (MB): <input type="text" name="mlimit" value="<?= q($this->mlimit) ?>" style="width:34px;" />
	</div>
	</form>
  </div>
  <? if ($this->content!="") { ?><div id="output"><div style="padding:4px;"><?= $this->content ?></div></div><? } ?>
</body>
</html>