<?php
/**
 * @package Simple Groupware
 * @link http://www.simple-groupware.de
 * @copyright Simple Groupware Solutions Thomas Bley 2002-2012
 * @license GPLv2
 */

class type_htmlarea extends type_default {

static function build_history($old, $new) {
  return asset::build_diff(modify::htmlmessage($old), modify::htmlmessage($new));
}

static function form_render_value($name, $value, $smarty) {
  static $init = false;
  if ($init === false) $init = <<<EOT
	<script>
		function htmlarea_preview(field) {
		  var data = getObj(field).value;
		  var field_frame = open_window("","preview",700,480);
		  field_frame.document.write("<html><head><link media='all' href='ext/cache/core_core_firefox.css' rel='stylesheet' type='text/css' /></head><body>");
		  field_frame.document.write("<table class='data' style='height:100%;'><tr><td valign='top'>"+data+"</td></tr></table></body></html>");
		  field_frame.document.close();
		}
		function htmlarea_refresh(field) {
		  var field_frame = getObj(field+"_iframe").contentWindow;
		  field_frame.refresh(getObj(field).value);
		}
	</script>
EOT;

  if ($smarty->sys["is_mobile"]) {
	$output = $init.<<<EOT
    <div style="padding-bottom:2px;">
	  <input type="button" value="{t}Preview{/t}" onclick="htmlarea_preview('{$name}');">&nbsp;
	  <input type="button" value="+" onclick="resize_obj('{$name}_iframe',120); resize_obj('{$name}',120);">&nbsp;
      <input type="button" value="&ndash;" onclick="resize_obj('{$name}_iframe',-120); resize_obj('{$name}',-120);"><br>
	</div>
	<textarea name="{$name}" id="{$name}" onchange="htmlarea_refresh('{$name}');" style="width:100%; height:64px;">{$value}</textarea>
EOT;
  } else {
	$item_size = "300";
	if (isset($smarty->item["INPUT_HEIGHT"])) $item_size = $smarty->item["INPUT_HEIGHT"];

	$output = $init.<<<EOT
    <div style="padding-bottom:2px;">
	  <input type="button" value="{t}Preview{/t}" onclick="htmlarea_preview('{$name}');">&nbsp;
	  <input type="button" value="HTML / {t}Editor{/t}" onclick="showhide('{$name}'); showhide('{$name}_iframe');">&nbsp;
	  <input type="button" value="+" onclick="resize_obj('{$name}_iframe',120); resize_obj('{$name}',120);">&nbsp;
      <input type="button" value="&ndash;" onclick="resize_obj('{$name}_iframe',-120); resize_obj('{$name}',-120);"><br>
	</div>
	<textarea name="{$name}" id="{$name}" onchange="htmlarea_refresh('{$name}');" style="width:100%; height:{$item_size}px; display:none;">{$value}</textarea>
	<iframe name="{$name}_iframe" id="{$name}_iframe" src="ext/lib/tinymce/index.html" style="margin:0px; padding:0px; border:0px; width:100%; height:{$item_size}px;"></iframe>	
EOT;
  }
  $init = "";
  return $output;
}

static function render_value($value, $unused, $unused2, $smarty) {
  if (!empty($smarty->field["INSECURE"])) {
	return modify::htmlfield(modify::unquote($value), false);
  }
  if (!empty($smarty->field["NO_CHECKS"])) {
	return modify::unquote($value);
  }
  return modify::htmlfield(modify::unquote($value));
}

static function export_as_html() {
  return true;
}
}