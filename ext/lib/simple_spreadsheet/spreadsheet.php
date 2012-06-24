<?php
/**
 * @package Simple Groupware
 * @link http://www.simple-groupware.de
 * @copyright Simple Groupware Solutions Thomas Bley 2002-2012
 * @license GPLv2
 */

$lang = preg_replace("/[^[:alnum:]]*/","",@$_REQUEST["lang"]);
if ($lang=="") $lang = "en";

$data = preg_replace("/[^[:alnum:]_]*/","",@$_REQUEST["data"]);

$url = @$_REQUEST["url"];
$mode = @$_REQUEST["mode"];

if ($url=="" and $mode=="") {
  $url = "examples/features_en.js";
  if ($lang == "de") $url = "examples/features_de.js";
}
$init_data = "";
if (strpos("@".$url,"http://")==1 or strpos("@".$url,"https://")==1 or dirname($url)=="examples") {
  $init_data = @file_get_contents($url);
  if (!$init_data) $init_data = "\n\nCannot load ".$url;
}
header("Content-Type: text/html; charset=utf-8");

?>
<html>
<head>
  <title>Simple Spreadsheet</title>
  <link media="all" href="styles.css" rel="stylesheet" type="text/css" />
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <script src="translations/<?php echo $lang; ?>.js" type="text/javascript"></script>
  <script src="spreadsheet.js" type="text/javascript"></script>
  <script src="json.js" type="text/javascript"></script>
</head>
<body onmouseover="showHeaderFooter(true);">
<!--
	Simple Spreadsheet is an open source Component created by Thomas Bley and licensed under GNU GPL v2.
	Simple Spreadsheet is copyright 2006-2012 by Thomas Bley.
	Translations implemented by Sophie Lee.
	More information and documentation at http://www.simple-groupware.de/
-->

<div class="data" id="data"></div>
<div id="source" align="center">
<script>
var out = "";
out += trans("Simple Spreadsheet code / CSV data / Tab separated values (copy/paste from Excel):");
document.write(out);
</script>
<br><textarea id="code" wrap="off"><?php
  echo htmlspecialchars($init_data,ENT_QUOTES);
?></textarea><br>
<script>
var out = "";
out += '<table class="default_table" id="nav_table_readonly" style="display:none; width:50%; text-align:center;">';
out += '<tr><td><input type="button" value="'+trans("Cancel")+'" onclick="cancelLoad();"></td></tr>';
out += '</table>';

out += '<table class="default_table" id="nav_table" style="width:50%;">';
out += '<tr><td colspan="2"><input type="button" value="'+trans("Load")+'" onclick="load(sys.getObj(\'code\').value);" style="width:100%;"></td><td><input type="button" value="'+trans("Cancel")+'" onclick="cancelLoad();"></td></tr>';
out += '<tr><td>'+trans("Url")+'</td>';
out += '<td style="width:100%;"><input type="Text" id="code_url" value="" style="width:100%;"></td>';
out += '<td><input type="button" value="'+trans("Load")+'" onclick="document.location=\'spreadsheet.php?lang=en&url=\'+sys.getObj(\'code_url\').value;"></td>';
out += '</tr></table>';

document.write(out);

<?php
if ($mode=="viewer") {
  echo '
    sys.isWriteable = false;
	sys.getObj("code").readOnly = true;
	sys.getObj("nav_table").style.display = "none";
	sys.getObj("nav_table_readonly").style.display = "";
    if (top.getObj("'.$data.'")) sys.getObj("code").value = top.getObj("'.$data.'").value;
    load(sys.getObj("code").value);
	showHeaderFooter(false);
  ';
} else if ($mode=="editor") {
  echo '
    sys.saveMethod = null;
    if (top.getObj("'.$data.'")) sys.getObj("code").value = top.getObj("'.$data.'").value;
    load(sys.getObj("code").value);
  ';
} else {
  echo 'load(sys.getObj("code").value);';
}
?>
</script>
</div>
</body>
</html>