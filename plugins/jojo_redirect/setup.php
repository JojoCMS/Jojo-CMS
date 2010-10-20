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
 * @package jojo_redirect
 */

// Edit Redirects
$data = Jojo::selectRow("SELECT pageid FROM {page} WHERE pg_link = ?", array('Jojo_Plugin_Admin_Redirects'));
if (!$data) {
   echo "Adding <b>Edit Redirects</b> Page to menu<br />";
   Jojo::insertQuery('INSERT INTO {page} SET pg_title = ?, pg_url = ?, pg_link = ?, pg_order = ?, pg_parent = ?, pg_contentcache = ?',
        array("Edit Redirects", "admin/redirects", "Jojo_Plugin_Admin_Redirects", "4", $_ADMIN_CONFIGURATION_ID, "no"));
}

if (isset($_SESSION['redirects'])) unset($_SESSION['redirects']);