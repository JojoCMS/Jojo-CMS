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

class Jojo_Field_internalurl extends Jojo_Field
{
    var $fd_size;
    var $error;
    var $readonly;
    var $counter = 0;
    var $texttype;
    var $prefix;

    function __construct($fielddata = array())
    {
        parent::__construct($fielddata);
        $this->fd_size = 20;
        $this->size = 20;
        $this->prefix = $this->fd_options;
    }

    /*
     * Check the value of this field
     */
    function checkvalue()
    {
        /* Check the value is not blank if required */
        if (($this->fd_required == 'yes') && ($this->isblank())) {
            $this->error = 'Required field';
        }

        /* Make URL lower case */
        $this->value = Jojo::getOption('lowercase_internalurl', 'yes')=='yes' ? strtolower($this->value) : $this->value;

        /* Remove UTF Characters */
        $this->value = utf8_encode($this->value);

        /* Remove some characters */
        $matches = array( '"', '!', '#', '$', '%', '^', '*', '<', '>',
                          '=',  '\'', ',', '(', ')', '?', '!',
                          ',','[',']','{','}',':',';','`','~','|');
        $this->value = str_replace($matches, '', $this->value);
        
        if (strpos($this->value, '.')!=(strlen($this->value)-4)) {
            $this->value = str_replace('.', '', $this->value);
        }

        /* Replace some characters */
        $matches = array( ' - ', ' ', ', ', '&',   '@',  ':', '--' );
        $replace = array( '-',   '-', '-',  'and', 'at', '-', '-'  );
        $this->value = str_replace($matches, $replace, $this->value);

        /* Remove remainging dashes */
        $this->value = str_replace('--', '-', $this->value);

        $this->value = trim($this->value, '/');

        /* Remove leading or trailing dashes */
        $this->value = trim($this->value, '-');

        /* Remove whitespace */
        $this->value = trim($this->value);

        return ($this->error == '');
    }

    /*
     * Return the html for editing this field
     */
    function displayedit()
    {
        global $smarty, $table;

        $this->texttype = $this->fd_options;
        $this->tableoptions = Jojo::selectRow("SELECT * FROM {tabledata} WHERE td_name = ?", $this->fd_table);
        $plugin = isset($this->tableoptions['td_plugin']) ? $this->tableoptions['td_plugin'] : '';
        $class = 'Jojo_Plugin_' . $plugin;
        $id = $this->table->getRecordID();
        $url = str_replace('http://', '' ,_SITEURL) . '/';
        if (class_exists($class) && method_exists($class, 'getPrefixById') && $id) { 
            $prefix = call_user_func($class . '::getPrefixById', $id);
        } else   {
            $prefix = !empty($this->prefix) ? $this->prefix : '';
        }
        $url .= $prefix ? $prefix . '/' : '';        
        $smarty->assign('url', $url);
        $smarty->assign('fd_field', $this->fd_field);
        $smarty->assign('readonly', $this->fd_readonly);
        $smarty->assign('size', $this->size);
        $smarty->assign('value', $this->value);
        $smarty->assign('fd_maxsize', $this->fd_maxsize);
        $smarty->assign('fd_help', htmlentities($this->fd_help));
        return  $smarty->fetch('admin/fields/internalurl.tpl');
    }
}