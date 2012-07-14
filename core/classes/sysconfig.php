<?php
/**
 * @package Simple Groupware
 * @link http://www.simple-groupware.de
 * @copyright Simple Groupware Solutions Thomas Bley 2002-2012
 * @license GPLv2
 */

class sysconfig {

static $inputs = array(
    "app_title"=>array("{t}Application title{/t}"),
    "cms_title"=>array("{t}CMS homepage title{/t}"),
	"---",
    "setup_db_host"=>array("{t}Database Hostname / IP{/t}"),
    "setup_db_user"=>array("{t}Database User{/t}"),
    "setup_db_pw"=>array("{t}Database Password{/t}"),
    "setup_db_name"=>array("{t}Database Name{/t}"),
	"---",
    "setup_admin_user"=>array("{t}Admin Username{/t}"),
    "setup_admin_pw"=>array("{t}Admin Password{/t}"),
    "setup_admin_user2"=>array("{t}Admin Username{/t} (2)"),
    "setup_admin_pw2"=>array("{t}Admin Password{/t} (2)"),
	"---",
    "virus_scanner"=>array("{t}Virus scanner{/t}"),
    "virus_scanner_params"=>array("{t}Virus scanner{/t}: {t}parameters{/t}"),
    "virus_scanner_display"=>array("{t}Virus scanner{/t}: {t}display filter{/t}"),
	"smtp_reminder"=>array("SMTP {t}mail reminder{/t}"),
	"smtp_notification"=>array("SMTP {t}mail notification{/t}"),
	"session_name"=>array("{t}Session name in cookie{/t}"),
	"cms_homepage"=>array("{t}Home page in the CMS{/t} (cms.php)"),
	"cms_real_url"=>array("{t}Real URL format in the CMS{/t} (cms.php)"),
);
static $auths = array(
    "sql"=>array("SQL",false,""),
	"htaccess"=>array("{t}Apache based (.htaccess, Basic, Digest, etc.){/t}",false,""),
	"ntlm"=>array("NTLM (Windows / Samba)",true,""),
	"ldap"=>array("LDAP",true,"(ldaps://server/ | ldap://server/)"),
	"imap"=>array("IMAP",true,"(server[:port[:ssl|tls]])"),
	"smtp"=>array("SMTP",true,"(server[:port[:ssl|tls]])"),
	"gdata"=>array("Google Apps",true,""),
);
static $bools = array(
	"DISABLE_BASIC_AUTH"=>"{t}Disable basic authentication{/t}",
    "ENABLE_ANONYMOUS"=>"{t}Enable anonymous access{/t}",
	"ENABLE_ANONYMOUS_CMS"=>"{t}Enable anonymous CMS{/t}",
    "SELF_REGISTRATION"=>"{t}Enable self registration{/t}",
	"SELF_REGISTRATION_CONFIRM"=>"{t}Self registration needs confirmation by an administrator.{/t}",
	"MOUNTPOINT_REQUIRE_ADMIN"=>"{t}Require admin access to set mountpoints{/t}",
    "SETUP_AUTH_AUTOCREATE"=>"{t}Enable automatic user creation{/t}<br>[htaccess, NTLM, LDAP, IMAP, SMTP]",
	"ARCHIVE_DELETED_FILES"=>"{t}Archive deleted files{/t}",
    "SYNC4J"=>"{t}Enable Sync4j / Funambol{/t}",
	"SYNC4J_REMOTE_DELETE"=>"{t}Sync4j remote delete items{/t}",
	"ENABLE_WEBDAV"=>"{t}Enable WebDAV{/t}",
	"ENABLE_EXT_MAILCLIENT"=>"{t}Enable external mail client for 'mailto:' links{/t}",
	"USE_MAIL_FUNCTION"=>"{t}Use the mail() function for sending mails (insecure){/t}",
	"USE_SYSLOG_FUNCTION"=>"{t}Use syslog() function for logging events{/t}",
	"USE_DEBIAN_BINARIES"=>"{t}Use Debian binaries (Warning: not latest versions){/t}",
	"FORCE_SSL"=>"{t}Force SSL{/t}",
	"CHECK_DOS"=>"{t}Check DoS Attacks{/t}",
	"CORE_COMPRESS_OUTPUT"=>"{t}Compress output{/t}",
	"APC_SESSION"=>"{t}Use APC for session storage{/t}",
    "MENU_AUTOHIDE"=>"{t}Automatically hide the menu{/t}",
	"TREE_AUTOHIDE"=>"{t}Automatically hide the tree{/t}",
	"FIXED_FOOTER"=>"{t}Fix paging bar to page bottom{/t}",
    "FDESC_IN_CONTENT"=>"{t}Show folder description in content area{/t}",
	"DEBUG"=>"",
	"DEBUG_SQL"=>"",
	"DEBUG_IMAP"=>"",
	"DEBUG_POP3"=>"",
	"DEBUG_SMTP"=>"",
	"DEBUG_JAVA"=>"",
	"DEBUG_WEBDAV"=>"",
	"DEBUG_JS"=>"",
);
static $caches = array(
    "ASSET_PAGE_LIMIT"=>"{t}Maximum number of assets per page{/t}", "FOLDER_REFRESH"=>"{t}Folder refresh period{/t}", 
    "LOGIN_TIMEOUT"=>"{t}Session timeout{/t}", "LOCKING"=>"",
    "SYSTEM_SLOW"=>"", "DB_SLOW"=>"", "CMS_SLOW"=>"",
	"OUTPUT_CACHE"=>"",
    "CSV_CACHE"=>"","LDIF_CACHE"=>"","BOOKMARKS_CACHE"=>"","ICALENDAR_CACHE"=>"","RSS_CACHE"=>"","VCARD_CACHE"=>"",
	"XML_CACHE"=>"","IMAP_CACHE"=>"","IMAP_LIST_CACHE"=>"","IMAP_MAIL_CACHE"=>"","POP3_LIST_CACHE"=>"",
	"POP3_MAIL_CACHE"=>"","GDOCS_CACHE"=>"","GDOCS_LIST_CACHE"=>"","GDOCS_PREVIEW_LIMIT"=>"","CIFS_PREVIEW_LIMIT"=>"",
	"LDAP_LIST_CACHE"=>"","CMS_CACHE"=>"", "FILE_TEXT_LIMIT"=>"", "FILE_TEXT_CACHE"=>"", "INDEX_LIMIT"=>"",
	"SIMPLE_CACHE"=>"","SIMPLE_STORE"=>"","SIMPLE_CUSTOM"=>"","SIMPLE_EXT"=>"","SIMPLE_IMPORT"=>"","CHMOD_DIR"=>"","CHMOD_FILE"=>""
);
static $textareas = array(
	"smtp_footer"=>array("SMTP {t}mail footer{/t}"),
	"invalid_extensions"=>array("{t}Invalid file extensions{/t}")
);
static $selects = array(
	"weekstart"=>array("{t}Week start{/t}",array(
		"0"=>"{t}Su{/t}", "1"=>"{t}Mo{/t}", "2"=>"{t}Tu{/t}", "3"=>"{t}We{/t}", "4"=>"{t}Th{/t}", "5"=>"{t}Fr{/t}", "6"=>"{t}Sa{/t}"
	))
);
static $multi_selects = array();

static function init() {
  self::$selects["default_style"] = array("{t}Default theme{/t}",select::themes());
  self::$selects["timezone"] = array("{t}Time zone{/t}, {t}current time{/t}: ".sys_date("{t}g:i a{/t}"), select::timezones(true));
  self::$selects["lang"] = array("{t}Default language{/t}", select::languages());
  $modules = select::modules_all();
  asort($modules);
  self::$multi_selects["disabled_modules"] = array("{t}Disabled modules{/t}",$modules);
  
  if (empty($_REQUEST["setup_auth_ldap_groups"])) {
	$_POST["setup_auth_ldap_groups"] = 0;
	$_REQUEST["setup_auth_ldap_groups"] = 0;
  }
}

static function validate() {
  if (!sys_validate_token()) {
	return "{t}Invalid security token{/t}";
  }
  if ($_REQUEST["setup_admin_user"]!=SETUP_ADMIN_USER and $validate=validate::username($_REQUEST["setup_admin_user"]) and $validate!="") {
	return "{t}Admin Username{/t}: {t}validation failed{/t} ".$validate;
  }
  if ($_REQUEST["setup_admin_user2"]!="" and $_REQUEST["setup_admin_user2"]!=SETUP_ADMIN_USER2 and $validate=validate::username($_REQUEST["setup_admin_user2"]) and $validate!="") {
	return "{t}Admin Username{/t} (2): {t}validation failed{/t} ".$validate;
  }
  if (!is_numeric($_REQUEST["login_timeout"]) or $_REQUEST["login_timeout"] <= 60) {
	return "{t}Session timeout{/t}: {t}validation failed{/t}";
  }
  if ($_REQUEST["setup_db_host"]=="") {
	return "{t}missing field{/t}: {t}Database Hostname / IP{/t}";
  }
  if ($_REQUEST["setup_db_user"]=="") {
	return "{t}missing field{/t}: {t}Database User{/t}";
  }
  if ($_REQUEST["setup_db_name"]=="") {
	return "{t}missing field{/t}: {t}Database Name{/t}";
  }
  if (empty($_REQUEST["setup_auth"])) {
	return "{t}missing field{/t}: {t}Authentication Mode{/t}";
  }
  if (!sql_connect($_REQUEST["setup_db_host"], $_REQUEST["setup_db_user"], $_REQUEST["setup_db_pw"], $_REQUEST["setup_db_name"])) {
    return "{t}Connection to database failed.{/t}\n".sql_error();
  }
  if (empty($_REQUEST["simple_cache"]) or !is_dir($_REQUEST["simple_cache"])) {
	return "SIMPLE_CACHE: {t}validation failed{/t} ".$_REQUEST["simple_cache"];
  }
  if (empty($_REQUEST["simple_store"]) or !is_dir($_REQUEST["simple_store"])) {
	return "SIMPLE_STORE: {t}validation failed{/t} ".$_REQUEST["simple_store"];
  }
  if (empty($_REQUEST["simple_custom"]) or !is_dir($_REQUEST["simple_custom"])) {
	return "SIMPLE_CUSTOM: {t}validation failed{/t} ".$_REQUEST["simple_custom"];
  }
  if (empty($_REQUEST["simple_ext"]) or !is_dir($_REQUEST["simple_ext"])) {
	return "SIMPLE_EXT: {t}validation failed{/t} ".$_REQUEST["simple_ext"];
  }
  if (!empty($_REQUEST["apc_session"]) and !APC) {
	return sprintf("{t}Please install the php-extension with name '%s'.{/t}", "apc");
  }
  if (strlen($_REQUEST["setup_admin_pw"])<5) {
	return "{t}Admin Password{/t}: {t}Password must be not null, min 5 characters.{/t}";
  }
  if (strlen($_REQUEST["setup_admin_pw2"])<5 and $_REQUEST["setup_admin_user2"]!="") {
	return "{t}Admin Password{/t} (2): {t}Password must be not null, min 5 characters.{/t}";
  }
  if (!empty($_REQUEST["sync4j"])) {
	echo sprintf("{t}Processing %s ...{/t}","Funambol schema")."<br>";
	if (SETUP_DB_TYPE=="mysql") {
	  $data = preg_replace("!/\*.+?\*/!s","",file_get_contents("tools/funambolv7_syncML/mysql/funambol.sql"));
	  if (($msg = db_query(explode(";",$data)))) {
		return "funambol.sql [mysql]: ".$msg."<br>";
	  }
	} else if (SETUP_DB_TYPE=="pgsql") {
	  $data = file_get_contents("tools/funambolv7_syncML/postgresql/funambol.sql");
	  if (($msg = db_query($data))) {
		return "funambol.sql [pgsql]: ".$msg."<br>";
	  }
	} else {
	  return "{t}Funambol only works with MySQL and PostgreSQL.{/t}<br>";
	} 
  }
  return "";
}

static function write_config($no_hash, $no_hash2) {
  $out = array();
  $out[] = "<?php";
  $out[] = "define('CORE_VERSION','".CORE_VERSION."');";
  $out[] = "define('CORE_VERSION_STRING','".CORE_VERSION_STRING."');";
  $out[] = "define('CORE_SGSML_VERSION','".CORE_SGSML_VERSION."');";
  $out[] = "define('SETUP_DB_TYPE','".SETUP_DB_TYPE."');";
  foreach ($_POST as $key=>$val) {
	$val = $_REQUEST[$key];
	if (is_array($val)) $val = implode("|", $val);
	if (in_array($key, array("action_sys","token"))) continue;
	if ($key=="invalid_extensions") $val = trim(preg_replace("|\s*,\s*|", ",", $val));
	if ($key=="setup_db_pw") $val = sys_encrypt($val,sha1($_REQUEST["setup_admin_user"]));
	if (!$no_hash and $key=="setup_admin_pw") $val = sha1($val);
	if (!$no_hash2 and $key=="setup_admin_pw2") $val = sha1($val);
	if (!is_numeric($val)) {
	  if (strpos($val,"\n") or strpos($val,"'")) {
		$val = "base64_decode('".base64_encode($val)."')";
	  } else {
		$val = "'".$val."'";
	  }
	}
	$key = strtoupper($key);
	if (isset(self::$bools[$key])) {
	  if ($val=="1") $val = "true"; else $val = "false";
	}
	$out[] = "define('".$key."',".$val.");";
  }
  $out[] = "if (TIMEZONE!='') date_default_timezone_set(TIMEZONE);\n".
		   "  elseif (!ini_get('date.timezone')) date_default_timezone_set(@date_default_timezone_get());";
  $out[] = "if (!ini_get('display_errors')) @ini_set('display_errors','1');";
  $out[] = "define('NOW',time());";
  $out[] = "define('APC',function_exists('apc_store') and ini_get('apc.enabled'));";
  $out[] = "?>";
  file_put_contents("simple_store/config.php", implode("\n",$out), LOCK_EX);
  admin::build_js($_REQUEST["lang"]);
  
  if (SIMPLE_CACHE!=$_REQUEST["simple_cache"]) {
	self::dirs_clear_caches(SIMPLE_CACHE);
	self::dirs_clear_caches($_REQUEST["simple_cache"]);
  }
  // TODO initialize simple_store if different
  if (SIMPLE_CUSTOM!=$_REQUEST["simple_custom"]) {
	self::dirs_clear_custom($_REQUEST["simple_custom"]);
  }
  if (SIMPLE_EXT!=$_REQUEST["simple_ext"]) {
	self::dirs_clear_custom($_REQUEST["simple_ext"]);
  }
  sys_log_message_log("info",sprintf("{t}Setup: setup-data written to %s.{/t}","simple_store/config.php"));
}

static function show_form() {
  echo '
	<form action="sysconfig.php" method="post">
	<input type="hidden" value="'.modify::get_form_token().'" name="token">
	<table class="data">
  ';
  foreach (self::$inputs as $key=>$input) {
	if ($input == "---") {
	  echo '
		<tr>
		<td><div style="border-top:1px solid #cccccc; width:100%; margin:10px 0px;"></div></td>
		<td><div style="border-top:1px solid #cccccc; width:450px; margin:10px 0px;"></div></td>
		</tr>
	  ';
	  continue;
	}
	echo '
	  <tr>
		<td>'.$input[0].' '.(isset($input[1])?'<a href="#" onclick="alert(\''.str_replace("\n","\\n",$input[1]).'\'); return false;">?</a>':'').'</td>
		<td>
	';
	if ($key=="setup_db_pw") {
		echo '<input type="password" size="60" maxlength="255" id="'.$key.'" name="'.$key.'" value="'.self::quote(sys_decrypt(SETUP_DB_PW,sha1(SETUP_ADMIN_USER))).'"><br/>';
		echo '<input id="'.$key.'_check" type="checkbox" onclick="document.getElementById(\''.$key.'\').type = this.checked ? \'text\':\'password\';"><label for="'.$key.'_check">{t}Show password{/t}</label>';
	} else if ($key=="setup_admin_pw" or $key=="setup_admin_pw2") {
		echo '<input type="password" size="60" maxlength="255" id="'.$key.'" name="'.$key.'" value=""><br/>';
		echo '<input id="'.$key.'_check" type="checkbox" onclick="document.getElementById(\''.$key.'\').type = this.checked ? \'text\':\'password\';"><label for="'.$key.'_check">{t}Show password{/t}</label>';
	} else {
		echo '<input type="text" size="60" maxlength="255" name="'.$key.'" value="'.self::quote(constant(strtoupper($key))).'">';
	}
	echo '
		</td>
	  </tr>
	';
  }
  echo '
	<tr>
	  <td>{t}Database{/t}</td>
	  <td>'.self::quote(SETUP_DB_TYPE).'</td>
	</tr>
	<tr>
	  <td><div style="border-top:1px solid #cccccc; width:100%; margin:10px 0px;"></div></td>
	  <td><div style="border-top:1px solid #cccccc; width:450px; margin:10px 0px;"></div></td>
	</tr>
	<tr>
	  <td nowrap valign="top">{t}Authentication Mode{/t}</td>
	  <td>
		<table class="data" style="background-color: #FFFFFF; border: 0px; margin: 0px;">
  ';
  foreach (self::$auths as $key=>$auth) {
	echo '
	<tr>
	<td><input type="Radio" class="radio" name="setup_auth" value="'.$key.'" '.(SETUP_AUTH==$key?"checked":"").'></td>
	';
	if (!$auth[1]) {
	  echo '<td colspan="2">'.$auth[0].'</td>';
	  echo '</tr>';
	} else {
	  if ($key=="gdata") {
		echo '
		  <td>'.$auth[0].'</td>
		  <td>{t}Domain{/t}</td><td><input type="Text" name="setup_auth_domain_gdata" value="'.self::quote(SETUP_AUTH_DOMAIN_GDATA).'"></td><td></td>
		  </tr>';
	  } else {
		echo '
		  <td>'.$auth[0].'</td>
		  <td>{t}Host{/t}</td><td><input type="Text" name="setup_auth_hostname_'.$key.'" value="'.self::quote(constant("SETUP_AUTH_HOSTNAME_".strtoupper($key))).'"></td><td>'.$auth[2].'</td>
		  </tr>';
	  }
	  if ($key=="imap") {
		echo '
		  <tr><td colspan="2"></td>
		  <td>{t}Domain{/t}</td><td><input type="Text" name="setup_auth_domain_imap" value="'.self::quote(SETUP_AUTH_DOMAIN_IMAP).'"></td><td></td>
		  </tr>';
	  }
	  if ($key=="ldap") {
		echo '<tr><td colspan="2"></td><td>{t}Domain{/t}</td><td><input type="Text" name="setup_auth_domain" value="'.self::quote(SETUP_AUTH_DOMAIN).'"></td><td>(Active Directory)</td></tr>';
		echo '<tr><td colspan="2"></td><td>Base DN</td><td><input type="Text" name="setup_auth_base_dn" value="'.self::quote(SETUP_AUTH_BASE_DN).'"></td><td>({t}if not autodetected with namingContexts{/t})</td></tr>';
		echo '<tr><td colspan="2"></td><td>User DN</td><td><input type="Text" name="setup_auth_ldap_user" value="'.self::quote(SETUP_AUTH_LDAP_USER).'"></td><td>({t}LDAP without anonymous access{/t})</td></tr>';
		echo '<tr><td colspan="2"></td><td>{t}Password{/t}</td><td><input type="Text" name="setup_auth_ldap_pw" value="'.self::quote(SETUP_AUTH_LDAP_PW).'"></td><td>({t}LDAP without anonymous access{/t})</td></tr>';
		echo '<tr><td colspan="2"></td><td>UID</td><td><input type="Text" name="setup_auth_ldap_uid" value="'.self::quote(SETUP_AUTH_LDAP_UID).'"></td><td>({t}LDAP attribute for usernames{/t}, {t}e.g.{/t} uid,cn)</td></tr>';
		echo '<tr><td colspan="2"></td><td>{t}Rooms{/t}</td><td><input type="Text" name="setup_auth_ldap_room" value="'.self::quote(SETUP_AUTH_LDAP_ROOM).'"></td><td>({t}LDAP attribute for rooms{/t})</td></tr>';
		echo '<tr><td colspan="2"></td><td>MemberOf</td><td><input type="Text" name="setup_auth_ldap_memberof" value="'.self::quote(SETUP_AUTH_LDAP_MEMBEROF).'"></td><td>({t}LDAP attribute for group memberships{/t})</td></tr>';
		echo '<tr><td colspan="2"></td><td>{t}Use LDAP Groups{/t}</td><td><input type="Checkbox" name="setup_auth_ldap_groups" class="checkbox" value="1" '.(SETUP_AUTH_LDAP_GROUPS?"checked":"").'></td></tr>';
	  }
	  if ($key=="ntlm") {
		echo '<tr><td colspan="2"></td><td>{t}Share{/t}</td><td><input type="Text" name="setup_auth_ntlm_share" value="'.self::quote(SETUP_AUTH_NTLM_SHARE).'"></td><td>(smb://server/share/)</td></tr>';
		echo '<input type="hidden" name="setup_auth_ntlm_sso" value="0">
		<tr><td colspan="2"></td><td>Single sign-on</td><td><input type="checkbox" name="setup_auth_ntlm_sso" class="checkbox" value="1" '.(SETUP_AUTH_NTLM_SSO?"checked":"").'></td></tr>';
	  }
	}
  }
  echo '
		</table>
	  </td>
	</tr>
	<tr>
	  <td><div style="border-top:1px solid #cccccc; width:100%; margin:10px 0px;"></div></td>
	  <td><div style="border-top:1px solid #cccccc; width:450px; margin:10px 0px;"></div></td>
	</tr>
  ';
  foreach (self::$selects as $key=>$val) {
	echo '
	  <tr>
		<td>'.$val[0].'</td>
		<td><select name="'.$key.'">
	';
	foreach ($val[1] as $key2=>$val2) {
	  echo '<option value="'.$key2.'" '.(constant(strtoupper($key))==$key2?"selected":"").'> '.$val2;
	}
	echo '	  
		</select>
	  </tr>
	';
  }
  foreach (self::$multi_selects as $key=>$val) {
	echo '
	  <tr>
		<td>'.$val[0].'</td>
		<td>
		  <table class="data"><tr><td>
		  <input type="hidden" name="'.$key.'[]" value=""/>
	';
	$i=0;
	foreach ($val[1] as $key2=>$val2) {
	  if ($val2[0]==" ") continue;
	  $checked = in_array($key2, explode("|", constant(strtoupper($key)))) ? "checked" : "";
	  echo '<input type="checkbox" name="'.$key.'[]" value="'.$key2.'" '.$checked.'> '.$val2.'<br>';
	  $i++;
	  if ($i%13 == 0) echo '</td><td>&nbsp; &nbsp;</td><td valign="top">';
	}
	echo '
		  </td></tr></table>
		</td>
	  </tr>
	';
  }
  foreach (self::$textareas as $key=>$input) {
	echo '
	  <tr>
		<td>'.$input[0].'</td>
		<td><textarea name="'.$key.'" style="width:450px; height:100px;">'.self::quote(constant(strtoupper($key))).'</textarea></td>
	  </tr>
	';
  }
  echo '
	<tr>
	  <td><div style="border-top:1px solid #cccccc; width:100%; margin:10px 0px;"></div></td>
	  <td><div style="border-top:1px solid #cccccc; width:450px; margin:10px 0px;"></div></td>
	</tr>
  ';
  foreach (self::$bools as $key=>$val) {
	echo '
	<input type="hidden" name="'.strtolower($key).'" value="">
	<tr>
	  <td style="width:26%;">'.($val?$val:$key).'</td>
	  <td><input type="Checkbox" class="checkbox" name="'.strtolower($key).'" value="1" '.(constant($key)?"checked":"").'></td>
	</tr>
	';
  }
  echo '
	<tr>
	  <td><div style="border-top:1px solid #cccccc; width:100%; margin:10px 0px;"></div></td>
	  <td><div style="border-top:1px solid #cccccc; width:450px; margin:10px 0px;"></div></td>
	</tr>
  ';
  foreach (self::$caches as $key=>$val) {
	echo '
	<tr>
	  <td>'.($val?$val:$key).'</td>
	  <td><input type="Text" size="15" maxlength="50" name="'.strtolower($key).'" value="'.self::quote(constant($key)).'"></td>
	</tr>
	';
  }
  echo '
	</table>
	<div style="border-bottom: 1px solid black;">&nbsp;</div>
	<br>
	<input type="submit" value="   {t}S a v e{/t}   " class="submit"><br>
	</form>
  ';
}

static function header() {
  echo '
    <html>
    <head>
	<title>Simple Groupware</title>
	<style>
		body, h2, img, div, table.data, a {
		  background-color: #FFFFFF; color: #666666; font-size: 13px; font-family: Arial, Helvetica, Verdana, sans-serif;
		}
		a,input,textarea { color: #0000FF; }
		input {
		  font-size: 11px; background-color: #F5F5F5; border: 1px solid #AAAAAA; height: 18px;
		  vertical-align: middle; padding-left: 5px; padding-right: 5px; border-radius: 10px;
		}
		textarea {
		  font-size: 11px; background-color: #F5F5F5; border: 1px solid #AAAAAA;
		  padding-left: 5px; padding-right: 5px; border-radius: 10px;
		}
		.checkbox, .radio { border: 0px; background-color: transparent; }
		.submit { color: #0000FF; background-color: #FFFFFF; width: 230px; font-weight: bold; }
	</style>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    </head>
    <body>
    <div style="float:right;">
	<a href="http://www.simple-groupware.de/cms/AdministrationConfiguration" target="_blank">{t}Help{/t}</a>
	</div>
    <div style="border-bottom: 1px solid black; letter-spacing: 2px; font-size: 18px; font-weight: bold;">
	Simple Groupware '.CORE_VERSION_STRING.'
	</div>
	<br>
	<div style="color:#FF0000;">
  ';
}

static function footer() {
  echo '
    <div style="border-top: 1px solid black;">Powered by Simple Groupware, Copyright (C) 2002-2012 by Thomas Bley.</div>
	</div>
	</body></html>
  ';
}

static function quote($str) {
  return str_replace("\\n","\n",q($str));
}

static function dirs_clear_custom($custom) {
  dirs_checkdir($custom);
  dirs_checkdir($custom."/ext/");
  @file_put_contents($custom."/.htaccess", "Order deny,allow\nDeny from all\n", LOCK_EX);
  @file_put_contents($custom."/ext/.htaccess", "Order deny,allow\nAllow from all\n", LOCK_EX);
}

static function dirs_clear_caches($cache) {
  $empty_dir = array(
	$cache, $cache."/debug", $cache."/imap", $cache."/pop3", $cache."/ip", $cache."/artichow",
	$cache."/output", $cache."/schema", $cache."/schema_data", $cache."/smarty", $cache."/thumbs",
	$cache."/upload", $cache."/backup", $cache."/cifs", $cache."/gdocs", $cache."/cms", $cache."/lang",
	$cache."/locking", $cache."/preview"
  );
  foreach ($empty_dir as $dir) dirs_create_empty_dir($dir);
  @file_put_contents($cache."/.htaccess", "Order deny,allow\nDeny from all\n", LOCK_EX);
}
}