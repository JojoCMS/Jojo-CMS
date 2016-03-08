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

class Jojo_Plugin_core_appletouchicon extends Jojo_Plugin_Core {

    /**
     * Output the Favicon
     *
     */
    public function __construct()
    {
        /* Read only session */
        define('_READONLYSESSION', true);
        $cachetime = Jojo::getOption('contentcachetime_resources', 604800);

        /*
         * Locate and return the favicon
         */

        /* Look for a png favicon in the theme */
        foreach (Jojo::listThemes('apple-touch-icon.png') as $themefile) {
            parent::sendCacheHeaders(filemtime($themefile), $cachetime);
            header('Content-Type: image/png');
            echo file_get_contents($themefile);
            ob_end_flush(); // Send the output and turn off output buffering
            exit;
        }

        header("HTTP/1.0 404 Not Found.");
        ob_end_flush(); // Send the output and turn off output buffering
        exit;
    }
}