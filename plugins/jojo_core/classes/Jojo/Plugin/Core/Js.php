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

        $cachetime = Jojo::getOption('contentcachetime_resources', 604800);

        /* Check for existence of cached copy if user has not pressed CTRL-F5 */
        if ($cachefile && Jojo::fileExists($cachefile) && !Jojo::ctrlF5()) {
            Jojo::runHook('jojo_core:jsCachedFile', array('filename' => $cachefile));
            parent::sendCacheHeaders(filemtime($cachefile), $cachetime);
            $content = file_get_contents($cachefile);
            if (Jojo::getOption('enablegzip') == 1) Jojo::gzip();
            header('Content-type: text/javascript');
            echo $content;
            exit;
        }

        $js = new Jojo_Stitcher();
        $js->type = 'javascript';
        $js->getServerCache();

        switch($file) {
            case 'common':
                /* Dynamic Javascript */
                $js->addText("var siteurl = '" . _SITEURL . "';");
                $js->addText("var secureurl = '" . Jojo::either(Jojo::getOption('secureurl') , _SITEURL) . "';");

                /* Core functions */
                $js->addFile(_BASEPLUGINDIR . '/jojo_core/js/functions.js');

                /* FRAJAX */
                $js->addFile(_BASEPLUGINDIR . '/jojo_core/external/frajax/frajax.js');

                if (Jojo::getOption('jquery_useanytime', 'no')=='yes')
                    $js->addFile(_BASEPLUGINDIR . '/jojo_core/external/anytime/anytimec.js');
                
                /* Twitter Bootstrap options */
                /* Transitions */
                if (Jojo::getOption('tbootstrap_js_modal', 'no') == 'yes' || Jojo::getOption('tbootstrap_js_dropdown', 'no') == 'yes' || Jojo::getOption('tbootstrap_js_scrollspy', 'no') == 'yes' || Jojo::getOption('tbootstrap_js_tab', 'no') == 'yes' || Jojo::getOption('tbootstrap_js_tooltip', 'no') == 'yes' || Jojo::getOption('tbootstrap_js_popover', 'no') == 'yes' || Jojo::getOption('tbootstrap_js_affix', 'no') == 'yes' || Jojo::getOption('tbootstrap_js_alert', 'no') == 'yes' || Jojo::getOption('tbootstrap_js_button', 'no') == 'yes' || Jojo::getOption('tbootstrap_js_collapse', 'no') == 'yes' || Jojo::getOption('tbootstrap_js_carousel', 'no') == 'yes')
                    $js->addFile(_BASEPLUGINDIR . '/jojo_core/external/bootstrap/js/transition.js');
                /* Modals */
                if (Jojo::getOption('tbootstrap_js_modal', 'no') == 'yes')
                    $js->addFile(_BASEPLUGINDIR . '/jojo_core/external/bootstrap/js/modal.js');
                /* Dropdowns */
                if (Jojo::getOption('tbootstrap_js_dropdown', 'no') == 'yes')
                    $js->addFile(_BASEPLUGINDIR . '/jojo_core/external/bootstrap/js/dropdown.js');
                /* Scrollspy */
                if (Jojo::getOption('tbootstrap_js_scrollspy', 'no') == 'yes')
                    $js->addFile(_BASEPLUGINDIR . '/jojo_core/external/bootstrap/js/scrollspy.js');
                /* Togglable tabs */
                if (Jojo::getOption('tbootstrap_js_tab', 'no') == 'yes')
                    $js->addFile(_BASEPLUGINDIR . '/jojo_core/external/bootstrap/js/tab.js');
                /* Tooltips */
                if (Jojo::getOption('tbootstrap_js_tooltip', 'no') == 'yes' || Jojo::getOption('tbootstrap_js_popover', 'no') == 'yes')
                    $js->addFile(_BASEPLUGINDIR . '/jojo_core/external/bootstrap/js/tooltip.js');
                /* Popovers */
                if (Jojo::getOption('tbootstrap_js_popover', 'no') == 'yes')
                    $js->addFile(_BASEPLUGINDIR . '/jojo_core/external/bootstrap/js/popover.js');
                /* Affix plugin */
                if (Jojo::getOption('tbootstrap_js_affix', 'no') == 'yes')
                    $js->addFile(_BASEPLUGINDIR . '/jojo_core/external/bootstrap/js/affix.js');
                /* Alert messages */
                if (Jojo::getOption('tbootstrap_js_alert', 'no') == 'yes')
                    $js->addFile(_BASEPLUGINDIR . '/jojo_core/external/bootstrap/js/alert.js');
                /* Buttons */
                if (Jojo::getOption('tbootstrap_js_button', 'no') == 'yes')
                    $js->addFile(_BASEPLUGINDIR . '/jojo_core/external/bootstrap/js/button.js');
                /* Collapse */
                if (Jojo::getOption('tbootstrap_js_collapse', 'no') == 'yes' || Jojo::getOption('tbootstrap_components_navbar', 'no') == 'yes')
                    $js->addFile(_BASEPLUGINDIR . '/jojo_core/external/bootstrap/js/collapse.js');
                /* Carousel */
                if (Jojo::getOption('tbootstrap_js_carousel', 'no') == 'yes')
                    $js->addFile(_BASEPLUGINDIR . '/jojo_core/external/bootstrap/js/carousel.js');

                 /* jQueryMobile */
                if (Jojo::getOption('jquery_touch', 'no') == 'yes' && Jojo::getOption('jquery_version', '') == '1.9.1')
                    $js->addFile(_BASEPLUGINDIR . '/jojo_core/external/jquery/jquery.mobile.touch.js');

               /* Javascript from Plugins */
                foreach (Jojo::listPlugins('js/functions.js') as $pluginfile) {
                    $js->addFile($pluginfile);
                }

                if (Jojo::getOption('js')) {
                    $js->addText(Jojo::getOption('js'));
                }

                break;
                
            case 'commonadmin':
                /* Dynamic Javascript */
                $js->addText("var siteurl = '" . _SITEURL . "';");
                $js->addText("var secureurl = '" . Jojo::either(Jojo::getOption('secureurl') , _SITEURL) . "';");

                /* Core functions */
                $js->addFile(_BASEPLUGINDIR . '/jojo_core/js/functions.js');

                /* FRAJAX */
                $js->addFile(_BASEPLUGINDIR . '/jojo_core/external/frajax/frajax.js');

                /* Admin helpers */
                $js->addFile(_BASEPLUGINDIR . '/jojo_core/js/jpop.js');
                $js->addFile(_BASEPLUGINDIR . '/jojo_core/external/tablesorter/jquery.tablesorter.min.js');
                if (Jojo::getOption('wysiwyg_style')=='popup') {
                    /* Include Markitup editor css if using popup editor*/
                    $js->addFile(_BASEPLUGINDIR . '/jojo_core/external/markitup/jquery.markitup.pack.js');
                    $js->addFile(_BASEPLUGINDIR . '/jojo_core/external/markitup/sets/html/set.js');
                    $js->addFile(_BASEPLUGINDIR . '/jojo_core/external/markitup/sets/bbcode/set.js');
                }
                $js->addFile(_BASEPLUGINDIR . '/jojo_core/external/jstree/dist/jstree.js');
                $js->addFile(_BASEPLUGINDIR . '/jojo_core/external/anytime/anytimec.js');

                /* Twitter Bootstrap options */
                /* Transitions */
                    $js->addFile(_BASEPLUGINDIR . '/jojo_core/external/bootstrap/js/transition.js');
                /* Dropdowns */
                    $js->addFile(_BASEPLUGINDIR . '/jojo_core/external/bootstrap/js/dropdown.js');
                /* Togglable tabs */
                    $js->addFile(_BASEPLUGINDIR . '/jojo_core/external/bootstrap/js/tab.js');
                /* Alert messages */
                    $js->addFile(_BASEPLUGINDIR . '/jojo_core/external/bootstrap/js/alert.js');
                /* Buttons */
                    $js->addFile(_BASEPLUGINDIR . '/jojo_core/external/bootstrap/js/button.js');

                /* Core functions */
                $js->addFile(_BASEPLUGINDIR . '/jojo_core/js/admin.js');

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
        $optimise = (boolean)(strpos($f, 'pack')===false && strpos($f, 'min')===false);
        $js->output($optimise);

        /* Cache a copy for later */
        if ($cachefile) {
            $content = $js->data;
            Jojo::RecursiveMkdir(dirname($cachefile));
            file_put_contents($cachefile, $content);
            touch($cachefile, $js->modified);
            Jojo::publicCache('js/' . $f, $content, $js->modified);
        }
        exit;
    }
}
