<?php
/**
 * @package Simple Groupware
 * @link http://www.simple-groupware.de
 * @author Thomas Bley
 * @copyright Copyright (C) 2002-2012 by Thomas Bley
 * @license GPLv2
 */

class setup {

static $config_old = "";
static $errors = array();

static function build_customizing($file) {
  if (!file_exists($file)) return;
  self::out("Building customizations:");
  self::out("Execute ".$file);
  require($file);
}

static function customize_replace($file,$code_remove,$code_new) {
  echo $file.":<br/>Replace:<br/>".nl2br(modify::htmlquote($code_remove)).
	"<br/><br/>with:<br/>".nl2br(modify::htmlquote($code_new))."<br/><br/>\n";
  $data = file_get_contents($file);
  if (strpos($data,$code_remove)===false) {
	throw new Exception("code not found in: ".$file." Code: ".$code_remove);
  }
  file_put_contents($file, str_replace($code_remove,$code_new,$data));
}

static function out($str="",$nl=true,$exit=false) {
  echo $str;
  if ($nl) echo "<br>\n";
  if ($exit) exit;
  flush();
  @ob_flush();
}

static function out_exit($str) {
  self::out($str,false,true);
}

static function get_config_old($key, $full=false, $default="") {
  $config_old = self::$config_old;
  if (($pos = strpos($config_old,"define('".$key."',"))) {
	$pos = $pos+strlen($key)+10;
	$end = strpos($config_old,"\n",$pos)-$pos-2;
	$result = substr($config_old,$pos,$end);
	if (!$full) $result = trim($result,"'\"");
	if ($key=="INVALID_EXTENSIONS") $result = str_replace(",url,", ",", $result);
	return $result;
  }
  return $default;
}

static function dirs_create_htaccess($dirname) {
  if (!file_exists($dirname.".htaccess")) {
    if (!@file_put_contents($dirname.".htaccess", "Order deny,allow\nDeny from all\n", LOCK_EX)) {
	  setup::error(sprintf("{t}Please give write access to %s{/t}",$dirname),25);
    }
  }
  dirs_create_index_htm($dirname);
}

static function dirs_create_dir($dirname) {
  if (!is_dir($dirname)) sys_mkdir($dirname);
  dirs_create_index_htm($dirname."/");
}

static function error($msg,$id=0) {
  self::$errors[] = array($msg,$id);
}

static function show_form($databases, $install, $accept_gpl) {
  $globals = ini_get("register_globals");
  $mb_string = !in_array("mbstring",get_loaded_extensions());
  
  echo '
    <html><head>
	<title>Simple Groupware & CMS</title>
	<style>
		body, h2, img, div, table.data,a {
		  background-color: #FFFFFF; color: #666666; font-size: 13px; font-family: Arial, Helvetica, Verdana, sans-serif;
		}
		a,input,select { color: #0000FF; }
		input {
		  font-size: 11px; background-color: #F5F5F5; border: 1px solid #AAAAAA; height: 18px;
		  vertical-align: middle; padding-left: 5px; padding-right: 5px; border-radius: 10px;
		}
		.logo {
		  border-radius:10px; border:1px solid #AAAAAA; width:532px; height:300px;
		}
		.logo_image { width:512px; height:280px; }
		select { font-size: 11px; background-color: #F5F5F5; border: 1px solid #AAAAAA;	}
		input:focus { border: 1px solid #FF0000; }
		.checkbox,.radio { border: 0px; background-color: transparent; }
		.submit { color: #0000FF; background-color: #FFFFFF; width: 230px; font-weight: bold; }
		table.data td,table.data td.data { padding-left: 5px; padding-right: 5px; }
		table.data tr.fields td { color: #FFFFFF; background-color: #B6BDD2; padding: 2px; }
		#sgs_logo { width: 100%; height: 98%; background-color: #FFFFFF; -moz-transition:opacity 3s; -webkit-transition:opacity 3s; -o-transition:opacity 3s; }
		.logo_table { color:#FFFFFF; background-image:url(ext/images/sgs_logo_bg.jpg); width:512px; height:280px; border-radius:5px; }
		.font {
			text-shadow: -1px -1px 0px #101010, 1px 1px 0px #505050;
			font-family: Coustard, serif;
		}
		@font-face {
		  font-family:"Coustard";
		  src:local("Coustard"), url("ext/images/coustard.woff") format("woff");
		}
	</style>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<script>
	function opacity() {
	  getObj("sgs_logo").style.opacity = 1;
	  setTimeout(activate,3000);
	}
	function getObj(id) {
	  return document.getElementById(id);
	}
	function activate() {
	  getObj("sgs_logo").style.display="none";
	  getObj("setup").style.display="";
	}
	function change_input_type(id,checked) {
	  var obj = getObj(id);
	  obj.type = checked ? "text":"password";
	}
	function change_db_type(obj) {
	  var val = obj.options ? (obj.options[obj.selectedIndex].value) : obj.value;
	  var ids = ["db_host_row", "db_user_row", "db_pw_row"];
	  for (var i=0; i<ids.length; i++) {
		getObj(ids[i]).style.display = (val == "sqlite") ? "none" : "";
	  }
	}
	</script>
    </head>
    <body onload="'.($install?"activate();":"opacity();").'">

	<img src="http://www.simple-groupware.de/cms/logos.php?v='.CORE_VERSION.'&d='.PHP_VERSION.'_'.PHP_OS.'" style="width:1px; height:1px;">
    <div id="sgs_logo" style="'.($install?"display:none;":"").'opacity:0;" onclick="activate();">
    <table style="width:100%; height:95%;"><tr><td align="center">
      <table><tr><td align="right">
      <table class="logo">
	    <tr><td align="center" valign="middle">
		  <table class="logo_table">
		  <tr style="height:45px;"><td colspan="2" align="center" valign="top" class="font" style="font-size:80%"><b>Simple Groupware Solutions</b></td></tr>
		  <tr><td colspan="2" align="center" class="font" style="font-size:170%;"><b>Simple Groupware<br>'.CORE_VERSION_STRING.'</b></td></tr>
		  <tr style="height:50px;">
			<td valign="bottom" style="font-size:80%">Photo from<br><b>Axel Kristinsson</b></td>
			<td align="right" valign="bottom" style="font-size:80%">Thomas Bley<br><b>(C) 2002-2012</b></b></td>
		  </tr>
		  </table>
	    </td></tr>
      </table>
      </td></tr></table>
    </td></tr></table>
    </div>
    <div id="setup" style="display:none;">
    <div style="border-bottom: 1px solid black; letter-spacing: 2px; font-size: 18px; font-weight: bold;">Simple Groupware '.CORE_VERSION_STRING.'</div>
    <br>
	<div style="color:#ff0000; margin-left:6px;"><b>
	'.($globals?sprintf("{t}Warning{/t}: {t}Please modify your php.ini or add an .htaccess file changing the setting '%s' to '%s' (current value is '%s') !{/t}<br><br>","register_globals","0",$globals):"").'
	'.($mb_string?sprintf("{t}Warning{/t}: {t}Please install the php-extension with name '%s'.{/t}<br><br>","mbstring"):"").'
	'.($install and !$accept_gpl)?"&nbsp;=&gt; {t}To continue installing Simple Groupware you must check the box under the license{/t}<br><br>":"").'
	</b></div>
	<form action="index.php" method="post">
	<table class="data">
	<tr id="db_host_row">
	  <td><label for="db_host">{t}Database Hostname / IP{/t}</label></td>
	  <td><input type="Text" value="localhost" size="30" maxlength="50" name="db_host" id="db_host"></td>
	</tr>
	<tr id="db_user_row">
	  <td><label for="db_user">{t}Database User{/t}</label></td>
	  <td><input type="Text" value="root" size="30" maxlength="50" name="db_user" id="db_user"></td>
	</tr>
	<tr id="db_pw_row">
	  <td><label for="db_pw">{t}Database Password{/t}</label></td>
	  <td><input type="text" value="" size="30" maxlength="50" name="db_pw" id="db_pw"></td>
	</tr>
	<tr>
	  <td><label for="db_name">{t}Database Name{/t}</label></td>
	  <td><input type="Text" value="sgs_'.CORE_VERSION.'" size="30" maxlength="50" name="db_name" id="db_name" required="true"></td>
	</tr>
	<tr>
	  <td><label for="db_type">{t}Database{/t}</label></td>
	  <td>
  ';
  if (count($databases)>1) {
    echo '<select name="db_type" id="db_type" onchange="change_db_type(this);">';
    foreach ($databases as $key=>$val) echo '<option value="'.$key.'"> '.$val[0];
    echo '</select>';
  }	else {
    foreach ($databases as $key=>$val) echo '<input type="hidden" name="db_type" id="db_type" value="'.$key.'"> '.$val[0];
  }
  echo '
	  <script>change_db_type(getObj("db_type"));</script>
	  </td>
	</tr>
	<tr>
	  <td><label for="admin_user">{t}Admin Username{/t}</label></td>
	  <td><input type="text" value="admin" size="30" maxlength="50" name="admin_user" id="admin_user" required="true"></td>
	</tr>
	<tr>
	  <td><label for="admin_pw">{t}Admin Password{/t}</label></td>
	  <td><input type="text" value="" size="30" maxlength="50" name="admin_pw" id="admin_pw" required="true"></td>
	</tr>
	<tr>
	  <td><label for="folders">{t}Folder structure{/t}</label></td>
	  <td>
		<select name="folders" id="folders">
		  '.(is_dir("import/")?'<option value="modules/core/folders.xml">{t}Install demo folders{/t}':'').'
		  <option value="modules/core/folders_small.xml">{t}Install default folder structure{/t}
		  <option value="modules/core/folders_minimal.xml">{t}Install minimal folder structure{/t}
		</select>
	  </td>
	</tr>
	</table>
    <div style="border-bottom: 1px solid black;">&nbsp;</div>
	<h2>GNU GPL {t}License{/t} Version 2</h2>
	<h4>
	<a href="http://www.gnu.org/copyleft/gpl.html" target="_blank">{t}More information about the GNU GPL{/t}</a><br>
	<a href="http://www.gnu.org/licenses/translations.html" target="_blank">{t}Translations of the GNU GPL{/t}</a><br> 
	<a href="http://www.gnu.org/licenses/gpl-faq.html" target="_blank">{t}GNU GPL Frequently Asked Questions{/t}</a>
	<br>
	</h4>
	<font color="#ff0000">*** {t}To continue installing Simple Groupware you must check the box under the license{/t} ***</font><br><br>
	{t}Please read the following license agreement. Use the scroll bar to view the rest of this agreement.{/t}<br>
    <div style="border-bottom: 1px solid black;">&nbsp;</div>
	<pre>'.trim(file_get_contents("License.txt")).'</pre>
    <div style="border-bottom: 1px solid black;">&nbsp;</div>
	<br>
	<div style="border: 2px solid #FF0000; width:400px;">&nbsp; <input onclick="if (this.checked) this.parentNode.style.border=\'2px solid #00A000\'; else this.parentNode.style.border=\'2px solid #FF0000\';" type="Checkbox" class="checkbox" name="accept_gpl" id="accept_gpl" value="yes" style="margin: 0px;" accesskey="a" required="true"> <label for="accept_gpl">{t}I Accept the GNU GENERAL PUBLIC LICENSE VERSION 2{/t}</label></div>
	<br><br>
	<input type="submit" name="install" value="{t}I n s t a l l{/t}" class="submit" style="width:400px;"><br><br>
	</form>
    <div style="border-top: 1px solid black;">Powered by Simple Groupware, Copyright (C) 2002-2012 by Thomas Bley.</div>
	</div></body></html>
  ';
}

static function dirs_create_default_folders() {
  setup::dirs_create_htaccess(SIMPLE_STORE."/");
  setup::dirs_create_dir(SIMPLE_EXT);
  setup::dirs_create_dir(SIMPLE_STORE."/home");
  setup::dirs_create_dir(SIMPLE_STORE."/backup");
  setup::dirs_create_dir(SIMPLE_STORE."/syncml");
  setup::dirs_create_dir(SIMPLE_STORE."/trash");
  setup::dirs_create_dir(SIMPLE_STORE."/cron");
  setup::dirs_create_dir(SIMPLE_STORE."/old");

  $empty_dir = array(
    SIMPLE_STORE."/locking",
	SIMPLE_CACHE, SIMPLE_CACHE."/debug", SIMPLE_CACHE."/imap", SIMPLE_CACHE."/pop3",
	SIMPLE_CACHE."/ip", SIMPLE_CACHE."/artichow", SIMPLE_CACHE."/output",
	SIMPLE_CACHE."/schema", SIMPLE_CACHE."/schema_data", SIMPLE_CACHE."/smarty",
	SIMPLE_CACHE."/thumbs", SIMPLE_CACHE."/upload", SIMPLE_CACHE."/backup",
	SIMPLE_CACHE."/preview", SIMPLE_CACHE."/cifs", SIMPLE_CACHE."/gdocs", SIMPLE_CACHE."/cms",
	SIMPLE_CACHE."/lang", "/ext/cache",
  );
  foreach ($empty_dir as $dir) dirs_create_empty_dir($dir);
  setup::dirs_create_htaccess(SIMPLE_CACHE."/");
  if (APC) apc_clear_cache("user");
}

static function display_errors($phpinfo=false) {
  $err = "";
  $msg = "";
  foreach (self::$errors as $message) {
    $msg .= str_replace("\n","<br>",modify::htmlquote($message[0]))."<br>";
	$err .= $message[1]."_";
  }
  echo '
	<center>
	<img src="http://www.simple-groupware.de/cms/logos.php?v='.CORE_VERSION.'&d='.PHP_VERSION.'_'.PHP_OS.'&e='.$err.'" start="width:1px; height:1px;">
	<div style="border-bottom: 1px solid black; letter-spacing: 2px; font-size: 18px; font-weight: bold;">Simple Groupware '.CORE_VERSION_STRING.' - Setup</div>
	<br>{t}Error{/t}:<br>
	<error>'.$msg.'</error><br>
	<a href="index.php">{t}Relaunch Setup{/t}</a><br>
	<hr>
	<a href="http://www.simple-groupware.de/cms/Main/Installation" target="_blank">Installation manual</a> / 
	<a href="http://www.simple-groupware.de/cms/Main/Update" target="_blank">Update manual</a> /
	<a href="http://www.simple-groupware.de/cms/Main/Documentation" target="_blank">Documentation</a> / 
	<a href="http://www.simple-groupware.de/cms/Main/FAQ" target="_blank">FAQ</a><hr>
	<br>
	</center>
  ';
  if ($phpinfo) phpinfo();
  exit();
}
}