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
        
        include _BASEPLUGINDIR . '/jojo_core/external/parsedown/Parsedown.php';

        $content = array();
        $listnames = array();
        $smarty->assign('title',  "Manage Site Plugins");

        $plugins = array();

        $pluginlocations = array(
            _BASEPLUGINDIR => 'core',
            _PLUGINDIR => 'client'
        );
        if (defined('_ALTPLUGINDIR')) {
            $pluginlocations[_ALTPLUGINDIR] = 'shared';
        }

        foreach ($pluginlocations as $loc => $type) {
            /* Get plugins from Jojo plugins dir */
            $pluginnames = Jojo::scanDirectory($loc);
            foreach ($pluginnames as $i => $name) {
                $readme = '';
                $version = '';

                /* ignore files, only look at directories */
                if (!is_dir($loc . '/' . $name) && !strpos($name, '.phar')) continue;

                /* Get plugin description */
                $path = strpos($name, '.phar') ? 'phar://' . $loc . '/' . $name : $loc . '/' . $name;
                $filename = $path . '/description.txt';
                $description = Jojo::fileExists($filename) ? file_get_contents($filename) : '';

                /* Get plugin readme */
                $filename = $path . '/readme.txt';
                $altfilename = $path . '/README.md';
                if (Jojo::fileExists($filename)){
                    $readme =  file_get_contents($filename);
                } elseif (Jojo::fileExists($altfilename)){
                    $readme =  file_get_contents($altfilename);
                } 
                if ($readme) {
                    //$readme = nl2br(htmlspecialchars($readme, ENT_COMPAT, 'UTF-8', false));
                    $readme = str_replace(array('[', ']'), array('&#91;', '&#93;'), $readme);
                    $parsedown = new Parsedown();
                    $readme = $parsedown->text($readme);
                }
 
                /* Get plugin version */
                if (file_exists($path . '/version.txt')) {
                    $version = file_get_contents($path . '/version.txt');
                }
                
                /* Get plugin status */
                $status = Jojo_Plugin_Admin_plugins::getPluginStatus($name);
                
                $plugins[] = array(
                    'name' => $name,
                    'description' => $description,
                    'readme' => $readme,
                    'status' => $status,
                    'version' => $version,
                    'type' => $type
                );
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
