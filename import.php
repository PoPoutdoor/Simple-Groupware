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
require("lib/spreadsheet/Reader.php");
@set_time_limit(1800);

if (empty($_REQUEST["folder"])) sys_error("Missing parameters.","403 Forbidden");
$folder = $_REQUEST["folder"];

sys_check_auth();
import::header();

if (isset($_FILES["file"]) and is_array($_FILES["file"])) {
  $files = import::process_files();
  if (!empty($files)) {
	if (!sys_validate_token()) sys_die(t("{t}Invalid security token{/t}"));
	$folder = folder_from_path($folder);
	$validate_only = isset($_REQUEST["validate_only"]);
	foreach ($files as $file) {
	  $message = $validate_only ? t("{t}Validating %s ...{/t}") : t("{t}Processing %s ...{/t}");
	  setup::out(sprintf("<b>".$message."</b>", quote(modify::basename($file))));
	  ajax::file_import($folder, $file, array("setup", "out"), $validate_only);
	  setup::out("<hr>");
} } }

$sgsml = new sgsml($folder, "new");
$view = $sgsml->view;
$required_fields = array();
foreach ($sgsml->current_fields as $name=>$field) {
  if (empty($field["REQUIRED"])) continue;
  $required_fields[$name] = !empty($field["DISPLAYNAME"])?$field["DISPLAYNAME"]:$name;
}
import::form($folder, $required_fields);

// TODO use URL for upload