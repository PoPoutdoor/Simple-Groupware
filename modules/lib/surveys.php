<?php
/**
 * @package Simple Groupware
 * @link http://www.simple-groupware.de
 * @author Thomas Bley
 * @copyright Copyright (C) 2002-2012 by Thomas Bley
 * @license GPLv2
 */

class lib_surveys extends ajax {

private static function _get_voter_id() {
  if ($_SESSION["username"]!="anonymous") return $_SESSION["username"];
  return $_SESSION["ip"];
}

private static function _has_voted($id) {
  $user = self::_get_voter_id();
  $votedby = db_select_json("simple_surveys","votedby","id=@id@",array("id"=>$id));
  if (isset($votedby[$user])) return true;
  return false;
}

static function ajax_store_vote($folder, $votes) {
  self::_require_access($folder, "read");
  if (empty($votes) or !is_array($votes)) return "";
  
  foreach ($votes as $qid=>$vote_elems) {
    if (!is_array($vote_elems) or implode("",$vote_elems)=="") continue;
	
	$row = db_select_first("simple_surveys","answers,votedby","id=@id@","",array("id"=>$qid));
	if (empty($row)) continue;

	$answers = json_decode($row["answers"], true);
	$votedby = json_decode($row["votedby"], true);
	
	$id = self::_get_voter_id();
	if (isset($votedby[$id])) exit("{t}Already voted.{/t} (".$qid.")");

	foreach ($vote_elems as $vote_elem) {
	  if ($vote_elem=="") continue;
	  if (!isset($answers[$vote_elem])) $answers[$vote_elem] = 0;
	  $answers[$vote_elem]++;
	}
	$votedby[$id] = 0;

	db_update("simple_surveys",array("votedby"=>json_encode($votedby),"answers"=>json_encode($answers)),array("id=@id@"),array("id"=>$qid));
  }
  return $folder;
}

static function answers($val) {
  if ($val=="") return "";
  $values = json_decode($val, true);
  ksort($values);
  $output = "";
  foreach ($values as $key=>$val) $output .= $key.": ".$val."\n";
  return $output;
}

static function votedby($val) {
  if ($val=="") return "";
  $values = json_decode($val, true);
  ksort($values);
  return implode(", ", array_keys($values));
}

static function choices($var, $args, $data) {
  static $has_choices = false;

  if (empty($data["qtype"]["data"][0])) {
    if (!$has_choices) return "{t}Thanks for voting!{/t}";
    return <<<EOT
	  <input class="surveys submit bold" type="button" value="{t}V o t e !{/t}" style="margin:0px;" onclick="
		ajax('lib_surveys::ajax_store_vote', [tfolder, form_values('.surveys')], locate_folder);"/>
	  </form>
EOT;
  }
  $var = modify::htmlquote($var);
  $id = modify::htmlquote($data["_id"]);
  if (self::_has_voted($id)) return "&#10003;"; // check mark

  $has_choices = true;
  $type = $data["qtype"]["data"][0];
  
  if ($type=="text") {
	return "<input id='{$id}' class='surveys' style='width:250px;' type='text' value=''/>";
  }
  if ($type=="textarea") {
    return "<textarea id='{$id}' class='surveys' style='width:250px;'></textarea>";
  }
  if (($type=="radio" or $type=="checkbox") and $var!="") {
	$output = "";
	foreach (explode(",",$var) as $value) {
	  $output .= "<input id='{$id}' name='{$id}' class='checkbox checkbox3 surveys' type='{$type}' value='{$value}' /> {$value}<br/>";
	}
	return $output;
  }
  return "";
}
}