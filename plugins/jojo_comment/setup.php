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

/* Edit Comments */
$data = Jojo::selectRow("SELECT * FROM {page}  WHERE pg_url='admin/edit/comment'");
if (!count($data)) {
    echo "Comments: Adding <b>Edit Comments</b> Page to menu<br />";
    $editCommentspage = Jojo::insertQuery("INSERT INTO {page} SET pg_title='Edit Comments', pg_link='Jojo_Plugin_Admin_Edit', pg_url='admin/edit/comment', pg_parent=?, pg_order=2", array($_ADMIN_CONTENT_ID));
} 

/* Comment Handler */
$data = Jojo::selectRow("SELECT * FROM {page} WHERE pg_link='jojo_plugin_jojo_comment'");
if (!count($data)) {
    echo "Comments: Adding <b>Comments Admin</b> Page<br />";
    Jojo::insertQuery("INSERT INTO {page} SET pg_title='Comment Handler', pg_link='jojo_plugin_jojo_comment', pg_url='commentadmin', pg_parent=?, pg_sitemapnav='no', pg_xmlsitemapnav='no'", $_NOT_ON_MENU_ID);
}
