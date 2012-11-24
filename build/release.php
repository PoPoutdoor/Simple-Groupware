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

new build(true, false);

class build {

	public function __construct($archives=true, $manuals=true) {
		$this->translationMaster();
		$this->sysCheck();
		$version = $this->getVersion(__DIR__."/..", false);
		$this->checkPhp(__DIR__."/..");

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
			$pdf = __DIR__."/SimpleGroupwareManual_{$version}.pdf";
			$this->html2pdf("http://www.simple-groupware.de/cms/ManualPrint", $pdf);
			
			$pdf = __DIR__."/SimpleGroupwareManual_sgsML_{$version}.pdf";
			$this->html2pdf("http://www.simple-groupware.de/cms/SgsMLReferencePrint", $pdf);

			$pdf = __DIR__."/SimpleGroupwareUserManual_{$version}.pdf";
			$this->html2pdf("http://www.simple-groupware.de/cms/UserManualPrint", $pdf);
		}
	}
	
	private function translationMaster() {
		$master_lang = array();
		$queue = array("../");
		while (count($queue)>0) {
			$src = array_shift($queue);
			foreach (scandir($src) as $file) {
				if ($file[0]==".") continue;
				if (is_dir($src.$file)) {
					if (in_array($file, array("lang", "tools", "lib", "build"))) continue;
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
		file_put_contents("../lang/master.lang", "\xEF\xBB\xBF".$header.implode("\n\n\n", array_unique($master_lang)));
		file_put_contents("../lang/en.lang", "\xEF\xBB\xBF".$header);
	}

	private function sysCheck() {
		$tools = array("zca", "perl -v");
		foreach ($tools as $tool) {
			$output = array();
			$code = 0;
			exec($tool." 2>&1", $output, $code);
			if ($code===1 or $code===127) throw new Exception("Tool not found: ".$tool);
		}
	}

	private function html2pdf($url, $pdf) {
		$ps = tempnam("/tmp", "ps").".ps";
		$output = array();
		exec("perl html2ps.pl -n -u -t -o {$ps} {$url}");
		if (filesize($ps)<1000000) throw new Exception("ps too small: ".$ps);

		$output = array();
		exec("gs -sDEVICE=pdfwrite -dNOPAUSE -dQUIET -dBATCH -sOutputFile={$pdf} {$ps}", $output);
		if (!empty($output)) throw new Exception("gs error: ".print_r($output, true));
		if (filesize($pdf)<100000) throw new Exception("pdf too small: ".$pdf);
		unlink($ps);
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
		$exclude_files = array(".", "..", "default.php", "Tar_137.php", "tar.php", "lib");

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