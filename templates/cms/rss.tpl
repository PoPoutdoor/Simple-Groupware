{*
 * @package Simple Groupware
 * @link http://www.simple-groupware.de
 * @author Thomas Bley
 * @copyright Copyright (C) 2002-2012 by Thomas Bley
 * @license GPLv2
 *}
<?xml version="1.0" encoding="UTF-8" ?>
<rss version="2.0">
<channel>
<title>PmWiki &amp; Simple Groupware</title>
<link>{$page.url}</link>
<description>{$page.description|escape:"html"}</description>
<generator>Simple Groupware &amp; CMS</generator>
<pubDate>{"r"|sys_date}</pubDate>

<image>
<url>ext/cms/icons/sgs_logo2.png</url>
<title>PmWiki &amp; Simple Groupware</title>
<link>{$page.url}</link>
<width>-15</width>
</image>
	
{foreach name=outer item=entry from=$rss_pages}
<item>
<title>{$entry.title|default:$entry.pagename|escape:"html"}</title>
<link>{$page.url}{$page.url_param}{$entry.pagename}</link>
<description>{$entry.change_summary|default:$entry.description|escape:"html"}</description>
<pubDate>{"r"|sys_date:$entry.lastmodified}</pubDate>
</item>
{/foreach}
</channel>
</rss>