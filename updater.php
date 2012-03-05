<?php
/**
 * @package Simple Groupware
 * @link http://www.simple-groupware.de
 * @author Thomas Bley
 * @copyright Copyright (C) 2002-2012 by Thomas Bley
 * @license GPLv2
 */

define("NOCONTENT",true);

require("index.php");
require("lib/tar/Tar.php");
@ini_set("output_buffering","0");
@set_time_limit(1800);

if (!sys_is_super_admin($_SESSION["username"])) sys_die(t("{t}Not allowed. Please log in as super administrator.{/t}"));

updater::header();

$mirror_id = "sourceforge";
if (!empty($_REQUEST["mirror"]) and in_array($_REQUEST["mirror"],array_keys(updater::$mirrors))) $mirror_id = $_REQUEST["mirror"];
$mirror = updater::$mirrors[$mirror_id];

$move_folders = array("build/", "core/", "docs/", "ext/", "import/", "lang/", "lib/", "templates/", "tools/", "modules/");

sys_mkdir(SIMPLE_STORE."/old/");
$folders = array_merge(array("./", SIMPLE_STORE."/old/"), $move_folders);
foreach ($folders as $folder) {
  if (is_dir($folder) and !is_writable($folder)) setup::out_exit(t("{t}Please give write access to %s{/t}",$folder));
}
if ((empty($_REQUEST["release"]) and empty($_REQUEST["cfile"])) or !sys_validate_token()) {
  updater::show_list($mirror_id);

} else if (!empty($_REQUEST["cfile"])) {
  $source = $_REQUEST["cfile"];
  if (!file_exists($source) or filesize($source) < 3*1048576) sys_die(t("{t}Error{/t}").": file-check [0] ".$source);

} else {
  $release = $_REQUEST["release"];
  if ($release=="latest" or !is_numeric($release)) {
    $data = @file_get_contents($mirror["url"]);
	$match = array();
    preg_match($mirror["pattern"], $data, $match);
    if (empty($match[1])) sys_die(t("{t}Error{/t}").": file-check ".$mirror["url"]);
	$release = $match[1];
  }
  $source = sprintf($mirror["source"], $release, $release);
}

$temp_folder = SIMPLE_CACHE."/updater/";
sys_mkdir($temp_folder);

$target = $temp_folder.substr(basename($source),0,-3);
updater::download($source, $target);

$source_folder = updater::extract($target, $temp_folder);
updater::move_files($move_folders, $source_folder);
updater::extensions();
updater::footer();