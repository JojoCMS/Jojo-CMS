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

$table = 'user';
$query = "
    CREATE TABLE {user} (
      `userid` int(11) NOT NULL auto_increment,
      `us_login` varchar(100) NOT NULL default '',
      `us_password` varchar(255) NOT NULL default '',
      `us_salt` varchar(16) NOT NULL default '',
      `us_lastfailure` datetime NOT NULL default '0000-00-00 00:00:00',
      `us_failures` int(11) NOT NULL default '0',
      `us_locked` int(11) NOT NULL default '0',
      `us_firstname` varchar(100) NOT NULL default '',
      `us_lastname` varchar(100) NOT NULL default '',
      `us_email` varchar(100) NOT NULL default '',
      `us_reminder` varchar(255) NOT NULL default '',
      `us_reset` varchar(255) NOT NULL default '',
      `us_timezone` int(11) NOT NULL default '12',
      `us_groups` varchar(255) NOT NULL default '',
      `blacklisted` tinyint(4) default '0',
      PRIMARY KEY  (`userid`)
    ) ENGINE=MyISAM CHARSET=utf8 COLLATE utf8_general_ci ;";

/* Check table structure */
$result = Jojo::checkTable($table, $query);

/* Output result */
if (isset($result['created'])) {
    echo sprintf("Table <b>%s</b> Does not exist - created empty table.<br />", $table);

    // Default user - u:admin p:(value of _MASTERPASS constant)
    echo "Adding admin user<br />";
    $passwordhash = Jojo_Auth_Local::hashPassword(_MASTERPASS, true);
    Jojo::insertQuery("INSERT INTO {user} SET  userid=1, us_login='admin', us_password=?, us_firstname='admin', us_lastname='admin'", array($passwordhash));
}

if (isset($result['added'])) {
    foreach ($result['added'] as $col => $v) {
        echo sprintf("Table <b>%s</b> column <b>%s</b> Does not exist - added.<br />", $table, $col);
    }
}

if (isset($result['different'])) Jojo::printTableDifference($table,$result['different']);
