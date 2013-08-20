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

class Jojo_Plugin_Core_Json extends Jojo_Plugin_Core {

    /**
     * Output the JSON responce
     *
     * In the getCorrectUrl function so we can interupt the code execution
     * without the overhead of all the smarty setup etc
     */
    function __construct()
    {
        /* Get requested filename */
        $file = Jojo::getFormData('file', false);

        /* Check file name is not empty */
        if (!$file) {
            /* Not found, 404 time */
            header("HTTP/1.0 404 Not Found", true, 404);
            exit;
        }
        /* Check for override in a Theme */
        $files = Jojo::listThemes('json/' . $file);

        if (isset($files[0])) {
            $file = $files[0];
        } else {
            /* Check for external in a Plugin */
            $files = Jojo::listPlugins('json/' . $file);
            if (isset($files[0])) {
                $file = $files[0];
            } else {
                /* Not found, 404 time */
                header("HTTP/1.0 404 Not Found", true, 404);
                exit;
            }
        }

        /* Change to directory */
        chdir(dirname($file));

        /* Include the php file */
        header('Content-Type: application/javascript; charset=utf-8');
        global $_USERID, $_USERGROUPS;
        include($file);
        exit;
   }
}



