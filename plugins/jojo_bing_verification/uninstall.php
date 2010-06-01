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
 * @author  Michael Brandon <michael@searchmasters.co.nz>
 * @author  Harvey Kane <code@ragepank.com>
 * @license http://www.fsf.org/copyleft/lgpl.html GNU Lesser General Public License
 * @link    http://www.jojocms.org JojoCMS
 * @package jojo_bing_verification
 */

/* Bing verification page */
$data = Jojo::selectQuery("SELECT * FROM {page} WHERE pg_link='Jojo_Plugin_jojo_bing_verification'");
if (count($data)) {
    echo "jojo_bing_verification: Deleting <b>Bing verification</b> Page from menu<br />";
    Jojo::insertQuery("DELETE FROM {page} WHERE pg_link='Jojo_Plugin_jojo_bing_verification');
}