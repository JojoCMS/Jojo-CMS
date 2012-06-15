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

        /* If the filename is clean, cache the css */
        $cachefile = false;
        if (preg_match('%^([a-zA-Z]+)$%', $file)) {
            $cachefile = _CACHEDIR . '/css/' . $file . '.css';
        }

        /* Check for existance of cached copy if user has not pressed CTRL-F5 */
        if ($cachefile && Jojo::fileExists($cachefile) && !Jojo::ctrlF5()) {
            Jojo::runHook('jojo_core:cssCachedFile', array('filename' => $cachefile));
            parent::sendCacheHeaders(filemtime($cachefile));
            $content = file_get_contents($cachefile);
            if (Jojo::getOption('enablegzip') == 1) Jojo::gzip();
            header('Content-type: text/css');
            //header('Cache-Control: ');
            //header('Pragma:');
            header('Last-Modified: '.date('D, d M Y H:i:s \G\M\T', filemtime($cachefile)));
            //header('Expires: ');
            header('Cache-Control: private, max-age=28800');
            header('Expires: ' . date('D, d M Y H:i:s \G\M\T', time() + 28800));
            header('Pragma: ');
            echo $content;
            exit;
        }

        if (!defined('_CONTENTCACHE')) {
            define('_CONTENTCACHE',     Jojo::getOption('contentcache') == 'no' ? false : true);
            define('_CONTENTCACHETIME', Jojo::either(Jojo::getOption('contentcachetime'), 3600));
        }

        $start = Jojo::timer();
        $css = new Jojo_Stitcher();
        $css->getServerCache();
        $useLess = Jojo::getOption('less', 'no') == 'no' ? false : true;
        switch($file) {
            case 'styles':
                /* Include Boilerplate css reset */
                if  (Jojo::getOption('normalize_cssreset', 'no')=='yes') {
                    $css->addFile(_BASEDIR . '/plugins/jojo_core/external/normalize/normalize.css');
                    if  (Jojo::getOption('modernizr', 'no')=='yes') {
                        $css->addFile(_BASEDIR . '/plugins/jojo_core/css/boilerplate_modernizr.css');
                    }
                }
                if ($useLess) {
                    /* start with the variable files */
                    if (Jojo::getOption('tbootstrap_variables', 'no') == 'yes')
                        $css->addFile(_BASEDIR . '/plugins/jojo_core/external/bootstrap/less/variables.less');
                    foreach (Jojo::listThemes('css/variables.less') as $themefile) {
                        $variableFound = $css->addFile($themefile);
                    }
                    /* mixins files */
                    if (Jojo::getOption('tbootstrap_mixins', 'no') == 'yes')
                        $css->addFile(_BASEDIR . '/plugins/jojo_core/external/bootstrap/less/mixins.less');
                    foreach (Jojo::listThemes('css/mixins.less') as $themefile) {
                        $variableFound = $css->addFile($themefile);
                    }
                }
                
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
        
        /* additional CSS files as added by plugins / themes */
        $additional = Jojo::mergeCSS($file);
        foreach ($additional as $f) {
            foreach (Jojo::listPlugins('css/'.$f) as $pluginfile) {
                $css->addFile($pluginfile);
            }
        }
        foreach ($additional as $f) {
            foreach (Jojo::listThemes('css/'.$f) as $themefile) {
                $css->addFile($themefile);
            }
        }

        $timetoadd = Jojo::timer($start) * 1000;
        if ($css->numfiles == 0) {
            /* Didn't find any files that match, 404 */
            header("HTTP/1.0 404 Not Found", true, 404);
            exit;
        }

        $css->setServerCache();
        $css->output();

        /* Cache a copy for later */
        if ($cachefile) {
            $content = $css->data;
            Jojo::RecursiveMkdir(dirname($cachefile));
            file_put_contents($cachefile, $content);
            touch($cachefile, $css->modified);
            Jojo::publicCache($f, $content, $css->modified);
        }

        if (_DEBUG) {
            echo "/* Adding files took " . $timetoadd . " ms*/\n";
            echo "/* Total time to ouput " . (Jojo::timer($start) * 1000) . " ms*/";
        }
        exit;
    }
}
