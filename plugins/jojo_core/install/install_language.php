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
  `direction` varchar(3) NOT NULL default 'ltr',
  `root` int(11) default '0',
  `home` int(11) default '1',
  `longcode` varchar(50) default NULL,
  `active` enum('yes','no') NOT NULL default 'no',
  `lang_htmllanguage` varchar(10) NOT NULL default '',
  PRIMARY KEY  (`languagetableid`)
    ) TYPE=MyISAM;";

/* add the new primary key field if it does not exist */
if (Jojo::tableExists('language') && !Jojo::fieldExists('language', 'languagetableid')) {
    Jojo::structureQuery("ALTER TABLE {language} ADD `languagetableid` INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST ;");
}


/* Check table structure */
$result = Jojo::checkTable($table, $query);

/* Output result */
if (isset($result['created'])) {
    echo sprintf("Table <b>%s</b> Does not exist - created empty table.<br />", $table);

    echo "Adding default languages<br />";
    $query = "INSERT INTO {language} (`languagetableid`, `languageid`, `name`, `english_name`, `charset`, `direction`, `root`, `home`, `longcode`, `active`) VALUES
                (1, 'en', 'English', 'English', 'utf-8', 'ltr', 0, 1, 'english', 'yes'),
                (2, 'fr', 'Français', 'French', 'utf-8', 'ltr', 0, 1, 'francais', 'no'),
                (3, 'ru', 'Русский', 'Russian', 'utf-8', 'ltr', 0, 1, 'russkiy', 'no'),
                (4, 'zh', '中文', 'Chinese', 'utf-8', 'ltr', 0, 1, 'zhongwen', 'no'),
                (5, 'ja', '日本語', 'Japanese', 'utf-8', 'ltr', 0, 1, 'nihongo', 'no'),
                (6, 'es', 'Español', 'Spanish', 'utf-8', 'ltr', 0, 1, 'espanol', 'no'),
                (7, 'pl', 'Polski', 'Polish', 'utf-8', 'ltr', 0, 1, 'polski', 'no'),
                (8, 'ko', '한국어', 'Korean', 'utf-8', 'ltr', 0, 1, 'hangugeo', 'no'),
                (9, 'ar', 'العربية', 'Arabic', '', 'rtl', 0, 1, 'araby', 'no'),
                (10, 'th', 'ไทย', 'Thai', 'utf-8', 'ltr', 0, 1, 'thai', 'no'),
                (11, 'cz', 'Čeština', 'Czech', 'utf-8', 'ltr', 0, 1, 'czech', 'no'),
                (12, 'de', 'Deutsch', 'German', 'utf-8', 'ltr', 0, 1, 'deutch', 'no');
            ";
    Jojo::insertQuery($query);
}

if (isset($result['added'])) {
    foreach ($result['added'] as $col => $v) {
        echo sprintf("Table <b>%s</b> column <b>%s</b> Does not exist - added.<br />", $table, $col);
    }
}

if (isset($result['different'])) Jojo::printTableDifference($table,$result['different']);

/* make English 'active' if  no languages active (for legacy installs) */
if (Jojo::tableExists('language') && Jojo::fieldExists('language', 'active') && count(Jojo::selectQuery("SELECT * FROM {language} WHERE active='yes'")) == 0 ) {
    Jojo::structureQuery("UPDATE {language} SET `active` = 'yes' WHERE `languageid` = 'en' LIMIT 1 ;");
}

// New language/country functionality begins
// James Pluck jamesp@searchmasters.co.nz
// 3 April 2009
$table = 'lang_country';
$query = "
    CREATE TABLE {lang_country} (
        `lc_id` INT(11) NOT NULL auto_increment ,
        `lc_code` VARCHAR( 10 ) NOT NULL ,
        `lc_name` VARCHAR( 255 ) NOT NULL ,
        `lc_englishname` VARCHAR( 255 ) NOT NULL ,
        `lc_root` INT(11) NOT NULL ,
        `lc_home` INT(11) NOT NULL ,
        `lc_longcode` varchar(50) default NULL,
        `lc_defaultlang` VARCHAR( 10 ) NOT NULL ,
        PRIMARY KEY ( `lc_id` )
    ) ENGINE= MyISAM DEFAULT CHARSET=utf8;
";

/* Check table structure */
$result = Jojo::checkTable($table, $query);

/* Output result */
if (isset($result['created'])) {
    echo sprintf("Table <b>%s</b> Does not exist - created empty table.<br />", $table);
    // New table created.  Copy the existing data from the language table into the new table
    $populateQuery = "
        INSERT INTO {lang_country} (
            lc_code,
            lc_name,
            lc_englishname,
            lc_root,
            lc_home,
            lc_longcode,
            lc_defaultlang
        )
        SELECT
            `languageid` as code,
            `name` as name,
            `english_name` as englishname,
            `root` as root,
            `home` as home,
            `longcode` as longcode,
            `languageid` as defaultlang
        FROM {language}
    ";
    Jojo::insertQuery( $populateQuery);
}
if (isset($result['added'])) {
    foreach ($result['added'] as $col => $v) {
        echo sprintf("Table <b>%s</b> column <b>%s</b> Does not exist - added.<br />", $table, $col);
    }
}

if (isset($result['different'])) Jojo::printTableDifference($table,$result['different']);
