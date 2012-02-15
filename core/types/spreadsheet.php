<?php
/**
 * @package Simple Groupware
 * @link http://www.simple-groupware.de
 * @author Thomas Bley
 * @copyright Copyright (C) 2002-2012 by Thomas Bley
 * @license GPLv2
 */

class type_spreadsheet extends type_default {

static function form_render_value($name, $value, $smarty) {
  $item_size = "300";
  if (isset($smarty->item["INPUT_HEIGHT"])) $item_size = $smarty->item["INPUT_HEIGHT"];
  $lang = LANG;
  return <<<EOT
	<textarea name="{$name}" id="{$name}" class="spreadsheet" style="display:none;">{$value}</textarea>
	<div style="margin-bottom:2px; width:100%; height:{$item_size}px;">
	  <iframe id="{$name}_iframe" style="margin:0px; padding:0px; width:100%; height:100%;" src="ext/lib/simple_spreadsheet/spreadsheet.php?mode=editor&lang={$lang}&data={$name}"></iframe>
	</div>
EOT;
}

static function render_value($value, $unused, $preview, $smarty) {
  $height = 350;
  if (isset($smarty->view["IMAGE_HEIGHT"])) $height = $smarty->view["IMAGE_HEIGHT"];
  $lang = LANG;
  $id = sha1($value);
  if ($preview) {
    return <<<EOT
	  <textarea id="{$id}" style="display:none;">{$value}</textarea>
	  <div style="margin:2px; width:100%; height:{$height}px;">
		<iframe style="margin:0px; padding:0px; width:99%; height:100%;" src="ext/lib/simple_spreadsheet/spreadsheet.php?mode=viewer&lang={$lang}&data={$id}"></iframe>
	  </div>
EOT;
  }
  return modify::nl2br($value);
}

static function export_as_text() {
  return true;
}
}