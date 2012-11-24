<?php

class Build extends PHPUnit_Framework_TestCase {

static function setUpBeforeClass() {
}
  
protected function setUp() {
}

function testModuleIcons() {
  $exceptions = array("nodb_calendar_contacts.xml", "nodb_calendar_departments.xml", "nodb_calendar_users.xml",
    "nodb_rights.xml", "nodb_index.xml", "nodb_ldif_contacts.xml", "nodb_pmwiki.xml", "nodb_rights_edit.xml",
    "nodb_schema.xml", "nodb_structure.xml", "search.xml");
  foreach (scandir("modules/schema/") as $module) {
    if (!strpos($module, ".xml") or $module[0]=="!") continue;
    $this->assertTrue(file_exists("ext/modules/".str_replace(".xml", ".png", $module)));
  }
  foreach (scandir("modules/schema_sys/") as $module) {
    if (!strpos($module, ".xml") or in_array($module, $exceptions)) continue;
    $this->assertTrue(file_exists("ext/modules/sys_".str_replace(".xml", ".png", $module)));
  }
}

function testTranslation() {
  $missing = array();
  $data = file_get_contents("lang/de.lang");
  foreach (file("lang/master.lang") as $line) {
    if (strpos($line, "** ")!==0) continue;
    if (strpos($data, $line)===false) $missing[] = $line;
  }
  $this->assertEquals($missing, array());
}

function testPhpAntiPatterns($dir=".") {
  static $exclude_files = array(".", "..", "default.php", "Tar_137.php", "tar.php", "lib", "tests");
  $patterns = array(
    "and false",
    "false and",
    "or true",
    "true or",
    "false &&",
    "|| true",
    "true ||",
    "if (true)",
    "if (false)",
  );
  $patterns = "!".implode("|", array_map("preg_quote", $patterns))."!";

  $dir .= "/";
  foreach (scandir($dir) as $file) {
    if (in_array($file, $exclude_files)) continue;
    if (is_dir($dir.$file)) {
      $this->testPhpAntiPatterns($dir.$file);
      continue;
    }
    if (!strpos($file, ".php")) continue;

    $content = str_replace("==false", "==false ", file_get_contents($dir.$file));
    if (!preg_match($patterns, $content)) return;
    foreach (file($dir.$file) as $line) {
      $this->assertFalse(preg_match($patterns, $line));
} } }

}