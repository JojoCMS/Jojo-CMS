<?php
/**
 *                    Jojo CMS
 *                ================
 *
 * Copyright 2008 Michael Cochrane <mikec@jojocms.org>
 *
 * See the enclosed file license.txt for license information (LGPL). If you
 * did not receive this file, see http://www.fsf.org/copyleft/lgpl.html.
 *
 * @author  Michael Cochrane <mikec@jojocms.org>
 * @license http://www.fsf.org/copyleft/lgpl.html GNU Lesser General Public License
 * @link    http://www.jojocms.org JojoCMS
 * @package jojo_core
 */

class Jojo_Plugin_core_favicon extends Jojo_Plugin_Core {

    /**
     * Output the Favicon
     *
     */
    public function __construct()
    {
        /* Read only session */
        define('_READONLYSESSION', true);

        /*
         * Locate and return the favicon
         */

        /* Look for a png favicon in the theme */
        foreach (Jojo::listThemes('favicon.png') as $themefile) {
            header('Content-Type: image/png');
            echo file_get_contents($themefile);
            exit;
        }

        /* Look for bmp favicon in the theme */
        foreach (Jojo::listThemes('favicon.bmp') as $themefile) {
            header('Content-Type: image/bmp');
            echo file_get_contents($themefile);
            exit;
        }

        /* Look for ico favicon in the theme */
        foreach (Jojo::listThemes('favicon.ico') as $themefile) {
            header('Content-Type: image/x-icon');
            echo file_get_contents($themefile);
            exit;
        }
        /* Look for png favicon in a plugin */
        foreach (Jojo::listPlugins('favicon.png', 'all', true) as $pluginfile) {
            header('Content-Type: image/png');
            echo file_get_contents($pluginfile);
            exit;
        }

        /* Look for favicon in a plugin */
        foreach (Jojo::listPlugins('favicon.ico', 'all', true) as $pluginfile) {
            header('Content-Type: image/x-icon');
            echo file_get_contents($pluginfile);
            exit;
        }

        /* Return the one in the core_plugin */
        header('Content-Type: image/x-icon');
        echo file_get_contents(_BASEPLUGINDIR . '/jojo_core/favicon.ico');
        exit;
    }
}