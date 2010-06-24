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

// Table Data
$table = 'tabledata';
$query = "
    CREATE TABLE {tabledata} (
      `tabledataid` int(11) NOT NULL auto_increment,
      `td_name` varchar(255) NOT NULL default '',
      `td_displayname` varchar(255) NOT NULL default '',
      `td_primarykey` varchar(255) NOT NULL default '',
      `td_displayfield` varchar(100) NOT NULL default '',
      `td_parentfield` varchar(100) NOT NULL default '',
      `td_plugin` varchar(100) NOT NULL default '',
      `td_categorytable` varchar(100) NOT NULL default '',
      `td_categoryfield` varchar(100) NOT NULL default '',
      `td_rolloverfield` varchar(100) NOT NULL default '',
      `td_filter` enum('yes','no') NOT NULL default 'no',
      `td_orderbyfields` varchar(255) NOT NULL default '',
      `td_deleteoption` enum('yes','no') NOT NULL default 'no',
      `td_addsimilar` enum('yes','no') NOT NULL default 'yes',
      `td_menutype` varchar(255) NOT NULL default 'auto',
      `td_group1` varchar(100) NOT NULL default '',
      `td_help` text NULL,
      `td_group2` varchar(100) NOT NULL default '',
      `td_filterby` varchar(255) NOT NULL default '',
      `td_groupowner` varchar(255) NOT NULL default '',
      `td_userowner` varchar(255) NOT NULL default '',
      `td_golivefield` varchar(255) NOT NULL default '',
      `td_expiryfield` varchar(255) NOT NULL default '',
      `td_activefield` varchar(255) NOT NULL default '',
      `td_privacyfield` varchar(255) NOT NULL default '',
      `td_languagefield` varchar(255) NOT NULL default '',
      `td_autoupdate` enum('yes','no') NOT NULL default 'yes',
      `td_defaultpermissions` text NULL,
      PRIMARY KEY  (`tabledataid`)
    ) TYPE=MyISAM ;";

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

if (isset($result['different'])) Jojo::printTableDifference($table,$result['different']);