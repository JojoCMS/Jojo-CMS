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

$table = 'language';
$query = "
    CREATE TABLE {language} (
  `languagetableid` int(11) NOT NULL auto_increment,
  `languageid` varchar(10) NOT NULL default '',
  `name` varchar(255) NOT NULL,
  `english_name` varchar(255) NOT NULL default '',
  `displayorder` int(11) NOT NULL default '1',
  `charset` varchar(15) NOT NULL default '',
  `ISOcode` varchar(15) NOT NULL default '',
  `direction` varchar(3) NOT NULL default 'ltr',
  `longcode` varchar(50) default NULL,
  `active` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`languagetableid`),
  KEY `active` (`active`)
    ) ENGINE=INNODB DEFAULT CHARSET=utf8 COLLATE utf8_general_ci ;
";

/* Convert yes no enum to binary int */
if (Jojo::tableExists($table) && Jojo::getMySQLType($table, 'active') == "enum('yes','no')") {
    Jojo::structureQuery("ALTER TABLE  {language} CHANGE `active` `active` TINYINT(1) NOT NULL DEFAULT '0'");
    $languages = Jojo::selectQuery("SELECT languagetableid, active FROM {language}");
    foreach ($languages as $k => $a) {
        if ($a['active']!=1) {
            Jojo::updateQuery("UPDATE {language} SET active=0 WHERE languagetableid=?", array($a['languagetableid']));
        }
    }
    Jojo::structureQuery("ALTER TABLE  {language} DROP `root`, DROP `home`, DROP `lang_htmllanguage`");
    echo "Update language table and remove old fields no longer in use<br />";
}

/* Check table structure */
$result = Jojo::checkTable($table, $query);

/* Output result */
if (isset($result['created'])) {
    echo sprintf("Table <b>%s</b> Does not exist - created empty table.<br />", $table);

    echo "Adding default languages<br />";
    $query = "INSERT INTO {language} (`languagetableid`, `languageid`, `name`, `english_name`, `charset`, `direction`,`longcode`, `active`) VALUES
                (1, 'en', 'English', 'English', 'utf-8', 'ltr', 'english', 'yes'),
                (2, 'fr', 'Français', 'French', 'utf-8', 'ltr', 'francais', 'no'),
                (3, 'ru', 'Русский', 'Russian', 'utf-8', 'ltr', 'russkiy', 'no'),
                (4, 'zh', '中文', 'Chinese', 'utf-8', 'ltr', 'zhongwen', 'no'),
                (5, 'ja', '日本語', 'Japanese', 'utf-8', 'ltr', 'nihongo', 'no'),
                (6, 'es', 'Español', 'Spanish', 'utf-8', 'ltr', 'espanol', 'no'),
                (7, 'pl', 'Polski', 'Polish', 'utf-8', 'ltr', 'polski', 'no'),
                (8, 'ko', '한국어', 'Korean', 'utf-8', 'ltr', 'hangugeo', 'no'),
                (9, 'ar', 'العربية', 'Arabic', 'utf-8', 'rtl', 'araby', 'no'),
                (10, 'th', 'ไทย', 'Thai', 'utf-8', 'ltr', 'thai', 'no'),
                (11, 'cz', 'Čeština', 'Czech', 'utf-8', 'ltr', 'czech', 'no'),
                (12, 'de', 'Deutsch', 'German', 'utf-8', 'ltr', 'deutch', 'no');
            ";
    Jojo::insertQuery($query);
}

if (isset($result['added'])) {
    foreach ($result['added'] as $col => $v) {
        echo sprintf("Table <b>%s</b> column <b>%s</b> Does not exist - added.<br />", $table, $col);
    }
}

if (isset($result['different'])) Jojo::printTableDifference($table,$result['different']);

// New language/country functionality begins
// James Pluck jamesp@searchmasters.co.nz
// 3 April 2009
$table = 'lang_country';
$query = "
    CREATE TABLE {lang_country} (
        `lc_id` int(11) NOT NULL auto_increment ,
        `lc_code` varchar( 10 ) NOT NULL ,
        `lc_name` varchar( 255 ) NOT NULL ,
        `lc_englishname` VARCHAR( 255 ) NOT NULL ,
        `lc_root` int(11) NOT NULL default '0',
        `lc_home` int(11) NOT NULL default '0',
        `lc_longcode` varchar(50) default NULL,
        `lc_defaultlang` varchar( 10 ) NOT NULL ,
        `displayorder` int(11) NOT NULL default '1',
        `default` tinyint(1) NOT NULL default '0',
        `active` tinyint(1) NOT NULL default '0',
        PRIMARY KEY ( `lc_id` ),
        KEY `active` (`active`)
    ) ENGINE= INNODB DEFAULT CHARSET=utf8 COLLATE utf8_general_ci ;
";

/* check for default */
$nodefault = (boolean)(Jojo::tableExists($table) && !Jojo::fieldExists($table, 'default'));

/* Check table structure */
$result = Jojo::checkTable($table, $query);

/* Output result */
if (isset($result['created'])) {
    echo sprintf("Table <b>%s</b> Does not exist - created empty table.<br />", $table);
    // New table created.  Copy the existing data from the language table into the new table
    echo "Adding default sub-site sections<br />";
    $query = "INSERT INTO {lang_country} (`lc_id`, `lc_code`, `lc_name`, `lc_englishname`, `lc_root`, `lc_home`,`lc_longcode`, `lc_defaultlang`,`active`,`default`) VALUES
                (1, 'en', 'Global', 'Global', 0, 1, 'english', 'en', 1, 1)
            ";
    Jojo::insertQuery($query);
}
if (isset($result['added'])) {
    foreach ($result['added'] as $col => $v) {
        echo sprintf("Table <b>%s</b> column <b>%s</b> Does not exist - added.<br />", $table, $col);
    }
}
if ($nodefault) {
    $default = Jojo::getOption('multilanguage-default', 'en');
    Jojo::updateQuery("UPDATE {lang_country} SET `default`=1, `active`=1 WHERE lc_code=?", array($default));
    echo "Update lang_country table to set default sub-section to $default<br />";
}

if (isset($result['different'])) Jojo::printTableDifference($table,$result['different']);
