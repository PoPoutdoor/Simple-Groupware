<?php
/**
 * @package Simple Groupware
 * @link http://www.simple-groupware.de
 * @author Thomas Bley
 * @copyright Copyright (C) 2002-2012 by Thomas Bley
 * @license GPLv2
 */

if (!defined("MAIN_SCRIPT")) exit;

class sys {

  static $db = null; // database link
  static $db_error = null; // sqlite
  static $db_queries = array(); // all queries
 
  static $time_start = 0; // script start
  static $time_end = 0; // script end

  // browser infos
  static $browser = array( "name"=>"", "ver"=>0, "str"=>"unknown", "is_mobile"=>false, "plattform"=>"", 
	"comp"=>array("htmledit"=>true, "codeedit"=>false, "javascript"=>true), "no_scrollbar"=>false );

  static $alert = array(); // force error message output
  
  static $notification = array(); // show notification messages
  
  static $warning = array(); // show warning messages

  static $smarty = null; // smarty reference

  static $urladdon = ""; // auto-append string to URL

  static $cache = array(); // cache data
  
  static function init() {
    self::$time_start = sys_get_microtime();

	// clean request vars
	if (ini_get("magic_quotes_gpc")!==false and get_magic_quotes_gpc()) modify::stripslashes($_REQUEST);
	foreach ($_REQUEST as $key=>$val) {
	  if (is_array($val) and count($val)>0) {
		$_REQUEST[$key] = array();
		foreach ($val as $val2) {
		  if (!is_array($val2)) $_REQUEST[$key][$val2] = $val2;
	} } }

	// refresh smarty cache
	if (DEBUG) debug_check_tpl();

	$files = array("functions.js", "functions_edit.js", "functions_sql.js");
	foreach ($files as $file) {
	  $cache_file = "ext/cache/".substr($file,0,-3)."_".LANG.".js";
	  if (file_exists($cache_file)) continue;
	  file_put_contents($cache_file, trans(file_get_contents("templates/js/".$file)));
	  
	  if (DEBUG and empty($_REQUEST["iframe"])) echo "reload js";
	}
	
	// set up smarty
	self::$smarty = new Smarty;
	self::$smarty->register_prefilter(array("modify","urladdon_quote"));
	if (isset($_REQUEST["print"])) self::$smarty->register_outputfilter(array("modify","striplinksforms"));
	if (isset($_REQUEST["print"])) self::$smarty->assign("print",$_REQUEST["print"]);
	self::$smarty->compile_dir = SIMPLE_CACHE."/smarty";
	self::$smarty->template_dir = "templates";
	self::$smarty->config_dir = "templates/css";
	self::$smarty->compile_check = false;

	$browsers = array("firefox", "safari", "msie", "opera", "chrome", "konqueror", "thunderbird", "mozilla");
	$themes = array("core", "rtl", "core_tree_icons", "contrast", "lake", "paradise", "earth", "water", "beach", "desert", "nature", "sunset", "blackwhite");
	foreach ($themes as $theme) {
	  foreach ($browsers as $browser) {
		$cache_file = "ext/cache/core_".$theme."_".$browser.".css";
		if (file_exists($cache_file)) continue;
		file_put_contents($cache_file, self::build_css($theme, $browser));
		if (DEBUG and empty($_REQUEST["iframe"])) echo "reload css";
	  }
	}

	// set up database
	if (!sql_connect(SETUP_DB_HOST, SETUP_DB_USER, sys_decrypt(SETUP_DB_PW,sha1(SETUP_ADMIN_USER)), SETUP_DB_NAME)) {
	  $err = sprintf("{t}Cannot connect to database %s on %s.{/t}\n",SETUP_DB_NAME,SETUP_DB_HOST).sql_error();
	  trigger_error($err,E_USER_ERROR);
	  sys_die($err);
	}

	// verify credentials
	login_handle_login();
  }
  
  static function build_css($theme, $browser) {
	  $smarty = clone self::$smarty;
	  $smarty->left_delimiter = "<";
	  $smarty->right_delimiter = ">";
	  $smarty->assign("style", $theme);
	  $smarty->assign("browser", $browser);
	  $output = $smarty->fetch("css/core.css");
	  
	  if ($browser=="safari") {
		$from = "/-moz-linear-gradient\(top,([^,]+),([^\)]+)\);/i";
		$to = "-webkit-gradient(linear, left top, left bottom, from(\\1), to(\\2));";
		$output = preg_replace($from,$to,$output);
	  }
	  if ($browser=="opera" or $browser=="msie") {
		$output = preg_replace("/^.*(-moz-)/m","",$output);
	  }
	  if ($browser=="msie") {
		$output = preg_replace("/max-height:([^;]+)px;/","height:expression(this.scrollHeight>\\1?'\\1px':'auto');",$output);
		$from = "/linear-gradient\(top,\s?([^,]+),\s?([^\)]+)\);/i";
		$to = "filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='\\1',endColorstr='\\2');";
		$output = preg_replace($from,$to,$output);
	  }
	  return $output;
  }

  static function shutdown() {
    // check execution time
    self::$time_end = number_format(sys_get_microtime()-self::$time_start,2);
	if (self::$time_end > SYSTEM_SLOW) {
	  sys_log_message_log("system-slow",sprintf("{t}%s secs{/t}",self::$time_end)." ".basename(_sys_request_uri()),_sys_request_uri());
	}

	// process error.txt
	$size = @filesize(SIMPLE_CACHE."/debug/error.txt");
	if ($size>0 and $size<=2097152 and $msgs = @file_get_contents(SIMPLE_CACHE."/debug/error.txt")) { // 2M
	  @unlink(SIMPLE_CACHE."/debug/error.txt");
	  $msgs = array_reverse(explode("\n",$msgs));
	  foreach ($msgs as $msg) {
		if ($msg=="") continue;
		$vars = unserialize($msg);
		sys_log_message($vars[0],$vars[1],$vars[2],$vars[3],true,$vars[4]);
	  }
	} else if ($size>0) {
	  sys_die("{t}The error logfile cannot be processed, too large:{/t} ".SIMPLE_CACHE."/debug/error.txt");
	}

	// logging
	sys_log_stat("pages",1);
  }
}

function sys_trans($file, $class) {
  $cache_file = SIMPLE_CACHE."/lang/".basename($class)."_".LANG."_".filemtime($file).".php";
  if (!file_exists($cache_file)) {
	@mkdir(SIMPLE_CACHE."/lang/");
	file_put_contents($cache_file, trans(file_get_contents($file)));
	if (DEBUG and empty($_REQUEST["iframe"])) echo "reload lang ".$class;
  }
  return $cache_file;
}

require(sys_trans("core/functions/funcs.php","funcs"));


function trans($content) {
  return preg_replace_callback("!\{t\}([^\{]+)\{/t\}!", "tr", $content);
}

function t($str, $params=null) {
  if ($params!=null) return vsprintf(tr(substr($str,3,-4)), $params);
  return tr(substr($str,3,-4));
}

function tr($str) {
  static $strings = null;
  if (is_array($str) and isset($str[1])) $str = $str[1]; // preg_callback
  if (LANG=="en") return $str;
  if ($strings===null) {
	$lang_file = "lang/".LANG.".lang";
	$cache_file = SIMPLE_CACHE."/lang/".LANG."_".filemtime($lang_file).".php";
	if (!file_exists($cache_file)) {
	  $matches = array();
	  preg_match_all("!\*\* ([^\n]+)\n([^\n]+)!", file_get_contents($lang_file), $matches);
	  $strings = array_combine($matches[1], $matches[2]);
	  file_put_contents($cache_file, "<?php \$strings = ".var_export($strings,true).";", LOCK_EX);
	}
	require($cache_file);
  }
  if (isset($strings[$str])) return $strings[$str];
  return $str;
}