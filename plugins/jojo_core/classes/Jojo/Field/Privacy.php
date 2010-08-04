<?php
/**
 *                    Jojo CMS
 *                ================
 *
 * Copyright 2007-2009 Harvey Kane <code@ragepank.com>
 * Copyright 2007-2009 Michael Holt <code@gardyneholt.co.nz>
 *
 * See the enclosed file license.txt for license information (LGPL). If you
 * did not receive this file, see http://www.fsf.org/copyleft/lgpl.html.
 *
 * @author  Harvey Kane <code@ragepank.com>
 * @license http://www.fsf.org/copyleft/lgpl.html GNU Lesser General Public License
 * @link    http://www.jojocms.org JojoCMS
 * @package jojo_core
 */

class Jojo_Field_privacy extends Jojo_Field
{
    var $rows;
    var $cols;
    var $counter = 0;
    var $texttype;

    function __construct($fielddata = array())
    {
        parent::__construct($fielddata);
        $this->rows = 5;
        $this->cols = 50;
        if (!empty($fielddata['fd_rows'])) $this->rows = $fielddata['fd_rows'];
        if (!empty($fielddata['fd_cols'])) $this->cols = $fielddata['fd_cols'];
    }

    function setPostData($data = false)
    {
        $privacy = isset($_POST['privacy']) ? $_POST['privacy'] : array();
        $hasprivacy = isset($_POST['hasprivacy']) ? $_POST['hasprivacy'] : array();
        $this->postdata = array('privacy' => $privacy, 'hasprivacy' => $hasprivacy);
    }

    function displayedit()
    {
        global $smarty;

        if ($this->texttype == 'metadescription') {
          $this->counter = 155;
        } elseif ($this->fd_options == 'metadescription') {
          $this->counter = 155;
        } elseif (is_numeric($this->fd_options)) {
          $this->counter = $this->fd_options;
        } else {
          $this->counter = 0;
        }

        $smarty->assign('rows',          $this->rows);
        $smarty->assign('cols',          $this->cols);
        $smarty->assign('counterstrlen', $this->counter - strlen($this->value));
        $smarty->assign('value',         $this->value);
        $smarty->assign('counter',       $this->counter);
        $smarty->assign('fd_field',      $this->fd_field);
        $smarty->assign('readonly',      $this->fd_readonly);
        $smarty->assign('fd_help',       htmlentities($this->fd_help));

        return  $smarty->fetch('admin/fields/privacy.tpl');
    }

    function displayview()
    {
        return '';
    }

    function setValue($newvalue)
    {
        $privacy = array('private' => array(), 'public' => array());

        foreach ($this->postdata['hasprivacy'] as $k => $v) {
       // echo $k .' ------'.$this->postdata['privacy'][$k].'<br />';
            if ( isset($this->postdata['privacy'][$k]) && ($this->postdata['privacy'][$k] == 'yes' || $this->postdata['privacy'][$k] == 'y' || $this->postdata['privacy'][$k] == 'Y')) {
                $privacy['private'][] = $k;
            } else {
                $privacy['public'][] = $k;
            }
        }

        $this->value = serialize($privacy);
        return true;
    }
}