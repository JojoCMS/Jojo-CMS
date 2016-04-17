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

class Jojo_Plugin_Admin_Eventlog extends Jojo_Plugin
{
    function _getContent()
    {
        global $smarty, $_USERGROUPS;
        Jojo_Plugin_Admin::adminMenu();

        $smarty->assign('title',  "Event Log");

        if (isset($_POST['submit'])) {
            Jojo::noFormInjection();
            if (isset($_POST['logdelete'])) {
                switch ($_POST['logdelete']) {
                    case '404':
                        $where = " WHERE `el_code` = '404'";
                        break;
                    case 'php':
                        $where = " WHERE `el_code` = 'PHP Error'";
                        break;
                    default: 
                    $where = '';
                }
                Jojo::deleteQuery("DELETE FROM {eventlog}" . $where);
            }
        }

        $log = Jojo::selectquery("SELECT * FROM {eventlog} ORDER BY el_datetime DESC LIMIT 1000");
        $n = count($log);
        for ($i = 0; $i < $n; $i++) {
            $log[$i]['friendlydate'] = Jojo::relativeDate(strtotime($log[$i]['el_datetime']));
            $log[$i]['el_desc'] = htmlentities($log[$i]['el_desc']);
        }
        $smarty->assign('log', $log);

        $content['content'] = $smarty->fetch('admin/eventlog.tpl');

        return $content;
    }
}