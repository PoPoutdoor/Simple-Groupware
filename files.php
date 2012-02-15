<?php
/**
 * @package Simple Groupware
 * @link http://www.simple-groupware.de
 * @author Thomas Bley
 * @copyright Copyright (C) 2002-2012 by Thomas Bley
 * @license GPLv2
 */

define("NOCONTENT",true);
define("NOSESSION",true);
require("index.php");

if (empty($_REQUEST["item"]) or empty($_REQUEST["action"]) or empty($_REQUEST["folder"]) or !isset($_REQUEST["subitem"])) {
  sys_error("Missing parameters.","403 Forbidden");
}

sys_check_auth();

if (empty($_REQUEST["field"])) $field = "filedata"; else $field = ltrim($_REQUEST["field"],"_");

$folder = folder_from_path($_REQUEST["folder"]);

if (strtolower($_REQUEST["action"])=="lock") {
  ajax::file_lock($folder, $_REQUEST["item"], $field, $_REQUEST["subitem"]);
  header("Cache-Control: private, max-age=1, must-revalidate");
  header("Expires: ".gmdate("D, d M Y H:i:s", NOW)." GMT");
  header("Content-Type: text/xml; charset=utf-8");
  header("Lock-Token: <opaquelocktoken:1>");
  echo '<?xml version="1.0" encoding="utf-8"?>
<D:prop xmlns:D="DAV:">
<D:lockdiscovery><D:activelock>
<D:lockscope><D:exclusive/></D:lockscope><D:locktype><D:write/></D:locktype>
<D:depth>0</D:depth><D:timeout>Second-7200</D:timeout>
<ns0:owner xmlns:ns0="DAV:">'.modify::htmlquote($_SESSION["username"]).'</ns0:owner>
<D:locktoken><D:href>opaquelocktoken:1</D:href></D:locktoken>
</D:activelock></D:lockdiscovery>
</D:prop>';
} else {
  ajax::file_unlock($folder, $_REQUEST["item"], $field, $_REQUEST["subitem"]);
  header("Cache-Control: private, max-age=1, must-revalidate");
  header("Expires: ".gmdate("D, d M Y H:i:s", NOW)." GMT");
  header("HTTP/1.1 204 No Content");
}