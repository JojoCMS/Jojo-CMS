<?php
/**
 *                    Jojo CMS
 *                ================
 *
 * Copyright 2010 Jojo CMS
 *
 * See the enclosed file license.txt for license information (LGPL). If you
 * did not receive this file, see http://www.fsf.org/copyleft/lgpl.html.
 *
 * @author  Harvey Kane <code@ragepank.com>
 * @license http://www.fsf.org/copyleft/lgpl.html GNU Lesser General Public License
 * @link    http://www.jojocms.org JojoCMS
 * @package jojo_core
 */

class Jojo_Field_checkboxes extends Jojo_Field
{
    var $fd_size;

    function __construct($fielddata = array())
    {
        parent::__construct($fielddata);
        $this->options = array();
    }

    function displayedit()
    {
        /*
        
        Checkboxes data is stored in the DB as a newline separated list of values
        
        */
        
        global $smarty;
        $checked_values = explode("\n", $this->value);
        foreach ($checked_values as $k => $v) {
            $checked_values[$k] = trim($v); //trim whitespace - in case of Windows \r characters
        }

        $vals        = array();
        $displayvals = array();
        $extras      = array();
        //$allextras   = array();
        
        $this->populate();
        
        foreach ($this->options as $option) {
            $vals[]         = $option['value'];
            $displayvals[]  = $option['name'];
            $checked[]      = (in_array(trim($option['value']), $checked_values)) ? true : false;
            //$extras[]       = $option['extra'];
        }
       

        $smarty->assign('vals',        $vals);
        $smarty->assign('displayvals', $displayvals);
        $smarty->assign('checked',     $checked);
        $smarty->assign('extras',      $extras);
        $smarty->assign('allextras',   $allextras);
        $smarty->assign('fd_help',     htmlentities($this->fd_help));
        $smarty->assign('readonly',    $this->fd_readonly);
        $smarty->assign('value',       $this->value);
        $smarty->assign('fd_field',    $this->fd_field);

        return  $smarty->fetch('admin/fields/checkboxes.tpl');
    }

    function displayView()
    {
        $this->populate();
        $checked_values = explode("\n", $this->value);
        foreach ($checked_values as $k => $v) {
            $checked_values[$k] = trim($v); //trim whitespace - in case of Windows \r characters
        }
        /* convert values to their user-friendly names for display */
        $checked_names = array();
        foreach ($this->options as $option) {
            if (in_array(trim($option['value']), $checked_values)) $checked_names[] = $option['name'];
        }
        
        return implode("\n", $checked_names);
    }
    
    function setValue($newvalue)
    {
        
        $this->populate();
        $vals = array();
        foreach ($this->options as $k => $option) {
            $val = Jojo::getFormData('fm_'.$this->fd_field.'_'.$k, false);
            if ($val) $vals[] = $val;
        }
        
        $this->value = implode("\n", $vals);

        return true;
    }
    
    function populate()
    {
        $i = 0;
        $optionsarr = explode("\n",$this->fd_options);
        foreach ($optionsarr as $option) {
            $option2 = str_replace("\r","",$option); //hack hack
            $optionarray = explode(":",$option2);
            if (count($optionarray) == 3) {
                $this->options[$i]['value'] = $optionarray[0];
                $this->options[$i]['name']  =  Jojo::either($optionarray[1],$optionarray[0]);
                $this->options[$i]['extra'] = $optionarray[2];
            } elseif (count($optionarray) == 2) {
                $this->options[$i]['value'] = $optionarray[0];
                $this->options[$i]['name']  =  Jojo::either($optionarray[1],$optionarray[0]);
            } else {
                $this->options[$i]['value'] = $optionarray[0];
                $this->options[$i]['name']  = $optionarray[0];
            }
            $i++;
        }
    }
}