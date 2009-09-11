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

/* add contact page */
Jojo::updateQuery("UPDATE {page} SET pg_link='Jojo_Plugin_Jojo_Contact' WHERE pg_link='jojo_contact.php'");
$data = Jojo::selectQuery("SELECT * FROM {page} WHERE pg_link='Jojo_Plugin_Jojo_Contact'");
if (!count($data)) {
    echo "Adding <b>Contact</b> Page to menu<br />";
    Jojo::insertQuery("INSERT INTO {page} SET pg_title='Contact', pg_link='Jojo_Plugin_Jojo_Contact', pg_url='contact'");
}