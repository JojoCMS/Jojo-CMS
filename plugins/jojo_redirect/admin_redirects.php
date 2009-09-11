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
 * @package jojo_redirect
 */

class Jojo_Plugin_Admin_Redirects extends Jojo_Plugin
{
    function _getContent()
    {
        global $smarty, $_USERGROUPS;
        $smarty->assign('title',  "Manage URL Redirects");

        $redirects = Jojo::selectQuery("SELECT * FROM {redirect} ORDER BY rd_order, rd_from");
        $smarty->assign('redirects', $redirects);

        Jojo_Plugin_Admin::adminMenu();
        $content['content'] = $smarty->fetch('admin/redirects.tpl');

        return $content;
    }
}