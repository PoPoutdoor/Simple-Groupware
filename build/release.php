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
// TODO version without demo data
// TODO add phpdoc

new build(true, false);

class build {

	public function __construct($archives=true, $manuals=true) {
		$this->translationMaster();

		$this->sysCheck();
		
		$dir = $this->git();
		$version = $this->getVersion($dir, false);
		
		$this->checkJs($dir);
		$this->checkPhp($dir);

		if ($archives) {
			$file = "SimpleGroupware_{$version}.tar";
			$this->tar($dir, $file, 5*1048576);
			$this->gzip($file, 3*1048576);

			$file = "SimpleGroupware_no_demo_data_{$version}.tar";
			$this->tar($dir, $file, 5*1048576, "import");
			$this->gzip($file, 3*1048576);

			$file = "SimpleGroupware_{$version}.zip";
			$this->zip($dir, $file, 3*1048576);
		}
		if ($manuals) {
			$pdf = $dir."/SimpleGroupwareManual_{$version}.pdf";
			$this->html2pdf("http://www.simple-groupware.de/cms/ManualPrint", $pdf);
			
			$pdf = $dir."/SimpleGroupwareManual_sgsML_{$version}.pdf";
			$this->html2pdf("http://www.simple-groupware.de/cms/SgsMLReferencePrint", $pdf);

			$pdf = $dir."/SimpleGroupwareUserManual_{$version}.pdf";
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
		$tools = array("git --version", "js -v", "zca", "tar", "gzip", "zip", "perl -v");
		foreach ($tools as $tool) {
			$output = array();
			$code = 0;
			exec($tool." 2>&1", $output, $code);
			if ($code===1 or $code===127) throw new Exception("Tool not found: ".$tool);
		}
	}

	private function git() {
		$dir = time();
		if (is_dir($dir)) throw new Exception("Build directory already exists.");
		mkdir($dir);
		chdir($dir);
		$dir = "SimpleGroupware";
		
		$output = array();
		exec("git clone git@github.com:simplegroupware/Simple-Groupware.git ".$dir, $output);
		if (!is_dir("SimpleGroupware")) throw new Exception("Git directory not found: ".print_r($output, true));
		return $dir;
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

	private function checkJs($dir) {
		$filter = "/window is not defined/";
		$dir .= "/ext/js/";
		if (!is_dir($dir)) throw new Exception("Directory not found: ".$dir);
		foreach (scandir($dir) as $file) {
			if (!strpos($file, ".js")) continue;
			$output = array();
			exec("js ".$dir.$file." 2>&1", $output);
			foreach ($output as $line) {
				if (preg_match($filter, $line)) continue;
				throw new Exception($line);
			}
		}
	}
	
	private function antiPatternPhp($file) {
		if (basename($file)=="release.php") return;
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
		$content = str_replace("==false", "==false ", file_get_contents($file));
		if (!preg_match($patterns, $content)) return;
		foreach (file($file) as $line) {
			if (preg_match($patterns, $line)) {
				throw new Exception("Anti-Pattern match in: {$file}\n{$line}");
			}
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
			$this->antiPatternPhp($dir.$file);

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
		preg_match("!Version ([^\\s]+)!", array_shift(file($dir."/Changelog_text.txt")), $match);
		$match2 = array();
		preg_match("!Version ([^\\s]+)!", array_shift(file($dir."/Changelog.txt")), $match2);

		if (empty($match[1]) or empty($match2[1])) throw new Exception("Version not found.");
		if ($force_valid and $match[1]!=$match2[1]) throw new Exception("Version not complete in both changelogs.");
		return $match[1];
	}
	
	private function tar($dir, $file, $minsize=0, $exclude="") {
		if (!is_dir($dir)) throw new Exception("Directory not found.");
		if (file_exists($file)) @unlink($file);
		if (file_exists($file)) throw new Exception("Tar archive already exists.");
		
		$output = array();
		if ($exclude!="") $exclude = "--exclude ".$exclude;
		exec("tar --exclude .git --exclude build {$exclude} {$param} -cf {$file} {$dir}", $output);
		if (!file_exists($file) or filesize($file)<$minsize) {
			throw new Exception("Error creating tar file: ".print_r($output, true));
		}
	}
	
	private function gzip($file, $minsize=0) {
		$file_gz = $file.".gz";
		if (file_exists($file_gz)) @unlink($file_gz);
		if (file_exists($file_gz)) throw new Exception("Gzip archive already exists.");
		$output = array();
		exec("gzip -9 {$file}", $output);
		if (!file_exists($file_gz) or filesize($file)<$minsize) {
		  throw new Exception("Error creating gzip file: ".print_r($output, true));
		}
	}
	
	private function zip($dir, $file, $minsize=0) {
		if (!is_dir($dir)) throw new Exception("Directory not found.");
		if (file_exists($file)) @unlink($file);
		if (file_exists($file)) throw new Exception("Zip archive already exists.");
		
		$output = array();
		exec("zip -r -9 -q {$file} {$dir}/** -x */build* */.git*", $output);
		if (!file_exists($file) or filesize($file)<$minsize) {
			throw new Exception("Error creating zip file: ".print_r($output, true));
		}
	}
}