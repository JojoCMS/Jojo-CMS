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

class Jojo_Plugin_Admin_themes extends Jojo_Plugin
{

    function _getContent()
    {
        global $smarty, $_USERGROUPS;

        $content = array();
        $listnames = array();
        $smarty->assign('title', "Manage Site Themes");

        $themes = array();

        /* Get Themes from jojo themes dir */
        $themenames = Jojo::scanDirectory(_BASETHEMEDIR);
        foreach($themenames as $i => $name) {
            /* ignore files, only look at directories */
            if (!is_dir(_BASETHEMEDIR . '/' . $name)) {
                continue;
            }

            /* Get theme description */
            $filename = _BASETHEMEDIR . '/' . $name . '/description.txt';
            $description = Jojo::fileExists($filename) ? file_get_contents($filename) : '';

            /* Get theme screenshot */
            $filename = _BASETHEMEDIR . '/' . $name . '/screenshot.jpg';
            $screenshot = Jojo::fileExists($filename) ? $filename : 'no-screenshot.jpg';

            /* Get the theme status */
            $status = Jojo_Plugin_Admin_themes::getThemeStatus($name);

            /* Add to the array to be displayed */
            $themes[] = array(
                            'name' => $name,
                            'description' => $description,
                            'status' => $status,
                            'screenshot' => $screenshot
                           );
        }

        /* Get Themes from wwwroot/themes dir */
        if (_BASETHEMEDIR != _THEMEDIR . '') {
            $themenames = Jojo::scanDirectory(_THEMEDIR . '');
            foreach($themenames as $i => $name) {
                /* ignore files, only look at directories */
                if (!is_dir(_THEMEDIR . '/' . $name)) {
                    continue;
                }

                /* Get theme description */
                $filename = _THEMEDIR . '/' . $name . '/description.txt';
                $description = Jojo::fileExists($filename) ? file_get_contents($filename) : '';

                /* Get theme screenshot */
                $filename = _BASETHEMEDIR . '/' . $name . '/screenshot.jpg';
                $screenshot = Jojo::fileExists($filename) ? $filename : 'no-screenshot.jpg';

                /* Add to the array to be displayed */
                $themes[] = array(
                                'name' => $name,
                                'description' => $description,
                                'status' => Jojo_Plugin_Admin_themes::getThemeStatus($name),
                                'screenshot' => $screenshot
                               );
            }
        }

        $smarty->assign('themes', $themes);

        Jojo_Plugin_Admin::adminMenu();
        $content['content'] = $smarty->fetch('admin/manage-themes.tpl');

        return $content;
    }

    static function getThemeStatus($name)
    {
        $row = Jojo::selectRow("SELECT * FROM {theme} WHERE active = 'yes' AND name = ? LIMIT 1", $name);
        return ($row) ? 'active' : 'not installed';
    }
}