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

class Jojo_Plugin_Admin_options extends Jojo_Plugin
{

    function _getContent()
    {
        global $smarty;
        $smarty->assign('title',  "Manage Site Options");

        Jojo_Plugin_Admin::adminMenu();

        /* Get categories list */
        $categories = Jojo::selectQuery("SELECT DISTINCT op_category FROM {option} ORDER BY op_category");

        /* If category has no name, call it 'Misc' */
        foreach ($categories as $c => $cat) {
            if (empty($cat['op_category'])) {
                $categories[$c]['op_category'] = 'Misc';
            }
        }
        $smarty->assign('categories', $categories);

        /* Get all the options from the database */
        $options = array();
        $res = Jojo::selectQuery("SELECT * FROM {option} ORDER BY op_category, op_name");
        foreach ($res as $o => $opt) {
            /* Set usable values if anything is missing */
            $opt['op_displayname'] = (empty($opt['op_displayname'])) ? $opt['op_name'] : $opt['op_displayname'];
            $opt['op_category']    = (empty($opt['op_category'])) ? 'Misc' : $opt['op_category'];
            $opt['op_value']       = (empty($opt['op_value']))    ? $opt['op_default'] : $opt['op_value'];

            /* Expand values for radio buttons */
            if ($opt['op_type'] == 'radio' || $opt['op_type'] == 'select' || $opt['op_type'] == 'checkbox') {
                $opt['options'] = explode(',', $opt['op_options']);
            }
            
            if ($opt['op_type'] == 'checkbox') {
                $opt['values'] = explode(',', $opt['op_value']);            
            }

            /* Group options by category */
            $options[$opt['op_category']][] = $opt;
        }
        $smarty->assign('options', $options);

        $content = array();
        $content['content'] = $smarty->fetch('admin/options.tpl');
        $content['head']    = $smarty->fetch('admin/options_head.tpl');

        return $content;
    }

}