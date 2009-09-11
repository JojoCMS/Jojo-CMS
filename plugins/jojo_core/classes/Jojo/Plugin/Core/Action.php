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

class Jojo_Plugin_Core_Action extends Jojo_Plugin_Core {

    /**
     * Output the Frajax response
     */
    function __construct()
    {
        global $smarty, $_USERGROUPS, $_USERID;

        /* Get the filename of the action */
        $temp = explode('?', _SITEURI);
        $filename = basename($temp[0]);

        /* Make the get variables available */
        if (isset($temp[1])) {
            parse_str($temp[1], $res);
            foreach ($res as $k => $v) {
                $_GET[$k] = $v;
            }
        }

        require_once(_BASEPLUGINDIR . '/jojo_core/external/frajax/frajax.class.php');

        /* Include the action */
        foreach (Jojo::listPlugins('actions/' . $filename) as $pluginfile) {
            include($pluginfile);
        }
        exit;
   }
}