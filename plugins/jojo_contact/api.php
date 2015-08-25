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

$_provides['pluginClasses'] = array(
        'Jojo_Plugin_Jojo_contact' => 'Contact - Contact Page'
        );

/* Content Filter */
Jojo::addFilter('output', 'contentFilter', 'jojo_contact');

/* add script library for ajax handling */
Jojo::addHook('foot', 'footjs', 'jojo_contact');

/* check downloads for permissions */
Jojo::addHook('jojo_core:downloadFile', 'downloadFile', 'jojo_contact');

$_options[] = array(
    'id'          => 'spam_links',
    'category'    => 'Spam',
    'label'       => 'Spam links',
    'description' => 'Block form submissions with more than this number of links in the body',
    'type'        => 'integer',
    'default'     => '5',
    'options'     => '',
    'plugin'      => 'jojo_contact'
);
