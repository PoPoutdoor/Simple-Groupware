<?php
/**
 * @package Simple Groupware
 * @link http://www.simple-groupware.de
 * @author Thomas Bley
 * @copyright Copyright (C) 2002-2012 by Thomas Bley
 * @license GPLv2
 */

define("MAIN_SCRIPT",basename($_SERVER["PHP_SELF"]));

error_reporting(E_ALL);
define("SIMPLE_STORE","../simple_store");
@include(SIMPLE_STORE."/config.php");
if (!defined("SETUP_DB_HOST")) exit;

require("core/functions.php");

set_error_handler("debug_handler");
if (ini_get("register_globals")) modify::dropglobals();
@ignore_user_abort(1);

header("Content-Type: text/plain; charset=utf-8");

if (!defined("SETUP_DB_HOST") or !sql_connect(SETUP_DB_HOST, SETUP_DB_USER, sys_decrypt(SETUP_DB_PW,sha1(SETUP_ADMIN_USER)), SETUP_DB_NAME)) {
  exit(sql_error());
}

@set_time_limit(180);
$lock_file = SIMPLE_STORE."/cron/lock_cron";
$_SESSION["username"] = "cron";
$_SESSION["password"] = "";
$_SESSION["groups"] = array();
$_SESSION["serverid"] = 1;
$_SESSION["permission_sql"] = "1=1";

$cron_conf = trigger::sendmail_getconn("cron", "");
if (empty($cron_conf["smtp"])) {
  $message = t("Mail identities").": ".t("SMTP not configured for %s", "cron");
  sys_log_message_log("php-fail", $message);
  exit($message);
}

if (!DEBUG and file_exists($lock_file) and filemtime($lock_file)+150 > time() and !isset($_REQUEST["debug"])) {
  exit("already running.");
}
touch($lock_file);

$notifications = db_select("simple_sys_notifications","*",array("sent=0", "delivery <= @now@", "category='email'"),"created desc","100",array("now"=>NOW));

if (!empty($notifications)) {
  $log = "";
  
  out("sending mails:\n");
  foreach ($notifications as $notification) {
	$smtp_data = array(
	  "efrom"=>"",
	  "eto"=>$notification["eto"],
	  "subject"=>$notification["subject"],
	  "message"=>trim($notification["message"]),
	  "attachment"=>$notification["attachment"],
	  "folder"=>"cron",
	);

	$result = asset_process_trigger("sendmail","",$smtp_data);
	if ($result!="") {
	  echo "ERROR ".$notification["eto"].": ".$result."\n";
	  sys_log_message_log("php-fail",$result,var_export($smtp_data,true));
	  db_update("simple_sys_notifications",array("error"=>$result),array("id=@id@"),array("id"=>$notification["id"]));
	} else {
	  $data = array("error"=>"");
	  if (!empty($notification["recurrence"])) {
		if ($notification["recurrence"][0]=="|") {
		  $match = array();
		  preg_match("/([^\|]+)\|([^&]+)/", $notification["reference"], $match);
		  if (!empty($match) and count($match)==3) {
			$row = db_select_first($match[1],"*","id=@id@","",array("id"=>$match[2]));
			if (!empty($row)) trigger::notify($match[2], $row, array(), $match[1]);
		  }
		} else {
		  $counter = 0;
		  $data["delivery"] = $notification["delivery"];
		  while ($data["delivery"] < NOW and $counter<150) {
			$data["delivery"] = strtotime($notification["recurrence"], $data["delivery"]);
			$counter++;
		  }
		}
	  } else {
	    $data["sent"] = "1";
	  }
	  db_update("simple_sys_notifications", $data, array("id=@id@"), array("id"=>$notification["id"]));
	  $log .= "ID: " . $notification["id"] . "\n";
	}
  }
  out($log);
  sys_log_message_log("info",t("Sent reminders"),$log);
}

out("finished.");
@unlink($lock_file);

function out($str) {
  if (!isset($_REQUEST["debug"])) return;
  echo $str."\n";
  flush();
  @ob_flush();
}