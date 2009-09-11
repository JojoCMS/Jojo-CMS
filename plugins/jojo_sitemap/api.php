<?php
/**
 *                    Jojo CMS
 *                ================
 *
 * Copyright 2007-2008 Harvey Kane <code@ragepank.com>
 * Copyright 2007-2008 Michael Cochrane <mikec@jojocms.org>
 *
 * See the enclosed file license.txt for license information (LGPL). If you
 * did not receive this file, see http://www.fsf.org/copyleft/lgpl.html.
 *
 * @author  Harvey Kane <code@ragepank.com>
 * @author  Michael Cochrane <mikec@jojocms.org>
 * @license http://www.fsf.org/copyleft/lgpl.html GNU Lesser General Public License
 * @link    http://www.jojocms.org JojoCMS
 * @package jojo_sitemap
 */

$_provides['pluginClasses'] = array(
        'Jojo_Plugin_Jojo_sitemap'    => 'Sitemap - Sitemap Generator',
        'Jojo_Plugin_Jojo_SitemapXML' => 'Sitemap - XML Sitemap',
        'Jojo_Plugin_Jojo_SitemapXSL' => 'Sitemap - XML Sitemap Style'
        );

/* Register URI patterns */
Jojo::registerURI("sitemap/[ping:ping]",        'Jojo_Plugin_Jojo_SitemapXML'); // "sitemap/ping/" - will ping all available engines
Jojo::registerURI("sitemap/ping/[ping:string]", 'Jojo_Plugin_Jojo_SitemapXML'); // "sitemap/ping/google/"

/* Register filters */
Jojo::addFilter('jojo_robots_rules', 'robots', 'Jojo_Sitemap');

