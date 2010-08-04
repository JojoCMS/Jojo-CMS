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

class Jojo_Field_plugin extends Jojo_Field
{
    var $rows;
    var $options;
    var $_types;

    public function __construct($fielddata = array())
    {
        parent::__construct($fielddata);

        /* Grab all the values currently in the page table */
        foreach (Jojo::selectQuery("SELECT DISTINCT pg_link FROM {page} WHERE pg_link != ''") as $row) {
            $this->_types[strtolower($row['pg_link'])] = $row['pg_link'];
        }
    }

    function displayedit()
    {
        global $smarty;

        /* Grab the api from each plugin */
        foreach (Jojo::listPlugins('api.php') as $pluginfile) {
            /* Include the api */
            $_provides = array();
            include($pluginfile);

            /* Add all the fieldTypes the plugin provides to the array */
            if (isset($_provides['pluginClasses']) && is_array($_provides['pluginClasses'])) {
                foreach($_provides['pluginClasses'] as $id => $name) {
                    $this->_types[strtolower($id)] = $name;
                }
            }
        }

        asort($this->_types);

        $smarty->assign('rows', $this->rows);
        $smarty->assign('_types', $this->_types);
        $smarty->assign('readonly', $this->fd_readonly);
        $smarty->assign('value', strtolower($this->value));
        $smarty->assign('rows', $this->rows);
        $smarty->assign('fd_field', $this->fd_field);
        $smarty->assign('fd_size', $this->fd_size);
        $smarty->assign('fd_help', htmlentities($this->fd_help));

        return  $smarty->fetch('admin/fields/plugin.tpl');
    }

    function displayView()
    {
        /* Grab the api from each plugin */
        foreach (Jojo::listPlugins('api.php') as $pluginfile) {
            /* Include the api */
            $_provides = array();
            include($pluginfile);

            /* Add all the fieldTypes the plugin provides to the array */
            if (isset($_provides['pluginClasses']) && is_array($_provides['pluginClasses'])) {
                foreach($_provides['pluginClasses'] as $id => $name) {
                    if (strtolower($this->value) == strtolower($id)) {
                        return $name;
                    }
                }
            }
        }
        return str_replace(' ', '_', ucwords(str_replace('_', ' ', strtolower($this->value))));
    }
}