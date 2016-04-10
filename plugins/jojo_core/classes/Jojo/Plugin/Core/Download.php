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

class Jojo_Plugin_Core_Download extends Jojo_Plugin_Core {
    /* File extensions to send inline */
    private $inlineExtensions = array('jpg', 'gif', 'jpeg', 'png', 'swf', 'xml');

    /**
     * Serve a file from the downloads folder.
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
        $filename = 'downloads/' . $file;
        $file = _DOWNLOADDIR . '/' . Jojo::relative2absolute(urldecode($file), '/');
        if (file_exists($file)) {
            $content = file_get_contents($file);
            Jojo::runHook('jojo_core:downloadFile', array('filename' => $file));
            $cachetime = Jojo::getOption('contentcachetime_resources', 604800);
            $lastmodified = filemtime($file);
            /* Send header */
            parent::sendCacheHeaders($lastmodified, $cachetime);
            header('Content-Type: ' . Jojo::getMimeType($file));
            header('Content-Length: ' . strlen($content));
            if (in_array(Jojo::getFileExtension($file), $this->inlineExtensions)) {
                header('Content-disposition: inline; filename="' . basename($file) . '"');
            } else {
                header('Content-disposition: attachment; filename="' . basename($file) . '"');
            }
            header('Content-Transfer-Encoding: binary');

            /* Send Content */
            echo $content;
            exit;
        }

        /* Not found, 404 */
        header("HTTP/1.0 404 Not Found", true, 404);
        exit;
    }
}
