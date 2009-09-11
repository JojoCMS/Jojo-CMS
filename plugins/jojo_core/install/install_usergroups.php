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

$table = 'usergroups';
$query = "
    CREATE TABLE {usergroups} (
      `groupid` varchar(20) NOT NULL default '',
      `gr_name` varchar(255) NOT NULL default '',
      PRIMARY KEY  (`groupid`)
    ) TYPE=MyISAM;";

/* Check table structure */
$result = Jojo::checkTable($table, $query);

/* Output result */
if (isset($result['created'])) {
    echo sprintf("Table <b>%s</b> Does not exist - created empty table.<br />", $table);

    echo "Adding default user groups<br />";
    Jojo::insertQuery("INSERT INTO {usergroups} VALUES ('admin', 'Administrators');");
    Jojo::insertQuery("INSERT INTO {usergroups} VALUES ('sysinstall', 'System Installers');");
    Jojo::insertQuery("INSERT INTO {usergroups} VALUES ('registered', 'Registered Users');");
    Jojo::insertQuery("INSERT INTO {usergroups} VALUES ('notloggedin', 'Not Logged In');");
}

$data = Jojo::selectQuery("SELECT groupid FROM {usergroups} WHERE groupid='notloggedin'");
if(!count($data)) {
    echo "Adding notloggedin user group<br />";
    Jojo::insertQuery("INSERT INTO {usergroups} VALUES ('notloggedin', 'Not Logged In');");
}

if (isset($result['added'])) {
    foreach ($result['added'] as $col => $v) {
        echo sprintf("Table <b>%s</b> column <b>%s</b> Does not exist - added.<br />", $table, $col);
    }
}

if (isset($result['different'])) Jojo::printTableDifference($table,$result['different']);