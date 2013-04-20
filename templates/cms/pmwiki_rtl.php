<?
/**
 * @package Simple Groupware
 * @link http://www.simple-groupware.de
 * @copyright Simple Groupware Solutions Thomas Bley 2002-2012
 * @license GPLv2
 */
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <title><?= q(CMS_TITLE) ?>: <?= q($this->page["title"] ? $this->page["title"] : $this->page["name"]) ?></title>
<? // You are not allowed to remove or alter the copyright. ?>
<!-- 
	This website is brought to you by Simple Groupware
	Simple Groupware is an open source Groupware and Web Application Framework created by Thomas Bley and licensed under GNU GPL v2.
	Simple Groupware is copyright 2002-2012 by Thomas Bley.	Extensions and translations are copyright of their respective owners.
	More information and documentation at http://www.simple-groupware.de/
-->
  <meta name="generator" content="Simple Groupware &amp; CMS" />
  <meta name="description" content="<?= q($this->page["description"]) ?>" />
  <meta name="author" content="<?= q($this->page["author"]) ?>" />
  <meta name="keywords" content="<?= q($this->page["keywords"]) ?>" />
  <link rel="stylesheet" href="<?= BASE ?>/ext/cms/styles.css" type="text/css" media="all" />
  <link rel="alternate" type="application/atom+xml" title="Atom-Feed" href="<?= SELF ?>/rss">
  <!-- right-to-left layout -->
  <style>
  body {
	font-family:Tahoma,Arial,Helvetica,Verdana,sans-serif;
	direction:RTL;
  }
  #wikihead {
	left:6px;
	right:auto;
  }
  </style>
</head>
<body class="body">
  <div id="wikilogo">
    <a href="<?= SELF ?>"><img src="<?= BASE ?>/ext/cms/icons/pmwiki-32.gif"> &amp; Simple Groupware</a>
  </div>
  <div id="wikihead">
  <form action="<?= SELF ?>/Site.Search" method="get">
    <input type="text" name="q" value="" class="inputbox searchbox" accesskey="s" />
    <input type="submit" class="inputbutton searchbutton" value="Search" />
  </form>
  </div>
  <table id="wikimid" width="100%" cellspacing="0" cellpadding="0"><tr>
    <td id="wikileft" valign="top">
	  <p style="text-align: left; font-size:83%;">- <a target="_blank" href="<?= SELF ?>/Site.SideBar?edit">{t}Edit{/t}</a> -</p>
	  <?= $this->cms->render("Site.SideBar") ?>
	</td>
    <td id="wikibody" valign="top">
	  <p style="text-align: left; font-size:83%;">- <a target="_blank" href="?edit" accesskey="e">{t}Edit{/t}</a> -</p>
	  <h1 class="pagetitle"><?= q($this->page["title"] ? $this->page["title"] : $this->page["name"]) ?></h1>
	  <div id="wikitext"><?= $this->cms->render($this->page["pagename"]) ?></div>
    </td>
  </tr></table>
  <div id="wikifoot">
    <div class="footnav">
	  <span style="float:left;">{t}Page last modified on{/t} <?= modify::localdateformat($this->page["lastmodified"],"{t}F j, Y{/t}") ?></span>
	  &nbsp;<? if (!sys_is_guest($_SESSION["username"])) { ?><a href="?logout" accesskey="l">{t}Logout{/t}</a> - <? } ?>
	  <a href="<?= SELF ?>/rss">{t}Recent changes{/t}</a> - 
	  <a href="?source">{t}Source{/t}</a>
	</div>
  </div>
</body>
</html>