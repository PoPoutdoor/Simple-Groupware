<?php

// TODO validate german translations
// TODO test install mysql, postgres, sqlite
// TODO rebuild master lang
// TODO build manuals
// TODO remove index.htm?
// TODO version without demo data
// TODO tar exclude self, exclude build

new build();

class build {

	const MB = 1048576;

	public function __construct($build=true) {
		$dir = $this->git();
		$this->checkJs($dir);
		$this->checkPhp($dir);

		$version = $this->getVersion($dir, false);
		$file = $dir."/SimpleGroupware_{$version}.tar";

		if ($build) {
			$this->tar($dir, $file, 5*self::MB);
			$this->gz($file, 3*self::MB);
		}
	}

	private function git() {
		$dir = time();
		if (is_dir($dir)) throw new Exception("Build directory already exists.");
		
		$output = array();
		exec("git clone git@github.com:simplegroupware/Simple-Groupware.git ".$dir, $output);
		if (!is_dir($dir)) throw new Exception("Git directory not found: ".print_r($output, true));
		return $dir;
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
				throw new Exception("Anti-Pattern match in: ".$file."\n".$line);
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
	
	private function getVersion($dir, $force_valid=true) {
		$match = array();
		preg_match("!Version ([^\\s]+)!", array_shift(file($dir."/Changelog_text.txt")), $match);
		$match2 = array();
		preg_match("!Version ([^\\s]+)!", array_shift(file($dir."/Changelog.txt")), $match2);

		if (empty($match[1]) or empty($match2[1])) throw new Exception("Version not found.");
		if ($force_valid and $match[1]!=$match2[1]) throw new Exception("Version not complete in both changelogs.");
		return $match[1];
	}
	
	private function tar($dir, $file, $minsize=0) {
		if (!is_dir($dir)) throw new Exception("Directory not found.");
		if (file_exists($file)) @unlink($file);
		if (file_exists($file)) throw new Exception("Tar archive already exists.");
		chdir($dir);
		$output = array();
		exec("tar --exclude .git -cf {$file} .", $output);
		if (!file_exists($file) or filesize($file)<$minsize) {
			throw new Exception("Error creating tar file: ".print_r($output, true));
		}
	}
	
	private function gz($file, $minsize=0) {
		$file_gz = $file.".gz";
		if (file_exists($file_gz)) @unlink($file_gz);
		if (file_exists($file_gz)) throw new Exception("Gzip archive already exists.");
		$output = array();
		exec("gzip -9 {$file}", $output);
		if (!file_exists($file_gz) or filesize($file)<$minsize) {
		  throw new Exception("Error creating gzip file: ".print_r($output, true));
		}
	}
}