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

class Jojo_Field_list extends Jojo_Field
{
    var $rows;
    var $options;
    var $error;

    function __construct($fielddata = array())
    {
        parent::__construct($fielddata);
        $this->rows = 1; //TODO: make this optional in Fielddata
        $this->options = array();
    }

    function checkvalue()
    {
        if ( ($this->fd_required == "yes") and ($this->isblank()) ) {
            $this->error = "Required field";
        }
        //echo $this->error;
        if ($this->error == "") {return true;} else {return false;}
    }

    function displayedit()
    {
        global $smarty;
        $this->populate();

        $smarty->assign('options', $this->options);
        $smarty->assign('fd_field', $this->fd_field);
        $smarty->assign('readonly', $this->fd_readonly);
        $smarty->assign('fd_help', htmlentities($this->fd_help));
        $smarty->assign('error', $this->error);
        $smarty->assign('rows', $this->rows);
        $smarty->assign('value', $this->value);

        return  $smarty->fetch('admin/fields/list.tpl');
    }

    function displayView()
    {
        if (empty($this->value)) {return '';}

        $this->populate();
        foreach ($this->options as $option) {
            if ($option['value'] == $this->value) {
                return $option['name'];
            }
        }
        return '';
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