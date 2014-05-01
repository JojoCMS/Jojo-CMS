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
        
        if (file_exists(_BASEDIR.'/version.txt')) {
            $jojoversion = file_get_contents(_BASEDIR.'/version.txt');
            $smarty->assign('jojoversion', $jojoversion);
        }

        /* Browser detection */
        $smarty->assign('browser', Jojo::getBrowser());

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
        $adminnav = Jojo::getNav($adminroot, 3);
        $smarty->assign('jojo_admin_nav', $adminnav);
    }
}