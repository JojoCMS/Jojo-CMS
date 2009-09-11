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
 * @package jojo_core
 */

/* Base Directory where the jojo code lives */
if (!defined('_BASEDIR')) {
    define('_BASEDIR', dirname(dirname(__FILE__)));
}

/* Jojo Plugins folder */
if (!defined('_BASEPLUGINDIR')) {
    define('_BASEPLUGINDIR', _BASEDIR . '/plugins');
}

/* Jojo Themes folder */
if (!defined('_BASETHEMEDIR')) {
    define('_BASETHEMEDIR', _BASEDIR . '/themes');
}

/* Dirrectoy where site specific files so */
if (!defined('_MYSITEDIR')) {
    if (defined('_WEBDIR')) {
        define('_MYSITEDIR', _WEBDIR . '/_mysite');
    } else {
        echo '_MYSITEDIR is not defined. Please define this in config.php';
        exit;
    }
}

/* Site specific Plugins folder */
if (!defined('_PLUGINDIR')) {
    define('_PLUGINDIR', _MYSITEDIR . '/plugins');
}

/* Site specific Themes folder */
if (!defined('_THEMEDIR')) {
    define('_THEMEDIR', _MYSITEDIR . '/themes');
}

/* Site specific Download folder */
if (!defined('_DOWNLOADDIR')) {
    define('_DOWNLOADDIR', _MYSITEDIR . '/downloads');
}

/* Site specific Cache folder */
if (!defined('_CACHEDIR')) {
    define('_CACHEDIR', _MYSITEDIR . '/cache');
}

/* Database table name prefix */
if (!defined('_TBLPREFIX')) {
    define('_TBLPREFIX', '');
}

/* Debug mode on/off */
if (!defined('_DEBUG')) {
    define('_DEBUG', false);
}

/* Debug mode on/off */
if (!defined('_ADMIN')) {
    define('_ADMIN', 'admin');
}