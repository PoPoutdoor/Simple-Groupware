<?php
/**
 * @package Simple Groupware
 * @link http://www.simple-groupware.de
 * @author Thomas Bley
 * @copyright Copyright (C) 2002-2012 by Thomas Bley
 * @license GPLv2
 */

class type_graphviz extends type_default {

static function form_render_value($name, $value) {
  static $init = false;
  if ($init === false) $init = <<<EOT
	<script>
		function graphviz_open_preview(field) {
		  var data = getObj(field).value;
		  ajax("type_graphviz::ajax_render_png",[data],function(filename){
			show(field+"_preview");
			getObj(field+"_preview").src = "preview.php?filename="+escape(filename);
		  });
		}
	</script>
EOT;

  $output = $init.<<<EOT
    <div style="padding-bottom:1px;">
	<input type="button" value="{t}Preview{/t}" onclick="graphviz_open_preview('{$name}'); getObj('{$name}').focus();">&nbsp;
	<input type="button" value="{t}Examples{/t}" onclick="nWin('http://graphviz.org/Gallery.php');">&nbsp;
	<input type="button" value="{t}Shapes{/t}" onclick="nWin('http://graphviz.org/doc/info/shapes.html');">&nbsp;
	<input type="button" value="{t}Documentation{/t}" onclick="nWin('http://graphviz.org/pdf/dotguide.pdf');">
	</div>
	<textarea name="{$name}" id="{$name}" style="width:100%; height:64px;">{$value}</textarea>
	<img id="{$name}_preview" src="about:blank" style="display:none;"/>
EOT;
  $init = "";
  return $output;
}

static function render_value($value, $unused, $preview) {
  if ($preview) {
    $filename = basename(self::render_png(modify::htmlunquote($value)));
	return <<<EOT
	  <a target="_blank" href="preview.php?filename={$filename}"><img class="cursor" src="preview.php?filename={$filename}"></a>
EOT;
  }
  return modify::nl2br($value);
}

static function ajax_render_png($data) {
  return basename(self::render_png($data));
}

static function render_png($data) {
  $filename = SIMPLE_CACHE."/thumbs/graphviz_".sha1($data).".png";
  if (!file_exists($filename)) {
	file_put_contents($filename.".dot", $data, LOCK_EX);
	$src = modify::realfilename($filename.".dot");
	$target = modify::realfilename($filename);
	$result = sys_exec(sys_find_bin("dot")." -Kdot -Tpng -o".$target." ".$src);
	if ($result!="") {
	  sys_log_message_log("php-fail","proc_open: ".$result);
	  $filename = SIMPLE_CACHE."/thumbs/graphviz_".sha1($result).".png";
	  if (!file_exists($filename)) sys_render_text($filename, $result);
	}
  }
  return $filename;
}

static function export_as_text() {
  return true;
}
}