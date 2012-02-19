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
  echo $file.":<br/>Replace:<br/>".nl2br(modify::htmlquote($code_remove))."<br/><br/>with:<br/>".nl2br(modify::htmlquote($code_new))."<br/><br/>\n";
  $data = file_get_contents("bin/".$file);
  if (strpos($data,$code_remove)===false) {
	throw new Exception("code not found in: ".$file." Code: ".$code_remove);
  }
  $data = str_replace($code_remove,$code_new,$data);
  file_put_contents("bin/".$file,$data);
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

static function display_errors($phpinfo=false) {
  $err = "";
  $msg = "";
  foreach (self::$errors as $message) {
    $msg .= str_replace("\n","<br>",modify::htmlquote($message[0]))."<br>";
	$err .= $message[1]."_";
  }
  echo '
    <br>
    <center>
	<img src="http://www.simple-groupware.de/cms/logos.php?v='.CORE_VERSION.'&d='.PHP_VERSION.'_'.PHP_OS.'&e='.$err.'" start="width:1px; height:1px;">
    <div style="border-bottom: 1px solid black; letter-spacing: 2px; font-size: 18px; font-weight: bold;">Simple Groupware Setup</div>
	<br>{t}Error{/t}:<br>
	<error>'.$msg.'</error>
	<br><br>
	<a href="index.php">{t}Relaunch Setup{/t}</a><br><br>
	<hr>
	<a href="http://www.simple-groupware.de/cms/Main/Installation" target="_blank">Installation manual</a> / 
	<a href="http://www.simple-groupware.de/cms/Main/Update" target="_blank">Update manual</a><hr>
	<a href="http://www.simple-groupware.de/cms/Main/Documentation" target="_blank">Documentation</a> / 
	<a href="http://www.simple-groupware.de/cms/Main/FAQ" target="_blank">FAQ</a><hr>
	<br>
	</center>
  ';
  if ($phpinfo) phpinfo();
  exit();
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

static function error_exit($str,$err) {
  echo '
    <html><body style="padding:0px;margin:0px;"><center><br>
	<img src="http://www.simple-groupware.de/cms/logos.php?v='.CORE_VERSION.'&d='.PHP_VERSION.'_'.PHP_OS.'&e='.$err.'" start="width:1px; height:1px;">
    <div style="border-bottom: 1px solid black; letter-spacing: 2px; font-size: 18px; font-weight: bold;">Simple Groupware Setup</div>
	<br>{t}Error{/t}:<br>
	<error>'.htmlspecialchars($str, ENT_QUOTES).'</error>
	<br><br>
	<a href="index.php">{t}Relaunch Setup{/t}</a>
	<br><br><hr>
	<a href="http://www.simple-groupware.de/cms/Main/Installation" target="_blank">Installation manual</a> / 
	<a href="http://www.simple-groupware.de/cms/Main/Update" target="_blank">Update manual</a><hr>
	<a href="http://www.simple-groupware.de/cms/Main/Documentation" target="_blank">Documentation</a> / 
	<a href="http://www.simple-groupware.de/cms/Main/FAQ" target="_blank">FAQ</a><hr><br>
  ';
  phpinfo();
  echo '</center></body></html>';
  exit;
}
}