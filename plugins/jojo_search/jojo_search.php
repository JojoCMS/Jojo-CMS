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
 * @package jojo_search
 */

class Jojo_Plugin_Jojo_search extends Jojo_Plugin
{
    function _getContent()
    {
        global $smarty, $_USERGROUPS;

        /* Remove dashs from url rewriting */
        $keywords = urldecode(str_replace('-', ' ', Jojo::getFormData('q', '')));

        /* Get Search Type */
        $searchtype = Jojo::getFormData('type', isset($_SESSION['jojo_search_type']) ? $_SESSION['jojo_search_type'] : '');
        $smarty->assign('searchtype', $searchtype);

        /* Get Search Language */
        $language = Jojo::getFormData('l', isset($_SESSION['jojo_search_language']) ? $_SESSION['jojo_search_language'] : '');
        $smarty->assign('language', $language);


        /* Setup Page content */
        $breadcrumbs = $this->_getBreadCrumbs();
        $content['title']    = 'Search';
        $content['seotitle'] = 'Site Search';

        if (strlen($keywords)) {
            /* Seperate keywords */
            $keywords       = explode(' ', trim($keywords));
            $keywords_str   = implode(' ', $keywords);
            $keywords_clean = implode('-', $keywords);
            $displaykeywords = htmlspecialchars($keywords_str, ENT_COMPAT, 'UTF-8', false);

            $booleanphrase = false;

            if ($searchtype == 'phrase') {
                $booleankeyword_str = '"' . $keywords_str . '"';
                $booleanphrase = true;
            } elseif ($searchtype == 'all') {
                $booleankeyword_str = '+' . implode(' +', $keywords);
            } else {
                $booleankeyword_str = '';
            }

            /* Add Search Results bread crumb */
            $breadcrumb = array();
            $breadcrumb['name']     = ucfirst($displaykeywords);
            $breadcrumb['rollover'] = sprintf('Search Results for "%s"', $displaykeywords);
            $breadcrumb['url']      = parent::getCorrectUrl() . htmlspecialchars($keywords_clean, ENT_COMPAT, 'UTF-8', false);
            $breadcrumbs[]          = $breadcrumb;

            /* Set page title */
            $content['title']    = $displaykeywords . ' - Search Results';
            $content['seotitle'] = sprintf('%s | Search Results', ucfirst($displaykeywords));

            /* Get results from plugins */
            $results = array();
            $results = Jojo::applyFilter('jojo_search', $results, $keywords, $language, $booleankeyword_str);
            $resulttypes = array();

            /* Convert the Body Text to a non-html snippet */
            foreach ($results as $k => $res) {
                $body = isset($res['bodyplain']) ? $res['bodyplain'] : strip_tags($res['body']);

                /* Strip any template include code ie [[ ]] */
                $body = preg_replace('/\[\[.*?\]\]/', '', $body);

                 /* Add result type if not added already */
                if (!in_array($results[$k]['type'], $resulttypes)) $resulttypes[] = $results[$k]['type'];

                /* Make keywords bold */
                $results[$k]['body'] = Jojo_Plugin_Jojo_search::search_format_content($body, $keywords_str, $booleanphrase );

                /* De-encode foreign text urls for display */
                $results[$k]['displayurl'] = !isset($results[$k]['displayurl']) ? urldecode($results[$k]['url']) : $results[$k]['displayurl'];
                $results[$k]['url'] = isset($results[$k]['absoluteurl']) ? $results[$k]['absoluteurl'] : $results[$k]['url'];

                /* Use relevance figure (x10) as a pixel width for displaying the relevance graphically */
                $results[$k]['displayrelevance'] = !empty($results[$k]['relevance']) ? ($results[$k]['relevance'] * 10 ) : '0';
            }

            /* Sort the results by relevance */
           usort($results, array('Jojo_Plugin_Jojo_Search', '_cmp_results'));
           /* Reverse the result order for highest relevance first */
           $results = array_reverse($results);

            /* Assign smarty variables */
            $smarty->assign('numresults', count($results));
            $smarty->assign('resulttypes', $resulttypes);
            $smarty->assign('keywords',   $displaykeywords);
            $smarty->assign('displaykeywords',  $displaykeywords);
            $smarty->assign('results',    $results);
        }

        if (_MULTILANGUAGE) {
            /* Get list of languages for drop down */
            $languages = array();
            foreach (Jojo::selectQuery("SELECT * FROM {language} WHERE active='yes' ORDER BY english_name") as $l) {
                $languages[$l['languageid']] = $l['name'];
            }
            $smarty->assign('languages', $languages);
        }

        /* Get page content */
        $smarty->assign('searchurl', parent::getCorrectUrl());
        $content['breadcrumbs'] = $breadcrumbs;
        $content['content']     = $smarty->fetch('jojo_search.tpl');

        return $content;
    }

    function _cmp_results($a, $b)
    {
        if ($a['relevance'] == $b['relevance']) {
            return 0;
        }
        return ($a['relevance'] < $b['relevance']) ? -1 : 1;

    }

    function getCorrectUrl()
    {
        global $page;
        $pagelanguage = Jojo::getMultiLanguageString( $page->page['pg_language'], false );

        /* Include any get variables in request_uri, this allows for rewrites */
        $t = strstr($_SERVER['REQUEST_URI'], '?');
        parse_str(substr($t, 1), $get);
        $_GET = array_merge($_GET, $get);

        $searchtype = Jojo::getFormData('type', isset($_SESSION['jojo_search_type']) ? $_SESSION['jojo_search_type'] : '');
        $_SESSION['jojo_search_type'] = $searchtype;

        $l = Jojo::getFormData('l', isset($_SESSION['jojo_search_language']) ? $_SESSION['jojo_search_language'] : '');
        $_SESSION['jojo_search_language'] = $l;

        $q = Jojo::getFormData('q');
        if ($q) {

            if (Jojo::getOption('search_urlquery', 'url')=='query' || !preg_match('/^([a-zA-Z0-9 -]*)$/', $q)) {
                return _SITEURL . '/' . $pagelanguage . 'search/?q=' . urlencode($q);
            }


            /* Remove dashs from url rewriting */
            $keywords = str_replace('-', ' ', $q);

            /* Separate keywords */
            $keywords = explode(' ', $keywords);

            $correcturl =  $pagelanguage . 'search/' . implode('-', $keywords) . '/';
            if ($correcturl) {
                return _SITEURL . '/' . $correcturl;
                exit;
            }

            return parent::getCorrectUrl();
        }

        return parent::getCorrectUrl();
    }

    /*-
     * Generic search function for plugins to call rather than repeating all the boolean logic themselves
     * Returns a raw array of language limited ids, tags (if used) and relevance rankings.
     * Display data for the results (title etc) and exclusions (expired items etc) to be handled by the plugin.
     */
    static function searchPlugin($searchfields, $keywords, $language, $booleankeyword_str=false)
    {
        $table = $searchfields['table'];
        $idfield = $searchfields['idfield'];
        $primaryfields = $searchfields['primaryfields'];
        $secondaryfields = $searchfields['secondaryfields'];
        $fieldarray = explode(', ', $secondaryfields);

        $_TAGS = (boolean)(class_exists('Jojo_Plugin_Jojo_Tags') && isset($searchfields['plugin']));
        if ($_TAGS) {
            $plugin = $searchfields['plugin'];
        }
        if ($language) {
            $languagefield = isset($searchfields['languagefield']) ? $searchfields['languagefield'] : '';
        }


        $boolean = ($booleankeyword_str) ? true : false;
        $keywords_str = ($boolean) ? $booleankeyword_str :  implode(' ', $keywords);
        if ($boolean && stripos($booleankeyword_str, '+') === 0  ) {
            $like = '1';
            foreach ($keywords as $keyword) {
                foreach ($fieldarray as $k => $f) {
                    if ($k == 0) {
                        $like .= sprintf(" AND (%s LIKE '%%%s%%'", $f, Jojo::clean($keyword));
                    } else {
                        $like .= sprintf(" OR %s LIKE '%%%s%%'", $f, Jojo::clean($keyword));
                    }
                }
                $like .= ') ';
            }
        } elseif ($boolean && stripos($booleankeyword_str, '"') === 0) {
            foreach ($fieldarray as $k => $f) {
                if ($k == 0) {
                    $like = sprintf("(%s LIKE '%%%s%%'", $f, implode(' ', $keywords));
                } else {
                    $like .= sprintf(" OR %s LIKE '%%%s%%'", $f, implode(' ', $keywords));
                }
            }
            $like .= ') ';
        } else {
            $like = '(0';
            foreach ($keywords as $keyword) {
                foreach ($fieldarray as $k => $f) {
                    if ($k == 0) {
                        $like .= sprintf(" OR %s LIKE '%%%s%%'", $f, Jojo::clean($keyword));
                    } else {
                        $like .= sprintf(" OR %s LIKE '%%%s%%'", $f, Jojo::clean($keyword));
                    }
                }
            }
            $like .= ')';
        }
        $tagid = ($_TAGS) ? Jojo_Plugin_Jojo_Tags::_getTagId(implode(' ', $keywords)): '';

        $query = "SELECT `$idfield` AS id, `$idfield`, ((MATCH($primaryfields) AGAINST (?" . ($boolean ? ' IN BOOLEAN MODE' : '') . ") * 0.2) + MATCH($secondaryfields) AGAINST (?" . ($boolean ? ' IN BOOLEAN MODE' : '') . ")) AS relevance";
        $query .= " FROM {$table} ";
        $query .= $tagid ? " LEFT JOIN {tag_item} AS tag ON (tag.itemid = $idfield AND tag.plugin='$plugin' AND tag.tagid = $tagid)" : '';
        $query .= " WHERE ($like";
        $query .= $tagid ? " OR (tag.itemid = $idfield AND tag.plugin='$plugin' AND tag.tagid = $tagid))" : ')';
        $query .= ($language && $languagefield) ? " AND `$languagefield` = '$language' " : '';
        $query .= " ORDER BY relevance DESC LIMIT 50";
        $rawresults = Jojo::selectAssoc($query, array($keywords_str, $keywords_str));
        if ($_TAGS) {
            foreach ($rawresults as $k => $r) {
                $rawresults[$k]['tags'] = Jojo_Plugin_Jojo_Tags::getTags($plugin, $k);
                if ($rawresults[$k]['tags']) {
                    foreach ($rawresults[$k]['tags'] as $t) {
                        if (strpos($t['cleanword'], $keywords_str) !== false) $rawresults[$k]['relevance'] = $r['relevance'] + 1;
                    }
                }
            }
        }

        return $rawresults;
    }

    /*-
     * Copyright (c) 2005-2006 Vladimir Fedorkov (http://astellar.com/)
     * All rights reserved.
     *
     * Redistribution and use in source and binary forms, with or without
     * modification, are permitted provided that the following conditions
     * are met:
     * 1. Redistributions of source code must retain the above copyright
     *    notice, this list of conditions and the following disclaimer.
     * 2. Redistributions in binary form must reproduce the above copyright
     *    notice, this list of conditions and the following disclaimer in the
     *    documentation and/or other materials provided with the distribution.
     *
     * THIS SOFTWARE IS PROVIDED BY THE AUTHOR AND CONTRIBUTORS ``AS IS'' AND
     * ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
     * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
     * ARE DISCLAIMED.  IN NO EVENT SHALL THE AUTHOR OR CONTRIBUTORS BE LIABLE
     * FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL
     * DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS
     * OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION)
     * HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
     * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY
     * OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF
     * SUCH DAMAGE.
     */
    function search_format_content($content, $q, $booleanphrase=false)
    {
        $CRAWL_SEARCH_TEXT_SURROUNDING_LENGHT = 50;
        $CRAWL_SEARCH_MAX_RES_WORD_COUNT = 10;

        $CRAWL_SEARCH_STRICT_RESULTS = false;

        // we shall use smaller alias ;-)
        $SL = $CRAWL_SEARCH_TEXT_SURROUNDING_LENGHT;

        if (empty($SL)) die("Empty CRAWL_SEARCH_TEXT_SURROUNDING_LENGHT");

        // remove some spaces from content
        $content = preg_replace("/(&nbsp;)+/i", " ", $content);
        $content = preg_replace("/\s[\s]+/ims", " ", $content);

        // === Creating chunks
        $chunks = array();
        $chunk_counter = 0;
        $q = str_replace("#", "", $q);
        $words = preg_split ("#\s+#i", $q);
        $ignore = array('with','for','this','the','from','are','can');
        foreach ($words as $dummy_id => $word)
        {
             if (empty($word)) continue;
             if (!$booleanphrase && (strlen($word) < 3 || in_array($word, $ignore))) continue;
             if ($booleanphrase) $word = $q;
             $word_counter = 0;
             if ($CRAWL_SEARCH_STRICT_RESULTS)
             {
                 /* Uncomment this to speed-up search
                     $found = preg_match_all("/\s+" . $word . "\s+(.{0," . $SL . "})/ims", $content, $matches, PREG_SET_ORDER);
                 */
                 $found = preg_match_all("#(.{0," . $SL . "})\s+" .  preg_quote($word) . "\s+(.{0," . $SL . "})#ims", $content, $matches, PREG_SET_ORDER);
             } else {
                 /* Uncomment this to speed-up search
                     $found = preg_match_all("/" . $word . "(.{0," . $SL . "})/ims", $content, $matches, PREG_SET_ORDER);
                 */
                 $found = preg_match_all("#(.{0," . $SL . "})" . preg_quote($word) . "(.{0," . $SL . "})#ims", $content, $matches, PREG_SET_ORDER);
             }
             if ($found == 0 || $found === false) continue;
             foreach($matches as $dummy => $match)
             {
                $chunks[$chunk_counter] = $match[0];
                $chunk_counter++;
                $word_counter++;
                if ($word_counter >= $CRAWL_SEARCH_MAX_RES_WORD_COUNT) break;
             }
             if ($booleanphrase) break;
        }

        // if no matches found
        if (count($chunks) == 0)
        {
            return substr($content, 0, 200);
        }

        // setting up positions
        $positions = array();
        $chunk_counter = 0;
        foreach ($chunks as $dummy_id => $chunk)
        {
             if (empty($word)) continue;
             $chunk_pos = strpos($content, $chunk);
             //$chunk_pos = strpos($content, $word, 0);
             //$word_pos = preg_match("/{$word}/ims", $content, $matches);
             if ($chunk_pos === false) continue;
             $positions[$chunk] = $chunk_pos;
        }
        asort($positions, SORT_NUMERIC);

        //computing text marks
        $marks = array();
        $chunk_counter = 0;
        $last_chunk_end = 0;
        $content_len = strlen($content);
        foreach($positions as $chunk => $text_pos)
        {

            $chunk_len = strlen($chunk);
            if ($chunk_len < 4) continue;
            if ($text_pos === false) continue;

            // *** check chunks overlapping
            if(($text_pos) < $last_chunk_end)
            {
                $marks[$chunk_counter]["end"] = (($text_pos + $chunk_len) > $content_len) ? $content_len : $text_pos + $chunk_len;
            } else {
                $marks[$chunk_counter]["from"] = (($text_pos) < 0) ? 0 : $text_pos;
                $marks[$chunk_counter]["end"] = (($text_pos + $chunk_len) > $content_len) ? $content_len : $text_pos + $chunk_len;
                $chunk_counter++;
            }

        }

        // *** making content
        $shown_result = "";
        foreach($marks as $chuck_id => $mark)
        {
            //var_dump($mark); die("stop");
            $text_chunk  = substr ( $content, $mark["from"], $mark["end"] - $mark["from"]);
            $text_chunk  = preg_replace("#^[^\s]*\s#i", "", $text_chunk);
            $text_chunk  = preg_replace("#\s[\S]*$#is", "", $text_chunk);
            $shown_result .= "..." . $text_chunk . "...  ";
        }

        foreach ($words as $dummy_id => $word)
        {
            if (!$booleanphrase && (strlen($word) < 4 || in_array($word, $ignore))) continue;
            if ($CRAWL_SEARCH_STRICT_RESULTS)
            {
                $shown_result = preg_replace ("#\s+" . preg_quote($word) . "\s+#ims", "<b>\\0</b>", $shown_result);
            } else {
                $shown_result = preg_replace ("#" . preg_quote($word) . "#ims", "<b>\\0</b>", $shown_result);
            }
        }
        return $shown_result;

    }
}

