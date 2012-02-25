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
@set_time_limit(1800);

header("Cache-Control: private, max-age=1, must-revalidate");
header("Pragma: no-cache");

define("CORE_VERSION","0_800");
define("CORE_VERSION_STRING","0.800");
define("CORE_SGSML_VERSION","5_00");
define("SIMPLE_CACHE","simple_cache/");
define("SIMPLE_CUSTOM","custom/");
define("SIMPLE_EXT","ext/");
define("USE_SYSLOG_FUNCTION",0);
define("CHMOD_DIR",777);
define("CHMOD_FILE",666);
define("DB_SLOW",0.5);
define("LANG","en");

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
  setup_exit(sprintf("{t}Please modify your php.ini or add an .htaccess file changing the setting '%s' to '%s' (current value is '%s') !{/t}","include_path",".".$sep.implode($sep,$include_path),ini_get("include_path")),1);
}

$phpversion = "5.2.0";
if (version_compare(PHP_VERSION, $phpversion, "<")) {
  setup_exit(sprintf("{t}Setup needs php with at least version %s !{/t} (".PHP_VERSION.")",$phpversion),3);
}
if (version_compare(PHP_VERSION,'5.3','>') and !ini_get('date.timezone')) {
  date_default_timezone_set(@date_default_timezone_get());
}

require("core/functions.php");

$databases = setup::validate_system();

$old_file = SIMPLE_STORE."/config_old.php";
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

if (!isset($_SERVER["SERVER_ADDR"]) or $_SERVER["SERVER_ADDR"]=="") $_SERVER["SERVER_ADDR"]="127.0.0.1";

// TODO change
// setup::build_customizing(SIMPLE_CUSTOM."customize.php");
setup::dirs_create_default_folders();
if (isset($_REQUEST["install"]) and isset($_REQUEST["accept_gpl"]) and $_REQUEST["accept_gpl"]=="yes") {
  install($databases);
} else {
  setup::show_form($databases, !empty($_REQUEST["install"]), !empty($_REQUEST["accept_gpl"]));
}

function install($databases) {
  setup::install_header();
  
  $_SESSION["groups"] = array();
  $_SESSION["serverid"] = 1;
  $_SESSION["username"] = "setup";
  $_SESSION["password"] = "";
  $_SESSION["permission_sql"] = "1=1";
  $_SESSION["permission_sql_read"] = "1=1";
  $_SESSION["permission_sql_write"] = "1=1";
  
  @unlink(SIMPLE_STORE."/setup.txt");
  if ($validate=validate::username($_REQUEST["admin_user"]) and $validate!="") setup::error_add("{t}Admin Username{/t} - {t}validation failed{/t} ".$validate,30);
  if ($_REQUEST["db_host"]=="") setup::error_add(sprintf("{t}missing field{/t}: %s","{t}Database Hostname / IP{/t}"),31);
  if ($_REQUEST["db_user"]=="") setup::error_add(sprintf("{t}missing field{/t}: %s","{t}Database User{/t}"),32);
  if ($_REQUEST["db_name"]=="") setup::error_add(sprintf("{t}missing field{/t}: %s","{t}Database Name{/t}"),33);
  if ($_REQUEST["admin_pw"]=="") setup::error_add(sprintf("{t}missing field{/t}: %s","{t}Admin Password{/t}"),34);
  if ($_REQUEST["admin_pw"]!="" and strlen($_REQUEST["admin_pw"])<5) setup::error_add("{t}Admin Password{/t}: {t}Password must be not null, min 5 characters.{/t}","34b");

  define("SETUP_DB_TYPE",$_REQUEST["db_type"]);

  if (!@sql_connect($_REQUEST["db_host"], $_REQUEST["db_user"], $_REQUEST["db_pw"], $_REQUEST["db_name"])) {
    if (!sql_connect($_REQUEST["db_host"], $_REQUEST["db_user"], $_REQUEST["db_pw"])) setup::error_add("{t}Connection to database failed.{/t}\n".sql_error(),35);
	setup::errors_show();
	if (!sgsml_parser::create_database($_REQUEST["db_name"])) setup::error_add("{t}Creating database failed.{/t}\n".sql_error(),36);
  }
  if (!sql_connect($_REQUEST["db_host"], $_REQUEST["db_user"], $_REQUEST["db_pw"], $_REQUEST["db_name"]) or empty(sys::$db)) {
    setup::error_add("{t}Connection to database failed.{/t}\n".sql_error(),37);
	setup::errors_show();
  }

  if (!$version = sgsml_parser::sql_version()) setup::error_add(sprintf("{t}Could not determine database-version.{/t}"),38);
  $database_min = (int)substr(str_replace(".","",$databases[SETUP_DB_TYPE][1]),0,3);
  if ($version < $database_min) setup::error_add(sprintf("{t}Wrong database-version (%s). Please use at least %s !{/t}",$version,$databases[SETUP_DB_TYPE]),"20".SETUP_DB_TYPE);

  if (sgsml_parser::table_column_exists("simple_sys_tree","id")) {
    echo '<img src="http://www.simple-groupware.de/cms/logo.php?v='.CORE_VERSION.'&u=1&p='.PHP_VERSION.'_'.PHP_OS.'&d='.SETUP_DB_TYPE.$version.'" style="width:1px; height:1px;">';
  } else {
    echo '<img src="http://www.simple-groupware.de/cms/logo.php?v='.CORE_VERSION.'&u=0&p='.PHP_VERSION.'_'.PHP_OS.'&d='.SETUP_DB_TYPE.$version.'" style="width:1px; height:1px;">';
  }
  
  if (SETUP_DB_TYPE=="pgsql") {
  	if (!sql_query("SELECT ''::tsvector;")) {
	  setup::error_add("{t}Please install 'tsearch2' for the PostgreSQL database.{/t}\n(Run <postgresql>/share/contrib/tsearch2.sql)\n".sql_error(),21);
	}
    if (!sql_query(file_get_contents("modules/core/pgsql.sql"))) setup::error_add("pgsql.sql: ".sql_error(),50);
  }
  setup::out(sprintf("{t}Processing %s ...{/t}","schema updates"));
  setup::errors_show();

  setup_update::change_database_pre();

  if (SETUP_DB_TYPE=="sqlite") {
	sql_query("begin");
	admin::rebuild_schema(false);
	sql_query("commit");
  } else {
	admin::rebuild_schema(false);
  }

  setup_update::change_database_post();

  setup::out(sprintf("{t}Processing %s ...{/t}","sessions"));
  db_delete("simple_sys_session",array(),array());

  setup::out(sprintf("{t}Processing %s ...{/t}","default groups"));
  $groups = array("admin_calendar","admin_news","admin_projects","admin_bookmarks","admin_contacts",
				  "admin_inventory","admin_helpdesk","admin_organisation","admin_files","admin_payroll",
				  "admin_surveys","admin_hr","admin_intranet","users_self_registration");
  foreach ($groups as $group) trigger::creategroup($group);

  setup_update::database_triggers();
  
  setup::out(sprintf("{t}Processing %s ...{/t}","folder structure"));
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
  
  setup::out(sprintf("{t}Processing %s ...{/t}","config.php"));

  $vars_static = array(
	"CORE_VERSION"=>"'".CORE_VERSION."'",
	"CORE_VERSION_STRING"=>"'".CORE_VERSION_STRING."'",
	"CORE_SGSML_VERSION"=>"'".CORE_SGSML_VERSION."'",
	"SETUP_DB_TYPE"=>"'".SETUP_DB_TYPE."'",
	"SETUP_DB_HOST"=>"'".$_REQUEST["db_host"]."'",
	"SETUP_DB_NAME"=>"'".$_REQUEST["db_name"]."'",
	"SETUP_DB_USER"=>"'".$_REQUEST["db_user"]."'",
	"SETUP_DB_PW"=>"'".sys_encrypt($_REQUEST["db_pw"],sha1($_REQUEST["admin_user"]))."'",
	"SETUP_ADMIN_USER"=>"'".$_REQUEST["admin_user"]."'",
	"SETUP_ADMIN_PW"=>"'".(isset($_REQUEST["auto_update"])?$_REQUEST["admin_pw"]:sha1($_REQUEST["admin_pw"]))."'",
  );
  $out = array();
  $out[] = "<?"."php";
  foreach ($vars_static as $key=>$var) $out[] = "define('".$key."',".$var.");";

  $vars = setup::config_defaults();
  foreach ($vars as $key=>$var) {
	$var = setup_update::get_config_old($key,true,$var);
	$out[] = "define('".$key."',".$var.");";
  }
  $out[] = "if (TIMEZONE!='') date_default_timezone_set(TIMEZONE);\n".
		   "  elseif (version_compare(PHP_VERSION,'5.3','>') and !ini_get('date.timezone')) date_default_timezone_set(@date_default_timezone_get());";
  $out[] = "if (!ini_get('display_errors')) @ini_set('display_errors','1');";
  $out[] = "define('NOW',time());";
  $lang = setup_update::get_config_old("lang",true,"'en'");
  $out[] = "define('LANG',".$lang.");";
  $out[] = "define('APC',function_exists('apc_store') and ini_get('apc.enabled'));";

  file_put_contents(SIMPLE_STORE."/config.php", implode("\n",$out), LOCK_EX);
  if (!file_exists(SIMPLE_STORE."/config.php") or filesize(SIMPLE_STORE."/config.php")==0) {
	sys_die("cannot write to: ".SIMPLE_STORE."/config.php");
  }
  chmod(SIMPLE_STORE."/config.php", 0600);
  sys_log_message_log("info",sprintf("{t}Setup: setup-data written to %s.{/t}",SIMPLE_STORE."/config.php"));
  setup::install_footer();
  db_optimize_tables();
}

function setup_exit($str,$err) {
  echo '
    <html><body><center>
	<img src="http://www.simple-groupware.de/cms/logos.php?v='.CORE_VERSION.'&d='.PHP_VERSION.'_'.PHP_OS.'&e='.$err.'" start="width:1px; height:1px;">
    <div style="border-bottom: 1px solid black; letter-spacing: 2px; font-size: 18px; font-weight: bold;">Simple Groupware '.CORE_VERSION_STRING.' - Setup</div>
	<br><div>{t}Error{/t}:</div>
	<error>'.htmlspecialchars($str, ENT_QUOTES).'</error><br><br>
	<div><a href="index.php">{t}Relaunch Setup{/t}</a></div>
	<br><hr>
	<a href="http://www.simple-groupware.de/cms/Main/Installation" target="_blank">Installation manual</a>
	<hr><br>
  ';
  phpinfo();
  echo '</body></html>';
  exit;
}