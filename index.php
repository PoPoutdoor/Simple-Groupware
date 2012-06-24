<?php
/**
 * @package Simple Groupware
 * @link http://www.simple-groupware.de
 * @copyright Simple Groupware Solutions Thomas Bley 2002-2012
 * @license GPLv2
 */

define("MAIN_SCRIPT",basename($_SERVER["PHP_SELF"]));
define("CORE_VERSION_config","0_800");
error_reporting(E_ALL);
  
if (ini_get("register_globals")) {
  $valid = array("GLOBALS","_REQUEST", "_FILES","_SERVER","_COOKIE","_GET","_POST","browser");
  foreach (array_keys($GLOBALS) as $key) if (!in_array($key,$valid)) unset($GLOBALS[$key]);
}
header("Content-Type: text/html; charset=utf-8");
@include("simple_store/config.php");
if (!defined("CORE_VERSION") or CORE_VERSION_config!=CORE_VERSION) {
  if (defined("CORE_VERSION")) {
	rename("simple_store/config.php","simple_store/config_old.php");
	header("Location: index.php");
  } else require("core/setup.php");
  exit;
}
if (!defined("SETUP_DB_HOST")) exit;

if (!empty($_POST)) @ignore_user_abort(1);

if (FORCE_SSL and (!isset($_SERVER["HTTPS"]) or $_SERVER["HTTPS"]!="on")) {
  header("Location: https://".$_SERVER["HTTP_HOST"].$_SERVER["SCRIPT_NAME"]."?".@$_SERVER["QUERY_STRING"]);
  exit;
}
if (!empty($_SERVER["PATH_INFO"]) and $_SERVER["PATH_INFO"]!=$_SERVER["SCRIPT_NAME"] and !strpos($_SERVER["PATH_INFO"],".exe")) {
  header("Location: http://".$_SERVER["HTTP_HOST"].$_SERVER["SCRIPT_NAME"]);
  exit;
}
if (CHECK_DOS and !SETUP_AUTH_NTLM_SSO and !DEBUG) pre_sys_checkdos();

require("core/functions.php");
require("lib/smarty/Smarty.class.php");

if (!defined("NOCONTENT")) ob_start();
set_error_handler("debug_handler");

if (empty($_SERVER["SERVER_ADDR"])) $_SERVER["SERVER_ADDR"]="127.0.0.1";
if (!isset($_SERVER["HTTP_USER_AGENT"])) $_SERVER["HTTP_USER_AGENT"]="mozilla/5 rv:1.4";
if (!isset($_SERVER["SERVER_SOFTWARE"])) $_SERVER["SERVER_SOFTWARE"]="Apache";

if (!defined("NOCONTENT") and !login_browser_detect() and !DEBUG and empty($_REQUEST["export"])) sys_die(t("{t}Browser not supported{/t}").": ".sys::$browser["str"],login::browser_detect_toString());

sys::init();

if (!defined("NOCONTENT")) {
  folder_process_session_request();
  folder_build_folders();
  $GLOBALS["table"] = db_get_schema($GLOBALS["schemafile"],$GLOBALS["tfolder"],$GLOBALS["tview"],true,!empty($_REQUEST["popup"]));
  $GLOBALS["tname"] = $GLOBALS["table"]["att"]["NAME"];

  if (!empty($GLOBALS["table"]["att"]["LOAD_LIBRARY"])) require($GLOBALS["table"]["att"]["LOAD_LIBRARY"]);
  sys_process_session_request();

  if (!empty($GLOBALS["current_view"]["ENABLE_CALENDAR"])) {
    date::process_session_request();
	$session = $_SESSION[ $GLOBALS["tname"] ][ "_".$GLOBALS["tfolder"] ];
	date::build_datebox($session["today"], $session["markdate"], $session["weekstart"]);
  }
  asset_process_session_request();

  if (!empty($GLOBALS["current_view"]["ENABLE_CALENDAR"]) and (empty($_REQUEST["iframe"]) or $_REQUEST["iframe"]=="2")) {
	date::build_views();
  }
  $output = ob_get_contents();
  ob_end_clean();
  if (!empty(sys::$alert) or trim($output)!="") sys_message_box(t("{t}Error{/t}").":",$output.implode("\n",sys::$alert));
  sys_process_output();
}

function pre_sys_checkdos() {
  if (defined("NOCONTENT") or !empty($_SERVER["HTTP_X_MOZ"]) or !APC) return;
  if (isset($_SERVER["HTTP_CLIENT_IP"])) $ip = $_SERVER["HTTP_CLIENT_IP"];
    else if (isset($_SERVER["HTTP_X_FORWARDED_FOR"])) $ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
    else if (isset($_SERVER["REMOTE_ADDR"])) $ip = $_SERVER["REMOTE_ADDR"];
    else return;

  $ip = filter_var($ip, FILTER_VALIDATE_IP);
  if (($val = apc_fetch("dos".$ip))===false) $val=0;
  apc_store("dos".$ip, ++$val, 1);
  if ($val>2) {
	if (empty($_SERVER["HTTP_ACCEPT_LANGUAGE"])) { // client
	  header("HTTP/1.0 408 Request timeout");
	} else {
      echo "<html><body><script>setTimeout('document.location.reload()',1500);</script>Please wait ...<noscript>Please hit reload.</noscript></body></html>"; 
	}
	exit;
  }
}