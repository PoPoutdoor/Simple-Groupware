<?php

class Build extends PHPUnit_Framework_TestCase {

public static function setUpBeforeClass() {
}
  
protected function setUp() {
}

private function testValidateModuleIcons() {
  $exceptions = array("nodb_calendar_contacts.xml", "nodb_calendar_departments.xml", "nodb_calendar_users.xml",
    "nodb_rights.xml", "nodb_index.xml", "nodb_ldif_contacts.xml", "nodb_pmwiki.xml", "nodb_rights_edit.xml",
    "nodb_schema.xml", "nodb_structure.xml", "search.xml");
  foreach (scandir("modules/schema/") as $module) {
    if (!strpos($module, ".xml") or $module[0]=="!") continue;
    if (!file_exists("ext/modules/".str_replace(".xml", ".png", $module))) {
      throw new Exception("module icon not found for: ".$module);
    }
  }
  foreach (scandir("modules/schema_sys/") as $module) {
    if (!strpos($module, ".xml") or in_array($module, $exceptions)) continue;
    if (!file_exists("ext/modules/sys_".str_replace(".xml", ".png", $module))) {
      throw new Exception("module icon not found for: ".$module);
} } }
  
}