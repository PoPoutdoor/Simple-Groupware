<?php
// commercial

/**
 * place your custom functions here
 * 
 * sgsML include:
 * <view name="display" displayname="{t}Display{/t}" function="testing" schema_mode="static" />
 *
 * => Output for this view is generated from PHP callback from main.tpl to custom::testing()
 *
 * default error reporting: E_ALL ^ E_NOTICE
 */
class custom {

static function testing($folder, $view) {
  $output = "<b>Hello World</b><br/>";
  $output .= "<br/>";
  $output .= "PHP file: ".__FILE__."<br/>";
  $output .= "class: ".__CLASS__."<br/>";
  $output .= "function: ".__FUNCTION__."<br/>";
  $output .= "folder: ".$folder."<br/>";
  $output .= "view: ".$view."<br/>";

  /**
   * Smarty can be also used here (templates are in templates/*):
   *
   * $output .= sys::$smarty->fetch("custom.tpl");
   */
  
  // use echo $output to avoid output filtering for bad HTML and Javascript
  return $output;
}
}