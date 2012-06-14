<?php
/**
 * @package Simple Groupware
 * @link http://www.simple-groupware.de
 * @author Thomas Bley
 * @copyright Copyright (C) 2002-2012 by Thomas Bley
 * @license GPLv2
 */

if (!defined("MAIN_SCRIPT")) exit;

@ini_set("display_errors","1");
@ini_set("output_buffering","0");
@set_time_limit(1800);

header("Cache-Control: private, max-age=1, must-revalidate");
header("Pragma: no-cache");

define("CORE_VERSION","0_800");
define("CORE_VERSION_STRING","0.800");
define("CORE_SGSML_VERSION","5_00");
define("SIMPLE_CACHE","simple_cache/");
define("SIMPLE_STORE","simple_store/");
define("SIMPLE_CUSTOM","custom/");
define("SIMPLE_EXT","ext/");
define("USE_SYSLOG_FUNCTION",0);
define("CHMOD_DIR",777);
define("CHMOD_FILE",666);
define("DB_SLOW",0.5);
define("LANG",!empty($_REQUEST["lang"])?$_REQUEST["lang"]:"en");

define("NOW",time());
define("DEBUG",false);
define("DEBUG_SQL",false);
define("FORCE_SSL",false);
define("APC",false);
define("CSV_CACHE",300);
define("INDEX_LIMIT",16384);
define("FILE_TEXT_CACHE",15552000);
define("VIRUS_SCANNER","");

if (strpos(PHP_OS,"WIN")!==false) $sep = ";"; else $sep = ":";
$include_path = explode($sep,ini_get("include_path"));
if (!in_array(".",$include_path)) {
  setup_exit(sprintf("Please modify your php.ini or add an .htaccess file changing the setting '%s' to '%s' (current value is '%s') !","include_path",".".$sep.implode($sep,$include_path),ini_get("include_path")),1);
}

$phpversion = "5.2.0";
if (version_compare(PHP_VERSION, $phpversion, "<")) {
  setup_exit(sprintf("Setup needs php with at least version %s ! (".PHP_VERSION.")",$phpversion),3);
}
if (version_compare(PHP_VERSION,'5.3','>') and !ini_get('date.timezone')) {
  date_default_timezone_set(@date_default_timezone_get());
}

require("core/functions.php");
require("lib/smarty/Smarty.class.php");

$databases = setup::validate_system();

$old_file = "simple_store/config_old.php";
if (file_exists($old_file) and filemtime($old_file)>time()-86400) {
  $_REQUEST["auto_update"] = true;
  $_REQUEST["accept_gpl"] = "yes";
  $_REQUEST["admin_user"] = setup_update::get_config_old("SETUP_ADMIN_USER");
  $_REQUEST["admin_pw"] = setup_update::get_config_old("SETUP_ADMIN_PW");
  $_REQUEST["db_type"] = setup_update::get_config_old("SETUP_DB_TYPE");
  $_REQUEST["db_host"] = setup_update::get_config_old("SETUP_DB_HOST");
  $_REQUEST["db_name"] = setup_update::get_config_old("SETUP_DB_NAME");
  $_REQUEST["db_user"] = setup_update::get_config_old("SETUP_DB_USER");
  $_REQUEST["db_pw"] = sys_decrypt(setup_update::get_config_old("SETUP_DB_PW"),sha1(setup_update::get_config_old("SETUP_ADMIN_USER")));
}

define("USE_DEBIAN_BINARIES",setup_update::get_config_old("USE_DEBIAN_BINARIES",false,0));
define("SMTP_REMINDER",setup_update::get_config_old("SMTP_REMINDER",false,""));
if (empty($_SERVER["SERVER_ADDR"])) $_SERVER["SERVER_ADDR"]="127.0.0.1";

// TODO change
// setup::build_customizing(SIMPLE_CUSTOM."customize.php");
setup::dirs_create_default_folders();
if (isset($_REQUEST["install"]) and isset($_REQUEST["accept_gpl"]) and $_REQUEST["accept_gpl"]=="yes") {
  install($databases);
} else if (!empty($_REQUEST["lang"])) {
  setup::show_form($databases, !empty($_REQUEST["install"]), !empty($_REQUEST["accept_gpl"]));
} else {
  setup::show_lang();
}

function install($databases) {
  setup::install_header();
  
  $_SESSION["groups"] = array();
  $_SESSION["username"] = "setup";
  $_SESSION["password"] = "";
  $_SESSION["permission_sql"] = "1=1";
  $_SESSION["permission_sql_read"] = "1=1";
  $_SESSION["permission_sql_write"] = "1=1";

  define("SETUP_DB_TYPE",$_REQUEST["db_type"]);
  $version = setup::validate_input($databases);  
  $update = sgsml_parser::table_column_exists("simple_sys_tree","id");
  setup::out('<img src="http://www.simple-groupware.de/cms/logo.php?v='.CORE_VERSION.'&u='.(int)$update.'1&p='.PHP_VERSION.'_'.PHP_OS.'&d='.
	SETUP_DB_TYPE.$version.'" style="width:1px; height:1px;">',false);

  setup::out(t("{t}Processing %s ...{/t}","schema updates"));
  setup_update::change_database_pre();

  if (SETUP_DB_TYPE=="sqlite") {
	sql_query("begin");
	admin::rebuild_schema(false);
	sql_query("commit");
  } else {
	admin::rebuild_schema(false);
  }

  setup_update::change_database_post();

  setup::out(t("{t}Processing %s ...{/t}","sessions"));
  db_delete("simple_sys_session",array(),array());

  setup::out(t("{t}Processing %s ...{/t}","default groups"));
  $groups = array("admin_calendar","admin_news","admin_projects","admin_bookmarks","admin_contacts",
				  "admin_inventory","admin_helpdesk","admin_organisation","admin_files","admin_payroll",
				  "admin_surveys","admin_hr","admin_intranet","users_self_registration");
  foreach ($groups as $group) trigger::creategroup($group);

  setup_update::database_triggers();
  
  setup::out(t("{t}Processing %s ...{/t}","folder structure"));
  $count = db_select_value("simple_sys_tree","id",array());
  if (empty($count)) {
	$folders = "modules/core/folders.xml";
	if (!empty($_REQUEST["folders"]) and file_exists(sys_custom($_REQUEST["folders"]))) {
	  $folders = $_REQUEST["folders"];
	}
	if (SETUP_DB_TYPE=="sqlite") {
	  sql_query("begin");
	  folders::create_default_folders($folders,0,true);
	  sql_query("commit");
	} else {
	  folders::create_default_folders($folders,0,true);
	}
  }

  setup_update::database_folders();
  
  setup::out(t("{t}Processing %s ...{/t}","css"));
  admin::build_css();
  
  setup::out(t("{t}Processing %s ...{/t}","js"));
  admin::build_js();
  
  setup::out(t("{t}Processing %s ...{/t}","icons"));
  admin::build_icons();
  
  setup::out(t("{t}Processing %s ...{/t}","config.php"));
  $vars = array(
	"SETUP_DB_TYPE"=>"'".$_REQUEST["db_type"]."'",
	"SETUP_DB_HOST"=>"'".$_REQUEST["db_host"]."'",
	"SETUP_DB_NAME"=>"'".$_REQUEST["db_name"]."'",
	"SETUP_DB_USER"=>"'".$_REQUEST["db_user"]."'",
	"SETUP_DB_PW"=>"'".sys_encrypt($_REQUEST["db_pw"],sha1($_REQUEST["admin_user"]))."'",
	"SETUP_ADMIN_USER"=>"'".$_REQUEST["admin_user"]."'",
	"SETUP_ADMIN_PW"=>"'".(isset($_REQUEST["auto_update"])?$_REQUEST["admin_pw"]:sha1($_REQUEST["admin_pw"]))."'",
  );
  setup::save_config($vars);
  setup::install_footer();
  db_optimize_tables();
}

function setup_exit($str,$err) {
  echo '
    <html><body><center>
	<img src="http://www.simple-groupware.de/cms/logos.php?v='.CORE_VERSION.'&d='.PHP_VERSION.'_'.PHP_OS.'&e='.$err.'" start="width:1px; height:1px;">
    <div style="border-bottom: 1px solid black; letter-spacing: 2px; font-size: 18px; font-weight: bold;">Simple Groupware '.CORE_VERSION_STRING.' - Setup</div>
	<br><div>'.t("{t}Error{/t}").':</div>
	<error>'.htmlspecialchars($str, ENT_QUOTES).'</error><br><br>
	<div><a href="index.php">'.t("{t}Relaunch Setup{/t}").'</a></div>
	<br><hr>
	<a href="http://www.simple-groupware.de/cms/Main/Installation" target="_blank">Installation manual</a>
	<hr><br>
  ';
  phpinfo();
  echo '</body></html>';
  exit;
}