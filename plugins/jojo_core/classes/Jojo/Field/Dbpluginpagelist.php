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

class Jojo_Field_dbpluginpagelist extends Jojo_Field_dblist
{
    function displayedit()
    {
        global $smarty;

        $this->populate();

        $smarty->assign('fd_field', $this->fd_field);
        $smarty->assign('value',    $this->value);
        $smarty->assign('rows',     $this->rows);
        $smarty->assign('fd_help',  htmlentities($this->fd_help));
        $smarty->assign('hktree',   $this->tree->printout_select(0, $this->value, $this->table->getRecordID()));
        $smarty->assign('error',    $this->error);
        $smarty->assign('readonly', $this->fd_readonly);
        $smarty->assign('readonlydisplay', $this->readonlydisplay);

        return  $smarty->fetch('admin/fields/dbpagelist.tpl');
    }

    function populate()
    {
        $this->tree = new hktree();
        $tablename = 'page';
        $pluginname = $this->fd_options;
        //TODO: Make it look a bit prettier - sort it by the same parent options as the treemenu
        $this->tableoptions = Jojo::selectRow("SELECT * FROM {tabledata} WHERE td_name = 'page'");

        $idfield         = $this->tableoptions['td_primarykey'];
        $displayfield    = Jojo::either($this->tableoptions['td_displayfield'], $this->tableoptions['td_primarykey']);
        $parentfield     = Jojo::either($this->tableoptions['td_parentfield'], $this->tableoptions['td_group1'], "'0'");
        $categorytable   = $this->tableoptions['td_categorytable'];
        $categoryfield   = Jojo::either($this->tableoptions['td_categoryfield'], $this->tableoptions['td_group1'], "'0'");
        $orderbyfield    = $this->tableoptions['td_orderbyfields'];
        $group1field     = $this->tableoptions['td_group1'];

        //TODO - Add group2 logic
        $datafilter      = "pg_link = '" . $pluginname . "'"; //filter results by plugin
        $datafilter .= ($this->readonly) ? ' AND ' . $idfield . ' = ' .  $this->value : '';
        $rolloverfield   = Jojo::either($this->tableoptions['td_rolloverfield'], "''");
        $html            = '';

        //Layer1 represents table structure where the first level is the grouping, then individual records underneath
        if ($this->tableoptions['td_group1'] != '') { // - this is used to pull down the main groupings - takes an extra query
            $layer1 = Jojo::selectQuery("SELECT ".$this->tableoptions['td_group1']." FROM {page} GROUP BY ".$this->tableoptions['td_group1']." ORDER BY ".$this->tableoptions['td_group1']."");
            foreach ($layer1 as $group) {
                $this->tree->addnode($group[$this->tableoptions['td_group1']], 0, $group[$this->tableoptions['td_group1']]);
            }
        }
        //Main query
        $query = sprintf("SELECT %s AS 'key', %s AS id, %s AS display, %s AS parent, %s AS categoryfield, %s AS rollover %s FROM {page} WHERE %s ORDER BY %s %s display",
                            $idfield,
                            $idfield,
                            $displayfield,
                            $parentfield,
                            $categoryfield,
                            $rolloverfield,
                            Jojo::onlyIf($group1field, ','.$group1field.' AS group1'),
                            $datafilter,
                            Jojo::onlyIf($group1field, ' '.$group1field.', '),
                            Jojo::onlyIf($orderbyfield, ' '.$orderbyfield.', ')
                        );
        $records = Jojo::selectAssoc($query);
        $this->readonlydisplay = ($records && $this->readonly) ? $records[0]['display']: '';
        foreach ($records as $record) {
            if (!isset($records[$record['parent']])) {
                $parent = 0;
            } else {
                $parent = $record['parent'];
            }
            $this->tree->addnode($record['id'], $parent, $record['display']);
        }
    }
}