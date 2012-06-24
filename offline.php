<?php
/**
 * @package Simple Groupware
 * @link http://www.simple-groupware.de
 * @copyright Simple Groupware Solutions Thomas Bley 2002-2012
 * @license GPLv2
 */

// TODO2 fix resize iframe with images (graphviz)

define("NOCONTENT",true);
define("NOSESSION",true);
require("index.php");

sys_check_auth();
login_browser_detect();

$folder_offline = db_select_value("simple_sys_tree","id","anchor=@anchor@",array("anchor"=>"offline_".$_SESSION["username"]));
$rows = db_select("simple_offline","*","folder=@folder@","id asc","",array("folder"=>(int)$folder_offline));
if (!is_array($rows)) exit("No entries found.");

foreach ($rows as $key=>$row) $rows[$key] = populate_row($row);
uasort($rows, "sort_rows");  

$tpl = new template();
$tpl->sync = isset($_REQUEST["sync"]) ? 1 : 0;
$tpl->username = $_SESSION["username"];
$tpl->browser = sys::$browser;
$tpl->rows = $rows;
echo $tpl->render("templates/offline.php");

function sort_rows($a, $b) {
  if ($a["path"]==$b["path"]) return 0;
  if ($a["path"]<$b["path"]) return -1; else return 1;
}

function populate_row($row) {
  $row["view"] = "display";
  $row["folder"] = 0;
  if (($pos=strpos($row["url"],"?"))) {
    $url = array();
	parse_str(substr($row["url"],$pos+1),$url);
	$row = array_merge($row,$url);
  }
  $row["path"] = modify::getpath($row["folder"]);
  return $row;
}