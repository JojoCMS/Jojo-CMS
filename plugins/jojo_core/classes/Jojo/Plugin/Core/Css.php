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

class Jojo_Plugin_Core_Css extends Jojo_Plugin_Core {

    /**
     * Output the CSS file
     *
     * In the getCorrectUrl function so we can interupt the code execution
     * without the overhead of all the smarty setup etc
     */
    function __construct()
    {
        /* Read only session */
        define('_READONLYSESSION', true);

        /* Get requested filename */
        $file = Jojo::getFormData('file', false);
        $f = $file;

        /* Check file name is .css */
        if (!$file || strpos($file, '.css') === false) {
            /* Not valid, 404 */
            header("HTTP/1.0 404 Not Found", true, 404);
            exit;
        } else {
            /* Valid file extension */
            $file = str_replace( '.css', '', $file);
        }

        $start = Jojo::timer();
        $css = new Jojo_Stitcher();
        $css->getServerCache();
        switch($file) {
            case 'styles':
                /* Include css from each plugin */
                foreach (Jojo::listPlugins('css/style_default.css', 'all', true) as $pluginfile) {
                    $css->addFile($pluginfile);
                }

                foreach (Jojo::listPlugins('css/style.css', 'all', true) as $pluginfile) {
                    $css->addFile($pluginfile);
                }

                foreach (Jojo::listPlugins('css/menu.css') as $pluginfile) {
                    $css->addFile($pluginfile);
                }

                /* Include theme css last */
                foreach (Jojo::listThemes('css/style.css') as $themefile) {
                    $css->addFile($themefile);
                }

                /* Include css snippet from database */
                if (Jojo::getOption('css')) {
                    $css->addText(Jojo::getOption('css'));
                }

                /* Add asset domains to css */
                $css->data = Jojo::CssAddAssets($css->data);
                break;

            case 'print':
                /* Include css from each plugin */
                foreach (Jojo::listPlugins('css/print.css', 'all', true) as $pluginfile) {
                    $css->addFile($pluginfile);
                }

                /* Include theme css last */
                foreach (Jojo::listThemes('css/print.css') as $themefile) {
                    $css->addFile($themefile);
                }

                /* Include css snippet from database */
                if (Jojo::getOption('css-print')) {
                    $css->addText(Jojo::getOption('css-print'));
                }
                break;

            case 'handheld':
                /* Include css from each plugin */
                foreach (Jojo::listPlugins('css/handheld.css') as $pluginfile) $css->addFile($pluginfile);

                /* Include css snippet from database */
                if (Jojo::getOption('css-handheld')) $css->addText(Jojo::getOption('css-handheld'));
                break;

            case 'admin':
                /* Include admin css from each plugin */
                foreach (Jojo::listPlugins('css/admin.css') as $pluginfile) {
                    $css->addFile($pluginfile);
                }
                break;

            case 'admin-print':
                /* Include admin css from each plugin */
                foreach (Jojo::listPlugins('css/admin_print.css') as $pluginfile) {
                    $css->addFile($pluginfile);
                }
                break;

            default:
                /* Include admin css from each plugin */
                foreach (Jojo::listPlugins('css/' . $file . '.css') as $pluginfile) {
                    $css->addFile($pluginfile);
                }
                break;
        }

        $timetoadd = Jojo::timer($start) * 1000;
        if ($css->numfiles == 0) {
            /* Didn't find any files that match, 404 */
            header("HTTP/1.0 404 Not Found", true, 404);
            exit;
        }

        $css->setServerCache();
        $css->output();
        Jojo::publicCache($f, $css->data);

        if (_DEBUG) {
            echo "/* Adding files took " . $timetoadd . " ms*/\n";
            echo "/* Total time to ouput " . (Jojo::timer($start) * 1000) . " ms*/";
        }
        exit;
    }
}