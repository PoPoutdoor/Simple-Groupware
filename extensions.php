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

if (!sys_is_super_admin($_SESSION["username"])) sys_die(t("{t}Not allowed. Please log in as super administrator.{/t}"));

extensions::header();

foreach (array("../old/", SIMPLE_EXT) as $folder) {
  if (!is_writable($folder)) setup::out_exit(t("{t}Please give write access to %s{/t}",$folder));
}

if ((empty($_REQUEST["extension"]) and empty($_REQUEST["uninstall"]) and empty($_REQUEST["cfile"])) or !sys_validate_token()) {
  extensions::showlist();
}

if (!empty($_REQUEST["uninstall"])) {
  extensions::uninstall(substr($_REQUEST["uninstall"],0,-3));
  extensions::update_modules_list();
  setup::out("<br><a href='extensions.php'>".t("{t}C O N T I N U E{/t}")."</a>");
  setup::out_exit('<br><div style="border-top: 1px solid black;">Powered by Simple Groupware, Copyright (C) 2002-2012 by Thomas Bley.</div></div></body></html>');
}

if (!empty($_REQUEST["cfile"])) {
  $source = $_REQUEST["cfile"];
  $filename = basename($source);
  if (!file_exists($source) or filesize($source)==0) sys_die(t("{t}Error{/t}").": file-check [0] ".$source);

  // TODO optimize
  $tar_object = new Archive_Tar($source);
  $tar_object->setErrorHandling(PEAR_ERROR_PRINT);
  $file_list = $tar_object->ListContent();

  if (!is_array($file_list) or empty($file_list[0]["filename"])) {
	sys_die(t("{t}Error{/t}").": file-check [0b] ".$source);
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