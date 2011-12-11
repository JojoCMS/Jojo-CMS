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
 * @package jojo_tags
 */

$table = 'tag';

/* need to change primary key for upgrade installs */
if (Jojo::tableExists('tag') && !Jojo::fieldExists('tag','tagid')) {
  echo "Adding new primary key to tags\n";
  Jojo::structureQuery("ALTER TABLE {tag} ADD `tagid` BIGINT(20) NOT NULL FIRST ;");
  $tags = Jojo::selectQuery("SELECT * FROM {tag}");
  $n = count($tags);
  for ($i=0;$i<$n;$i++) {
      Jojo::updateQuery("UPDATE {tag} SET tagid = ".($i+1)." WHERE tag = ? LIMIT 1", array($tags[$i]['tag']));
  }
  Jojo::structureQuery("ALTER TABLE {tag} DROP PRIMARY KEY;");
  Jojo::structureQuery("ALTER TABLE {tag} ADD PRIMARY KEY ( `tagid` )");
  Jojo::structureQuery("ALTER TABLE {tag} CHANGE `tagid` `tagid` BIGINT(20) NOT NULL auto_increment ");
  Jojo::structureQuery("ALTER TABLE {tag} ENGINE = innodb");
  Jojo::structureQuery("ALTER TABLE {tag} DROP `tag`");
}

$query = "
        CREATE TABLE {tag} (
          `tagid` bigint(20) NOT NULL auto_increment,
          `tg_tag` varchar(255) NOT NULL,
          `tg_seotitle` varchar(255) default NULL,
          `tg_metadesc` varchar(255) default NULL,
          `tg_body` text,
          `tg_bbbody` text,
          `tg_replace` int(11),
          PRIMARY KEY  (`tagid`),
          UNIQUE KEY `tg_tag` (`tg_tag`)
        ) ENGINE=InnoDB;";

/* Check table structure */
$result = Jojo::checkTable($table, $query);

/* Output result */
if (isset($result['created'])) {
    echo sprintf("jojo_article: Table <b>%s</b> Does not exist - created empty table.<br />", $table);
}

if (isset($result['added'])) {
    foreach ($result['added'] as $col => $v) {
        echo sprintf("jojo_article: Table <b>%s</b> column <b>%s</b> Does not exist - added.<br />", $table, $col);
    }
}

if (isset($result['different'])) Jojo::printTableDifference($table,$result['different']);

if (Jojo::fieldExists('tag','tag')) {
    Jojo::updateQuery("UPDATE {tag} SET tg_tag=tag WHERE tg_tag=''");
    Jojo::structureQuery("ALTER TABLE {tag} DROP `tag`");
}

/*Add tags field to Page table */

$table = 'page';
$query = "
    CREATE TABLE {page} (
      `pageid` int(11) NOT NULL auto_increment,
      `pg_tags` text
    );";

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

if (isset($result['different'])) Jojo::printTableDifference($table, $result['different']);

/*Add tags field to Articles table (if there is one)*/

if (Jojo::tableExists('article')) {
    $table = 'article';
    $query = "
        CREATE TABLE {article} (
          `articleid` int(11) NOT NULL auto_increment,
          `ar_tags` text NULL
        );";


    /* Check table structure */
    $result = Jojo::checkTable($table, $query);

    /* Output result */
    if (isset($result['created'])) {
        echo sprintf("jojo_article: Table <b>%s</b> Does not exist - created empty table.<br />", $table);
    }

    if (isset($result['added'])) {
        foreach ($result['added'] as $col => $v) {
            echo sprintf("jojo_article: Table <b>%s</b> column <b>%s</b> Does not exist - added.<br />", $table, $col);
        }
    }

    if (isset($result['different'])) Jojo::printTableDifference($table, $result['different']);
}