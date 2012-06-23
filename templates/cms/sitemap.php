<?
/**
 * @package Simple Groupware
 * @link http://www.simple-groupware.de
 * @author Thomas Bley
 * @copyright Copyright (C) 2002-2012 by Thomas Bley
 * @license GPLv2
 */
?>
<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
<? foreach ($this->sitemap_pages as $entry) { ?>
<url>
    <loc><?= $this->page["url"].$this->page["url_param"].$entry["pagename"] ?></loc>
    <lastmod><?= sys_date("Y-m-d", $entry["lastmodified"]) ?></lastmod>
</url>
<? } ?>
</urlset>