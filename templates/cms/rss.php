<?
/**
 * @package Simple Groupware
 * @link http://www.simple-groupware.de
 * @copyright Simple Groupware Solutions Thomas Bley 2002-2012
 * @license GPLv2
 */
?>
<?xml version="1.0" encoding="UTF-8" ?>
<rss version="2.0">
<channel>
<title>PmWiki &amp; Simple Groupware</title>
<link><?= $this->page["url"] ?></link>
<description><?= q($this->page["description"]) ?></description>
<generator>Simple Groupware &amp; CMS</generator>
<pubDate><?= sys_date("r") ?></pubDate>

<image>
<url>ext/cms/icons/sgs_logo2.png</url>
<title>PmWiki &amp; Simple Groupware</title>
<link><?= $this->page["url"] ?></link>
<width>-15</width>
</image>

<? foreach ($this->rss_pages as $entry) { ?>
<item>
<title><?= q($entry["title"] ?: $entry["pagename"]) ?></title>
<link><?= $this->page["url"]."/".$entry["pagename"] ?></link>
<description><?= q($entry["change_summary"] ?: $entry["description"]) ?></description>
<pubDate><?= sys_date("r", $entry["lastmodified"]) ?></pubDate>
</item>
<? } ?>
</channel>
</rss>