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
        switch($file) {
            case 'styles':
                /* Include Boilerplate css reset */
                if  (Jojo::getOption('normalize_cssreset', 'no')=='yes') {
                    $css->addFile(_BASEPLUGINDIR . '/jojo_core/external/normalize/normalize.css');
                    if  (Jojo::getOption('modernizr', 'no')=='yes') {
                        $css->addFile(_BASEPLUGINDIR . '/jojo_core/css/boilerplate_modernizr.css');
                    }
                }
                if (Jojo::getOption('jquery_useanytime', 'no')=='yes') {
                    $css->addFile(_BASEPLUGINDIR . '/jojo_core/external/anytime/anytimec.css');
                }
                /* start with the variable files */
                if (Jojo::getOption('tbootstrap_variables', 'no') == 'yes')
                    $css->addFile(_BASEPLUGINDIR . '/jojo_core/external/bootstrap/less/variables.less');
                    $css->addFile(_BASEPLUGINDIR . '/jojo_core/external/bootstrap/less/utilities.less');
                foreach (Jojo::listThemes('css/variables.less') as $themefile) {
                    $variableFound = $css->addFile($themefile);
                }
                /* mixins files */
                if (Jojo::getOption('tbootstrap_mixins', 'no') == 'yes')
                    $css->addFile(_BASEPLUGINDIR . '/jojo_core/external/bootstrap/less/mixins.less');
                foreach (Jojo::listThemes('css/mixins.less') as $themefile) {
                    $variableFound = $css->addFile($themefile);
                }
                /* Body type and links file */
                if (Jojo::getOption('tbootstrap_scaffolding_typelinks', 'no') == 'yes')
                    $css->addFile(_BASEPLUGINDIR . '/jojo_core/external/bootstrap/less/scaffolding.less');
                /* Grid System file */
                if (Jojo::getOption('tbootstrap_scaffolding_grid', 'no') == 'yes')
                    $css->addFile(_BASEPLUGINDIR . '/jojo_core/external/bootstrap/less/grid.less');
                /* Headings, body, etc file */
                if (Jojo::getOption('tbootstrap_bass_type', 'no') == 'yes')
                    $css->addFile(_BASEPLUGINDIR . '/jojo_core/external/bootstrap/less/type.less');
                /* Code and pre file */
                if (Jojo::getOption('tbootstrap_bass_code', 'no') == 'yes')
                    $css->addFile(_BASEPLUGINDIR . '/jojo_core/external/bootstrap/less/code.less');
                /* Labels and badges file */
                if (Jojo::getOption('tbootstrap_bass_labels', 'no') == 'yes')
                    $css->addFile(_BASEPLUGINDIR . '/jojo_core/external/bootstrap/less/labels.less');
                    $css->addFile(_BASEPLUGINDIR . '/jojo_core/external/bootstrap/less/badges.less');
                /* Tables file */
                if (Jojo::getOption('tbootstrap_bass_tables', 'no') == 'yes')
                    $css->addFile(_BASEPLUGINDIR . '/jojo_core/external/bootstrap/less/tables.less');
                /* Forms file */
                if (Jojo::getOption('tbootstrap_bass_forms', 'no') == 'yes')
                    $css->addFile(_BASEPLUGINDIR . '/jojo_core/external/bootstrap/less/forms.less');
                    $css->addFile(_BASEPLUGINDIR . '/jojo_core/external/bootstrap/less/input-groups.less');
                /* Buttons file */
                if (Jojo::getOption('tbootstrap_bass_buttons', 'no') == 'yes')
                    $css->addFile(_BASEPLUGINDIR . '/jojo_core/external/bootstrap/less/buttons.less');
                /* Icons file */
                if (Jojo::getOption('tbootstrap_bass_sprites', 'no') == 'yes')
                    $css->addFile(_BASEPLUGINDIR . '/jojo_core/external/bootstrap/less/glyphicons.less');
                /* Media file */
                if (Jojo::getOption('tbootstrap_bass_media', 'no') == 'yes')
                    $css->addFile(_BASEPLUGINDIR . '/jojo_core/external/bootstrap/less/media.less');
                /* Button groups and dropdowns file */
                if (Jojo::getOption('tbootstrap_components_buttongroups', 'no') == 'yes')
                    $css->addFile(_BASEPLUGINDIR . '/jojo_core/external/bootstrap/less/button-groups.less');
                /* Navs, tabs, and pills file */
                if (Jojo::getOption('tbootstrap_components_navs', 'no') == 'yes')
                    $css->addFile(_BASEPLUGINDIR . '/jojo_core/external/bootstrap/less/navs.less');
                /* Navbar file */
                if (Jojo::getOption('tbootstrap_components_navbar', 'no') == 'yes')
                    $css->addFile(_BASEPLUGINDIR . '/jojo_core/external/bootstrap/less/navbar.less');
                /* Breadcrumbs file */
                if (Jojo::getOption('tbootstrap_components_breadcrumbs', 'no') == 'yes')
                    $css->addFile(_BASEPLUGINDIR . '/jojo_core/external/bootstrap/less/breadcrumbs.less');
                /* Pagination file */
                if (Jojo::getOption('tbootstrap_components_pagination', 'no') == 'yes')
                    $css->addFile(_BASEPLUGINDIR . '/jojo_core/external/bootstrap/less/pagination.less');
                /* Pager file */
                if (Jojo::getOption('tbootstrap_components_pager', 'no') == 'yes')
                    $css->addFile(_BASEPLUGINDIR . '/jojo_core/external/bootstrap/less/pager.less');
                /* Thumbnails file */
                if (Jojo::getOption('tbootstrap_components_thumbnails', 'no') == 'yes')
                    $css->addFile(_BASEPLUGINDIR . '/jojo_core/external/bootstrap/less/thumbnails.less');
                /* Alerts file */
                if (Jojo::getOption('tbootstrap_components_alerts', 'no') == 'yes')
                    $css->addFile(_BASEPLUGINDIR . '/jojo_core/external/bootstrap/less/alerts.less');
                /* Progress bars file */
                if (Jojo::getOption('tbootstrap_components_progressbars', 'no') == 'yes')
                    $css->addFile(_BASEPLUGINDIR . '/jojo_core/external/bootstrap/less/progress-bars.less');
                /* Hero unit file */
                if (Jojo::getOption('tbootstrap_components_herounit', 'no') == 'yes')
                    $css->addFile(_BASEPLUGINDIR . '/jojo_core/external/bootstrap/less/jumbotron.less');

                /* Bootstrap css as required by the javascript plugins */
                /* Tooltips file */
                if (Jojo::getOption('tbootstrap_js_tooltip', 'no') == 'yes')
                    $css->addFile(_BASEPLUGINDIR . '/jojo_core/external/bootstrap/less/tooltip.less');
                /* Popovers file */
                if (Jojo::getOption('tbootstrap_js_popover', 'no') == 'yes')
                    $css->addFile(_BASEPLUGINDIR . '/jojo_core/external/bootstrap/less/popovers.less');
                /* Modals file */
                if (Jojo::getOption('tbootstrap_js_modal', 'no') == 'yes')
                    $css->addFile(_BASEPLUGINDIR . '/jojo_core/external/bootstrap/less/modals.less');
                /* Dropdowns file */
                if (Jojo::getOption('tbootstrap_js_dropdown', 'no') == 'yes')
                    $css->addFile(_BASEPLUGINDIR . '/jojo_core/external/bootstrap/less/dropdowns.less');
                /* Carousel file */
                if (Jojo::getOption('tbootstrap_js_carousel', 'no') == 'yes')
                    $css->addFile(_BASEPLUGINDIR . '/jojo_core/external/bootstrap/less/carousel.less');

                /* Wells file */
                if (Jojo::getOption('tbootstrap_miscellaneous_wells', 'no') == 'yes')
                    $css->addFile(_BASEPLUGINDIR . '/jojo_core/external/bootstrap/less/wells.less');
                /* Close icon file */
                if (Jojo::getOption('tbootstrap_miscellaneous_close', 'no') == 'yes')
                    $css->addFile(_BASEPLUGINDIR . '/jojo_core/external/bootstrap/less/close.less');
                /* Utilities file */
                if (Jojo::getOption('tbootstrap_miscellaneous_utilities', 'no') == 'yes')
                    $css->addFile(_BASEPLUGINDIR . '/jojo_core/external/bootstrap/less/utilities.less');
                /* Component animations file */
                if (Jojo::getOption('tbootstrap_miscellaneous_componentanimations', 'no') == 'yes' || Jojo::getOption('tbootstrap_js_collapse', 'no') == 'yes')
                    $css->addFile(_BASEPLUGINDIR . '/jojo_core/external/bootstrap/less/panels.less');
                    $css->addFile(_BASEPLUGINDIR . '/jojo_core/external/bootstrap/less/component-animations.less');

                /* get a pre-responsive file from theme if exists */
                foreach (Jojo::listThemes('css/pre-responsive.less') as $themefile) {
                    $css->addFile($themefile);
                }
                    
                /* Responsive files */
                if (Jojo::getOption('tbootstrap_responsive', 'no') == 'yes') {
                    $css->addFile(_BASEPLUGINDIR . '/jojo_core/external/bootstrap/less/responsive-utilities.less');
                }

                /* Include css from each plugin */
                foreach (Jojo::listPlugins('css/style_default.css', 'all', true) as $pluginfile) {
                    $css->addFile($pluginfile);
                }

                foreach (Jojo::listPlugins('css/style_default.less', 'all', true) as $pluginfile) {
                    $css->addFile($pluginfile);
                }

                foreach (Jojo::listPlugins('css/style.css', 'all', true) as $pluginfile) {
                    $css->addFile($pluginfile);
                }

                foreach (Jojo::listPlugins('css/style.less', 'all', true) as $pluginfile) {
                    $css->addFile($pluginfile);
                }

                foreach (Jojo::listPlugins('css/menu.css') as $pluginfile) {
                    $css->addFile($pluginfile);
                }
                
                foreach (Jojo::listPlugins('css/menu.less', 'all', true) as $pluginfile) {
                    $css->addFile($pluginfile);
                }

                /* Include theme css last */
                foreach (Jojo::listThemes('css/style.css') as $themefile) {
                    $css->addFile($themefile);
                }

                foreach (Jojo::listThemes('css/style.less') as $themefile) {
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
                /* Include Boilerplate css reset */
                $css->addFile(_BASEPLUGINDIR . '/jojo_core/external/normalize/normalize.css');
                /* Include Anytime datepicker css */
                $css->addFile(_BASEPLUGINDIR . '/jojo_core/external/anytime/anytimec.css');
                if (Jojo::getOption('wysiwyg_style')=='popup') {
                /* Include Markitup editor css if using popup editor*/
                    $css->addFile(_BASEPLUGINDIR . '/jojo_core/external/markitup/skins/markitup/style.css');
                    $css->addFile(_BASEPLUGINDIR . '/jojo_core/external/markitup/sets/html/style.css');
                    $css->addFile(_BASEPLUGINDIR . '/jojo_core/external/markitup/sets/bbcode/style.css');
                }
                /* start with the variable files */
                $css->addFile(_BASEPLUGINDIR . '/jojo_core/external/bootstrap/less/variables.less');
                /* mixins and utilities files */
                $css->addFile(_BASEPLUGINDIR . '/jojo_core/external/bootstrap/less/mixins.less');
                $css->addFile(_BASEPLUGINDIR . '/jojo_core/external/bootstrap/less/utilities.less');
                /* Body type and links file */
                $css->addFile(_BASEPLUGINDIR . '/jojo_core/external/bootstrap/less/scaffolding.less');
                /* Grid System file */
                $css->addFile(_BASEPLUGINDIR . '/jojo_core/external/bootstrap/less/grid.less');
                /* Headings, body, etc file */
                $css->addFile(_BASEPLUGINDIR . '/jojo_core/external/bootstrap/less/type.less');
                /* Code and pre file */
                $css->addFile(_BASEPLUGINDIR . '/jojo_core/external/bootstrap/less/code.less');
                /* Labels and badges file */
                $css->addFile(_BASEPLUGINDIR . '/jojo_core/external/bootstrap/less/labels.less');
                $css->addFile(_BASEPLUGINDIR . '/jojo_core/external/bootstrap/less/badges.less');
                /* Tables file */
                $css->addFile(_BASEPLUGINDIR . '/jojo_core/external/bootstrap/less/tables.less');
                /* Forms file */
                $css->addFile(_BASEPLUGINDIR . '/jojo_core/external/bootstrap/less/forms.less');
                $css->addFile(_BASEPLUGINDIR . '/jojo_core/external/bootstrap/less/input-groups.less');
                /* Buttons file */
                $css->addFile(_BASEPLUGINDIR . '/jojo_core/external/bootstrap/less/buttons.less');
                /* Icons file */
                $css->addFile(_BASEPLUGINDIR . '/jojo_core/external/bootstrap/less/glyphicons.less');
                /* Button groups and dropdowns file */
                $css->addFile(_BASEPLUGINDIR . '/jojo_core/external/bootstrap/less/button-groups.less');
                /* Navs, tabs, and pills file */
                $css->addFile(_BASEPLUGINDIR . '/jojo_core/external/bootstrap/less/navs.less');
                /* Navbar file */
                $css->addFile(_BASEPLUGINDIR . '/jojo_core/external/bootstrap/less/navbar.less');
                /* Breadcrumbs file */
                $css->addFile(_BASEPLUGINDIR . '/jojo_core/external/bootstrap/less/breadcrumbs.less');
                /* Pagination file */
                $css->addFile(_BASEPLUGINDIR . '/jojo_core/external/bootstrap/less/pagination.less');
                /* Pager file */
                $css->addFile(_BASEPLUGINDIR . '/jojo_core/external/bootstrap/less/pager.less');
                /* Thumbnails file */
                $css->addFile(_BASEPLUGINDIR . '/jojo_core/external/bootstrap/less/thumbnails.less');
                /* Alerts file */
                $css->addFile(_BASEPLUGINDIR . '/jojo_core/external/bootstrap/less/alerts.less');
                /* Progress bars file */
                $css->addFile(_BASEPLUGINDIR . '/jojo_core/external/bootstrap/less/progress-bars.less');
                /* Hero unit file */
                $css->addFile(_BASEPLUGINDIR . '/jojo_core/external/bootstrap/less/jumbotron.less');
                $css->addFile(_BASEPLUGINDIR . '/jojo_core/external/bootstrap/less/wells.less');
                $css->addFile(_BASEPLUGINDIR . '/jojo_core/external/bootstrap/less/panels.less');
                /* Close icon file */
                $css->addFile(_BASEPLUGINDIR . '/jojo_core/external/bootstrap/less/close.less'); 
                /* Bootstrap css as required by the javascript plugins */
                $css->addFile(_BASEPLUGINDIR . '/jojo_core/external/bootstrap/less/component-animations.less');
                /* Tooltips file */
                $css->addFile(_BASEPLUGINDIR . '/jojo_core/external/bootstrap/less/tooltip.less');
                /* Popovers file */
                $css->addFile(_BASEPLUGINDIR . '/jojo_core/external/bootstrap/less/popovers.less');
                /* Modals file */
                $css->addFile(_BASEPLUGINDIR . '/jojo_core/external/bootstrap/less/modals.less');
                /* Dropdowns file */
                $css->addFile(_BASEPLUGINDIR . '/jojo_core/external/bootstrap/less/dropdowns.less');

                $css->addFile(_BASEPLUGINDIR . '/jojo_core/external/bootstrap/less/responsive-utilities.less');


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

    public static function parseImports($css, $filepath=false) {
        if (Jojo::getOption("css_imports", 'no') == 'yes') {
            $pattern = "/@import url ?\(?(['\"]?)([^'\"\)]+)\\1\)?\s?(.*?)\;/ims";
            preg_match_all($pattern, $css, $matches, PREG_SET_ORDER);
            /*
                $matches[0] = full import statement
                $matches[2] = file path
                $matches[3] = media query
            */

            $basedir = ($filepath) ? dirname($filepath) : dirname(jojo::getFormData('uri'));

            foreach ($matches as $import) {
                $file = $basedir.'/'.$import[2];
                $output = '';
                $found = false;
                // Todo: Add code to protect against "../../" strings. Should be able to trust the CSS files, but just to be safe.
                /*while (strpos($file, '../')) {
                    $file = preg_replace("#[^/]+/\.\./#", '', $file);
                }*/
                if ($import[3]) {
                    $output = '@media '.$import[3].' { ';
                }
                foreach (Jojo::listThemes($file) as $pluginfile) {
                    $output .= Jojo_Plugin_Core_Css::parseImports(file_get_contents($pluginfile), $file);
                    $found = true;
                    break;
                }
                if (!$found) {
                    foreach (Jojo::listPlugins($file) as $pluginfile) {
                        $output .= Jojo_Plugin_Core_Css::parseImports(file_get_contents($pluginfile), $file);
                        $found = true;
                        break;
                    }
                }
                if ($found && $import[3]) {
                    $output .= ' }';
                }
                if (!$found) {
                    $log             = new Jojo_Eventlog();
                    $log->code       = 'missing file';
                    $log->importance = 'high';
                    $log->shortdesc  = 'CSS Import failed for: '.$file;
                    $log->desc       = 'CSS Import failed for: '.$file;
                    $log->savetodb();
                    unset($log);
                }
                $css = str_replace($import[0], $output, $css);
            }
        }
        return $css;
    }
}
