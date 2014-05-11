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
 * @license http://www.fsf.org/copyleft/lgpl.html GNU Lesser General Public License
 * @link    http://www.jojocms.org JojoCMS
 * @package jojo_google_verification
 */

/* Google verification page */
$data = Jojo::selectQuery("SELECT * FROM {page} WHERE pg_link='Jojo_Plugin_jojo_google_verification'");
if (!count($data)) {
    echo "jojo_google_verification: Adding <b>Google verification</b> Page to menu<br />";
    Jojo::insertQuery("INSERT INTO {page} SET pg_title='Google verification', pg_link='Jojo_Plugin_jojo_google_verification', pg_url='', pg_parent = ?, pg_order=0, pg_mainnav='no', pg_footernav='no', pg_sitemapnav='no', pg_xmlsitemapnav='no', pg_index='yes', pg_body='', pg_status='hidden'", array($_NOT_ON_MENU_ID));
}