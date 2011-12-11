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
 * @package jojo_core
 */

/* rename pg_bbbody to pg_body_code */

if (Jojo::tableExists('page') && Jojo::fieldExists('page','pg_bbbody')) {
    if (Jojo::fieldExists('page', 'pg_body_code')) {
        Jojo::updateQuery("UPDATE {page} SET pg_body_code=pg_bbbody WHERE pg_bbbody!='' AND pg_body_code=''");
        Jojo::structureQuery("ALTER TABLE {page} DROP `pg_bbbody`");
    } else {
        Jojo::structureQuery("ALTER TABLE {page} CHANGE `pg_bbbody` `pg_body_code` TEXT NOT NULL");
    }
}

$table = 'page';
$query = "
    CREATE TABLE {page} (
      `pageid` int(11) NOT NULL auto_increment,
      `pg_title` varchar(100) NOT NULL default '',
      `pg_menutitle` varchar(100) NOT NULL default '',
      `pg_desc` varchar(255) NOT NULL default '',
      `pg_seotitle` varchar(255) NOT NULL default '',
      `pg_url` varchar(255) NOT NULL default '',
      `pg_body_code` text NULL,
      `pg_body` text NULL,
      `pg_head` text NULL,
      `pg_link` varchar(255) NOT NULL default '',
      `pg_order` int(11) NOT NULL default '0',
      `pg_parent` int(11) NOT NULL default '0',
      `pg_status` enum('active','inactive','hidden') NOT NULL default 'active',
      `pg_ssl` enum('yes','no') NOT NULL default 'no',
      `pg_index` enum('yes','no') NOT NULL default 'yes',
      `pg_followto` enum('yes','no') NOT NULL default 'yes',
      `pg_followfrom` enum('yes','no') NOT NULL default 'yes',
      `pg_metakeywords` text NULL,
      `pg_contentcache` enum('yes','no','auto') NOT NULL default 'auto',
      `pg_metadesc` text NULL,
      `pg_mainnav` enum('yes','no') NOT NULL default 'yes',
      `pg_mainnavalways` enum('yes','no') NOT NULL default 'no',
      `pg_secondarynav` enum('yes','no') NOT NULL default 'no',
      `pg_breadcrumbnav` enum('yes','no') NOT NULL default 'yes',
      `pg_footernav` enum('yes','no') NOT NULL default 'yes',
      `pg_sitemapnav` enum('yes','no') NOT NULL default 'yes',
      `pg_xmlsitemapnav` enum('yes','no') NOT NULL default 'yes',
      `pg_livedate` int(11) NOT NULL default '0',
      `pg_expirydate` int(11) NOT NULL default '0',
      `pg_updated` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
      `pg_permissions` text NULL,
      `pg_language` varchar(10) NOT NULL default 'en',
      `pg_htmllang` varchar(10) NOT NULL default '',
      PRIMARY KEY  (`pageid`),
      KEY `pg_language` (`pg_language`),
      FULLTEXT KEY `title` (`pg_title`),
      FULLTEXT KEY `body` (`pg_title`,`pg_desc`,`pg_body`)
    ) ENGINE=MyISAM ;";

/* Check table structure */
$result = Jojo::checkTable($table, $query);

/* Output result */
if (isset($result['created'])) {
    echo sprintf("Table <b>%s</b> Does not exist - created empty table.<br />", $table);
}

if (isset($result['added'])) {
    foreach ($result['added'] as $col => $v) {
        echo sprintf("Table <b>%s</b> column <b>%s</b> Does not exist - added.<br />", $table, $col);
    }
}

if (isset($result['different'])) Jojo::printTableDifference($table, $result['different']);

/* make a copy of all HTML content into the bbbody field, adding the editor tag to mark it as html */
$bbhead = "[editor:html]\n";
$num = Jojo::updateQuery("UPDATE {page} SET pg_body_code=CONCAT(?, pg_body) WHERE pg_body_code='' AND pg_body!=''", array($bbhead));
if ($num) echo "Copy HTML content to texteditor field - $num $table records affected.";

/* remove pg_noindex field */
if (Jojo::fieldExists('page', 'pg_noindex')) {
    Jojo::structureQuery("ALTER TABLE {page} DROP `pg_noindex`;");
}

/* remove pg_ajaxlinks field */
if (Jojo::fieldExists('page', 'pg_ajaxlinks')) {
    Jojo::structureQuery("ALTER TABLE {page} DROP `pg_ajaxlinks`;");
}