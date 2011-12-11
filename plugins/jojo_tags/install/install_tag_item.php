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

$table = 'tag_item';
$query = "
        CREATE TABLE {tag_item} (
        `tagid` BIGINT NOT NULL ,
        `itemid` VARCHAR( 50 ) NOT NULL ,
        `plugin` VARCHAR( 50 ) NOT NULL ,
        PRIMARY KEY ( `tagid` , `itemid` , `plugin` ),
        FOREIGN KEY fk_tag (tagid) REFERENCES {tag} (tagid)
        ) ENGINE=InnoDB;";

/* Check table structure */
$result = Jojo::checkTable($table, $query);

/* Output result */
if (isset($result['created'])) {
    echo sprintf("jojo_tags: Table <b>%s</b> Does not exist - created empty table.<br />", $table);
}

if (isset($result['added'])) {
    foreach ($result['added'] as $col => $v) {
        echo sprintf("jojo_tags: Table <b>%s</b> column <b>%s</b> Does not exist - added.<br />", $table, $col);
    }
}

if (isset($result['different'])) {
    Jojo::printTableDifference($table, $result['different']);
}

/* When a tag is deleted, automatically delete any tag_items that match */
/* find the name of the foreign key constraint which is created internally by MySQL */
$data = Jojo::selectQuery("SHOW CREATE TABLE {tag_item}");
preg_match_all('/CONSTRAINT `(.*?)` FOREIGN KEY/', $data[0]['Create Table'], $result, PREG_PATTERN_ORDER);
if (isset($result[1][0])) {
    $constraint = $result[1][0];
    /* remove the old foreign key constraint */
    Jojo::structureQuery("ALTER TABLE {tag_item} DROP FOREIGN KEY `$constraint`");
    /* add the new constraint with the CASCADE option */
    Jojo::structureQuery("ALTER TABLE {tag_item} ADD CONSTRAINT `$constraint` FOREIGN KEY (`tagid`) REFERENCES {tag} (`tagid`) ON DELETE CASCADE;");
}