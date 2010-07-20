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

if (Jojo::tableExists('articlecomment')) {
    Jojo::structureQuery("RENAME TABLE  {articlecomment} TO  {comment}");
    Jojo::structureQuery("ALTER TABLE  {comment} ENGINE = INNODB DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci");
    $query = "ALTER TABLE  {comment} CHANGE `articlecommentid`  `commentid` int(11) NOT NULL AUTO_INCREMENT, ";
    $query .= "CHANGE  `ac_articleid`  `itemid` int(11) NOT NULL, ";
    $query .= "CHANGE  `ac_timestamp`  `timestamp` int(11) NOT NULL, ";
    $query .= "CHANGE  `ac_name`  `name` varchar(255) NOT NULL, ";
    $query .= "CHANGE  `ac_email`  `email` varchar(255) NOT NULL, ";
    $query .= "CHANGE  `ac_website`  `website` varchar(255) NOT NULL, ";
    $query .= "CHANGE  `ac_anchortext`  `anchortext` varchar(255) NOT NULL, ";
    $query .= "CHANGE  `ac_authorcomment`  `authorcomment` tinyint(1) NOT NULL default '0', ";
    $query .= "CHANGE  `ac_ip`  `ip` varchar(255) NOT NULL, ";
    $query .= "CHANGE  `ac_useanchortext`  `useanchortext`  tinyint(1) NOT NULL default '0', ";
    $query .= "CHANGE  `ac_body`  `body`text NOT NULL, ";
    $query .= "CHANGE  `ac_bbbody`  `bbbody`text NOT NULL, ";
    $query .= "CHANGE  `ac_nofollow`  `nofollow`  tinyint(1) NOT NULL default '1', ";
    $query .= "CHANGE  `ac_status`  `active`  tinyint(1) NOT NULL default '1', ";
    $query .= "CHANGE  `ac_userid`  `userid` int(11) NOT NULL, ";
    $query .= "CHANGE  `ac_approvecode`  `approvecode`varchar(255) NOT NULL, ";
    $query .= "CHANGE  `ac_anchortextcode`  `anchortextcode`varchar(255) NOT NULL, ";
    $query .= "CHANGE  `ac_deletecode`  `deletecode`varchar(255) NOT NULL, ";
    $query .= "ADD  `plugin` varchar(255) NOT NULL ";
    Jojo::structureQuery($query);
    Jojo::updateQuery("UPDATE {comment} SET authorcomment=0 WHERE authorcomment=2");
    Jojo::updateQuery("UPDATE {comment} SET useanchortext=0 WHERE useanchortext=2");
    Jojo::updateQuery("UPDATE {comment} SET nofollow=0 WHERE nofollow=2");
    Jojo::updateQuery("UPDATE {comment} SET active=0 WHERE active=2");
    Jojo::updateQuery("UPDATE {comment} SET plugin='jojo_article' ");
}

if (Jojo::tableExists('articlecommentsubscription')) {
    Jojo::structureQuery("RENAME TABLE  {articlecommentsubscription} TO  {commentsubscription}");
    Jojo::structureQuery("ALTER TABLE  {commentsubscription} ENGINE = INNODB DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci");
    $query = "ALTER TABLE  {commentsubscription} ";
    $query .= "CHANGE  `articleid`  `itemid` int(11) NOT NULL default '0', ";
    $query .= "ADD  `plugin` varchar(255) NOT NULL ";
    Jojo::structureQuery($query);
    Jojo::updateQuery("UPDATE {commentsubscription} SET plugin='jojo_article' ");
}

$table = 'comment';
$query = "
    CREATE TABLE {comment} (
      `commentid` int(11) NOT NULL auto_increment,
      `itemid` int(11) NOT NULL,
      `plugin` varchar(255) NOT NULL,
      `timestamp` int(11) NOT NULL,
      `name` varchar(255) NOT NULL,
      `email` varchar(255) NOT NULL,
      `website` varchar(255) NOT NULL,
      `anchortext` varchar(255) NOT NULL,
      `authorcomment` tinyint(1) NOT NULL default '0',
      `ip` varchar(255) NOT NULL,
      `useanchortext` tinyint(1) NOT NULL default '0',
      `body` text NOT NULL,
      `bbbody` text NOT NULL,
      `nofollow` tinyint(1) NOT NULL default '1',
      `active` tinyint(1) NOT NULL default '1',
      `userid` int(11) NOT NULL,
      `approvecode` varchar(255) NOT NULL,
      `anchortextcode` varchar(255) NOT NULL,
      `deletecode` varchar(255) NOT NULL,
      PRIMARY KEY  (`commentid`),
      KEY `plugin` (`plugin`),
      KEY `itemid` (`itemid`),
      KEY `userid` (`userid`)
    ) ENGINE=innodb;";

/* Check table structure */
$result = Jojo::checkTable($table, $query);

/* Output result */
if (isset($result['created'])) {
    echo sprintf("Comments: Table <b>%s</b> Does not exist - created empty table.<br />", $table);
}

if (isset($result['added'])) {
    foreach ($result['added'] as $col => $v) {
        echo sprintf("Comments: Table <b>%s</b> column <b>%s</b> Does not exist - added.<br />", $table, $col);
    }
}

if (isset($result['different'])) Jojo::printTableDifference($table,$result['different']);

/* create commentsubscription table */
$table = 'commentsubscription';
    $query = "
        CREATE TABLE {commentsubscription} (
        `userid` int(11) NOT NULL default '0',
        `itemid` int(11) NOT NULL default '0',
        `plugin` varchar(255) NOT NULL,
        `lastviewed` int(11) NOT NULL default '0',
        `lastemailed` int(11) NOT NULL default '0',
        `lastupdated` int(11) NOT NULL default '0',
        `code` varchar(16) NOT NULL default ''
    ) ENGINE=innodb;";

/* Check table structure */
$result = Jojo::checkTable($table, $query);

/* Output result */
if (isset($result['created'])) {
    echo sprintf("Comments: Table <b>%s</b> Does not exist - created empty table.<br />", $table);
}

if (isset($result['added'])) {
    foreach ($result['added'] as $col => $v) {
        echo sprintf("Comments: Table <b>%s</b> column <b>%s</b> Does not exist - added.<br />", $table, $col);
    }
}

if (isset($result['different'])) Jojo::printTableDifference($table,$result['different']);
