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
    function __construct($fielddata = array())
    {
        parent::__construct($fielddata);
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
        global $smarty;
        $itemid = $this->table->getRecordID();
        $tree = new hktree('tree');

        /* Get all the current selections */
        $selections = Jojo::selectAssoc("SELECT " . $this->linkcatid . " as `key`, " . $this->linkcatid . " as `value` FROM {" . $this->linktable . "} WHERE `" . $this->linkitemid . "` = ?", array($itemid));

        /* Add group by level one */
        $tableoptions = Jojo::selectRow("SELECT * FROM {tabledata} WHERE td_name = ? LIMIT 1", array($this->cattable));
        if ($tableoptions['td_group1'] != "") {
            $layer1 = Jojo::selectAssoc("SELECT " . $tableoptions['td_group1'] . ' as `key`, '  . $tableoptions['td_group1']." as `value` FROM {".$tableoptions['td_name']."} GROUP BY ".$tableoptions['td_group1']." ORDER BY ".$tableoptions['td_group1']."");
            foreach($layer1 as $k => $v) {
                $tree->addNode($v, 0, '<strong>' . $v . '</strong>');
            }
        }

        /* Main query */
        $displayfield   = Jojo::either($tableoptions['td_displayfield'], $tableoptions['td_primarykey']);
        $parentfield    = Jojo::either($tableoptions['td_parentfield'], $tableoptions['td_group1'], "'0'");
        $orderbyfield   = $tableoptions['td_orderbyfields'];
        $query = "SELECT
                    " . $tableoptions['td_primarykey'] . " AS id,
                    $displayfield AS display,
                    $parentfield AS parent
                 FROM
                    {" . $tableoptions['td_name'] . "}
                 ORDER BY
                  ". Jojo::onlyIf($tableoptions['td_group1'],' '.$tableoptions['td_group1'].', ').
                     Jojo::onlyIf($orderbyfield,' '.$orderbyfield.', ').
                     Jojo::onlyIf($displayfield,' '.$displayfield.', ').
                    ' display';
        $records = Jojo::selectQuery($query);
        
        $smarty->assign('fieldname', $this->fd_field);
        $smarty->assign('readonly', $this->fd_readonly);
        $smarty->assign('selections', $selections);
        $smarty->assign('records', $records);
        
        return $smarty->fetch('admin/fields/many2many.tpl');
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