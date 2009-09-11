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

class Jojo_Plugin_Login extends Jojo_Plugin
{
    function _getContent()
    {
        global $smarty, $username, $password, $remember, $_USERID;
        $redirect = Jojo::getFormData('redirect', false);

        /* redirect them to 1. The location specified in GET. 2. The HTTP_REFERER if an internal URL. 3. The homepage. */
        if (!$redirect  && isset($_SERVER['HTTP_REFERER'])) {
            /* is the referer the login page? don't want an infinite loop */
            if (trim($_SERVER['HTTP_REFERER'], '/') == trim(_SITEURL.'/'._SITEURI, '/')) {
                $redirect = '';
            /* is the referer an internal URL? */
            } elseif (preg_match('%^'.str_replace('.', '\\.', _SITEURL).'/(.*)$%im', $_SERVER['HTTP_REFERER'])) {
                $redirect = preg_replace('%^'.str_replace('.', '\\.', _SITEURL).'/(.*)$%im', '$1', $_SERVER['HTTP_REFERER']);
            /* external or malformed referers */
            } else {
                $redirect = '';
            }
        } elseif (!$redirect) {
            $redirect = '';
        }

        if (!empty($_USERID)) {
            Jojo::redirect(_SITEURL.'/'.$redirect);
        }

        $content = array();
        $smarty->assign('username',        $username);
        $smarty->assign('password',        $password);
        $smarty->assign('remember',        $remember);
        $smarty->assign('redirect',        $redirect);

        $content['content'] = $smarty->fetch('login.tpl');
        return $content;
    }

    function getCorrectUrl()
    {
        $redirect = Jojo::getGet('redirect', false);
        if ($redirect) return parent::getCorrectUrl().$redirect.'/';
        return parent::getCorrectUrl();
    }
}