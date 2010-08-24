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
      `ar_date` int(11) default '0',
      `ar_image` varchar(255) NOT NULL default '',
      `ar_livedate` int(11) NOT NULL default '0',
      `ar_expirydate` int(11) NOT NULL default '0',
      `ar_bbbody` text NULL,";
if (class_exists('Jojo_Plugin_Jojo_comment')) {
    $query .= "
     `ar_comments` enum('yes','no') NOT NULL default 'yes',";
}
$query .= "
      `ar_author` varchar(255) NOT NULL default '',
      `ar_source` varchar(255) NOT NULL default '',
      `ar_seotitle` varchar(255) NOT NULL default '',
      `ar_metadesc` varchar(255) NOT NULL default '',
      `ar_language` varchar(100) NOT NULL default 'en',
      `ar_htmllang` varchar(100) NOT NULL default 'en',
      PRIMARY KEY  (`articleid`),
      KEY `category` (`ar_category`),
      KEY `language` (`ar_language`),
      FULLTEXT KEY `title` (`ar_title`),
      FULLTEXT KEY `body` (`ar_title`,`ar_desc`,`ar_body`)
    ) TYPE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci  AUTO_INCREMENT=1000;";

/* Convert mysql date format to unix timestamps */
if (Jojo::tableExists($table) && Jojo::getMySQLType($table, 'ar_date') == 'date') {
    date_default_timezone_set(Jojo::getOption('sitetimezone', 'Pacific/Auckland'));
    $articles = Jojo::selectQuery("SELECT articleid, ar_date FROM {article}");
    Jojo::structureQuery("ALTER TABLE  {article} CHANGE  `ar_date`  `ar_date` INT(11) NOT NULL DEFAULT '0'");
    foreach ($articles as $k => $a) {
        if ($a['ar_date']!='0000-00-00') {
            $timestamp = strtotime($a['ar_date']);
        } else {
            $timestamp = 0;
        }
       Jojo::updateQuery("UPDATE {article} SET ar_date=? WHERE articleid=?", array($timestamp, $a['articleid']));
    }
}

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
/* Convert old Article Category pageid field to new */
if (Jojo::tableExists($table) && Jojo::fieldExists($table, 'ac_pageid') ) {
    Jojo::structureQuery("ALTER TABLE  {articlecategory} CHANGE  `ac_pageid`  `pageid` INT( 11 ) NOT NULL DEFAULT  '0'");
    Jojo::structureQuery("ALTER TABLE  {articlecategory} DROP `ac_url`");
    echo "Jojo_Plugin_Jojo_Article: Update category pageid field<br />";
}

$query = "
    CREATE TABLE {articlecategory} (
      `articlecategoryid` int(11) NOT NULL auto_increment,
      `pageid` int(11) NOT NULL default '0',
      `type` enum('normal','parent','index') NOT NULL default 'normal',
      `sortby` enum('ar_title asc','ar_date desc','ar_livedate desc','ar_author') NOT NULL default 'ar_date desc',
      `weighting` tinyint(1) NOT NULL default '1',
      `showdate` tinyint(1) NOT NULL default '1',
      `externalrsslink` varchar(255) NOT NULL default '',
      `rsslink` tinyint(1) default '1',
      `thumbnail` varchar(255) NOT NULL default '',";
if (class_exists('Jojo_Plugin_Jojo_comment')) {
    $query .= "
     `comments` tinyint(1) NOT NULL default '1',";
}
$query .= "
      PRIMARY KEY  (`articlecategoryid`),
      KEY `id` (`pageid`)
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

/* convert old rss_external_url to being set in the article category table */
$rsscat=Jojo::selectRow("SELECT externalrsslink from {articlecategory} where articlecategoryid = 1");
if(!$rsscat['externalrsslink']) {
  $rssexternal=Jojo::selectRow("SELECT op_value from {option} where op_name='rss_external_url'");
  if(isset($rssexternal['op_value'])) {
    Jojo::updateQuery("UPDATE {articlecategory} set externalrsslink=? where articlecategoryid=1",$rssexternal['op_value']);
    Jojo::deleteQuery("DELETE from {option} where op_name='rss_external_url' LIMIT 1");
    echo "Moved external rss link from options to article category.<br />";
    }
}
