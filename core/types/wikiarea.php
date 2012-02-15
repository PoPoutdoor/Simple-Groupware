<?php
/**
 * @package Simple Groupware
 * @link http://www.simple-groupware.de
 * @author Thomas Bley
 * @copyright Copyright (C) 2002-2012 by Thomas Bley
 * @license GPLv2
 */

class type_wikiarea extends type_default {

static function form_render_value($name, $value) {
  static $init = false;
  if ($init === false) $init = <<<EOT
	<script>
		function wikiarea_preview(field) {
		  var obj = getObj("preview_"+field);
		  obj.style.display = "";
		  ajax("type_wikiarea::ajax_wikiarea_render_preview",[getObj(field).value],function(data){ set_html(obj,data); });
		}
	</script>
EOT;
  $pv = t("Preview");
  $tf = t("Text formatting rules");
  $output = $init.<<<EOT
	<div style="margin-bottom:1px;">
	  <input type="button" value="{$pv}" onclick="wikiarea_preview('{$name}'); getObj('{$name}').focus();">&nbsp;
	  <input type="button" value="{$tf}" onclick="nWin('http://pear.reversefold.com/dokuwiki/text_wiki:samplepage');">
	</div>
	<textarea name="{$name}" id="{$name}" style="width:100%; height:64px;">{$value}</textarea>
	<div class="wikibody">
	  <div id="preview_{$name}" style="display:none; padding:8px;"><img src="ext/icons/loading.gif"/></div>
	</div>
EOT;
  $init = "";
  return $output;
}

static function render_value($value) {
  return modify::htmlfield(modify::htmlunquote($value));
}

static function render_page($str) {
  if ($str=="") return "";
  if (!class_exists("Text_Wiki",false)) require("lib/wiki/Wiki.php");
  $wiki = new Text_Wiki();
  $wiki->disableRule("Interwiki");
  $wiki->disableRule("Image");
  return str_replace("<p>","<p style='padding-bottom:16px;'>",$wiki->transform($str, "Xhtml"));
}

static function ajax_wikiarea_render_preview($data) {
  return "<b>".t("Preview").":</b><br/><br/>".modify::htmlfield(self::render_page($data));
}

static function export_as_html() {
  return true;
}
}