<?php
/**
 * @package Simple Groupware
 * @link http://www.simple-groupware.de
 * @copyright Simple Groupware Solutions Thomas Bley 2002-2012
 * @license GPLv2
 */

// TODO test install mysql, postgres, sqlite
// TODO rebuild google master lang
// TODO remove index.htm?
// TODO deploy to google code, sf, homepage

new build(true, true);

class build {

	public function __construct($archives=true, $manuals=true) {
		$this->translationMaster();
		$this->sysCheck();
		$version = $this->getVersion(__DIR__."/..", false);
		//$this->checkPhp(__DIR__."/..");

		if ($archives) {
			$target = __DIR__."/SimpleGroupware_{$version}.zip";
			$output = exec("wget -O ".$target." https://github.com/simplegroupware/Simple-Groupware/archive/master.zip");
			if (!file_exists($target) or filesize($target)<3*1048576) {
				throw new Exception("Error creating zip file".print_r($output, true));
			}
			$target = __DIR__."/SimpleGroupware_{$version}.tar.gz";
			$output = exec("wget -O ".$target." https://github.com/simplegroupware/Simple-Groupware/archive/master.tar.gz");
			if (!file_exists($target) or filesize($target)<3*1048576) {
				throw new Exception("Error creating gzip file: ".print_r($output, true));
			}
		}
		if ($manuals) {
			// TODO check for errors
			$url = "http://www.simple-groupware.de/cms/SgsMLReferencePrint";
			$pdf = __DIR__."/SimpleGroupwareManual_sgsML_{$version}.pdf";
			exec("phantomjs ".__DIR__."/html2pdf.js ".$url." ".$pdf);

			$url = "http://www.simple-groupware.de/cms/ManualPrint";
			$pdf = __DIR__."/SimpleGroupwareManual_{$version}.pdf";
			exec("phantomjs ".__DIR__."/html2pdf.js ".$url." ".$pdf);

			$url = "http://www.simple-groupware.de/cms/UserManualPrint";
			$pdf = __DIR__."/SimpleGroupwareUserManual_{$version}.pdf";
			exec("phantomjs ".__DIR__."/html2pdf.js ".$url." ".$pdf);

			// TODO set meta data in PDFs, http://code.google.com/p/phantomjs/issues/detail?id=883
			// TODO Title: Simple Groupware sgsML Reference Guide
			// TODO Title: Simple Groupware Manual
			// TODO Title: Simple Groupware User Manual
			// TODO Author: Simple Groupware Solutions Thomas Bley
		}
	}
	
	private function translationMaster() {
		$master_lang = array();
		$queue = array(__DIR__."/../");
		while (count($queue)>0) {
			$src = array_shift($queue);
			foreach (scandir($src) as $file) {
				if ($file[0]==".") continue;
				if (is_dir($src.$file)) {
					if (in_array($file, array("tools", "lib", "simple_cache", "simple_store"))) continue;
					$queue[] = $src.$file."/";
					continue;
				}
				$data = file_get_contents($src.$file);
				$matches = array();
				if (preg_match_all("!\{t\}([^\{]+)!i", $data, $matches, PREG_SET_ORDER)) {
					foreach ($matches as $match) $master_lang[] = "** ".$match[1];
				}
			}
		}
		sort($master_lang);
		$header = "/**\n * @package Simple Groupware\n * @license GPLv2\n */\n\n";
		$header .= "** !_Language\nEnglish\n\n";
		file_put_contents(__DIR__."/../lang/master.lang", "\xEF\xBB\xBF".$header.implode("\n\n\n", array_unique($master_lang)));
	}

	private function sysCheck() {
		$tools = array("zca", "phantomjs");
		foreach ($tools as $tool) {
			$output = array();
			$code = 0;
			exec($tool." 2>&1", $output, $code);
			if ($code===1 or $code===127) throw new Exception("Tool not found: ".$tool);
		}
	}

	private function checkPhp($dir) {
		$filter = array(
			"Bad escape sequence",
			"if-if-else construction else relates",
			"'unused' is never used",
			"'unused' encountered only once",
			"'args' is never used",
			"include/require with user-accessible variable",
			"'id' is never used",
			"'unused2' is never used",
		);
		$filter = "!".implode("|", array_map("preg_quote", $filter))."!";
		$exclude_files = array(".", "..", "default.php", "Tar_137.php", "tar.php", "lib", "simple_cache", "simple_store");

		$dir .= "/";
		if (!is_dir($dir)) throw new Exception("Directory not found: ".$dir);
		foreach (scandir($dir) as $file) {
			if (in_array($file, $exclude_files)) continue;
			if (is_dir($dir.$file)) {
				$this->checkPhp($dir.$file);
				continue;
			}
			if (!strpos($file, ".php")) continue;

			$output = array();
			exec("zca ".$dir.$file." 2>&1", $output);
			array_shift($output);
			array_shift($output);
			foreach ($output as $line) {
				if (preg_match($filter, $line)) continue;
				throw new Exception($line);
			}
		}
	}
	
	private function getVersion($dir, $force_valid=true) {
		$match = array();
		preg_match("!Version ([^\\s]+)!", @array_shift(file($dir."/docs/Changelog_text.txt")), $match);
		$match2 = array();
		preg_match("!Version ([^\\s]+)!", @array_shift(file($dir."/docs/Changelog.txt")), $match2);

		if (empty($match[1]) or empty($match2[1])) throw new Exception("Version not found.");
		if ($force_valid and $match[1]!=$match2[1]) throw new Exception("Version not complete in both changelogs.");
		return $match[1];
	}
}