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
 * @package jojo_sitemap
 */

/* Add a Sitemap page is one does not exist */
Jojo::updateQuery("UPDATE {page} SET pg_link='Jojo_Plugin_Jojo_sitemap' WHERE pg_link='jojo_sitemap.php'");
$data = Jojo::selectQuery("SELECT * FROM {page} WHERE pg_link='Jojo_Plugin_Jojo_Sitemap'");
if (!count($data)) {
    echo "Adding <b>Sitemap</b> Page to menu<br />";
    Jojo::insertQuery("INSERT INTO {page} SET pg_title='Sitemap', pg_link='Jojo_Plugin_Jojo_Sitemap', pg_url='sitemap', pg_desc='A simple listing of all pages on this website', pg_mainnav='no', pg_footernav='yes'");
}

/* Update for old filename */
Jojo::updateQuery("UPDATE {page} SET pg_link = 'Jojo_Plugin_Jojo_SitemapXML' WHERE pg_link = 'xml_sitemap.php' OR pg_link = 'Jojo_Plugin_Xml_sitemap'");

/* Add Google sitemap (sitemap.xml) page if one does not exist */
$data = Jojo::selectQuery("SELECT * FROM {page} WHERE pg_link = 'Jojo_Plugin_Jojo_SitemapXML'");
if (!count($data)) {
    echo "Adding <b>Google Sitemap</b> Page<br />";
    Jojo::insertQuery("INSERT INTO {page} SET pg_title = 'XML Sitemap', pg_link = 'Jojo_Plugin_Jojo_SitemapXML', pg_url = 'sitemap.xml', pg_parent = ?, pg_order=0, pg_mainnav='no', pg_xmlsitemapnav='no', pg_index='yes', pg_body = ''", array($_NOT_ON_MENU_ID));
}

/* Add gss.xsl page if one does not exist */
$data = Jojo::selectQuery("SELECT * FROM {page} WHERE pg_link = 'Jojo_Plugin_Jojo_SitemapXSL'");
if (!count($data)) {
    echo "Adding <b>Google_sitemap_style.xsl</b> Page<br />";
    Jojo::insertQuery("INSERT INTO {page} SET pg_title = 'Google_Sitemap_Style.xsl', pg_link = 'Jojo_Plugin_Jojo_SitemapXSL', pg_url = 'google_sitemap_style.xsl', pg_parent = ?, pg_order=0, pg_mainnav='no', pg_xmlsitemapnav='no', pg_index='yes', pg_body = ''", array($_NOT_ON_MENU_ID));
}


/* add extra page fields for xml lastmod/changefreq/priority  - michaelbrandon 10/7/08*/

$table="page";

$query="CREATE table {page} (
`pg_xmlsitemap_lastmod` ENUM( 'yes', 'no' ) NOT NULL DEFAULT 'yes',
`pg_xmlsitemap_priority` ENUM('1.0','0.9','0.8','0.7','0.6','0.5','0.4','0.3','0.2','0.1','0.0','' ) NULL,
`pg_xmlsitemap_changefreq` ENUM( '','always', 'hourly', 'daily', 'weekly', 'monthly', 'yearly', 'never' ) NOT NULL,
PRIMARY KEY  (`pageid`)
) ENGINE=MyISAM;
";

$result = Jojo::checkTable($table, $query);

/* Output result */
if (isset($result['added'])) {
    foreach ($result['added'] as $col => $v) {
        echo sprintf("Table <b>%s</b> column <b>%s</b> Does not exist - added.<br />", $table, $col);
    }
}

