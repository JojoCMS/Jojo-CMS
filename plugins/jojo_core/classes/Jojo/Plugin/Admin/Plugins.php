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

class Jojo_Plugin_Admin_plugins extends Jojo_Plugin
{

    function _getContent()
    {
        global $smarty, $_USERGROUPS;

        $content = array();
        $listnames = array();
        $smarty->assign('title',  "Manage Site Plugins");

        $plugins = array();


        /* Get plugins from Jojo plugins dir */
        $pluginnames = Jojo::scanDirectory(_BASEPLUGINDIR);
        foreach ($pluginnames as $i => $name) {
            /* ignore files, only look at directories */
            if (!is_dir(_BASEPLUGINDIR . '/' . $name) && !strpos($name, '.phar')) continue;

            /* Get plugin description */
            $path = strpos($name, '.phar') ? 'phar://' . _BASEPLUGINDIR . '/' . $name : _BASEPLUGINDIR . '/' . $name;
            $filename = $path . '/description.txt';
            $description = Jojo::fileExists($filename) ? file_get_contents($filename) : '';

            /* Get plugin readme */
            $filename = $path . '/readme.txt';
            $readme = Jojo::fileExists($filename) ? file_get_contents($filename) : '';
            $readme = str_replace(array('[', ']'), array('\\[', '\\]'), $readme);

            /* Get plugin status */
            $status = Jojo_Plugin_Admin_plugins::getPluginStatus($name);
            $plugins[] = array(
                                'name' => $name,
                                'description' => $description,
                                'readme' => $readme,
                                'status' => $status,
                                'type' => 'core'
                                );

        }

        /* Get plugins from wwwroot/plugins dir */
        $pluginnames = Jojo::scanDirectory(_PLUGINDIR);
        $list = array();
        foreach ($pluginnames as $i => $name) {
            /* ignore files, only look at directories */
            if (!is_dir(_PLUGINDIR . '/' . $name) && !strpos($name, '.phar')) continue;

            /* Get plugin description */
            $path = strpos($name, '.phar') ? 'phar://' . _PLUGINDIR . '/' . $name : _PLUGINDIR . '/' . $name;
            $filename = $path . '/description.txt';
            $description = Jojo::fileExists($filename) ? file_get_contents($filename) : '';

            /* Get plugin readme */
            $filename = $path . '/readme.txt';
            $readme = Jojo::fileExists($filename) ? file_get_contents($filename) : '';
            $readme = str_replace(array('[', ']'), array('\\[', '\\]'), $readme);

            /* Get plugin status */
            $status = Jojo_Plugin_Admin_plugins::getPluginStatus($name);
            $plugins[] = array(
                                'name' => $name,
                                'description' => $description,
                                'readme' => $readme,
                                'status' => $status,
                                'type' => 'client'
                                );

            /* Check for updated version */
            for  ($s = 1; $s <= count($list); $s++) {
                if ($list[$s]['NAME'] == $name) {
                    $list[$s]['INSTALLED']= 'yes';

                    $filename = $path . '/version.txt';

                    if ( Jojo::fileExists($filename)) {
                        $version = file_get_contents($filename);
                    }

                    $compareVersion = version_compare($version, $list[$s+1]['VERSION']);
                    if ($compareVersion == -1) {
                        $plugins[max(array_keys($plugins))]['download'] = "upgrade to Version " . $list[$s]['VERSION'];
                        $plugins[max(array_keys($plugins))]['url'] = $list[$s]['URL'];
                    }
                }
            }
        }

        /* sort plugin array alphabetically */
        foreach ($plugins as $p) {
            $sortedplugins[$p['name']] = $p;
        }
        ksort($sortedplugins);
        $plugins = array();
        foreach ($sortedplugins as $p) {
            $plugins[] = $p;
        }


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
            $options[] = $opt;
        }
        $smarty->assign('options', $options);

        $smarty->assign('list', $list);

        $smarty->assign('plugins', $plugins);

        Jojo_Plugin_Admin::adminMenu();
        $content['content'] = $smarty->fetch('admin/manage-plugins.tpl');

        return $content;
    }

    static function getPluginStatus($name)
    {
        $data = Jojo::selectQuery("SELECT * FROM {plugin} WHERE name = ? LIMIT 1", $name);
        if (count($data) == 1) {
            $active = $data[0]['active'];
            switch($active) {
                case 'yes':
                    return 'active';
                case 'no':
                    return 'disabled';
            }
        }
        return 'not installed';
    }
}