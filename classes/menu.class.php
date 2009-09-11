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

class menu
{
    var $page;
    var $pageid;
    var $pg_parent;
    var $pg_title;
    var $pg_menutitle;
    var $pg_link;
    var $pg_description;
    var $pg_order;
    var $pg_mainnav;
    var $pg_access;
    var $pg_followto;
    var $menu;
    var $data;
    var $indent;
    var $html;
    var $css;
    var $js;
    var $rewrite;
    var $domid = '';
    var $maxdepth = 5;

    var $useindex = false;

    function menu()
    {
        $this->pg_mainnav = "pg_mainnav";
        $this->menu = array();
        $this->data = array();
        $this->indent = "";
        $this->html = "";
        $this->activepageid = "";
        $this->css = "";
        $this->js = "";
        $this->rewrite = true;
    }

    /*
     * Reads all pages from database into array keyed by pageid
     */
    function populate()
    {
        global $_USERGROUPS;

        $followto = Jojo::fieldExists('page', 'pg_followto') ? ' , pg_followto ' : ''; //prevent Jojo from breaking if setup hasn't been run recently
        if ( Jojo::fieldExists ( 'page', 'pg_mainnavalways' )) {
            $query = sprintf("SELECT
                                  pageid, pg_parent, pg_title, pg_menutitle,
                                  pg_link, pg_desc, pg_order, pg_ssl,
                                  pg_url, pg_language, %s $followto , pg_mainnavalways
                              FROM
                                  {page}
                              WHERE
                                  ( %s = 'yes' OR pg_mainnavalways = 'yes' )
                                AND
                                  pg_livedate < ?
                                AND
                                 (pg_expirydate = 0 OR pg_expirydate> ?)
                              ORDER BY
                                pg_order",
                            $this->pg_mainnav,
                            $this->pg_mainnav
                            );
        } else {
            $query = sprintf("SELECT
                                  pageid, pg_parent, pg_title, pg_menutitle,
                                  pg_link, pg_desc, pg_order, pg_ssl,
                                  pg_url, pg_language, %s $followto
                              FROM
                                  {page}
                              WHERE
                                  %s = 'yes'
                                AND
                                  pg_livedate < ?
                                AND
                                 (pg_expirydate = 0 OR pg_expirydate> ?)
                              ORDER BY
                                pg_order",
                            $this->pg_mainnav,
                            $this->pg_mainnav
                            );
        }

        $values = array(time(), time());
        $rows = Jojo::selectQuery($query, $values);
        $perms = new Jojo_Permissions();

        foreach ($rows as $row) {
            $perms->getPermissions('page', $row['pageid']);
            if ($perms->hasPerm($_USERGROUPS, 'show')) {
                $this->data[$row['pageid']] = $row;
                if (!isset($this->{'data' . $row['pg_parent']})) {
                    $this->{'data' . $row['pg_parent']} = array();
                }
                if (empty($row['pg_followto'])) $row['pg_followto'] = 'yes';
                $this->{'data' . $row['pg_parent']}[] = $row;
            }
        }
    }

    /*
     * Function to create an unordered list from data arrays. Will call itself recursively for subitems
     */
    function additem($pageid, $parentid, $index=0, $depth = 0, $firstitem = false, $lastitem = false)
    {
        if ($depth > 0) {
            $class_begin = '';
            $class_end = '';
            $a_class = array();
            $li_class = array();
            $h2_begin = '';
            $h2_end = '';
            $onclick = '';

            if (isset($this->{'data' . $pageid})) {
                $a_class[] = 'daddy';
            }

            /* First and last items may need to be handled differently with the css */
            if ($firstitem) {
                $li_class[] = 'first';
            }
            if ($lastitem) {
                $li_class[] = 'last';
            }


            /* Set class if it's a top level item */

            /* Set class if something??? */
            if (in_array('daddy', $a_class) && in_array('level1', $a_class) ) {
                $a_class[] = 'daddylevel1';
            }

            if ($this->useindex) {
                $a_class[]  = 'index' . $index;
                $li_class[] = 'index' . $index;
            }

            /* Isactive is now defunct - style the menu so that it uses class="selected" instead for the selected item. Thius fits better with the ajax model */
            if ($this->activepageid == $pageid) {
              $li_class[] = 'selected';
            }

            /* Set li tags */
            $liclasscode = count($li_class) > 0 ? ' class="'.implode(' ', $li_class) . '"' : '';
            $aclasscode  = count($a_class)  > 0 ? ' class="'.implode(' ', $a_class)  . '"' : '';

            /* Create the link */
            $link =  Jojo::urlPrefix( Jojo::yes2true($this->data[$pageid]['pg_ssl']));
            if (substr(strtolower($this->data[$pageid]['pg_link']), 0, 7) == 'http://') {
                $link .= $this->data[$pageid]['pg_link'];
            } elseif (substr(strtolower($this->data[$pageid]['pg_link']), 0, 8) == 'https://') {
                $link .= $this->data[$pageid]['pg_link'];
            } elseif ($this->data[$pageid]['pg_url']) {
                $link .= $this->data[$pageid]['pg_url'] . '/';
            } else {
                $link .=  Jojo::rewrite('page', $pageid, $this->data[$pageid]['pg_title']);
            }

            /* Add the language code if multilanguage */
            if (_MULTILANGUAGE) {
                $link = Jojo::getMultiLanguageCode($this->data[$pageid]['pg_language']) . $link;
            }


            /* Are we on the homepage? */
            $expectedurl = $link;
            if (_MULTILANGUAGE) {
                $mldata = Jojo::getMultiLanguageData();
                if ($mldata['homes'][$this->data[$pageid]['pg_language']] == $pageid) {
                    /* Are on the homepage */
                    $link = Jojo::getMultiLanguageCode($this->data[$pageid]['pg_language']);
                }
            }

            /* Are we on the root page */
            if ($this->data[$pageid]['pg_link'] == 'index.php' || $pageid == 1) {
                /* Are on the homepage */
                $link = _SITEURL.'/';
            }

            /* Is this a secure page? */
            if ( Jojo::yes2true($this->data[$pageid]['pg_ssl'])) {
                //$link = _SECUREURL . $link;
                //$link = _SECUREURL .'/'. $link;
                $link = $link;
            } else {
                //$link = _SITEURL . $link;
                $link = $link;
            }

            $nofollow = $this->data[$pageid]['pg_followto'] == 'no' ? ' rel="nofollow"' : '';

            // create the li and the link
            $this->html .= sprintf('%s<li%s>%s<a%s href="%s" title="%s"%s%s>%s%s%s</a>%s',
                                $this->indent,
                                $liclasscode,
                                $class_begin,
                                $aclasscode,
                                $link,
                                htmlentities( Jojo::either($this->data[$pageid]['pg_desc'],$this->data[$pageid]['pg_title'])),
                                $onclick,
                                $nofollow,
                                $h2_begin,
                                htmlentities( Jojo::either($this->data[$pageid]['pg_menutitle'],$this->data[$pageid]['pg_title'])),
                                $h2_end,
                                $class_end
                            );
        }

        if (isset($this->{'data' . $pageid}) && ($depth < $this->maxdepth)) {
            if ($depth > 0) {
                $this->indent .= '  '; //increase indent
                $ul_class = array();
                if ($this->useindex) {
                    $ul_class[] = 'index' . $index;
                }
                $ulclasscode = count($ul_class) > 0 ? ' class="'.implode(' ', $ul_class) . '"' : '';
                $this->html .= "\n".$this->indent.'<ul'.$ulclasscode.">\n";
            }
            $this->indent .= '    '; //increase indent
            $i = $index;
            /*
                $this->additem($v["pageid"],$pageid, $i);
                $i++;
            }
            */
            $n = count($this->{'data' . $pageid});
            for ($j=0;$j<$n;$j++) {
                $f = ($j == 0) ? true : false;
                $l = (($j+1) == count($this->{'data' . $pageid})) ? true : false;
                $this->additem($this->{'data' . $pageid}[$j]["pageid"],$pageid, $i, $depth + 1, $f, $l);
                $i++;
            }


            $this->indent = substr($this->indent, 0, strlen($this->indent) -4); //reduce indent
            if ($depth > 0) {
                $this->html .= $this->indent."</ul>\n";
                $this->indent = substr($this->indent, 0, strlen($this->indent) -4); //reduce indent
            }
        }

        if ($depth > 0) {
            if (isset(${'data' . $pageid})) {$this->html .= $this->indent;}
            $this->html .= "</li>\n";
        }
    }

    /* Displays the menu code */
    function display($parent = 0)
    {
        $this->html .= !empty($this->domid) ? "<ul id=\"".$this->domid."\">\n" : "<ul>\n";
        $this->additem($parent, $parent);
        $this->html .= "</ul>\n";
        return $this->html;
    }
}
