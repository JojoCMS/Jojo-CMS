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

class Jojo_Field_texteditor extends Jojo_Field
{
    var $rows;
    var $cols;

    function __construct($fielddata = array())
    {
        parent::__construct($fielddata);
        $this->rows = 30;
        $this->cols = 50;
    }

    function displayedit()
    {
        global $smarty;

        $this->rows = $this->rows > 0 ? $this->rows : 8;
        $this->cols = $this->cols > 0 ? $this->cols : 50;

        $wysiwygfield = ($this->fd_options != '') ? $this->fd_options : false;

        /*
        decide if the data on this record is BB or WYSIWYG
        -if BB is not empty, then BB
        -elseif BB is empty and WYSIWYG is not empty, then WYSIWYG
        -else, go from site preferences
         */
        if ($wysiwygfield) {
            $recordid = $this->table->getRecordID();
            if (empty($recordid)) {
                $selectededitor = Jojo::getOption('preferrededitor');
            } elseif ($this->value != '') {
            //if ($this->value != '') {
                $selectededitor = 'wysiwyg';
            } else {
                /* get value of WYSIWYG field */
                $data = Jojo::selectQuery("SELECT `" . $this->fd_options . "` FROM {" . $this->table->getTableName() . "} WHERE `" . $this->table->getOption('primarykey') . "` = ? LIMIT 1", array($this->table->getRecordID()));
                $wysiwygvalue = $data[0][$this->fd_options];
                $selectededitor = ($wysiwygvalue == '') ? Jojo::getOption('preferrededitor') : 'wysiwyg';
            }
        }

        if ($this->value == '') {
            $editortype = Jojo::getOption('preferrededitor');
            if ($editortype == 'bbcode') {
                $editortype = 'bb';
            } elseif ($editortype == 'wysiwyg') {
                $editortype = 'html';
            }
        } elseif (preg_match('/\\[editor:([a-z]+)\\]\\s(.*)/si', $this->value, $result)) {
            $editortype = $result[1];
            $this->value = $result[2];
        } else {
            $editortype = 'wysiwyg';
        }

        $smarty->assign('rows', $this->rows);
        $smarty->assign('cols', $this->cols);
        $smarty->assign('fd_field', $this->fd_field);
        $smarty->assign('value', htmlspecialchars($this->value) );
        $smarty->assign('wysiwygfield', $wysiwygfield);
        $smarty->assign('selectededitor', $selectededitor);
        $smarty->assign('editortype', $editortype);
        $smarty->assign('fd_name', $this->fd_name);
        $smarty->assign('readonly', $this->fd_readonly);
        $smarty->assign('fd_help', htmlentities($this->fd_help, ENT_COMPAT, 'UTF-8'));
        $smarty->assign('value', htmlentities($this->value, ENT_COMPAT, 'UTF-8'));
        $smarty->assign('valuehidden', htmlentities($this->value, ENT_QUOTES, 'UTF-8'));
        return $smarty->fetch('admin/fields/texteditor.tpl');
    }

    function displayview()
    {
        $editortype = false;
        if (preg_match('/\\[editor:([a-z]+)\\]\\s(.*)/si', $this->value, $result)) {
            $editortype = $result[1];
            $code = $result[2];
        } else {
            $code = $this->value;
        }

        if ($editortype != 'bb') {
            return $code;
        }

        $bb = new bbconverter();
        $bb->truncateurl = 30;
        $bb->imagedropshadow =  Jojo::yes2true(Jojo::getOption('imagedropshadow'));
        if (Jojo::getOption('magtemplate')) {
            $bb->magtemplate = Jojo::getOption('magtemplate');
        }
        $bb->setBBCode($code);
        return $bb->convert('bbcode2html');
    }

    function setValue($newvalue)
    {
        $editortype = Jojo::getFormData('editor_' . $this->fd_field, false);
        $this->value = ($editortype) ? '[editor:' . $editortype . "]\n" . $newvalue : $newvalue;
        return true;
    }

    function checkvalue()
    {
        $editortype = false;
        if (preg_match('/\\[editor:([a-z]+)\\]\\s(.*)/si', $this->value, $result)) {
            $editortype = $result[1];
            $code = $result[2];
        } else {
            $code = $this->value;
        }

        if ($this->fd_options) {
            $targetField = trim($this->fd_options);
            if ($editortype == 'bb') {
                $bb = new bbconverter();
                $bb->truncateurl = 30;
                $bb->imagedropshadow =  Jojo::yes2true(Jojo::getOption('imagedropshadow'));
                if (Jojo::getOption('magtemplate')) {
                    $bb->magtemplate = Jojo::getOption('magtemplate');
                }
                $bb->setBBCode($code);
                $this->table->setFieldValue($targetField, $bb->convert('bbcode2html'));
            } else {
                $this->table->setFieldValue($targetField, $code);
            }
        }

        return true;
    }

}