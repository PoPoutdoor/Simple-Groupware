<?php
/**
 * @package Simple Groupware
 * @link http://www.simple-groupware.de
 * @author Thomas Bley
 * @copyright Copyright (C) 2002-2012 by Thomas Bley
 * @license GPLv2
 */

$obj = htmlspecialchars($_REQUEST["obj"], ENT_QUOTES);
echo "
    <html>
  	<head>
	  <title>Simple Groupware Icons</title>
	  <script>
	  	function select(value) {
		  opener.set_val('{$obj}',value);
		  window.close();
		}
	  </script>
	  <style>
	  	a { text-decoration: none; }
		img { border:0px; }
		td { padding-right:20px; }
	  </style>
	</head>
  	<body>
	  <h3>Simple Groupware Icons</h3>
	  <table><tr>
";
$i=0;
$path = "./";
$dir = opendir($path);
while (($file=readdir($dir))) {
  if ($file[0]=="." or is_dir($path.$file) or strpos($file,".php")) continue;
  $i++;
  echo "<td><a href='#' onclick='select(\"{$file}\");'><img src='{$path}{$file}'><br/>{$file}</a></td>";
  if ($i%5==0) echo "</tr><tr><td colspan='5'><hr></td></tr><tr>";
}
closedir($dir);
echo "
  	  </tr></table>
	</body></html>
";