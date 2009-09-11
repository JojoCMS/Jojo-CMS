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

$table = 'contentcache';
$query = "
    CREATE TABLE {contentcache} (
      `cc_url` varchar(255) NOT NULL default '',
      `cc_userid` int(11) NOT NULL default '0',
      `cc_content` text NOT NULL,
      `cc_cached` int(11) NOT NULL default '0',
      `cc_expires` int(11) NOT NULL default '0',
      PRIMARY KEY  (`cc_url`,`cc_userid`)
    ) TYPE=MyISAM;";

/* Check table structure */
$result = Jojo::checkTable($table, $query);

/* Change collation of cc_content field to UTF-8 to avoid ? characters appearing in cached output*/
Jojo::structureQuery("ALTER TABLE {contentcache} CHANGE `cc_content` `cc_content` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ");

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