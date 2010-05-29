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

class Jojo_Plugin_Logout extends Jojo_Plugin
{
    function _getContent()
    {
        global $smarty;

        /* Delete all cookies from the computer */
        setcookie("jojoR", "", time() - 3600, '/' . _SITEFOLDER);

        /* Delete session values */
        $userid = $_SESSION['userid'];
        unset($_SESSION['userid']);

        $_SESSION['loggingout'] = true;
        
        Jojo::runHook('after_logout', array($userid));        

        /* Redirect */
        header('Location: ' . _SITEURL);
        exit();
    }
}
