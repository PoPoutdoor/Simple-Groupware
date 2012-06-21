<?php
/**
 * @package Simple Groupware
 * @link http://www.simple-groupware.de
 * @author Thomas Bley
 * @copyright Copyright (C) 2002-2012 by Thomas Bley
 * @license GPLv2
 */

if (!defined("MAIN_SCRIPT")) exit;

require(sys_trans("core/functions/funcs.php","funcs"));

function sys_trans($file, $class) {
  $cache_file = SIMPLE_CACHE."/lang/".basename($class)."_".LANG."_".filemtime($file).".php";
  if (!file_exists($cache_file)) {
	@mkdir(SIMPLE_CACHE."/lang/");
	file_put_contents($cache_file, preg_replace("!<\?\s!", "<?php ", trans(file_get_contents($file))), LOCK_EX);
	if (DEBUG and empty($_REQUEST["iframe"]) and ob_get_level()==0) echo "reload lang ".$class;
  }
  return $cache_file;
}

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
	  // TODO append custom lang file?
	  @mkdir(SIMPLE_CACHE."/lang/");
	  file_put_contents($cache_file, "<?php \$strings = ".var_export($strings,true).";", LOCK_EX);
	}
	require($cache_file);
  }
  if (isset($strings[$str])) return $strings[$str];
  return $str;
}