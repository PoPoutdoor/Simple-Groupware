<?php
/**
 * @package Simple Groupware
 * @link http://www.simple-groupware.de
 * @author Thomas Bley
 * @copyright Copyright (C) 2002-2012 by Thomas Bley
 * @license GPLv2
 */

define("NOCONTENT",true);
error_reporting(E_ALL);

if (ini_get("register_globals")) {
  foreach (array_keys($GLOBALS) as $key) if (!in_array($key,array("GLOBALS","_REQUEST","_SERVER"))) unset($GLOBALS[$key]);
}
@include("simple_store/config.php");
if (!defined("SETUP_DB_HOST")) exit;
@ignore_user_abort(0);


if (!empty($_REQUEST["image"])) {
  $file_conf = sys_custom("templates/css/core_css.conf");
  $image = basename($_REQUEST["image"]);
  if (!empty($_REQUEST["color"])) $newcolor = $_REQUEST["color"]; else $newcolor = "";

  predownload($file_conf,$image.$newcolor);

  if (in_array($image,array("folder1","folder2"))) {
    if ($newcolor=="") {
      header("HTTP/1.1 303 See Other");
	  header("Location: ext/".$image.".gif");
    } else if ($newcolor!="") {
	  image_newcolor("ext/icons/".$image.".gif",$file_conf,$newcolor);
	}
  } else {
	$image_file = "ext/icons/folder".(strpos($image,"1")?"1":"2").".gif";
	if ($newcolor!="") image_newcolor($image_file,$file_conf,$newcolor);
    header("HTTP/1.1 303 See Other");
	header("Location: ".$image_file);
  }
}
if (isset($_REQUEST["search"])) {
  if (isset($_SERVER["HTTPS"]) and $_SERVER["HTTPS"]=="on") $proto = "https"; else $proto = "http";
  if (FORCE_SSL) $proto = "https";
  $url = $proto."://".$_SERVER["HTTP_HOST"].str_replace("images.php","",$_SERVER["SCRIPT_NAME"]);
  echo '<?xml version="1.0"?>
<OpenSearchDescription xmlns="http://a9.com/-/spec/opensearch/1.1/">
<ShortName>'.htmlspecialchars(APP_TITLE,ENT_QUOTES).'</ShortName>
<Description>'.htmlspecialchars(APP_TITLE,ENT_QUOTES).'</Description>
<Image height="16" width="16" type="image/x-icon">'.$url.'ext/images/favicon.ico</Image>
<Url type="text/html" method="get" template="'.$url.'index.php?folder=1&amp;view=search&amp;search={searchTerms}"/>
<!--<Url type="application/x-suggestions+json" method="GET" template=""/>-->
</OpenSearchDescription>';
  exit;
}

function sys_custom($file) {
  if (file_exists(SIMPLE_CUSTOM.$file)) return SIMPLE_CUSTOM.$file;
  return $file;
}

function image_newcolor($image_file,$file_conf,$newcolor) {
  $rgb = array('r' => hexdec(substr($newcolor,0,2)), 'g' => hexdec(substr($newcolor,2,2)), 'b' => hexdec(substr($newcolor,4,2)));
  $img = greyscale(imagecreatefromgif(sys_custom($image_file)));
  $img = imageselectivesolor($img,floor(($rgb["r"]+30)/2.55),floor(($rgb["g"]+30)/2.55),floor(($rgb["b"]+30)/2.55));
  download($file_conf,"image/png","",false); 
  imagepng($img);
  exit;
}

function greyscale($img) {
  for ($i=0; $i < imagecolorstotal($img); $i++) {
 	$c = ImageColorsForIndex($img, $i);
 	$t = ($c["red"]+$c["green"]+$c["blue"])/3;
    imagecolorset($img, $i, $t, $t, $t);   
  }
  return $img;
}

function imageselectivesolor($img,$red,$green,$blue) {
  for($i=0;$i<imagecolorstotal($img);$i++) {
    $col=ImageColorsForIndex($img,$i);
    $red_set=$red/100*$col['red'];
    $green_set=$green/100*$col['green'];
    $blue_set=$blue/100*$col['blue'];
    if($red_set>255)$red_set=255;
    if($green_set>255)$green_set=255;
    if($blue_set>255)$blue_set=255;
    imagecolorset($img,$i,$red_set,$green_set,$blue_set);
  }
  return $img;
}

function predownload($filename,$id) {
  if (DEBUG) return;
  $modified = filemtime($filename);
  $etag = '"'.md5($filename.$id.$modified.CORE_VERSION).'"';
  header("Last-Modified: ".gmdate("D, d M Y H:i:s", $modified)." GMT");
  header("ETag: $etag");
  if (!empty($_SERVER["HTTP_IF_NONE_MATCH"]) and $etag == stripslashes($_SERVER["HTTP_IF_NONE_MATCH"])) {
    header("HTTP/1.1 304 Not Modified");
	exit;
  }
}

function download($filename,$mimetype,$output,$show) {
  $until = 3600*24*30;
  if (DEBUG) $until = 1;
  header("Content-Type: ".$mimetype."; charset=utf-8");
  header("Cache-Control: public, max-age=".$until.", must-revalidate");
  header("Pragma: public");
  header("Expires: ".gmdate("D, d M Y H:i:s", NOW+$until)." GMT");
  if (!$show) return;
  if ($mimetype=="text/css" and CORE_COMPRESS_OUTPUT and isset($_SERVER["HTTP_ACCEPT_ENCODING"]) and
	  strpos($_SERVER["HTTP_ACCEPT_ENCODING"],"gzip")!==false and !@ini_get("zlib.output_compression")) {
	if ($output=="") $hash = "SGS".strlen($filename).crc32($filename).filemtime($filename); else $hash = "SGS".strlen($output).crc32($output);
	header("Content-Encoding: gzip");
    $cache_file = SIMPLE_CACHE."/output/".$hash.".cache";
	if (!file_exists($cache_file) or filesize($cache_file)==0) {
      if ($output=="") $output = file_get_contents($filename);
	  $output = gzencode($output);
	  file_put_contents($cache_file, $output, LOCK_EX);
	} else $output = file_get_contents($cache_file);
	exit($output);
  } else {
    if ($output=="") $output = file_get_contents($filename);
	exit($output);
  }
}