<?php
/**
 * @package Simple Groupware
 * @link http://www.simple-groupware.de
 * @copyright Simple Groupware Solutions Thomas Bley 2002-2012
 * @license GPLv2
 */

define("MAIN_SCRIPT",basename($_SERVER["PHP_SELF"]));
define("NOCONTENT",true);
error_reporting(E_ALL);

@include("simple_store/config.php");
if (!defined("SETUP_DB_HOST")) exit;

require("core/functions.php");

set_error_handler("debug_handler");
if (ini_get("magic_quotes_gpc")!==false and get_magic_quotes_gpc()) modify::stripslashes($_REQUEST);
if (ini_get("register_globals")) modify::dropglobals();
if (empty($_SERVER["SERVER_ADDR"])) $_SERVER["SERVER_ADDR"]="127.0.0.1";
@ignore_user_abort(1);

if (!sql_connect(SETUP_DB_HOST, SETUP_DB_USER, sys_decrypt(SETUP_DB_PW,sha1(SETUP_ADMIN_USER)), SETUP_DB_NAME)) {
  $err = t("{t}Cannot connect to database %s on %s.{/t}",SETUP_DB_NAME,SETUP_DB_HOST)."\n".sql_error();
  trigger_error($err,E_USER_ERROR);
  exit($err);
}

$save_session = false;
if (ini_get("suhosin.session.encrypt")) $save_session = true; // workaround for broken session_encode()
login_handle_login($save_session);

$class = "ajax";
if (!empty($_REQUEST["class"]) and strpos($_REQUEST["class"],"_ajax")) $class = $_REQUEST["class"];

if (empty($_REQUEST["function"]) and empty($_SERVER["HTTP_SOAPACTION"])) {
  $reflect = new ReflectionClass($class); 
  $output = "";
  foreach($reflect->getMethods(ReflectionMethod::IS_PUBLIC) as $reflectmethod) {
	$output .= $reflectmethod->getDocComment()."\n";
    $output .= "{$reflectmethod->getName()}(";
    foreach($reflectmethod->getParameters() as $num => $param) {
		if ($param->isArray()) $output .= " array";
		$output .= " \$".$param->getName();
		if ($param->isDefaultValueAvailable()) $output .= "=".str_replace("\n","",var_export($param->getDefaultValue(),true));
        if ($reflectmethod->getNumberOfParameters() != $num+1) $output .= ",";
    }
	$output .= " )\n\n";
  }
   sys_die("Simple Groupware Soap/Ajax Functions",$output,true);
}

if (!empty($_SERVER["HTTP_SOAPACTION"])) {
  if (!extension_loaded("soap")) sys_die(t("{t}%s is not compiled / loaded into PHP.{/t}","Soap"));
  $soap = new SoapServer(null, array('uri'=>'sgs'));
  $soap->setClass($class);
  $soap->handle();

} else if ($_SERVER["HTTP_X_REQUESTED_WITH"]=="XMLHttpRequest") {
  $func = $_REQUEST["function"];
  if ($func=="type_pmwikiarea::ajax_render_preview") require("lib/pmwiki/pmwiki.php");
  if (!function_exists("json_encode")) require("lib/json/JSON.php");

  if ((strpos($func,"_ajax::") or strpos($func,"::ajax_")) and substr_count($func,"::")==1) {
    list($class,$func) = explode("::",$func);
  }
  ajax::require_method($func, $class);
  
  if (!empty($_REQUEST["params"])) {
    $params = json_decode($_REQUEST["params"], true);
  } else {
    $params = json_decode(file_get_contents("php://input"),true);
  }
  header("Content-Type: application/json; charset=utf-8");
  echo json_encode(call_user_func_array(array($class, $func), $params));

  if (!empty($_SESSION["notification"]) or !empty($_SESSION["warning"])) ajax::session_save();
}