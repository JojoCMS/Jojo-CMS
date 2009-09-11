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

$_provides['pluginClasses'] = array(
        'Jojo_Plugin_Jojo_Robots' => 'Robots - Robots.txt generator',
        );

$_options[] = array(
    'id' => 'robots.txt',
    'category' => 'SEO',
    'label' => 'robots.txt',
    'description' => 'Jojo provides a basic robots.txt file for your site. If you wish to use your own, enter your robots.txt data here. DO NOT change this option if you are unsure of what you are doing.',
    'type' => 'textarea',
    'default' => '',
    'options' => '',
);