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

class Jojo_Plugin_Core_File extends Jojo_Plugin_Core {

    /**
     * Serve an external file.
     */
    function __construct()
    {
        /* Read only session */
        define('_READONLYSESSION', true);

        /* Get requested filename */
        $file = Jojo::getFormData('file', false);

        /* Check file name is set */
        if (!$file) {
            /* Not valid, 404 */
            header("HTTP/1.0 404 Not Found", true, 404);
            exit;
        }

        /* Look for the files in a plugin */
        foreach (Jojo::listPlugins('files/' . $file) as $pluginfile) {
            $mimetype = Jojo::getMimeType($pluginfile);
            if ($mimetype) {
                $cachetime = Jojo::getOption('contentcachetime_resources', 604800);
                $lastmodified = filemtime($pluginfile);
                parent::sendCacheHeaders($lastmodified, $cachetime);
                header('Content-Type:' . $mimetype);
                $content = file_get_contents($pluginfile);
                header('Content-Length: ' . strlen($content));
                echo $content;
                Jojo::publicCache('files/' . $file, $content, $lastmodified);
                exit;
            }
        }

        /* Not found, 404 */
        header("HTTP/1.0 404 Not Found", true, 404);
        exit;
    }
}