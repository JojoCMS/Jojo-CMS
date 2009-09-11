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

$table = 'fielddata';
$query = "
    CREATE TABLE {fielddata} (
      `fielddataid` int(11) NOT NULL auto_increment,
      `fd_table` varchar(255) NOT NULL default '',
      `fd_field` varchar(255) NOT NULL default '',
      `fd_name` varchar(255) NOT NULL default '',
      `fd_sqltype` varchar(100) NOT NULL default '',
      `fd_type` varchar(255) NOT NULL default '',
      `fd_options` text NULL,
      `fd_required` enum('yes','no') NOT NULL default 'no',
      `fd_showlabel` enum('yes','no') NOT NULL default 'yes',
      `fd_default` varchar(255) NOT NULL default '',
      `fd_maxvalue` varchar(20) NOT NULL default '',
      `fd_minvalue` varchar(20) NOT NULL default '',
      `fd_size` varchar(20) NOT NULL default '0',
      `fd_rows` varchar(20) NOT NULL default '0',
      `fd_cols` varchar(20) NOT NULL default '0',
      `fd_help` text,
      `fd_nulls` enum('yes','no') NOT NULL default 'yes',
      `fd_order` int(11) NOT NULL default '0',
      `fd_tabname` varchar(255) NOT NULL default '',
      `fd_m2m_linktable` varchar(255) NOT NULL default '',
      `fd_m2m_linkitemid` varchar(255) NOT NULL default '',
      `fd_m2m_linkcatid` varchar(255) NOT NULL default '',
      `fd_m2m_cattable` varchar(255) NOT NULL default '',
      `fd_maxsize` int(11) NOT NULL default '0',
      `fd_units` varchar(100) NOT NULL default '',
      `fd_autoupdate` enum('yes','no') NOT NULL default 'yes',
      `fd_flags` text NOT NULL,
      PRIMARY KEY  (`fielddataid`)
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

if (Jojo::fieldExists('fielddata', 'fd_quickedit')) {
    Jojo::structureQuery("ALTER TABLE {fielddata} DROP fd_quickedit");
}