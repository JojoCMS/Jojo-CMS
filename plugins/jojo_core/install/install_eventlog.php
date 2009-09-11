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

$table = 'eventlog';
$query = "
    CREATE TABLE {eventlog} (
      `eventlogid` int(11) NOT NULL auto_increment,
      `el_datetime` datetime NOT NULL default '0000-00-00 00:00:00',
      `el_code` varchar(100) NOT NULL,
      `el_desc` text NOT NULL,
      `el_importance` enum('critical','high','normal','low','very low') NOT NULL default 'normal',
      `el_shortdesc` varchar(255) NOT NULL default '',
      `el_userid` int(11) NOT NULL default '0',
      `el_username` varchar(255) NOT NULL default '',
      `el_ip` varchar(255) NOT NULL default '',
      `el_uri` varchar(255) NOT NULL default '',
      `el_referer` varchar(255) NOT NULL default '',
      `el_browser` varchar(255) NOT NULL default '',
      PRIMARY KEY  (`eventlogid`)
    ) TYPE=MyISAM;";

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