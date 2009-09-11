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
 * @package jojo_credits
 */

$_options[] = array(
    'id'          => 'credits',
    'category'    => 'SEO',
    'label'       => 'Webmaster credits',
    'description' => 'A newline seperated list of credits, which are semi-randomly added to the bottom of every page. eg Web Design by [Company]. We recommended 16 different variations are added, see the readme for more details.',
    'type'        => 'textarea',
    'default'     => '',
    'options'     => '',
    'plugin'      => 'jojo_credits'
);

$_options[] = array(
    'id'          => 'credits_nofollow',
    'category'    => 'SEO',
    'label'       => 'Nofollow credits links',
    'description' => 'Nofollows all webmaster credits links except for on the homepage. For more info, please see the readme.',
    'type'        => 'radio',
    'default'     => 'no',
    'options'     => 'yes,no',
    'plugin'      => 'jojo_credits'
);