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
@set_time_limit(600);

if (!sys_is_super_admin($_SESSION["username"])) sys_die("{t}Not allowed. Please log in as super administrator.{/t}");

extensions::header();

foreach (array("../old/", SIMPLE_EXT) as $folder) {
  if (!is_writable($folder)) setup::out_exit(sprintf("{t}Please give write access to %s{/t}",$folder));
}

if ((empty($_REQUEST["extension"]) and empty($_REQUEST["uninstall"]) and empty($_REQUEST["cfile"])) or !sys_validate_token()) {
  setup::out("
	<div style='color:#ff0000;'>
	<b>{t}Warning{/t}</b>:<br>
	- Please make a complete backup of your database (e.g. using phpMyAdmin)<br>
	- Please make a complete backup of your sgs folder (e.g. /var/www/htdocs/sgs/)<br>
	- Make sure both backups are complete!
    </div>
  ");
  setup::out("{t}Downloading extension list{/t} ...<br>");

  $url = "http://sourceforge.net/projects/simplgroup/files/simplegroupware_modules/modules.xml";
  if (!($data = sys_cache_get("modules.xml"))) {
	$data = @file_get_contents($url);
	sys_cache_set("modules.xml", $data, 3600);
  }
  if (($xml = @simplexml_load_string($data))) {
	foreach ($xml as $package) {
	  $php_version = (string)$package->php_version;
	  $sgs_version = (string)$package->require_version;

	  $target = SIMPLE_EXT.substr(basename($package->filename),0,-3);
	  if (file_exists($target)) continue;
	  $id = md5($package->filename);
	  
	  if (version_compare(PHP_VERSION, $php_version, "<")) {
		setup::out(sprintf("{t}Setup needs php with at least version %s !{/t} ", $php_version), false);
	  } else if (version_compare(CORE_VERSION_STRING, $sgs_version, "<")) {
		setup::out(sprintf("{t}Setup needs Simple Groupware with at least version %s !{/t} ", $sgs_version), false);
	  } else {
		setup::out("<a href='extensions.php?token=".modify::get_form_token()."&extension=".$package->name."&filename=".$package->filename."'>{t}I n s t a l l{/t}</a> ", false);
	  }
	  setup::out($package->title." <a href='#' onclick='return showhide(\"".$id."\")'>{t}Info{/t}</a>", false);
	  setup::out("<br><div class='description' style='display:none;' id='".$id."'>".nl2br(trim($package->description))."</div>");
	}
  } else {
	setup::out(sprintf("{t}Connection error: %s [%s]{/t}", $url, "HTTP")."<br>".strip_tags($data, "<br><p><h1><center>"));
  }
  
  setup::out("{t}Package from local file system (.tar.gz){/t}:<br/>{t}current path{/t}: ".str_replace("\\","/",getcwd())."/<br/>");

  $dir = opendir("./");
  while (($file=readdir($dir))) {
    if ($file!="." and $file!=".." and preg_match("|^SimpleGroupware\_.*?.tar\.gz\$|i",$file)) {
	  setup::out("<a href='extensions.php?token=".modify::get_form_token()."&cfile=".$file."'>{t}I n s t a l l{/t}</a>&nbsp; ".$file."<br/>");
	}
  }
  closedir($dir);
  setup::out("<form method='POST'><input type='hidden' name='token' value='".modify::get_form_token()."'><input type='text' name='cfile' value='/tmp/SimpleGroupware_SomeExtension_0.x.tar.gz' style='width:300px;'>&nbsp;<input type='submit' class='submit' value='{t}I n s t a l l{/t}'><br>");
  
  $can_uninstall = false;
  foreach (scandir(SIMPLE_EXT) as $file) {
    if ($file[0]=="." or !is_dir(SIMPLE_EXT.$file) or !file_exists(SIMPLE_EXT.$file."/package.xml")) continue;

	$package = simplexml_load_file(SIMPLE_EXT.$file."/package.xml");
	$id = md5($package->filename);

	setup::out("<a onclick='if (!confirm(\"{t}Really uninstall the module ?{/t}\")) return false;' href='extensions.php?token=".modify::get_form_token()."&uninstall=".$package->filename."'>{t}U n i n s t a l l{/t}</a> ".$package->title, false);
	setup::out(" <a href='#' onclick='return showhide(\"".$id."\")'>{t}Info{/t}</a>", false);
	setup::out(" ({t}installed{/t} ".sys_date("{t}m/d/Y{/t}", filemtime(SIMPLE_EXT.$file)).")");
	setup::out("<div class='description' style='display:none;' id='".$id."'>".nl2br(trim($package->description))."</div>");
	$can_uninstall = true;
  }
  if ($can_uninstall) setup::out("<b>{t}Note{/t}:</b> {t}Uninstall does not delete any data in the database.{/t}<br>");
  setup::out_exit('<div style="border-top: 1px solid black;">Powered by Simple Groupware, Copyright (C) 2002-2012 by Thomas Bley.</div></div></body></html>');
}

if (!empty($_REQUEST["uninstall"])) {
  extensions::uninstall(substr($_REQUEST["uninstall"],0,-3));
  extensions::update_modules_list();
  setup::out("<br><a href='extensions.php'>{t}C O N T I N U E{/t}</a>");
  setup::out_exit('<br><div style="border-top: 1px solid black;">Powered by Simple Groupware, Copyright (C) 2002-2012 by Thomas Bley.</div></div></body></html>');
}

if (!empty($_REQUEST["cfile"])) {
  $source = $_REQUEST["cfile"];
  $filename = basename($source);
  if (!file_exists($source) or filesize($source)==0) sys_die("{t}Error{/t}: file-check [0] ".$source);

  // TODO optimize
  $tar_object = new Archive_Tar($source);
  $tar_object->setErrorHandling(PEAR_ERROR_PRINT);
  $file_list = $tar_object->ListContent();

  if (!is_array($file_list) or empty($file_list[0]["filename"])) {
	sys_die("{t}Error{/t}: file-check [0b] ".$source);
  }
  $extension = dirname($file_list[0]["filename"]);
} else if (!empty($_REQUEST["filename"]) and !empty($_REQUEST["extension"])) {
  $filename = $_REQUEST["filename"];
  $extension = $_REQUEST["extension"];
  $source = "http://sourceforge.net/projects/simplgroup/files/simplegroupware_modules/".$extension."/".$filename."/download";
} else {
  sys_die("Missing parameters.");
}
if (file_exists(SIMPLE_EXT.$extension."/package.xml")) {
  $xml = simplexml_load_file(SIMPLE_EXT.$extension."/package.xml");
  extensions::uninstall(substr($xml->filename,0,-3));
  setup::out("<hr>");
}
extensions::install($source, $filename);
extensions::footer();