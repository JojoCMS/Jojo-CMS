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

/* Register URI patterns */
Jojo::registerURI("[filename:LiveSearchSiteAuth\\.xml]", 'Jojo_Plugin_jojo_bing_verification');

$_options[] = array(
    'id' => 'bingverification',
    'category' => 'SEO',
    'label' => 'Bing Verification',
    'description' => 'A newline separated list or users ID\'s for the Bing XML file LiveSearchSiteAuth.xml',
    'type' => 'textarea',
    'default' => '',
    'options' => '',
);