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

class Jojo_Plugin_Core_Js extends Jojo_Plugin_Core {

    /**
     * Output the JS code
     *
     */
    public function __construct()
    {
        /* Read only session */
        define('_READONLYSESSION', true);

        /* Get requested filename */
        $file = Jojo::getFormData('file', false);
        $f = $file;

        /* Check file name is .js */
        if (!$file || strpos($file, '.js') === false) {
            /* Not valid, 404 */
            header("HTTP/1.0 404 Not Found", true, 404);
            exit;
        } else {
            /* Valid file extension */
            $file = str_replace( '.js', '', $file);
        }

        /* If the filename is clean, cache the js */
        $cachefile = false;
        if (preg_match('%^([a-zA-Z]+)$%', $file)) {
            $cachefile = _CACHEDIR . '/js/' . $file . '.js';
        }

        /* Check for existance of cached copy if user has not pressed CTRL-F5 */
        if ($cachefile && Jojo::fileExists($cachefile) && !Jojo::ctrlF5()) {
            Jojo::runHook('jojo_core:jsCachedFile', array('filename' => $cachefile));
            parent::sendCacheHeaders(filemtime($cachefile));
            $content = file_get_contents($cachefile);
            if (Jojo::getOption('enablegzip') == 1) Jojo::gzip();
            header('Content-type: text/javascript');
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

        $js = new Jojo_Stitcher();
        $js->type = 'javascript';
        $js->getServerCache();

        switch($file) {
            case 'common':
                /* Dynamic Javascript */
                $js->addText("var siteurl = '" . Jojo::getOption('siteurl') . "';");
                $js->addText("var secureurl = '" . Jojo::getOption('secureurl') . "';");

                /* Core functions */
                $js->addFile(_BASEPLUGINDIR . '/jojo_core/js/functions.js');

                /* FRAJAX */
                $js->addFile(_BASEPLUGINDIR . '/jojo_core/external/frajax/frajax.js');

                /* Javascript from Plugins */
                foreach (Jojo::listPlugins('js/functions.js') as $pluginfile) {
                    $js->addFile($pluginfile);
                }

                if (Jojo::getOption('js')) {
                    $js->addText(Jojo::getOption('js'));
                }

                break;

            default:
                /* Search for custom js in each plugin */
                foreach (Jojo::listPlugins('js/' . $file . '.js') as $pluginfile) {
                    $js->addFile($pluginfile);
                }
                break;
        }

        if ($js->numfiles == 0) {
            /* Didn't find any files that match, 404 */
            header("HTTP/1.0 404 Not Found", true, 404);
            exit;
        }
        $js->setServerCache();
        $js->output();

        /* Cache a copy for later */
        if ($cachefile) {
            $content = $js->data;
            Jojo::RecursiveMkdir(dirname($cachefile));
            file_put_contents($cachefile, $content);
            Jojo::publicCache($f, $content);
        }
        exit;
    }
}