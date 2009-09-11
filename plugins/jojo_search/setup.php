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
 * @package jojo_search
 */

/* Site Search (Search Results) Page */
$data = Jojo::selectQuery("SELECT * FROM {page} WHERE pg_link = 'Jojo_Plugin_Jojo_Search'");
if (!count($data)) {
    echo "Adding <b>Site Search</b> Page to menu<br />";
    Jojo::insertQuery("INSERT INTO {page} SET pg_title = 'Search Results', pg_link = 'Jojo_Plugin_Jojo_Search', pg_url = 'search', pg_parent = ?, pg_order=0, pg_mainnav='no', pg_body = ''", array($_NOT_ON_MENU_ID));
}