<?php
/**
 *                    Jojo CMS
 *                ================
 *
 * Copyright 2007-2008 Harvey Kane <code@ragepank.com>
 * Copyright 2007-2008 Michael Holt <code@gardyneholt.co.nz>
 * Copyright 2007 Melanie Schulz <mel@gardyneholt.co.nz>
 *
 * See the enclosed file license.txt for license information (LGPL). If you
 * did not receive this file, see http://www.fsf.org/copyleft/lgpl.html.
 *
 * @author  Harvey Kane <code@ragepank.com>
 * @author  Michael Cochrane <mikec@jojocms.org>
 * @author  Melanie Schulz <mel@gardyneholt.co.nz>
 * @license http://www.fsf.org/copyleft/lgpl.html GNU Lesser General Public License
 * @link    http://www.jojocms.org JojoCMS
 * @package jojo_article
 */

/* Articles */
$data = Jojo::selectRow("SELECT * FROM {page}  WHERE pg_link='Jojo_Plugin_Jojo_article'");
if (!count($data)) {
    echo "Jojo_Plugin_Jojo_Article: Adding <b>Articles</b> Page to menu<br />";
    Jojo::insertQuery("INSERT INTO {page} SET pg_title='Articles', pg_link='Jojo_Plugin_Jojo_article', pg_url='articles'");
}

/* RSS feed */
$data = Jojo::selectRow("SELECT * FROM {page}  WHERE pg_link='Jojo_Plugin_Jojo_article_rss'");
if (!count($data)) {
    echo "Jojo_Plugin_Jojo_Article: Adding <b>Articles RSS</b> Page to menu<br />";
    $data = Jojo::selectQuery("SELECT * FROM {page}  WHERE pg_link='Jojo_Plugin_Jojo_Article'");
    foreach ($data as $d) {
        Jojo::insertQuery("INSERT INTO {page} SET pg_title='Articles RSS Feed', pg_link='Jojo_Plugin_Jojo_article_rss', pg_url='articles/rss', pg_parent = ?, pg_order=1, pg_mainnav='no'", array($d['pageid']));
    }
}

/* Edit Articles */
$data = Jojo::selectRow("SELECT * FROM {page}  WHERE pg_url='admin/edit/article'");
if (!count($data)) {
    echo "Jojo_Plugin_Jojo_Article: Adding <b>Edit Articles</b> Page to menu<br />";
    Jojo::insertQuery("INSERT INTO {page} SET pg_title='Edit Articles', pg_link='Jojo_Plugin_Admin_Edit', pg_url='admin/edit/article', pg_parent=?, pg_order=2", array($_ADMIN_CONTENT_ID));
}


/* Edit Article Categories */
$data = Jojo::selectRow("SELECT pg_url FROM {page} WHERE pg_url='admin/edit/articlecategory'");
if (!count($data)) {
    $parent = Jojo::selectRow("SELECT pageid FROM {page} WHERE pg_url='admin/edit/article'");
    echo "Jojo_Plugin_Jojo_Article: Adding <b>Article Categories</b> Page to Edit Content menu<br />";
    Jojo::insertQuery("INSERT INTO {page} SET pg_title='Article Categories', pg_link='Jojo_Plugin_Admin_Edit', pg_url='admin/edit/articlecategory', pg_parent=?, pg_order=3", $parent['pageid']);
}

/* Article Admin Handler */
$data = Jojo::selectRow("SELECT * FROM {page} WHERE pg_link='Jojo_Plugin_Jojo_article_admin'");
if (!count($data)) {
    echo "Jojo_Plugin_Jojo_Article: Adding <b>Articles Admin</b> Page<br />";
    Jojo::insertQuery("INSERT INTO {page} SET pg_title='Articles', pg_link='Jojo_Plugin_Jojo_article_admin', pg_url='articles/admin', pg_parent=?, pg_sitemapnav='no', pg_xmlsitemapnav='no'", $_NOT_ON_MENU_ID);
}
/* ensure the article admin page doesn't show up in the sitemap / XML sitemap */
Jojo::updateQuery("UPDATE {page} SET pg_sitemapnav='no', pg_xmlsitemapnav='no' WHERE pg_url='articles/admin'");

/* Ensure there is a folder for uploading article images */
$res = Jojo::RecursiveMkdir(_DOWNLOADDIR . '/articles');
if ($res === true) {
    echo "Jojo_Plugin_Jojo_Article: Created folder: " . _DOWNLOADDIR . '/articles';
} elseif($res === false) {
    echo 'Jojo_Plugin_Jojo_Article: Could not automatically create ' .  _DOWNLOADDIR . '/articles' . 'folder on the server. Please create this folder and assign 777 permissions.';
}

/* If no articles, add a sample article */
$data = Jojo::selectRow("SELECT * FROM {article}");
if (!count($data)) {
    echo "Jojo_Plugin_Jojo_Article: Added sample article.<br/>";
    Jojo::insertQuery("INSERT INTO {article} ( `ar_title` , `ar_url`, `ar_desc` , `ar_body` , `ar_bbbody` , `ar_category` , `ar_date` , `ar_image` , `ar_author` , `ar_source` , `ar_seotitle` , `ar_metadesc` , `ar_language` )
    VALUES (
    'Welcome to JojoCMS', 'test', 'A test article', 'Welcome to the articles section of your JojoCMS site. This part of the site is under construction. Article plugin powered by <a href=\"http://www.jojocms.org\">Jojo CMS</a>.', '[editor:bb]Welcome to the articles section of your JojoCMS site. This part of the site is under construction. Article plugin powered by [url=http://www.jojocms.org]Jojo CMS[/url].', '0', NULL , '', '', '', '', '', 'en'
    );");
}

/* create articlecommentsubscription table */
if (!Jojo::tableExists('articlecommentsubscription')) {
    echo "Table <b>articlecommentsubscription</b> Does not exist - creating empty table<br />";
    $query = "
        CREATE TABLE {articlecommentsubscription} (
        `userid` INT NOT NULL DEFAULT '0',
        `articleid` INT NOT NULL DEFAULT '0',
        `lastviewed` INT NOT NULL DEFAULT '0',
        `lastemailed` INT NOT NULL DEFAULT '0',
        `lastupdated` INT NOT NULL DEFAULT '0',
        `code` VARCHAR(16) NOT NULL DEFAULT ''
        ) TYPE = MYISAM ;
    ";
    Jojo::structureQuery($query);
}

/* Regenerating HTML cache for Article */
$articles = Jojo::selectQuery("SELECT * FROM {article} WHERE ar_bbbody != ''");
if (count($articles)) {
    echo 'Jojo_Plugin_Jojo_Article: Regenerating HTML cache for Articles<br />';
    $n = count($articles);
    for ($i=0; $i<$n; $i++) {
        $bbcode = $articles[$i]['ar_bbbody'];
        $cache = '';
        if (strpos($bbcode, '[editor:bb]') !== false) {
            /* BB Code field */
            $bbcode = preg_replace('/\\[editor:bb\\][\\r\\n]{0,2}(.*)/si', '$1', $bbcode);
            $bb = new bbconverter;
            $bb->truncateurl = 30;
            $bb->imagedropshadow = true; // Jojo::yes2true(Jojo::getOption('imagedropshadow'));
            $bb->setBBCode($bbcode);
            $cache = $bb->convert('bbcode2html');
        } elseif (strpos($bbcode, '[editor:html]') !== false) {
            $cache = str_replace('[editor:html]', '', $bbcode);
        }
        if ($cache){
            /* Update DB with the cached HTML data */
            Jojo::updateQuery("UPDATE {article} SET ar_body = ? WHERE articleid = ? LIMIT 1", array($cache, $articles[$i]['articleid']));
        }
    }
}