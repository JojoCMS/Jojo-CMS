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

/*
    Many-to-many relationships are commonly handled when an item belongs to one or more categories.
    A product may belong to several categories
    An invoice might have several lines
    etc.

    To be compatible, please implement your table structure as such (it's fairly standard)

    PRODUCT (referred to as the ITEM)
    productid
    productname
    productdesc
    etc

    CATEGORY (referred to as the CATEGORY)
    categoryid
    categoryname
    etc

    PRODUCTCATEGORYLINK (referred to as the LINK)
    productid
    categoryid

    Enter the details into TABLEDATA as such...


    */

//////////////////////MANY2MANYFIELD//////////////////////
class Jojo_Field_many2many extends Jojo_Field
{
    var $rows;
    var $options;

    function __construct($fielddata = array())
    {
        parent::__construct($fielddata);
        $this->rows = 1; //TODO: make this optional in Fielddata
        $this->options = array();
    }

    function checkvalue()
    {
        if (($this->fd_required == 'yes') && $this->isblank()) {
            $this->error = 'Required field';
        }
        return empty($this->error) ? true : false;
    }

    function displayedit()
    {
        $output = '';
        $itemid = $this->table->getRecordID();

        /* get all current selections */
        $categories = Jojo::selectQuery("SELECT * FROM {" . $this->linktable . "} WHERE `" . $this->linkitemid . "` = ?", array($itemid));
        $selections = array(); //simple array holding all the categories the item is assigned to
        $n = count($categories);
        for ($i=0;$i<$n;$i++) {
            $selections[] = $categories[$i][$this->linkcatid];
        }

        $tablename = $this->cattable;
        $rows = Jojo::selectQuery("SELECT * FROM {tabledata} WHERE td_name = ? LIMIT 1", array($tablename));
        $tableoptions = $rows[0];

        $idfield        = $tableoptions['td_primarykey'];
        $displayfield   = Jojo::either($tableoptions['td_displayfield'], $tableoptions['td_primarykey']);
        $parentfield    = Jojo::either($tableoptions['td_parentfield'], $tableoptions['td_group1'], "'0'");
        $categorytable  = $tableoptions['td_categorytable'];
        $categoryfield  = Jojo::either($tableoptions['td_categoryfield'], $tableoptions['td_group1'], "'0'");
        $orderbyfield   = $tableoptions['td_orderbyfields'];
        $group1field    = $tableoptions['td_group1'];

        /* TODO - Add group2 logic */
        $datafilter =  Jojo::either($tableoptions['td_filterby'],'1'); //filter results
        $rolloverfield =  Jojo::either($tableoptions['td_rolloverfield'],"''");
        $html = ''; //this will be used for output

        //Layer1 represents table structure where the first level is the grouping, then individual records underneath
        if ($tableoptions['td_group1'] != "") { // - this is used to pull down the main groupings - takes an extra query
            $layer1 = Jojo::selectQuery("SELECT ".$tableoptions['td_group1']." FROM {".$tableoptions['td_name']."} GROUP BY ".$tableoptions['td_group1']." ORDER BY ".$tableoptions['td_group1']."");
        }
        /* Main query */
        $query = "SELECT $idfield AS id, $displayfield AS display, $parentfield AS parent, $categoryfield AS categoryfield, $rolloverfield AS rollover ". Jojo::onlyIf($group1field,",".$group1field." AS group1").
                    ' FROM {'.$tableoptions['td_name']."}
                    WHERE $datafilter ".
                    ' ORDER BY '. Jojo::onlyIf($group1field,' '.$group1field.', ').
                     Jojo::onlyIf($orderbyfield,' '.$orderbyfield.', ').
                     Jojo::onlyIf($displayfield,' '.$displayfield.', ').
                    ' display';

        $records = Jojo::selectQuery($query);

        $i = 1;
        foreach ($records as $record) {
            $options[$i]['name']        = $record['display'];
            $options[$i]['value']       = $record['id'];
            $options[$i]['parent']      = $record['parent'];
            $this->options[$i]['group'] = isset($record['group1']) ? $record['group1'] : '';
            $i++;
        }

        $tree = new hktree('tree');

        /* loop through each option and display */
        $n = count($options);
        for ($i=1;$i<=$n;$i++) {
            $isselected = in_array($options[$i]['value'], $selections) ? ' checked="checked"' : '';
            $item = "<label><input type='checkbox' name=\"fm_".$this->fd_field."_".$options[$i]['value']."\" id=\"fm_".$this->fd_field."_".$options[$i]['value']."\" value=\"".$options[$i]['value']."\" onchange=\"fullsave = true;\"".$isselected." /> ".$options[$i]['name']."</label><br />\n";

            if (empty($categoryfield)) {
                $tree->addNode($options[$i]['value'], $options[$i]['parent'], $item);
            } else {
                $tree->addNode($options[$i]['value'], $options[$i]['parent'], $item);
            }
        }
        $output .= '<input type="hidden" name="fm_'.$this->fd_field.'" value="1" />'.$tree->printout_plain();

        return $output;
    }



    function setvalue($newvalue)
    {

    }

    function afterSave()
    {
        $selected = array();
        if (!$this->table->getRecordID()) {
            /* Don't save many-many records where ID is empty */
            return true;
        }

        if (Jojo::getPost('fm_'.$this->fd_field, false)) {
            /* if an update to this data was not POSTed explicitly, do nothing */
            foreach ($_POST as $k => $v) {
                if (strpos($k,'fm_'.$this->fd_field.'_') === 0) {
                    $selected[] = $v;
                }
            }
            $q = "DELETE FROM {" . $this->linktable . "} WHERE `" . $this->linkitemid . "` = ?";
            Jojo::deleteQuery($q, array($this->table->getRecordID()));

            foreach ($selected as $k => $v) {
                $q = "REPLACE INTO {" . $this->linktable . "} SET `" . $this->linkitemid . "` = ?, `" . $this->linkcatid . "` = ?";
                Jojo::insertQuery($q, array($this->table->getRecordID(), $v));
            }
        }
        return true;
    }
}