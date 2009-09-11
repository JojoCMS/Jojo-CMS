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
 * @package jojo_google_verification
 */

/* Register URI patterns */
Jojo::registerURI("[filename:google[0-9a-f]{16}\\.html]", 'Jojo_Plugin_jojo_google_verification'); // "google487f41eac1c4595a.html"

$_options[] = array(
    'id' => 'googleverification',
    'category' => 'SEO',
    'label' => 'Google Verification',
    'description' => 'A newline seperated list of Google verification filenames - eg google487f41eac1c4595a.html',
    'type' => 'textarea',
    'default' => '',
    'options' => '',
);