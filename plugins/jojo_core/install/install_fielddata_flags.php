<?php
/**
 *                    Jojo CMS
 *                ================
 *
 * Copyright 2007-2009 Harvey Kane <code@ragepank.com>
 * Copyright 2007-2009 Michael Holt <code@gardyneholt.co.nz>
 *
 * See the enclosed file license.txt for license information (LGPL). If you
 * did not receive this file, see http://www.fsf.org/copyleft/lgpl.html.
 *
 * @author  Harvey Kane <code@ragepank.com>
 * @license http://www.fsf.org/copyleft/lgpl.html GNU Lesser General Public License
 * @link    http://www.jojocms.org JojoCMS
 * @package jojo_core
 */

$table = 'fielddata_flags';
$query = "
     CREATE TABLE {fielddata_flags} (
      `flag` VARCHAR( 255 ) NOT NULL ,
      `name` VARCHAR( 255 ) NOT NULL ,
      `description` VARCHAR( 255 ) NOT NULL ,
      PRIMARY KEY ( `flag` )
      ) ENGINE = InnoDB ";

/* Check table structure */
$result = Jojo::checkTable($table, $query);

/* Output result */
if (isset($result['created'])) {
    echo sprintf("Table <b>%s</b> Does not exist - created empty table.<br />", $table);
    
    echo "Adding default flags<br />";
    Jojo::insertQuery("INSERT INTO {fielddata_flags} VALUES ('PRIVACY', 'Privacy Option', 'Shows the option to keep this data private');");
    Jojo::insertQuery("INSERT INTO {fielddata_flags} VALUES ('PRIVATE', 'Private by default', 'Will set this field to be private by default (requires the PRIVACY flag)');");
    Jojo::insertQuery("INSERT INTO {fielddata_flags} VALUES ('REGISTER', 'Show on Register', 'This field will be shown on the register page');");
    Jojo::insertQuery("INSERT INTO {fielddata_flags} VALUES ('PROFILE', 'Show on Profile', 'This field will be editable on the user profile page.');");
}

if (isset($result['added'])) {
    foreach ($result['added'] as $col => $v) {
        echo sprintf("Table <b>%s</b> column <b>%s</b> Does not exist - added.<br />", $table, $col);
    }
}

if (isset($result['different'])) Jojo::printTableDifference($table,$result['different']);