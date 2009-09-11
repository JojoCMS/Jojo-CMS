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

/* Delete old Tags {page} */
$data = Jojo::selectQuery("SELECT * FROM {page} WHERE pg_link = 'tags.php'");
if (count($data)) {
    echo "jojo_tags: Deleting old <b>Tags</b> {page} from menu<br />";
    Jojo::deleteQuery("DELETE FROM {page} WHERE pg_link = 'tags.php'");
}

/* Tags {page} */
Jojo::updateQuery("UPDATE {page} SET pg_link='Jojo_Plugin_Jojo_tags' WHERE pg_link='jojo_tags.php'");
$data = Jojo::selectQuery("SELECT * FROM {page} WHERE pg_link = 'Jojo_Plugin_Jojo_Tags'");
if (!count($data)) {
    echo "Jojo_Plugin_Jojo_Tags: Adding <b>Tags</b> {page} to menu<br />";
    Jojo::insertQuery("INSERT INTO {page} SET pg_title = 'Tags', pg_link = 'Jojo_Plugin_Jojo_Tags', pg_url = 'tags', pg_parent= ? , pg_order=0, pg_mainnav='no', pg_body = ''", array($_NOT_ON_MENU_ID));
}

/* Edit Tags */
$data = Jojo::selectQuery("SELECT * FROM {page} WHERE pg_url = 'admin/edit/tag'");
if (!count($data)) {
    echo "Jojo_Plugin_Jojo_Tags: Adding <b>Edit Tags</b> {page} to menu<br />";
    Jojo::insertQuery("INSERT INTO {page} SET pg_title = 'Edit Tags', pg_link = 'Jojo_Plugin_Admin_Edit', pg_url = 'admin/edit/tag', pg_parent=?, pg_order=3", array($_ADMIN_CONTENT_ID));
}