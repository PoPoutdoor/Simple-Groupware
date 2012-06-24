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
<title>Simple Groupware - Login</title>
<? // You are not allowed to remove or alter the copyright. ?>
<!-- 
	This website is brought to you by Simple Groupware
	Simple Groupware is an open source Groupware and Web Application Framework created by Thomas Bley and licensed under GNU GPL v2.
	Simple Groupware is copyright 2002-2012 by Thomas Bley.	Extensions and translations are copyright of their respective owners.
	More information and documentation at http://www.simple-groupware.de/
-->
<link media="all" href="ext/cache/core_<?= DEFAULT_STYLE ?>_<?= $this->browser["name"] ?>.css?<?= CORE_VERSION ?>" rel="stylesheet" type="text/css" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta name="generator" content="Simple Groupware <?= CORE_VERSION_STRING ?>" />
<meta name="viewport" content="initial-scale=1.0; minimum-scale=1.0; maximum-scale=1.0;" />
<link href="ext/images/favicon.ico" rel="shortcut icon">
<script>
function getObj(id) {
  return document.getElementById(id);
}
function set_html(obj,txt) {
  if (!obj) return;
  obj.innerHTML = txt;
}
function load() {
  getObj("login_table_obj").style.opacity = 0.75;
  if (top.sys && !top.sys.is_guest) {
    getObj("username").value = top.sys.username;
    getObj("redirect").checked = false;
    getObj("password").focus();
  } else {
    document.getElementById("username").focus();
  }
}
function generate_password(field) {
  var keys = "abcdefghijklmnopqrstuvwxyz1234567890@";
  var temp = "";
  for (i=0;i<8;i++) {
    temp += keys.charAt(Math.floor(Math.random()*keys.length));
  }
  alert("{t}The new password is{/t}: " + temp);
  getObj(field).value = temp;
  getObj(field+"_confirm").value = temp;
}
function validate_signup() {
  if (getObj('spassword').value!="" && getObj('spassword').value==getObj('spassword_confirm').value) return true;
  alert("{t}password not confirmed.{/t}");
  return false;
}
</script>
<style>
body {
  overflow:hidden;
  overflow-y:hidden;
  overflow-x:hidden;
  height:1px;
}
</style>
</head>
<body onload="load();">
<div class="bg_full"><img src="<?= $this->c("bg_login") ?>" style="width:100%; height:100%;"></div>
<noscript>
<div style="background-color:#FFFFFF; text-align:center;">
<h2>{t}Please enable Javascript in your browser.{/t}</h2>
</div>
</noscript>

<? if (isset($this->alert)) { ?>
<div class="login_alert" style="text-align:center">
  <table style="margin:auto; text-align:center"><tr><td>
  <div class="default10">
  <? foreach ($this->alert as $item) echo nl2br(q($item))."<br>" ?>
  </div>
  </td></tr></table>
</div>
<? } ?>

<div id="login_table_obj" style="text-align:center; <? if (SELF_REGISTRATION) echo $this->browser["is_mobile"] ? "top:10%;" : "top:33%;" ?>">
  <table style="margin:auto;"><tr><td class="login_table" style="<? if (!$this->browser["is_mobile"]) echo "padding:0 75px;" ?>">
    <a target="_blank" href="<?= $this->c("logo_link") ?>"><img src="<?= $this->c("logo_login") ?>"></a><br>
    <form method="post" action="index.php?folder=<?= q($this->folder) ?>&view=<?= q($this->view) ?><?= q($this->find . $this->page . $this->item) ?>">
	<input type="hidden" name="loginform" value="true">
	<input type="text" id="username" name="username" style="margin-bottom:2px;" required="true">
	<input type="password" id="password" name="password" style="margin-bottom:2px;" required="true">
	<input type="submit" value=" {t}L o g i n{/t} " style="margin-bottom:2px;">
	<div class="default" style="padding:2px; padding-top:3px;">
	<? if (empty($this->page)) { ?>
	<input class="checkbox" id="redirect" type="checkbox" name="redirect" value="1" style="margin:0px; margin-bottom:2px;" <? if (empty($this->folder) and empty($this->find)) echo "checked" ?>>
	<label for="redirect" class="default10">{t}Redirect to home directory{/t}</label>
	<? } ?>
	</div>
	</form>
  </td></tr>
  </table>

  <? if (SELF_REGISTRATION) { ?>
  <br>
  <table style="margin:auto;"><tr><td class="login_table" style="<? if (!$this->browser["is_mobile"]) echo "padding:0 75px;" ?>">
    <form method="post" action="index.php" onsubmit="return validate_signup();">
	<input type="hidden" name="signupform" value="true">
	<input type="hidden" name="redirect" value="1">
	<table style="width:100%;">
	<tr>
	  <td style="text-align:center;" colspan="2">{t}Self registration{/t}<hr></td>
	</tr>
	<tr>
	  <td class="default10">{t}Username{/t}</td>
	  <td><input type="text" name="username" value=""/></td>
	</tr>
	<tr>
	  <td class="default10" rowspan="2">{t}Password{/t}</td>
	  <td><input type="password" id="spassword" name="password" value=""/>
		<input type="button" value="&lt;" onclick="generate_password('spassword');"/>
	  </td>
	</tr>
	<tr>
	  <td><input type="password" id="spassword_confirm" value=""/></td>
	</tr>
	<tr>
	  <td class="default10">{t}E-mail{/t}</td>
	  <td><input type="text" name="email" value=""/></td>
	</tr>
	<tr>
	  <td></td>
	  <td><input type="submit" value=" {t}R e g i s t e r{/t} "></td>
	</tr>
	</table>
	</form>
  </td></tr>
  </table>
  <? } ?>
</div>
<div class="notice2"><a href="<?= $this->c("login_notice_link") ?>" class="lnotice" target="_blank"><?= $this->c("login_notice") ?></a></div>
<? // You are not allowed to remove or alter the copyright. ?>
<div class="notice2 notice3"><a href="http://www.simple-groupware.de" class="lnotice" target="_blank" onmouseover="set_html(this,'Powered by Simple Groupware, Copyright (C) 2002-2012 by Thomas Bley.');" onmouseout="set_html(this,'Powered by Simple Groupware.');">Powered by Simple Groupware.</a></div>
</body>
</html>