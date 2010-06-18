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

class Jojo_Field_dblist extends Jojo_Field_list
{
    var $rows;
    protected $tree;

    function displayedit()
    {
        global $smarty;

        $this->populate();

        $smarty->assign('fd_field', $this->fd_field);
        $smarty->assign('value',    $this->value);
        $smarty->assign('rows',     $this->rows);
        $smarty->assign('fd_help',  htmlentities($this->fd_help));
        $smarty->assign('hktree',   $this->tree->printout_select(0, $this->value));
        $smarty->assign('error',    $this->error);
        $smarty->assign('readonly', $this->readonly);

        return  $smarty->fetch('admin/fields/dblist.tpl');
    }

    function displayView()
    {
        if (empty($this->value)) {
            return '';
        }

        $this->populate();
        return (isset($this->tree->nodes[$this->value])) ? $this->tree->nodes[$this->value] : '';
    }


    function populate()
    {
        $this->tree = new hktree();
        $tablename = $this->fd_options;
        //TODO: Make it look a bit prettier - sort it by the same parent options as the treemenu
        $this->tableoptions = Jojo::selectRow("SELECT * FROM {tabledata} WHERE td_name = ?", $tablename);

        $idfield         = $this->tableoptions['td_primarykey'];
        $displayfield    = Jojo::either($this->tableoptions['td_displayfield'], $this->tableoptions['td_primarykey']);
        $parentfield     = Jojo::either($this->tableoptions['td_parentfield'], $this->tableoptions['td_group1'], "'0'");
        $categorytable   = $this->tableoptions['td_categorytable'];
        $categoryfield   = Jojo::either($this->tableoptions['td_categoryfield'], $this->tableoptions['td_group1'], "'0'");
        $orderbyfield    = $this->tableoptions['td_orderbyfields'];
        $group1field     = $this->tableoptions['td_group1'];

        //TODO - Add group2 logic
        $datafilter      = Jojo::either($this->tableoptions['td_filterby'], '1'); //filter results
        $rolloverfield   = Jojo::either($this->tableoptions['td_rolloverfield'], "''");
        $html            = '';

        //Layer1 represents table structure where the first level is the grouping, then individual records underneath
        if ($this->tableoptions['td_group1'] != '') { // - this is used to pull down the main groupings - takes an extra query
            $layer1 = Jojo::selectQuery("SELECT ".$this->tableoptions['td_group1']." FROM {".$this->tableoptions['td_name']."} GROUP BY ".$this->tableoptions['td_group1']." ORDER BY ".$this->tableoptions['td_group1']."");
            foreach ($layer1 as $group) {
                $this->tree->addnode($group[$this->tableoptions['td_group1']], 0, $group[$this->tableoptions['td_group1']]);
            }
        }
        //Main query
        $query = sprintf("SELECT %s AS id, %s AS display, %s AS parent, %s AS categoryfield, %s AS rollover %s FROM {%s} WHERE %s ORDER BY %s %s display",
                            $idfield,
                            $displayfield,
                            $parentfield,
                            $categoryfield,
                            $rolloverfield,
                            Jojo::onlyIf($group1field, ','.$group1field.' AS group1'),
                            $this->tableoptions['td_name'],
                            $datafilter,
                            Jojo::onlyIf($group1field, ' '.$group1field.', '),
                            Jojo::onlyIf($orderbyfield, ' '.$orderbyfield.', ')
                        );
            $records = Jojo::selectQuery($query);
            if ($this->tableoptions['td_displayfield']) {            
                $displayfielddata = Jojo::selectRow("SELECT fd_type, fd_options FROM {fielddata} WHERE fd_table = ? AND fd_field = ?", array($tablename, $this->tableoptions['td_displayfield']));
                $displayfieldtype = isset($displayfielddata['fd_type']) ? $displayfielddata['fd_type'] : '';
                $displayfieldoptions = isset($displayfielddata['fd_options']) ? $displayfielddata['fd_options'] : '';
                if ($displayfieldtype == 'dbpluginpagelist') {
                    $displaytitles = Jojo::selectAssoc("SELECT pageid AS id, pageid, pg_title, pg_language FROM {page} WHERE pg_link = ? ", array($displayfielddata['fd_options']));
                    foreach ($records as &$r) {
                        $r['display'] = isset($displaytitles[$r['display']]['pg_title']) ? $displaytitles[$r['display']]['pg_title'] . (_MULTILANGUAGE ? ' (' . $displaytitles[$r['display']]['pg_language'] . ')' : '') : 'page missing';
                    }
                } 
           }
        foreach ($records as $record) {
            $this->tree->addnode($record['id'], $record['parent'], $record['display']);
        }
    }
}