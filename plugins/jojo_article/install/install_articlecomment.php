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

$table = 'articlecomment';
$query = "
    CREATE TABLE {articlecomment} (
      `articlecommentid` int(11) NOT NULL auto_increment,
      `ac_articleid` int(11) NOT NULL,
      `ac_timestamp` int(11) NOT NULL,
      `ac_name` varchar(255) NOT NULL,
      `ac_email` varchar(255) NOT NULL,
      `ac_website` varchar(255) NOT NULL,
      `ac_anchortext` varchar(255) NOT NULL,
      `ac_authorcomment` enum('yes','no') NOT NULL DEFAULT 'no',
      `ac_ip` varchar(255) NOT NULL,
      `ac_useanchortext` enum('yes','no') NOT NULL DEFAULT 'no',
      `ac_body` text NOT NULL,
      `ac_bbbody` text NOT NULL,
      `ac_nofollow` enum('yes','no') NOT NULL DEFAULT 'yes',
      `ac_status` enum('active','inactive') NOT NULL,
      `ac_userid` int(11) NOT NULL,
      `ac_approvecode` varchar(255) NOT NULL,
      `ac_anchortextcode` varchar(255) NOT NULL,
      `ac_deletecode` varchar(255) NOT NULL,
      PRIMARY KEY  (`articlecommentid`)
    ) TYPE=MyISAM;";

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

if (isset($result['different'])) Jojo::printTableDifference($table,$result['different']);