<?php
/**
 *                    Jojo CMS
 *                ================
 *
 * Copyright 2007-2008 Harvey Kane <code@ragepank.com>
 * Copyright 2007-2008 Michael Holt <code@gardyneholt.co.nz>
 * Copyright 2007 Melanie Schulz <mel@gardyneholt.co.nz>
 *
 * See the enclosed file license.txt for license information (LGPL). If you
 * did not receive this file, see http://www.fsf.org/copyleft/lgpl.html.
 *
 * @author  Harvey Kane <code@ragepank.com>
 * @author  Michael Cochrane <mikec@jojocms.org>
 * @author  Melanie Schulz <mel@gardyneholt.co.nz>
 * @license http://www.fsf.org/copyleft/lgpl.html GNU Lesser General Public License
 * @link    http://www.jojocms.org JojoCMS
 * @package jojo_core
 */

class Jojo_Stitcher {
    var $data;
    var $modified;
    var $dirty = true;
    var $type = 'css'; //css or javascript
    var $numfiles = 0;
    var $header = '';

    function Jojo_Stitcher($modified = 0)
    {
        $this->modified = $modified;
        if (!defined('_PROTOCOL')) {
            define('_PROTOCOL', 'http://');
        }
    }

    function addFile($file)
    {
        static $_added;
        if (!file_exists($file)) {
            return false;
        }
        if (isset($_added[$file])) {
            return false;
        }

        $handle = fopen($file, "r");
        $data = filesize($file) > 0 ? fread($handle, filesize($file)) : '';
        fclose($handle);
        $this->data .= $data . "\n";
        $this->numfiles++;
        $this->dirty = true;
        $_added[$file] = true;

        /* Set the modified to this file if it's the most recent */
        $this->modified = max($this->modified, filemtime($file));
        return true;
    }

    function addText($text)
    {
        $this->data .= $text . "\n";
        $this->numfiles++;
        $this->dirty = true;
    }

    function getServerCache()
    {
        if (_CONTENTCACHE && !isset($_SERVER['HTTP_PRAGMA'])) {
            //$cacheuserid = isset($_USERID) ? $_USERID : 0;
            $cacheuserid = 0;

            $query = 'SELECT * FROM {contentcache} WHERE cc_url = ? AND cc_userid = ? AND cc_expires > ? LIMIT 1';
            $values = array(
                        _PROTOCOL . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'],
                        $cacheuserid,
                        strtotime('now')
                      );

            $contentcache = Jojo::selectQuery($query, $values);

            if (count($contentcache) == 1) {
                $this->header .= '/** [Page generated '.date('d M y h:i:sa') . ' based on copy cached '.date('d M h:i:s',$contentcache[0]['cc_cached']) . '. This copy expires '.date('d M h:i',$contentcache[0]['cc_expires']) . '] **/'."\n";
                $modified = $contentcache[0]['cc_cached'];

                $this->addText($contentcache[0]['cc_content']);
                $this->output(false);
                exit;
            }
        }
    }

    function setServerCache()
    {
        //global $_USERID;
        if (_CONTENTCACHE) {
            $this->optimize();
            //$cacheuserid = isset($_USERID) ? $_USERID : 0;
            $cacheuserid = 0;

            $query = 'REPLACE INTO {contentcache} SET cc_url = ?, cc_userid = ?, cc_content = ?, cc_cached = ?, cc_expires = ?';
            $values = array(
                        _PROTOCOL . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'],
                        $cacheuserid,
                        $this->data,
                        strtotime('now'),
                        strtotime('+'._CONTENTCACHETIME . ' second')
                      );
            Jojo::updateQuery($query, $values);
        }

        if (_CONTENTCACHE) {
            $this->header .= '/** [Code generated '.date('d M y h:i:sa',strtotime('now')) . ' and cached until '.date('d M y h:ia',strtotime('+'._CONTENTCACHETIME . ' second')) . '] **/'."\n";
        } else {
            $this->header .= '/** [Code generated '.date('d M y h:i:sa',strtotime('now')) . '] **/'."\n";
        }
    }

    function optimize()
    {
        if ($this->dirty && $this->type == 'css') {
            $this->data = $this->optimizeCSS($this->data);
        } elseif ($this->dirty && $this->type == 'javascript') {
            $this->data = $this->optimizeJS($this->data);
        }
        $this->dirty = false; //we can set this because we have just optimised the code
    }

    function output($optimize = true)
    {
        header('Content-type: text/' . $this->type);
        $this->sendCacheHeaders($this->modified);

        if ($optimize) {
            $this->optimize();
        }

        if (Jojo::getOption('enablegzip') == 1) {
            Jojo::gzip();
        }

        header('Cache-Control: ');
        header('Pragma: ');
        header('Expires: ');

        if (_DEBUG) {
            echo $this->header . $this->data;
            return;
        }
        echo $this->data;
    }

    function fetch()
    {
        $this->optimize();
        if (_DEBUG) {
            return $this->header . $this->data;
        }
        return $this->data;
    }

    /* TODO - a simple php4 JS optimisation function, which removes extra characters */
    function optimizeJS($js)
    {
        $original = $js;

        /* JSMin will only work on PHP5+ */
        require_once(_BASEPLUGINDIR . '/jojo_core/external/jsmin/jsmin.php');
        try {
            $js = JSMin::minify($js);
        } catch (Exception $e) { }

        $savings = strlen($original) - strlen($js);
        if ($savings != 0) {
            $this->header .= "/* optimization saves $savings bytes & " . ($this->numfiles -  1) . " HTTP requests */\n";
        }
        return $js;
    }

    function optimizeCSS($css)
    {
        $timer = Jojo::timer();
        $original = $css;
        $css = Jojo_Plugin_Core_Css::parseImports($css);
        
        /* if option is set preprocess less css */
        if (Jojo::getOption('less', 'no') == 'yes') {
            foreach (Jojo::listPlugins('external/lessphp/lessc.inc.php') as $pluginfile) {
                require_once($pluginfile);
                break;
            }
            $lc = new lessc();
            $css = $lc->parse($css);
        }

        require_once(_BASEPLUGINDIR . '/jojo_core/external/csstidy/class.csstidy.php');
        $csstidy = new csstidy();
        $csstidy->load_template('highest_compression');
        if (_DEBUG) {
            $csstidy->load_template('default');
        }
        $csstidy->parse($css);
        $optimized = $csstidy->print->plain();

        $savings = strlen($css) - strlen($optimized);
        $this->header .= "/* Optimization saves $savings bytes & ".($this->numfiles -  1) . " HTTP requests\n";
        $this->header .= sprintf("   Optimization took: %0.2f ms */\n", Jojo::timer($timer) * 1000);

        return $optimized;
    }

    function sendCacheHeaders($timestamp)
    {
        /* Send last modified headers */
        $timestamp = $timestamp ? $timestamp : strtotime('00:00');
        $last_modified = gmdate('D, d M Y H:i:s', $timestamp) . ' GMT';
        $etag = '"' . md5($last_modified) . '"';
        header("Last-Modified: $last_modified");
        header("ETag: $etag");
        header('X-Jojo-Plugin: Jojo_Stitcher');

        if (!isset($_SERVER['HTTP_IF_NONE_MATCH']) && !isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])) {
            /* Client doesn't indicate it has a chached copy */
            return;
        }

        if (isset($_SERVER['HTTP_IF_NONE_MATCH']) && $_SERVER['HTTP_IF_NONE_MATCH'] != $etag) {
            /* Client sent and etag but different to the current one */
            return;
        }

        if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) && $_SERVER['HTTP_IF_MODIFIED_SINCE'] != $last_modified) {
            /* Client sent a date but it's different to our current one */
            return;
        }

        /* Nothing has changed since their last request - serve a 304 and exit */
        header('Expires: ' . gmdate('D, d M Y H:i:s \G\M\T', time() + _CONTENTCACHETIME));
        header('Cache-Control: private, max-age=' . _CONTENTCACHETIME);
        header('HTTP/1.0 304 Not Modified');
        exit;
    }
}
