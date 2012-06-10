<?php
/**
 * @package Simple Groupware
 * @link http://www.simple-groupware.de
 * @author Thomas Bley
 * @copyright Copyright (C) 2002-2012 by Thomas Bley
 * @license GPLv2
 */

class cms {

static $cache_file = "";
static $time_start = 0;

private $smarty = null;
private $page = array();

function __construct() {
  set_error_handler("debug_handler");
  if (ini_get("magic_quotes_gpc")!==false and get_magic_quotes_gpc()) modify::stripslashes($_REQUEST);
  if (ini_get("register_globals")) modify::dropglobals();
  @ignore_user_abort(0);

  self::$time_start = sys_get_microtime();

  if (!sql_connect(SETUP_DB_HOST, SETUP_DB_USER, sys_decrypt(SETUP_DB_PW,sha1(SETUP_ADMIN_USER)), SETUP_DB_NAME)) {
	$err = sprintf("{t}Cannot connect to database %s on %s.{/t}\n",SETUP_DB_NAME,SETUP_DB_HOST).sql_error();
	trigger_error($err,E_USER_ERROR);
	sys_die($err);
  }

  session_set_cookie_params(2592000); // 1 month
  session_name(SESSION_NAME);
  session_set_save_handler("_login_session_none","_login_session_none","_login_session_read","_login_session_none","_login_session_destroy","_login_session_none");
  session_start();
  header("Cache-Control: private, max-age=1, must-revalidate");
  header("Pragma: private");

  $this->smarty = new Smarty;
  $this->smarty->compile_dir = SIMPLE_CACHE."/smarty";
  $this->smarty->template_dir = "templates";
  $this->smarty->register_prefilter("pmwiki_url");
  class_exists("modify"); // load class
}

function __destruct() {
  $time = number_format(sys_get_microtime()-self::$time_start,2);
  echo "<!-- ".$time."s -->";
  if ($time > CMS_SLOW) sys_log_message_log("cms-slow",sprintf("{t}%s secs{/t}",$time)." ".$_REQUEST["page"],var_export($_REQUEST,true));

  if (DEBUG and function_exists("memory_get_usage") and function_exists("memory_get_peak_usage")) {
	echo "<!-- ".modify::filesize(memory_get_usage())." - ".modify::filesize(memory_get_peak_usage())." -->";
  }
}
  
function render_page() {
  $pagename = $_REQUEST["page"];
  global $FmtPV;
  $FmtPV['$RequestedPage'] = "'$pagename'";

  $this->page = PageDbStore::read($pagename);
  if (!empty($this->page["id"])) return;
  if (PageDbStore::exists($pagename)) {
	$this->page = PageDbStore::read("Site.Authform");
	if (empty($this->page["id"])) sys_die("{t}Page not found{/t}: ".$pagename.", Site.Authform");
  } else {
	header('HTTP/1.1 404 Not Found');
	$this->page = PageDbStore::read("Site.PageNotFound");
	if (empty($this->page["id"])) sys_die("{t}Page not found{/t}: ".$pagename.", Site.PageNotFound");
  }
}

function output() {
  if (isset($_REQUEST["rss"])) $this->_output_rss();
  if (isset($_REQUEST["sitemap"])) $this->_output_sitemap();
  $this->smarty->assign_by_ref("cms", $this);
  $this->smarty->assign("page", $this->page);
  $this->smarty->assign("config", array("cms_title"=>CMS_TITLE));

  $output = $this->smarty->fetch("cms/".basename($this->page["template"]));
  if ($output=="") {
	sys_log_message_log("cms-fail",sprintf("{t}Output empty: %s{/t}",$this->page["pagename"]." ".$this->page["template"]),var_export($_REQUEST,true));
	$output = $this->smarty->fetch("cms/pmwiki.tpl");
  }
  echo $output;
  if (self::$cache_file!="" and $output!="" and $this->page["staticcache"]=="1" and sys_is_guest($_SESSION["username"]) and strpos($this->page["rread_users"],"|anonymous|")!==false) {
	sys_mkdir(dirname(self::$cache_file));
	file_put_contents(self::$cache_file, $output, LOCK_EX);
  
	if ($this->page["attachment"]=="") return;
	$files = explode("|",trim($this->page["attachment"],"|"));
	foreach ($files as $file) {
	  copy($file,dirname(self::$cache_file)."/".modify::basename($file));
} } }
  
function exists($pagename) {
  return PageDbStore::exists($pagename);
}

function render($pagename) {
  $page = PageDbStore::read($pagename);
  if (empty($page["id"])) {
	global $FmtPV;
	$FmtPV['$RequestedPage'] = "'$pagename'";
	$page = PageDbStore::read("Site.PageNotFound");
	if (empty($page["id"])) sys_die("{t}Page not found{/t}: ".$pagename.", Site.PageNotFound");
  }
  if (isset($_REQUEST["source"])) return "<code>".nl2br(modify::htmlquote($page["data"]))."</code>";
  return pmwiki_render($page["pagename"],"(:groupheader:)".$page["data"]."(:groupfooter:)","simple_cms",$page["staticcache"],$page["lastmodified"]);
}

static function get_content_from_url($url, $regexp="", $regexp_format="", $xpath="", $time=1800, $timeout=10) {
  return pmwiki_get_content($url, $regexp, $regexp_format, $xpath, $time, $timeout);
}

static function build_cache_file() {
  $hash = "";
  $dirs = array("templates/cms/",SIMPLE_CUSTOM."templates/cms/");
  foreach ($dirs as $dir) {
	if (!is_dir($dir)) continue;
	foreach (scandir($dir) as $file) {
      if ($file[0]!="." and !is_dir($dir.$file)) $hash .= filemtime($dir.$file);
	}
  }
  $page = preg_replace("/^Main\./","",$_REQUEST["page"]);
  $page = strtolower(str_replace("/",".",$page));
  $path = SIMPLE_CACHE."/cms/".urlencode($page);
  $file = $path."/".md5($hash);

  if (!empty($_REQUEST["file"])) {
	$filename = basename($_REQUEST["file"]);
	self::build_headers($filename);
	self::$cache_file = $path."/".$filename;
	return;
  }
  $no_cache = array("logout","q");
  foreach ($no_cache as $param) {
	if (isset($_REQUEST[$param])) {
	  self::$cache_file = "";
	  return;
	}
  }
  $params = array("source","rss","sitemap");
  foreach ($params as $param) if (isset($_REQUEST[$param])) $file .= "_".$param;
  if (isset($_REQUEST["q"])) $file .= md5($_REQUEST["q"]);
  self::$cache_file = $file.".html";
}
  
static function build_headers($filename) {
  $extensions = array("gif","jpg","jpeg","png","svg");
  $ext = substr($filename,strpos($filename,".")+1);
  if (in_array($ext,$extensions)) {
	$dispo = "inline";
	$ctype = "image/jpg";
  } else {
	$dispo = "attachment";
	$ctype = "application/octet-stream";
  }
  header("Content-Type: ".$ctype."; charset=utf-8");
  header("Content-Transfer-Encoding: binary");
  header("Content-Disposition: ".$dispo."; filename=\"".$filename."\"");
}
  
static function checkdos() {
  if (isset($_SERVER["HTTP_CLIENT_IP"])) $ip = $_SERVER["HTTP_CLIENT_IP"];
	else if (isset($_SERVER["HTTP_X_FORWARDED_FOR"])) $ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
	else if (isset($_SERVER["REMOTE_ADDR"])) $ip = $_SERVER["REMOTE_ADDR"];
	else return;
  
  $ip = filter_var($ip, FILTER_VALIDATE_IP);
  if (($val = apc_fetch("dos".$ip))===false) $val=0;
  apc_store("dos".$ip, ++$val, 1);
  if ($val>2) exit("<html><body><script>setTimeout('document.location.reload()',1500);</script>{t}Please wait ...{/t}<noscript>{t}Please hit reload.{/t}</noscript></body></html>");
}

private function _set_base_url() {
  $base_url = "http".(sys_https()?"s":"")."://".$_SERVER["HTTP_HOST"];
  if (CMS_REAL_URL=="") {
	$this->page["url"] = $base_url.$_SERVER["SCRIPT_NAME"];
	$this->page["url_param"] = "?page=";
  } else {
	$this->page["url"] = $base_url.CMS_REAL_URL;
  }
}

private function _output_rss() {
  $this->_set_base_url();
  $this->smarty->assign("rss_pages", pmwiki_recent_pages(20, "and rss_include=1"));
  $this->page["template"] = "rss.tpl";
  $this->page["staticcache"] = false;
}

private function _output_sitemap() {
  $this->_set_base_url();
  $this->smarty->assign("sitemap_pages", pmwiki_recent_pages(50000));
  $this->page["template"] = "sitemap.tpl";
  $this->page["staticcache"] = false;
}
}