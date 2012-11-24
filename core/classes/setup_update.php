<?php
/**
 * @package Simple Groupware
 * @link http://www.simple-groupware.de
 * @copyright Simple Groupware Solutions Thomas Bley 2002-2012
 * @license GPLv2
 */

class setup_update {

static function change_database_pre() {
  // 0.730
  $status = array("{t}completed{/t}"=>"completed","{t}confirmed{/t}"=>"confirmed","{t}booked{/t}"=>"booked", "{t}canceled{/t}"=>"canceled");
  sgsml_parser::table_column_translate("simple_timesheets", "status", $status);
  sgsml_parser::table_column_translate("simple_expenses", "status", $status);

  // completed=0 && status=unconfirmed -> status=open
  // completed=1 && status=unconfirmed -> status=completed
  if (sgsml_parser::table_column_exists("simple_timesheets","completed")) {
	db_update("simple_timesheets",array("status"=>"open"),array("completed=0", "status=@status@"),array("status"=>"{t}unconfirmed{/t}"),array("no_defaults"=>1));
	db_update("simple_timesheets",array("status"=>"completed"),array("completed=1", "status=@status@"),array("status"=>"{t}unconfirmed{/t}"),array("no_defaults"=>1));
  }
  if (sgsml_parser::table_column_exists("simple_expenses","completed")) {
	db_update("simple_expenses",array("status"=>"open"),array("completed=0", "status=@status@"),array("status"=>"{t}unconfirmed{/t}"),array("no_defaults"=>1));
	db_update("simple_expenses",array("status"=>"completed"),array("completed=1", "status=@status@"),array("status"=>"{t}unconfirmed{/t}"),array("no_defaults"=>1));
  }
  
  // 0.662
  $priority = array("{t}lowest{/t}"=>"1", "{t}low{/t}"=>"2", "{t}normal{/t}"=>"3", "{t}urgent{/t}"=>"4", "{t}immediate{/t}"=>"5");
  sgsml_parser::table_column_translate("simple_calendar", "priority", $priority);
  sgsml_parser::table_column_translate("simple_tasks", "priority", $priority);
  sgsml_parser::table_column_translate("simple_helpdesk", "priority", $priority);
  sgsml_parser::table_column_translate("simple_projects", "priority", $priority);

  // 0.658
  if (!sgsml_parser::table_column_rename("simple_emails","attachments","attachment")) setup::error_add("rename[10]: ".sql_error(),1152);
  if (!sgsml_parser::table_column_rename("simple_helpdesk","attachments","attachment")) setup::error_add("rename[9]: ".sql_error(),1153);

  // 0.400
  if (!sgsml_parser::table_column_rename("simple_projects","started","begin")) setup::error_add("rename[8]: ".sql_error(),152);
  if (!sgsml_parser::table_column_rename("simple_projects","finished","ending")) setup::error_add("rename[7]: ".sql_error(),153);

  // 0.220
  if (!sgsml_parser::table_column_rename("simple_gallery","title","filename")) setup::error_add("rename[5]: ".sql_error(),52);
  if (!sgsml_parser::table_column_rename("simple_gallery","attachment","filedata")) setup::error_add("rename[6]: ".sql_error(),53);

  // 0.219
  if (!sgsml_parser::table_column_rename("simple_calendar","end","ending")) setup::error_add("rename[1]: ".sql_error(),54);
  if (!sgsml_parser::table_column_rename("simple_contactactivities","end","ending")) setup::error_add("rename[2]: ".sql_error(),55);
  if (!sgsml_parser::table_column_rename("simple_tasks","end","ending")) setup::error_add("rename[3]: ".sql_error(),56);
  
  // process funambol schema views on sgs update
  if (self::get_config_old("SYNC4J",false,0) == "1") {
    setup::out(sprintf("{t}Processing %s ...{/t}","Funambol schema"));
	if (SETUP_DB_TYPE=="mysql") {
	  $data = preg_replace("!/\*.+?\*/!s","",file_get_contents("tools/funambolv7_syncML/mysql/funambol.sql"));
	  if (($msg = db_query(explode(";",$data)))) setup::error_add("funambol.sql [mysql]: ".$msg." ".sql_error(),100);
	} else if (SETUP_DB_TYPE=="pgsql") {
	  $data = file_get_contents("tools/funambolv7_syncML/postgresql/funambol.sql");
	  if (($msg = db_query($data))) setup::error_add("funambol.sql [pgsql]: ".$msg." ".sql_error(),101);
	}
  }

  // 0.720
  if (sgsml_parser::table_column_exists("simple_sys_custom_fields","id")) {
	setup::out(sprintf("{t}Processing %s ...{/t}","customization fields"));
	$rows = db_select("simple_sys_custom_fields","*","activated=1","","");
	if (is_array($rows) and count($rows)>0) {
	  foreach ($rows as $row) sgsml_customizer::trigger_build_field($row["id"], $row, null, "simple_sys_custom_fields");
	}
  }
  setup::errors_show();
}

static function change_database_post() {
  // change ftype, 0.646
  db_update("simple_sys_tree",array("ftype"=>"replace(ftype,'sys_nosql_','sys_nodb_')"),array("ftype like 'sys_nosql_%'"),array(),array("quote"=>false));
  
  // change anchor for rooms, 0.310
  db_update("simple_sys_tree",array("anchor"=>"locations"),array("ftype='locations'","flevel=2","ftitle='{t}Rooms{/t}'"),array());

  // change anchor for demo, debug folder, workspace, organisation, 0.292
  db_update("simple_sys_tree",array("anchor"=>"demo"),array("ftype='blank'","flevel=1","ftitle='{t}Demo{/t}'"),array());
  db_update("simple_sys_tree",array("anchor"=>"debug"),array("ftype='blank'","flevel=2","ftitle='{t}Debug{/t}'"),array());
  db_update("simple_sys_tree",array("anchor"=>"workspace"),array("ftype='blank'","flevel=0"),array());
  db_update("simple_sys_tree",array("anchor"=>"organisation"),array("ftype='blank'","flevel=1","ftitle='{t}Organisation{/t}'"),array());
  db_update("simple_sys_tree",array("anchor"=>"system"),array("ftype='sys_nodb_admin'","flevel=1","ftitle='{t}System{/t}'"),array());

  // remove sys_nodb_processes 0.721
  db_update("simple_sys_tree",array("ftype"=>"blank"),array("ftype='sys_nodb_processes'"),array());
  
  // change System folder to administration menu, 0.242
  db_update("simple_sys_tree",array("ftype"=>"sys_nodb_admin"),array("anchor=@anchor@"),array("anchor"=>"system"));
}

static function database_triggers() {
   // 0.664
  if (!file_exists(SIMPLE_STORE."/setup_emails")) {
	setup::out(sprintf("{t}Processing %s ...{/t}","emails message"));
	$rows = db_select("simple_emails","*",array("message_html='' and message!=''"),"","");
	if (is_array($rows) and count($rows)>0) {
	  foreach ($rows as $row) trigger::createemail($row["id"],$row);
	}
	touch(SIMPLE_STORE."/setup_emails");
  }

 // 0.704
  if (!file_exists(SIMPLE_STORE."/setup_notify")) {
    $notifications = array(
	  "simple_tasks"=>"closed='0'",
	  "simple_contacts"=>"birthday!=''",
	  "simple_contactactivities"=>"finished='0'",
	  "simple_sys_users"=>"activated='1'",
	);
	foreach ($notifications as $table=>$where) {
	  setup::out(sprintf("{t}Processing %s ...{/t}",$table));
	  $rows = db_select($table,"*",array($where,"notification!=''"),"","");
	  if (!is_array($rows) or count($rows)==0) continue;
	  foreach ($rows as $row) trigger::notify($row["id"],$row,array(),$table);
	}
	touch(SIMPLE_STORE."/setup_notify");
  }

  if (!file_exists(SIMPLE_STORE."/setup_duration")) {
	setup::out(sprintf("{t}Processing %s ...{/t}","tasks duration"));
	$rows = db_select("simple_tasks","*",array(),"","");
	if (is_array($rows) and count($rows)>0) {
	  foreach ($rows as $row) trigger::duration($row["id"],$row,false,"simple_tasks");
	}

	setup::out(sprintf("{t}Processing %s ...{/t}","projects duration"));
	$rows = db_select("simple_projects","*",array(),"","");
	if (is_array($rows) and count($rows)>0) {
	  foreach ($rows as $row) trigger::createeditproject($row["id"],$row);
	}
	touch(SIMPLE_STORE."/setup_duration");
  }

  setup::out(sprintf("{t}Processing %s ...{/t}","appointments"));
  $rows = db_select("simple_calendar","*",array(),"","");
  if (is_array($rows) and count($rows)>0) {
	foreach ($rows as $row) trigger::calcappointment($row["id"],$row,null,"simple_calendar");
  } 
}

static function database_folders() {
  $parent = folder_from_path("^system");
  if (!empty($parent)) {
    $row_id = folder_from_path("!sys_nodb_backups");
    if (empty($row_id)) {
	  folders::create("{t}Backups{/t}","sys_nodb_backups","",$parent,false);
    }
    $row_id = folder_from_path("^trash");
    if (empty($row_id)) {
	  folders::create("{t}Trash{/t}","blank","",$parent,false,array("anchor"=>"trash"));
    }
    $row_id = folder_from_path("!sys_notifications");
    if (empty($row_id)) {
	  folders::create("{t}Notifications{/t}","sys_notifications","{t}Delivery{/t}: cron.php",$parent,false);
    }
    $row_id = folder_from_path("^customize");
    if (empty($row_id)) {
	  folders::create("{t}Customize{/t}","blank","",$parent,false,array("anchor"=>"customize"));
    }
    $row_id = folder_from_path("!sys_console");
    if (empty($row_id)) {
	  $id = folders::create("{t}Console{/t}","sys_console","",$parent,false,array());
	  folders::import_data("modules/core/data_console.xml", $id);
    }
  }
  $parent = folder_from_path("^customize");
  $row_id = folder_from_path("!sys_custom_fields");
  if (empty($row_id) and !empty($parent)) {
	folders::create("{t}Fields{/t}","sys_custom_fields","{t}Customization rules\nfor modules based on sgsML{/t}",$parent,false);
  }
  $parent = folder_from_path("^workspace");
  $row_id = folder_from_path("^extensions");
  if (!empty($parent) and empty($row_id)) {
	folders::create("{t}Extensions{/t}","blank","",$parent,false,array("anchor"=>"extensions"));
  }
}

static function get_config_old($key, $full=false, $default="") {
  static $config_old = null;
  if ($config_old===null) {
	$old_file = "simple_store/config_old.php";
	if (file_exists($old_file)) $config_old = str_replace("\r","",file_get_contents($old_file));
  }
  if ($config_old===null and ($pos = strpos($config_old,"define('".$key."',"))) {
	$pos = $pos+strlen($key)+10;
	$end = strpos($config_old,"\n",$pos)-$pos-2;
	$result = substr($config_old,$pos,$end);
	if (!$full) $result = trim($result,"'\"");
	if ($key=="INVALID_EXTENSIONS") $result = str_replace(",url,", ",", $result);
	return $result;
  }
  return $default;
}
}