<?php
/**
 * @package Simple Groupware
 * @link http://www.simple-groupware.de
 * @author Thomas Bley
 * @copyright Copyright (C) 2002-2012 by Thomas Bley
 * @license GPLv2
 */

class type_codearea extends type_default {

static function form_render_value($name, $value, $smarty) {
  if (!$smarty->sys["browser"]["comp"]["codeedit"]) {
    return <<<EOT
	  <textarea name="{$name}" style="width:100%; height:64px;">{$value}</textarea>
EOT;
  } else {
	$item_size = "300";
	if (isset($smarty->item["INPUT_HEIGHT"])) $item_size = $smarty->item["INPUT_HEIGHT"];
	
    return <<<EOT
      <div style="padding-bottom:2px;">
		<input type="button" value="+" onclick="resize_obj('{$name}_iframe',60);">&nbsp;
		<input type="button" value="&ndash;" onclick="resize_obj('{$name}_iframe',-60);"><br>
	  </div>
	  <input type="hidden" name="{$name}" id="{$name}" value="{$value}" onsubmit="getObj('{$name}_iframe').contentWindow.change();">
	  <iframe name="{$name}_iframe" id="{$name}_iframe" src="ext/lib/codepress/index.html" style="margin:0px; padding:0px; border:0px; width:100%; height:{$item_size}px;"></iframe>
EOT;
  }
}

static function render_value($value) {
  return "<div style='text-align:left;'>".modify::nl2br($value)."</div>";
}

static function export_as_text() {
  return true;
}
}