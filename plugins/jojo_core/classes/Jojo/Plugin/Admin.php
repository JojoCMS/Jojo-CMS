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

class Jojo_Plugin_Admin extends Jojo_Plugin
{

    function _getContent()
    {
        global $smarty;

        $content = array();
        /* Top level pages */
        $toppages = Jojo::selectQuery("SELECT * FROM {page} WHERE pg_parent = 0 AND pg_mainnav='yes' ORDER BY pg_order, pg_title LIMIT 10");
        for ($i=0;$i<count($toppages);$i++) {
            $toppages[$i]['url'] =  Jojo::urlPrefix(false). Jojo::either( Jojo::onlyIf($toppages[$i]['pg_url'],$toppages[$i]['pg_url'].'/'), Jojo::rewrite('pages',$toppages[$i]['pageid'],$toppages[$i]['pg_title'],''));
        }
        $smarty->assign('toppages',$toppages);

        /* Articles */
        if (Jojo::tableexists('article')) {
            $articles = Jojo::selectQuery("SELECT * FROM {article} ORDER BY ar_date DESC LIMIT 5");
            for ($i = 0; $i < count($articles); $i++) {
                $articles[$i]['ar_bodyplain'] = Jojo::html2text($articles[$i]['ar_body']);
                $articles[$i]['ar_datefriendly'] = Jojo::mysql2date($articles[$i]['ar_date'],"medium");
                $articles[$i]['url'] =  Jojo::urlPrefix(false) . Jojo::rewrite('articles', $articles[$i]['articleid'], $articles[$i]['ar_title'],'');
            }
            $smarty->assign('articles', $articles);
        }

        /* Article Comments */
        if (defined('_ARTICLECOMMENTS') && _ARTICLECOMMENTS && Jojo::tableExists('articlecomment')) {
            $articlecomments = Jojo::selectQuery("SELECT * FROM {articlecomment} ORDER BY ac_timestamp DESC LIMIT 10");
            $smarty->assign('articlecommentsenabled', _ARTICLECOMMENTS);
            $smarty->assign('articlecomments', $articlecomments);
        }

        if (file_exists(_BASEDIR.'/version.txt')) {
            $jojoversion = file_get_contents(_BASEDIR.'/version.txt');
            $smarty->assign('jojoversion', $jojoversion);
        }

        /* Browser detection */
        $smarty->assign('browser', Browser::singleton());

        Jojo_Plugin_Admin::adminMenu();
        $content['content'] = $smarty->fetch('admin/admin.tpl');

        return $content;
    }

    public static function adminMenu()
    {
        global $isadmin, $smarty, $page;
        $smarty->assign('isadmin', true);
        $isadmin = true;

        /* Find ID of admin root */
        $data = Jojo::selectRow("SELECT pageid FROM {page} WHERE pg_link='Jojo_Plugin_Admin_Root'");
        $adminroot = $data['pageid'];
        $adminnav = _getNav($adminroot, 3);
        $smarty->assign('jojo_admin_nav', $adminnav);
    }
}