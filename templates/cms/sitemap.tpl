{*
 * @package Simple Groupware
 * @link http://www.simple-groupware.de
 * @author Thomas Bley
 * @copyright Copyright (C) 2002-2012 by Thomas Bley
 * @license GPLv2
 *}
<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
{foreach name=outer item=entry key=key from=$sitemap_pages}
<url>
    <loc>{$page.url}{$page.url_param}{$entry.pagename}</loc>
    <lastmod>{$entry.lastmodified|date_format:"%Y-%m-%d"}</lastmod>
</url>
{/foreach}
</urlset>