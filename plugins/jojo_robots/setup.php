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
 * @package jojo_robots
 */

/* Add robots.txt page if one does not exist */
Jojo::updateQuery("UPDATE {page} SET pg_link='Jojo_Plugin_Jojo_robots' WHERE pg_link='jojo_robots.php'");
Jojo::updateQuery("UPDATE {page} SET pg_sitemapnav='no', pg_xmlsitemapnav='no' WHERE pg_link='Jojo_Plugin_Jojo_robots'");

$data = Jojo::selectQuery("SELECT * FROM {page} WHERE pg_link = 'Jojo_Plugin_Jojo_Robots'");
if (!count($data)) {
    echo "Adding <b>Robots.txt</b> Page<br />";
    Jojo::insertQuery("INSERT INTO {page} SET pg_title = 'Robots.txt', pg_link = 'Jojo_Plugin_Jojo_Robots', pg_url = 'robots.txt', pg_parent = ?, pg_order=0, pg_mainnav='no', pg_body = '',pg_sitemapnav='no', pg_xmlsitemapnav='no'", array($_NOT_ON_MENU_ID));
}