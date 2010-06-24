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

$table = 'article';
$query = "
    CREATE TABLE {article} (
      `articleid` int(11) NOT NULL auto_increment,
      `ar_title` varchar(255) NOT NULL default '',
      `ar_desc` varchar(255) NOT NULL default '',
      `ar_url` varchar(255) NOT NULL default '',
      `ar_body` text NULL,
      `ar_category` int(11) NOT NULL default '0',
      `ar_date` date default NULL,
      `ar_image` varchar(255) NOT NULL default '',
      `ar_livedate` int(11) NOT NULL default '0',
      `ar_expirydate` int(11) NOT NULL default '0',
      `ar_bbbody` text NULL,
      `ar_comments` enum('yes','no') NOT NULL default 'yes',
      `ar_author` varchar(255) NOT NULL default '',
      `ar_source` varchar(255) NOT NULL default '',
      `ar_seotitle` varchar(255) NOT NULL default '',
      `ar_metadesc` varchar(255) NOT NULL default '',
      `ar_language` varchar(100) NOT NULL default 'en',
      `ar_htmllang` varchar(100) NOT NULL default 'en',
      PRIMARY KEY  (`articleid`),
      FULLTEXT KEY `title` (`ar_title`),
      FULLTEXT KEY `body` (`ar_title`,`ar_desc`,`ar_body`)
    ) TYPE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci  AUTO_INCREMENT=1000;";


/* Check table structure */
$result = Jojo::checkTable($table, $query);

/* Output result */
if (isset($result['created'])) {
    echo sprintf("jojo_article: Table <b>%s</b> Does not exist - created empty table.<br />", $table);
}

if (isset($result['added'])) {
    foreach ($result['added'] as $col => $v) {
        echo sprintf("jojo_article: Table <b>%s</b> column <b>%s</b> Does not exist - added.<br />", $table, $col);
    }
}

if (isset($result['different'])) Jojo::printTableDifference($table, $result['different']);


$table = 'articlecategory';
$query = "
    CREATE TABLE {articlecategory} (
      `articlecategoryid` int(11) NOT NULL auto_increment,
      `ac_url` varchar(255) NOT NULL default '',
      `ac_pageid` int(11) NOT NULL default '0',
      `type` enum('normal','parent','index') NOT NULL default 'normal',
      `sortby` enum('ar_title asc','ar_date desc','ar_livedate desc','ar_author') NOT NULL default 'ar_date desc',
      `weighting` binary(1) default '1',
      `rsslink` binary(1) default '1',
      `thumbnail` varchar(255) NOT NULL default '',
      PRIMARY KEY  (`articlecategoryid`),
      KEY `id` (`ac_pageid`)
    ) TYPE=MyISAM ;";

/* Check table structure */
$result = Jojo::checkTable($table, $query);

/* Output result */
if (isset($result['created'])) {
    echo sprintf("jojo_article: Table <b>%s</b> Does not exist - created empty table.<br />", $table);
}

if (isset($result['added'])) {
    foreach ($result['added'] as $col => $v) {
        echo sprintf("jojo_article: Table <b>%s</b> column <b>%s</b> Does not exist - added.<br />", $table, $col);
    }
}

if (isset($result['different'])) Jojo::printTableDifference($table, $result['different']);