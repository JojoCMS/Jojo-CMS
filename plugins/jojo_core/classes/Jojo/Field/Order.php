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

class Jojo_Field_order extends Jojo_Field
{
    var $size;
    var $minvalue;
    var $maxvalue;

    function __construct($fielddata = array())
    {
        parent::__construct($fielddata);
        $this->size     = 5;
        $this->minvalue = ''; //'' means no minimum
        $this->maxvalue = ''; //'' means no maximum
    }

    function displayedit()
    {
        global $smarty;

        $smarty->assign('fd_field', $this->fd_field);
        $smarty->assign('readonly', $this->readonly);
        $smarty->assign('size',     $this->size);
        $smarty->assign('fd_help',  htmlentities($this->fd_help));
        $smarty->assign('error',    $this->error);
        $smarty->assign('value',    $this->value);
        $smarty->assign('fd_type',  $this->fd_type);

        /*New Way assigning to Smarty -Templates*/
        return  $smarty->fetch('admin/fields/order.tpl');
    }

    function afterSave()
    {
      if ($this->table->getRecordID()) {
        $query     = "SELECT * FROM {tabledata} WHERE td_name='".$this->fd_table."' LIMIT 1";
        $tabledata = Jojo::selectQuery($query);
        $td        = $tabledata[0];
        $query     = "SELECT * FROM {".$this->fd_table."} WHERE `".$td['td_primarykey']."`=? LIMIT 1";
        $records   = Jojo::selectQuery($query, $this->table->getRecordID());
        $record    = $records[0];
        $where     = '1';

        if ($td['td_parentfield']) {
            $where = ' `'.$td['td_parentfield'].'`= '.$record[$td['td_parentfield']].'';
        }

        $values = array();
        if ($td['td_group1']) {
            $where = ' `'.$td['td_group1'].'`= ?';
            $values = array($record[$td['td_group1']]);
        }
        $values[] = $this->table->getRecordID();
        $query = "SELECT * FROM {".$this->fd_table."} WHERE ".$where." AND `".$td['td_primarykey']."`!=? ORDER BY ".$this->fd_field."";
        $siblings = Jojo::selectQuery($query, $values);

        $min_order_query = "SELECT MIN(".$this->fd_field.") as min_order FROM {".$this->fd_table."} WHERE ".$where." AND `".$td['td_primarykey']."`!=? ORDER BY ".$this->fd_field."";
        $min_sibling_order = Jojo::selectQuery($min_order_query, $values);
        $neworder_start = is_array($min_sibling_order) ?   min($min_sibling_order[0]['min_order'],$this->value) : 0;

        $n = count($siblings);
        $neworder = $neworder_start;
        $i = 0;
        $done = false;

        while ($i<$n) {
            /* for the current record */
            if ($neworder==$this->value) {
                $query = "UPDATE {".$this->fd_table."} SET `".$this->fd_field."`=".($neworder-$neworder_start)." WHERE `".$td['td_primarykey']."`=? LIMIT 1";
                Jojo::updateQuery($query, $this->table->getRecordID());
                ++$neworder;
                $done = true;
            }

            /* for other records */
            if (($neworder-$neworder_start) != $siblings[$i][$this->fd_field]) {
                $query = "UPDATE {".$this->fd_table."} SET `".$this->fd_field."`=".($neworder-$neworder_start)." WHERE `".$td['td_primarykey']."`=? LIMIT 1";
                Jojo::updateQuery($query, $siblings[$i][$td['td_primarykey']]);
            }

            ++$neworder;
            ++$i;
        }
        /* only happens when order is off the chart */
        if (!$done) {
            $query = "UPDATE {".$this->fd_table."} SET `".$this->fd_field."`=".($neworder-$neworder_start)." WHERE `".$td['td_primarykey']."`=? LIMIT 1";
         Jojo::updateQuery($query, $this->table->getRecordID());
        }

      }
      return true;
    }
}