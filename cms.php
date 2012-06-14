<?php
/**
 * @package Simple Groupware
 * @link http://www.simple-groupware.de
 * @author Thomas Bley
 * @copyright Copyright (C) 2002-2012 by Thomas Bley
 * @license GPLv2
 */

define("MAIN_SCRIPT",basename($_SERVER["PHP_SELF"]));
$base_dir = dirname($_SERVER["SCRIPT_NAME"]);
error_reporting(E_ALL);

header("Content-Type: text/html; charset=utf-8");
@include("simple_store/config.php");
if (!defined("SETUP_DB_HOST")) {
  header("HTTP/1.0 503 Service Temporarily Unavailable");
  exit;
}
if (FORCE_SSL and (!isset($_SERVER["HTTPS"]) or $_SERVER["HTTPS"]!="on")) {
  header("Location: https://".$_SERVER["HTTP_HOST"].$_SERVER["SCRIPT_NAME"]."?".@$_SERVER["QUERY_STRING"]);
  exit;
}

if (empty($_REQUEST["page"])) $_REQUEST["page"] = CMS_HOMEPAGE;

if (isset($_REQUEST["edit"]) and !empty($_REQUEST["page"])) {
// TODO fix intranet attachment
  header("Location: {$base_dir}/index.php?view=edit&find=assets|simple_cms|1|pagename=".$_REQUEST["page"]);
  exit;
}

require("core/functions.php");
require("lib/smarty/Smarty.class.php");

cms::build_cache_file();
if (cms::$cache_file!="" and file_exists(cms::$cache_file)) {
  header("Cache-Control: private, max-age=1, must-revalidate");
  header("Pragma: private");
  readfile(cms::$cache_file);
  exit;
}

if (!empty($_REQUEST["file"]) and !empty($_REQUEST["page"])) {
  header("Location: {$base_dir}/download.php?find=asset|simple_cms|1|pagename=".$_REQUEST["page"]."&view=details&field=attachment&subitem=".$_REQUEST["file"]);
  exit;
}

if (CHECK_DOS and APC and !DEBUG) cms::checkdos();

$cms = new cms();

if (isset($_REQUEST["logout"])) {
  if (!empty($_SESSION["username"])) login::process_logout();
  if (isset($_COOKIE[SESSION_NAME])) unset($_COOKIE[SESSION_NAME]);
  $_SESSION = array();
}
if ((ENABLE_ANONYMOUS or ENABLE_ANONYMOUS_CMS) and empty($_SESSION["username"])) login_anonymous_session();
if (empty($_SESSION["username"])) sys_redirect("{$base_dir}/index.php?logout&page=".$_REQUEST["page"]);

error_reporting(E_ALL);
require_once("lib/pmwiki/pmwiki.php");

$cms->render_page();
$cms->output();