<?php

// TODO validate PHP
// TODO jslint
// TODO validate german translations
// TODO test install mysql, postgres, sqlite
// TODO rebuild master lang
// TODO remove index.htm?
// TODO version without demo data
// TODO tar exclude self, exclude build
// TODO separate validate script

$build_dir = time();
exec("git clone git@github.com:simplegroupware/Simple-Groupware.git ".$build_dir, $output);
if (!is_dir($build_dir)) throw new Exception("Git directory not found: ".print_r($output, true));
chdir($build_dir);

preg_match("!Version ([^\s]+)!", file("Changelog_text.txt")[0], $match);
preg_match("!Version ([^\s]+)!", file("Changelog.txt")[0], $match2);

if (empty($match[1]) or empty($match2[1])) throw new Exception("Version not found.");
// if ($match[1]!=$match2[1]) throw new Exception("Version not complete in both changelogs.");
$version = $match2[1];

$file = "SimpleGroupware_{$version}.tar";
$file_gz = "SimpleGroupware_{$version}.tar.gz";
if (file_exists($file)) @unlink($file);
if (file_exists($file_gz)) @unlink($file_gz);
if (file_exists($file) or file_exists($file_gz)) throw new Exception("Archive already exists.");

exec("tar --exclude .git -cf {$file} .", $output);
if (!file_exists($file) or filesize($file)<5242880) {
  throw new Exception("Error creating tar file: ".print_r($output, true));
}

exec("gzip -9 {$file}", $output);
print_r($output);
if (!file_exists($file_gz) or filesize($file)<3145728) {
  throw new Exception("Error creating gzip file: ".print_r($output, true));
}