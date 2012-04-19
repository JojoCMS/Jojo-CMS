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
 * @package jojo_contact
 */

/* Add "Edit Form Options" to Content menu */
$editFormsPage = 0;
$data = JOJO::selectQuery("SELECT * FROM {page} WHERE pg_url='admin/edit/form'");
if (count($data) == 0) {
    echo "Jojo_Plugin_Jojo_Contact: Adding <b>Forms</b> Page to Content menu<br />";
    $editFormsPage = Jojo::insertQuery("INSERT INTO {page} SET pg_title='Forms', pg_link='Jojo_Plugin_Admin_Edit', pg_url='admin/edit/form', pg_parent=". Jojo::clean($_ADMIN_CONTENT_ID)  .", pg_order=3");
}

/* Add "Edit Formfields" to Content menu */
$data = Jojo::selectQuery("SELECT * FROM {page} WHERE pg_url='admin/edit/formfield'");
if (count($data) == 0) {
    echo "Jojo_Plugin_Jojo_Contact: Adding <b>Formfields</b> Page to menu<br />";
    Jojo::insertQuery("INSERT INTO {page} SET pg_title='Form Fields', pg_link='Jojo_Plugin_Admin_Edit', pg_url='admin/edit/formfield', pg_parent=". $editFormsPage .", pg_order=1");
}

// View Contact log
if (!Jojo::selectRow("SELECT pageid FROM {page} WHERE pg_link='Jojo_Plugin_Admin_Contactlog'")) {
    echo "Adding <b>Contact Log</b> Page to menu<br />";
    Jojo::insertQuery("INSERT INTO {page} SET pg_title = 'Contact Log', pg_link='jojo_plugin_admin_contactlog', pg_url = 'admin/contactlog', pg_parent = ?, pg_order=12", array($_ADMIN_REPORTS_ID));
}
