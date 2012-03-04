<?php
/**
 * @package Simple Groupware
 * @link http://www.simple-groupware.de
 * @author Thomas Bley
 * @copyright Copyright (C) 2002-2012 by Thomas Bley
 * @license GPLv2
 */

class import extends Spreadsheet_Excel_Reader {

private $_sgsml = null;
private $_folder = 0;
private $_validate_only = false;
private $_errors = array();

private $_fields = array();
private $_headers = array();

private $_data = array();
private $_last_row = -1;
private $_output_func = false;

function __construct() {
  parent::Spreadsheet_Excel_Reader();
  $this->setOutputEncoding("UTF-8");
  $this->setRowColOffset(0);
}

function file($file, $folder, $output_func=false, $validate_only=false) {
  if (!file_exists($file) or filesize($file)==0) return array();

  $this->_sgsml = new sgsml($folder, "new", array(), true);
  $this->_folder = $folder;
  $this->_fields = array_flip(self::get_fields($this->_sgsml));
  $this->_validate_only = $validate_only;
  $this->_output_func = $output_func;
  $this->read($file);
  return $this->_errors;
}

function process_row($row, $line) {
  $data = array("folder"=>$this->_folder);
  if (DEBUG) print_r(array($line, $row));
  foreach ($row as $key=>$val) {
	if (isset($this->_fields[$this->_headers[$key]])) $data[$this->_fields[$this->_headers[$key]]] = $val;
  }
  if (DEBUG) print_r($data);

  $id = !empty($data["id"]) ? $data["id"] : -1;
  if ($this->_validate_only) {
	$result = $this->_sgsml->validate($data, $id);
  } else {
	$result = $this->_sgsml->update($data, $id);
  }
  sys::$db_queries = array(); // reduce memory usage
  if (empty($result)) { // validate
	$this->out(".");
  } else if (is_array($result)) {
	$message = sprintf("{t}line{/t} %s: %s", $line, self::err_to_str($result));
	$this->_errors[] = $message;
	$this->out("<span style='color:red; font-weight:bold;'>{t}Error{/t}:</span> ".modify::htmlquote($message).", ");
  } else {
	$this->out("#".$line.": ".modify::htmlquote($result).", ");
  }
}

function out($message) {
  if (empty($this->_output_func)) return;
  call_user_func($this->_output_func, $message, false);
}

function addcell($row, $col, $string) {
  if ($row!=$this->_last_row and $this->_last_row!=-1) {
	if (empty($this->_headers)) {
	  $this->_headers = $this->_data;
	} else {
	  $this->process_row($this->_data, $this->_last_row+1);
	}
	$this->_data = array();
  }
  if ($row<0) return;
  $this->_data[$col] = $string;
  $this->_last_row = $row;
}

function _parsesheet($spos) {
  parent::_parsesheet($spos);
  $this->addcell(-1, 0, "");
  $this->_headers = array();
  $this->_data = array();
  $this->_last_row = -1;
}

static function err_to_str($error) {
  if (empty($error) or !is_array($error)) return "";
  $result = array();
  foreach ($error as $field) {
	foreach ($field as $error) $result[] = "{t}Column{/t} \"".$error[0]."\": ".$error[1];
  }
  return implode(", ", $result);
}

static function get_fields($sgsml) {
  $fields = array("id"=>"{t}Id{/t}", "folder"=>"{t}Folder{/t}");
  $view = $sgsml->view;
  foreach ($sgsml->current_fields as $name=>$field) {
	if (isset($field["READONLYIN"][$view]) or isset($field["READONLYIN"]["all"])) continue;
	if (!isset($field["EDITABLE"]) and (isset($field["HIDDENIN"][$view]) or isset($field["HIDDENIN"]["all"]))) continue;
	$fields[$name] = !empty($field["DISPLAYNAME"])?$field["DISPLAYNAME"]:$name;
  }
  return $fields;
}

static function header() {
  setup::out('
	<html><head>
	<title>Simple Groupware {t}Import{/t}</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<style>
	  body, h2, img, div, table.data, a {
		background-color: #FFFFFF; color: #666666; font-size: 13px; font-family: Arial, Helvetica, Verdana, sans-serif;
	  }
	  a,input { color: #0000FF; }
	  input {
		font-size: 11px; background-color: #F5F5F5; border: 1px solid #AAAAAA; height: 18px;
		vertical-align: middle; padding-left: 5px; padding-right: 5px; border-radius: 10px;
	  }
	  .checkbox, .radio { border: 0px; background-color: transparent; }
	  .submit { color: #0000FF; background-color: #FFFFFF; width: 125px; font-weight: bold; }
	  .border {	border-bottom: 1px solid black; }
	  .headline {
		letter-spacing: 2px;
		font-size: 18px;
		font-weight: bold;
	  }
	</style>
	</head>
	<body>
	<div class="border headline">Simple Groupware {t}Import{/t}</div><br>
	<a href="index.php">{t}Back{/t}</a><br>
  ');
}

static function form($folder, $required_fields) {
  setup::out_exit('
	Folder: '.modify::htmlquote(modify::getpathfull($folder)).'<br>
	<br>
	<a href="index.php?export=calc&limit=1&hide_fields=id&folder='.modify::htmlquote($folder).'&view=details">{t}Download example file{/t} (.xls)</a>
	<br>
	{t}Required fields{/t}: '.modify::htmlquote(implode(", ", $required_fields)).'
	<br><br>
	{t}File{/t} (.xls):<br>
	<form method="post" action="import.php?" enctype="multipart/form-data">
	<input type="hidden" name="token" value="'.modify::get_form_token().'">
	<input type="hidden" name="folder" value="'.modify::htmlquote($folder).'">
	<input type="File" name="file[]" value="" multiple="true" required="true">
	<input type="submit" value="{t}I m p o r t{/t}" class="submit">
	<input type="submit" name="validate_only" value="{t}V a l i d a t e{/t}" class="submit">
	</form>
	<br>
	<b>{t}Note{/t}:</b> {t}Assets can be imported into multiple folders by adding the "Folder" column.{/t}<br>
	<b>{t}Note{/t}:</b> {t}Assets can be overwritten by adding the "Id" column.{/t}<br>
	<br>
	<div style="border-top: 1px solid black;">Powered by Simple Groupware, Copyright (C) 2002-2012 by Thomas Bley.</div></div>
	</body>
	</html>
  ');
}
}