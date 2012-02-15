<?php
/**
 * @package Simple Groupware
 * @link http://www.simple-groupware.de
 * @author Thomas Bley
 * @copyright Copyright (C) 2002-2012 by Thomas Bley
 * @license GPLv2
 */

error_reporting(E_ALL);

if (ini_get("magic_quotes_gpc")!==false and get_magic_quotes_gpc()) stripslashes($_SERVER["QUERY_STRING"]);
$url = trim(str_replace(array("\n","\r","'","\"","<wbr/>"),"",urldecode(trim(substr($_SERVER["QUERY_STRING"],strpos($_SERVER["QUERY_STRING"],"=")+1)))));
$match = array();
if (preg_match("/<([^>]+)>/",$url,$match)) $url = $match[1];
$url = str_replace("ext/norefer.php?url=","",$url);

if (preg_match("!^(https?|ftp)://!i",$url)) {
  header("Location: ".$url);
} else if (strpos("@".$url,"index.php?")==1) {
  header("Location: ../".$url);
} else if (strpos("@".$url,"www.")==1) {
  header("Location: http://".$url);
} else if (preg_match("/([\S]*?@[\S]+|mailto:[\S]+)/",$url,$match)) {
  $url = str_replace(array("mailto:","(",")"),"",$match[1]);
  if (strpos(file_get_contents("../../../simple_store/config.php"),"'ENABLE_EXT_MAILCLIENT',true")) {
    echo "<script>document.location='mailto:".$url."';window.close();</script>";
  } else {
	$url = "../../index.php?onecategory=1&find=folder|simple_sys_tree|1|ftype=emails&view=new&eto=".$url;
    header("Location: ".$url);
  }
} else die("Link restricted");