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

class Jojo_Plugin_Core_Inline extends Jojo_Plugin_Core {

    /**
     * Serve a file as explicitly inline (to override default browser download for file types like PDF).
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

        $filename = 'inline/' . $file;
        $file = _DOWNLOADDIR . '/' . Jojo::relative2absolute(urldecode($file), '/');
        if (file_exists($file)) {
            Jojo::runHook('jojo_core:downloadFile', array('filename' => $file));

            /* Get Content */
            $content = file_get_contents($file);

            $cachetime = Jojo::getOption('contentcachetime_resources', 604800);
            /* Send header */
            $lastmodified = filemtime($file);
            parent::sendCacheHeaders($lastmodified, $cachetime);
            header('Content-Type: ' . Jojo::getMimeType($file));
            header('Content-Length: ' . strlen($content));
			header('Content-disposition: inline; filename="' . basename($file) . '"');
            header("Content-Transfer-Encoding: binary");

            /* Send Content */
            echo $content;

            /* Cache Content */
            Jojo::publicCache($filename, $content, $lastmodified);
            exit;
        }

        /* Not found, 404 */
        header("HTTP/1.0 404 Not Found", true, 404);
        exit;
    }
}
