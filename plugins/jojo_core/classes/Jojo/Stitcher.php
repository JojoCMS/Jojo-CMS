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


    /* a simple CSS optimisation function, which removes extra characters from CSS */
    function optimizeCSS($css)
    {
        $timer = Jojo::timer();
        $original = $css;

        /* Remove commented out blocks of code */
        $css = preg_replace('#/\*(.*)\*/#Ums', '', $css);

        preg_match_all('/([\r\n\s\S]*)\{([\r\n\s\S]*)}/Um', $css, $matches);

        $temp = array();
        $css = array();
        foreach ($matches[1] as $k => $class) {
            $class = "\n" . rtrim(str_replace(array("\n", "\r"), '', $class));
            $class = substr($class, strrpos($class, "\n") + 1);
            $attribs = trim(str_replace("\r\n", "\n", $matches[2][$k])) . ';';
            $cleanAttribs = '';

            /* Clean attribs*/
            //preg_match_all('/([a-z \-]*):(.*);/mU', $attribs, $attribMatches);
            preg_match_all('/([a-z \-]*):(.*)(?<!data:image\/png|data:image\/gif|data:image\/jpg|data:image\/jpeg);/mU', $attribs, $attribMatches); //fix for uri data
            $attribs = '';

            foreach ($attribMatches[1] as $j => $attribName) {
                /* Replace strings that might only occure in attributes */
                $value = trim(str_replace(array(' 0px', '.0em'), array(' 0', 'em'), ' ' . $attribMatches[2][$j]));

                /* url('foo.jpg') => url(foo.jpg) */
                $value = preg_replace('/url\\(\'(.+?)\'\\)/i', 'url(\\1)', $value);

                $attribs .= sprintf("%s:%s;\n", trim($attribName), trim($value));
            }
            $attribs = trim($attribs);

            /* Check for a class already being declared */
            if (isset($temp[$class])) {
                /* Check for overwritten attributes */
                foreach ($temp[$class] as $tk => $tattribs) {
                    if ($tattribs == $attribs) {
                        /* Second declaration same as first, removing first */
                        unset($css[$temp[$class][$tk]['pos']]);
                        unset($temp[$class][$tk]);
                    } else {
                        /* Extract attributes from first declaration */
                        preg_match_all('/([a-z \-]*):(.*);/mU', $temp[$class][$tk]['attribs'], $attribMatches);
                        $firstAttribs = array();
                        foreach ($attribMatches[1] as $j => $attribName) {
                            $firstAttribs[trim($attribName)] = $attribMatches[2][$j];
                        }

                        /* Remove over-written attribs from first declaration
                           and clean attribs in the same loop */
                        $cleanAttribs = '';
                        preg_match_all('/([a-z \-]*):(.*);/mU', $attribs, $attribMatches);
                        foreach ($attribMatches[1] as $j => $attribName) {
                            /* Replace strings that might only occure in attributes */
                            $value = trim(str_replace(array(' 0px', '.0em'), array(' 0', 'em'), ' ' . $attribMatches[2][$j]));

                            /* Trim whitespace */
                            $attribName = trim($attribName);

                            /* url('foo.jpg') => url(foo.jpg) */
                            $value = preg_replace('/url\\(\'(.+?)\'\\)/i', 'url(\\1)', $value);

                            $cleanAttribs .= sprintf("%s:%s;\n", $attribName, $value);

                            if (isset($firstAttribs[$attribName])) {
                                unset($firstAttribs[$attribName]);
                            }
                        }
                        $attribs = trim($cleanAttribs);

                        /* Save remaining attributes in first delcaration*/
                        if (count($firstAttribs) == 0) {
                            /* No attributes left, delete */
                            unset($css[$temp[$class][$tk]['pos']]);
                            unset($temp[$class][$tk]);
                        } else {
                            $newAttribs = '';
                            foreach ($firstAttribs as $name => $value) {
                                $newAttribs .= sprintf("%s:%s;\n", $name, $value);
                            }
                            $newAttribs = trim($newAttribs);
                            $css[$temp[$class][$tk]['pos']] =  array('class' => $class, 'attribs' => $newAttribs);
                            $temp[$class][$tk]['attribs'] = $newAttribs;
                        }
                    }
                }
            }

            if ($attribs) {
                $css[] = array('class' => $class, 'attribs' => $attribs);
                $temp[$class][] = array('pos' => max(array_keys($css)), 'attribs' => $attribs);
            }
        }

        $optimizedText = '';
        foreach($css as $c) {
            if (_DEBUG) {
                $optimizedText .= sprintf("%s{\n%s\n}\n", $c['class'], $c['attribs']);
            } else {
                $optimizedText .= sprintf("%s{%s}", $c['class'], str_replace("\n", '', $c['attribs']));
            }
        }

        /* use shorthand colour codes eg #ffffff => #fff */
        $optimizedText = preg_replace('/#(0{3}|1{3}|2{3}|3{3}|4{3}|5{3}|6{3}|7{3}|8{3}|9{3}|a{3}|b{3}|c{3}|d{3}|e{3}|f{3}){2}/i', '#\\1', $optimizedText);

        $search = array(
                    ', ',                  /* ', ' => ', ' (remove spaces after commas) */
                    'font-weight:bold;' ,  /* font-weight: bold; => font-weight: 700; */
                    'font-weight:normal;', /* font-weight: normal; => font-weight: 400; */
                    );

        $replace = array(
                    ',',
                    'font-weight:700;',
                    'font-weight:400;'
                    );
        $optimizedText = str_replace($search, $replace, $optimizedText);

        $savings = strlen($original) - strlen($optimizedText);
        $this->header .= "/* Optimization saves $savings bytes & ".($this->numfiles -  1) . " HTTP requests\n";
        $this->header .= sprintf("   Optimization took: %0.2f ms */\n", Jojo::timer($timer) * 1000);

        return $optimizedText;
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
        header('Expires: ' . gmdate('D, d M Y H:i:s \G\M\T', $timestamp + _CONTENTCACHETIME));
        header('Cache-Control: private, max-age=' . _CONTENTCACHETIME);
        header('HTTP/1.0 304 Not Modified');
        exit;
    }
}