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

class Jojo_Table {
    /* The name of the database table */
    private $table;

    /* The id of the current record */
    private $currentrecord;

    /* The options for the table data for this table */
    private $tabledata;

    /* Object of type Jojo_Field representing each field in this table */
    private $fields;

    /* cached id of the first permissions field in table */
    private $permsfield;

    /* Constructor */
    function __construct($table, $id = -1, $index = '')
    {
        $this->table = $table;

        /* Get info about this table */
        $tablerows = Jojo::selectRow("SELECT * FROM {tabledata} WHERE td_name = ?", array($table));
        if (isset($tablerows)) {
            foreach($tablerows as $k => $v) {
                $k = str_replace('td_', '', $k);
                $this->setOption($k, $v);
            }
        }

        /* Ensure options have values */
        if (!$this->getOption('displayfield')) {
            $this->setOption('displayfield', $this->getOption('primarykey'));
        }
        if (!$this->getOption('displayname')) {
            $this->setOption('displayname', $this->getOption('name'));
        }

        /* Get details about the fields in this table */
        $fieldlist = Jojo::selectQuery("SELECT * FROM {fielddata} WHERE fd_table = ? ORDER BY fd_order, fielddataid", array($table));
        $this->fields = array();
        foreach ($fieldlist as $field) {
            $this->fields[$field['fd_field']] = $field;
            if (!isset($this->permsfield) && ($field['fd_type'] == 'permissions')) {
                $this->permsfield = $field['fd_field'];
            }
        }

        if ($id > -1) {
            $this->getRecord($id);
        }
    }

    /**
     * Retrieve specific record in the database
     */
    function getRecord($id)
    {
        /* Create the query */
        $this->currentrecord = $id;
        if ($this->getOption('displayfield')) {
            $query = sprintf("SELECT *, %s AS DISPLAYFIELDVALUE FROM {%s} WHERE `%s` = ? LIMIT 1", $this->getOption('displayfield'), $this->table, $this->getOption('primarykey'));
        } else {
            $query = sprintf("SELECT * FROM {%s} WHERE `%s` = ? LIMIT 1", $this->table, $this->getOption('primarykey'));
        }

        /* Fetch the record from the database */
        $fieldvalues = Jojo::selectRow($query, array($id));

        if ($this->getOption('displayfield')) {
            $displayfielddata = Jojo::selectRow("SELECT fd_type, fd_options FROM {fielddata} WHERE fd_table = ? AND fd_field = ?", array($this->table, $this->getOption('displayfield')));
            $displayfieldtype = isset($displayfielddata['fd_type']) ? $displayfielddata['fd_type'] : '';
            if ($displayfieldtype == 'dbpluginpagelist') {
                $pageid = isset($fieldvalues[$this->getOption('displayfield')]) ? $fieldvalues[$this->getOption('displayfield')] : '';
                if ($pageid) {
                    $page = Jojo::selectRow("SELECT pg_title FROM {page} WHERE pageid = ? ", array($pageid));
                    $fieldvalues['DISPLAYFIELDVALUE'] = $page['pg_title'];
                }
            }
        }
        /* Set all the fields to their values */
        if ($fieldvalues) {
            foreach ($fieldvalues as $k => $v) {
                if ($k == 'DISPLAYFIELDVALUE') {
                    $this->setOption('displayvalue', $v);
                } else {
                    $this->getField($k)->setValueFromDB($v);
                }
            }
        }
    }

    /**
     * Return the ID of the current record
     */
    function getRecordID()
    {
        return $this->currentrecord;
    }

    /* Return the name of this table */
    function getTableName()
    {
        return $this->table;
    }

    /* Return the name of the first permissions field */
    function getPermsField()
    {
        if (isset($this->permsfield)) {
            return $this->permsfield;
        }
        return false;
    }

    /* Return the Default Permissions for this table */
    function getDefaultPermissions()
    {
        static $cache;

        if (!is_array($cache)) {
            preg_match_all("/([a-zA-Z]+)\.([a-zA-Z]+)[\s=]+([01])+/", $this->getOption('defaultpermissions'), $parts);
            $cache = array();
            foreach ($parts[0] as $k => $v) {
                $cache[$parts[1][$k]][$parts[2][$k]] = ($parts[3][$k] == 1);
            }
        }
        return $cache;
    }

    /* Return HTML view of the current record in this table */
    function getHTML($mode = 'view')
    {
        $html = '';
        $fieldHTML = array();
        foreach($this->getFieldNames() as $fieldname) {
            $field = $this->getField($fieldname);
            $fieldHTML[$fieldname] = array(
                                        'name'      => $field->getDisplayName(),
                                        'tabname'   => $field->getOption('tabname'),
                                        'showlabel' => $field->getOption('showlabel'),
                                        'html'      => $field->getHTML($mode),
                                        'js'        => $field->getJS($mode),
                                        'value'     => $field->getValue(),
                                        'error'     => $field->getError(),
                                        'type'      => $field->getOption('type'),
                                        'required'  => $field->getOption('required'),
                                        'flags'     => $field->getOption('flags'),
                                        'privacy'   => $field->getPrivacy(),
                                        );
        }
        return $fieldHTML;
    }

    /* Return HTML view of the current record in this table, for a single field only */
    function getFieldHTML($fieldname, $mode = 'view')
    {
        $field = $this->getField($fieldname);
        $fieldHTML = array(
                                    'name' => $field->getDisplayName(),
                                    'tabname'  => $field->getOption('tabname'),
                                    'showlabel'  => $field->getOption('showlabel'),
                                    'html' => $field->getHTML($mode),
                                    'error' => $field->getError(),
                                    'type' => $field->getOption('type'),
                                    'required' => $field->getOption('required'),
                                    );
        return $fieldHTML;
    }

    /* Return an array of all the field names */
    function getFieldNames()
    {
        return array_keys($this->fields);
    }

    /* Check all the fields and ensure there are no errors with any of them */
    function fieldErrors()
    {
        $errors = array();
        foreach($this->getFieldNames() as $fieldname) {
            $field = $this->getField($fieldname);
            $valid = $field->validate();
            if ($valid !== true) {
                $errors[$fieldname] = sprintf('%s: %s', $field->getDisplayName(), $valid);
            }
        }
        if (count($errors) > 0) {
            return $errors;
        }
        return false;
    }

    /* Save this record to the database */
    function saveRecord()
    {
        $result = '';

        /* For tables with varchar based primary keys */
        $newrecord = true;
        if ($this->currentrecord > 0) {
            $newrecord = false;
        } else {
            $sqltype = Jojo::getMySQLType($this->getTableName(), $this->getOption('primarykey'));
            if (strpos($sqltype,'varchar') !== false && $this->currentrecord != '') {
                $newrecord = false;
            }
        }

        /* Create SQL query */
        if (!$newrecord) {
            $query = "UPDATE {" . $this->table . "} SET ";
        } else {
            $query = "INSERT INTO {" . $this->table . "} SET ";
        }

        /* Add all the values */
        $values = array();
        foreach ($this->getFieldNames() as $fieldname) {
            $field = $this->getField($fieldname);
            $value = $field->getValue();
            switch ($field->getOption('type')) {
                case 'readonly':
                    break;

                case 'permissions':
                    /* permissions fields are stared as an array, but must be converted to text for the SQL */
                    if (is_array($value)) {
                        $strvalue = '';
                        foreach ($value as $k => $v) {
                            $strvalue .= "$k = $v\n";
                        }
                    } else {
                        $strvalue = $value;
                    }

                    $query .= sprintf(' `%s` = ?,', $fieldname);
                    $values[] = $strvalue;
                    break;

                default:
                    $query .= sprintf(' `%s` = ?,', $fieldname);
                    $values[] = $value;
                    break;
            }
        }
        $query = rtrim($query, ', ');

        if (!$newrecord) {
            /* Update record */
            $query .= " WHERE `" . $this->getOption('primarykey') . "`= ? LIMIT 1";
            $values[] = $this->currentrecord;
            $res = Jojo::updateQuery($query, $values);
            if ($res === false) {
                return "An error occured updating the record.";
            }
        } else {
            /* Insert new record */
            $this->currentrecord = Jojo::insertQuery($query, $values);
        }

        /* Output result to the user */
        if (!$newrecord) {
            $result = $this->getOption('displayname') . " updated.";
        } else {
            $result = "A new " . $this->getOption('displayname') . " has been added. ID#" . $this->currentrecord;
        }

        /* Run any post-save procedures */
        foreach ($this->getFieldNames() as $fieldname) {
            $this->getField($fieldname)->afterSave();
        }

        return $result;
    }

    function deleteRecord()
    {
        global $_USERID;

        /* Create the query */
        $query = sprintf("DELETE FROM {%s} WHERE `%s`= ? LIMIT 1",
                            $this->table,
                            $this->getOption('primarykey')
                        );
        $values = array($this->currentrecord);

        /* Delete the record */
        if (Jojo::deleteQuery($query, $values)) {
            /* Run any post-delete procedures */
            $res = true;
            foreach ($this->getFieldNames() as $fieldname) {
                $res = $res && $this->getField($fieldname)->onDelete();
            }
            return $res;
        }

        return false;
    }

    /* Set the value of an option for this table */
    function setOption($option, $value)
    {
        $this->tabledata[$option] = $value;
    }

    /* Return the value of an option for this table from table data */
    function getOption($option)
    {
        return isset($this->tabledata[$option]) ? $this->tabledata[$option] : false;
    }

    /**
     * Return a field by name
     */
    function getField($field)
    {
        /* Does the field exist? */
        if (!isset($this->fields[$field])) {
            /* No */
            return false;
        }

        /* Is this a field object yet? */
        if (is_array($this->fields[$field])) {
            /* No, create it */
            $this->fields[$field] = Jojo_Field::factory($this->fields[$field]['fd_type'], $this->fields[$field], $this);
        }

        /* Return the field */
        return $this->fields[$field];
    }

    /**
     * Return the first field of a matching type
     */
    function getFieldByType($type)
    {
        foreach ($this->getFieldNames() as $fieldname) {
            $f = $this->getField($fieldname);
            if ($f instanceof $type) {
                return $f;
            }
        }
        return false;
    }

    /* Set the value of a field */
    function setFieldValue($field, $value)
    {
        $field = $this->getField($field);
        if ($field) {
            $field->setPostData($value);
            return $field->setValue($value);
        }
        return false;
    }

    /* Get the value of a field */
    function getFieldValue($field)
    {
        $field = $this->getField($field);
        if ($field) {
            return $field->getValue();
        }
        return false;
    }

    /* Return an instance of table */
    static function &singleton($table = null)
    {
        static $instances;

        if (!isset($instances)) {
            $instances = array();
        }

        if (empty($instances[$table])) {
            $instances[$table] = new Jojo_table($table);
        }

        return $instances[$table];
    }

    /* DTree with ajax needs to be treated carefully, because it returns script, not HTML */
    function createlist($menutype = "tree", $ajax = false, $prefix = 'edit', $selectednode=false)
    {
        if ($menutype == "auto" && ($this->getOption('parentfield') || $this->getOption('group1'))) {
            $menutype = "tree";
        } elseif ($menutype == "auto") {
            $menutype = 'list';
        }

        if ($menutype == 'tree') {
            global $smarty;
            $smarty->assign('draggable', ($this->getOption('group1') || $this->getOption('parentfield')) && $this->getFieldByType('Jojo_Field_Order'));
            $smarty->assign('table', $this->table);
            $smarty->assign('displayname', $this->getOption('displayname'));
            return $smarty->fetch('admin/edit-ajaxtree.tpl');
        } elseif ($menutype == 'list' || $menutype == 'recursivePath' || $menutype == 'array') {
            global $_USERGROUPS;
            $idfield = $this->getOption('primarykey');
            $displayfield  =  Jojo::either($this->getOption('displayfield'), $this->getOption('primarykey'));
            $parentfield   =  Jojo::either($this->getOption('parentfield'), $this->getOption('group1'), "'0'");
            $categorytable = $this->getOption('categorytable');
            $categoryfield =  Jojo::either($this->getOption('categoryfield'), $this->getOption('group1'), "'0'");
            $orderbyfield  = $this->getOption('orderbyfields');
            $group1field   = $this->getOption('group1');
            $group2field   = $this->getOption('group2');
            $golivefield   = $this->getOption('golivefield');
            $expiryfield   = $this->getOption('expiryfield');
            $activefield   = $this->getOption('activefield');

            //filter results
            $datafilter =  Jojo::either($this->getOption('filterby'), '1');
            $groupownerfilter = '';
            if ($this->getOption('groupowner') != '') {
                $groupownerfilter = ' AND ( ';
                foreach ($_USERGROUPS as $g) {
                    $groupownerfilter .= "(" . $this->getOption('groupowner') . "='" . $g . "') OR";
                }
                $groupownerfilter .= " " . $this->getOption('groupowner') . "='' )";
            }
            if (is_array($_USERGROUPS) && in_array('admin', $_USERGROUPS)) {
                $groupownerfilter = '';
            }
            $rolloverfield =  Jojo::either($this->getOption('rolloverfield'), "''");
            //this will be used for output
            $html = "";

            // - this is used to pull down the main groupings - takes an extra query
            if ($this->getOption('group2') != "") {
                // Done in main loop below, no extra query needed.
            } elseif ($this->getOption('group1') != "") {
                //Layer1 represents table structure where the first level is the grouping, then individual records underneath
                $layer1 = Jojo::selectQuery("SELECT " . $this->getOption('group1') . " FROM {" . $this->table . "} GROUP BY " . $this->getOption('group1') . " ORDER BY " . $this->getOption('group1') . "");
            }

            //Main query
            $query = "SELECT $idfield AS id, $displayfield AS display, $parentfield AS parent, $categoryfield AS categoryfield, $rolloverfield AS rollover "
            .  Jojo::onlyIf($group1field, ", " . $group1field . " AS group1")
            .  Jojo::onlyIf($group2field, ", " . $group2field . " AS group2")
            .  Jojo::onlyIf($golivefield, ", " . $golivefield . " AS golive")
            .  Jojo::onlyIf($expiryfield, ", " . $expiryfield . " AS expiry")
            .  Jojo::onlyIf($activefield, ", " . $activefield . " AS active")
            . " FROM {" . $this->table . "} WHERE $datafilter " . $groupownerfilter . " ORDER BY " .  Jojo::onlyIf($group1field, " " . $group1field . ", ")
            .  Jojo::onlyIf($orderbyfield, " " . $orderbyfield . ", ")
            . " display";
            $records = Jojo::selectQuery($query);

            if ($this->getOption('displayfield')) {
                $displayfielddata = Jojo::selectRow("SELECT fd_type, fd_options FROM {fielddata} WHERE fd_table = ? AND fd_field = ?", array($this->table, $this->getOption('displayfield')));
                $displayfieldtype = isset($displayfielddata['fd_type']) ? $displayfielddata['fd_type'] : '';
                $displayfieldoptions = isset($displayfielddata['fd_options']) ? $displayfielddata['fd_options'] : '';
                if ($displayfieldtype == 'dbpluginpagelist') {
                    $displaytitles = Jojo::selectAssoc("SELECT pageid AS id, pageid, pg_title, pg_language FROM {page} WHERE pg_link = ? ", array($displayfielddata['fd_options']));
                    foreach ($records as &$r) {
                        $r['display'] = isset($displaytitles[$r['display']]['pg_title']) ? $displaytitles[$r['display']]['pg_title'] . (_MULTILANGUAGE ? ' (' . $displaytitles[$r['display']]['pg_language'] . ')' : '') : 'page missing';
                    }
                }
           }
            //get the TABLEDATA options for the category
            $catidfield = '';
            $catoptions = Jojo::selectQuery("SELECT * FROM {tabledata} WHERE td_name = ? LIMIT 1", array($categorytable));
            if (isset($catoptions[0])) {
                $catidfield = $catoptions[0]['td_primarykey'];
                $catdisplayfield     =  Jojo::either($catoptions[0]['td_displayfield'], $catoptions[0]['td_primarykey']);
                $catparentfield      =  Jojo::either($catoptions[0]['td_parentfield'], $catoptions[0]['td_group1'], "'0'");
                $catorderbyfield     = $catoptions[0]['td_orderbyfields'];
                $catrolloverfield    =  Jojo::either($catoptions[0]['td_rolloverfield'], "'0'");
                $catgroupownerfilter = '';
                if ($catoptions[0]['td_groupowner'] != '') {
                    $catgroupownerfilter = ' AND ( ';
                    foreach ($_USERGROUPS as $g) {
                        $catgroupownerfilter .= "(" . $catoptions[0]['td_groupowner'] . "='" . $g . "') OR";
                    }
                    $catgroupownerfilter .= " " . $catoptions[0]['td_groupowner'] . "='' )";
                }
                if (is_array($_USERGROUPS) && in_array('admin', $_USERGROUPS)) {
                    $catgroupownerfilter = '';
                }
            }

            /* Category query */
            if ($catidfield != '') {
                //TODO: Make this query a join so it hides empty categories (if this would be useful)
                $query = "SELECT $catidfield AS id, $catdisplayfield AS display, $catparentfield AS parent, $catrolloverfield AS rollover
                FROM {" . $categorytable . "}
                WHERE 1 " . $catgroupownerfilter .  Jojo::onlyif($catorderbyfield, "ORDER BY " . $catorderbyfield . "");
                $catrecords = Jojo::selectQuery($query);
            }

            $tree = new hktree($this->table);

            /* Add group 1 categories to HKTree */
            $groups = self::_transpose($records);
            if (isset($groups['group1']) && is_array($groups['group1'])) {
                $groups['group1'] = array_unique($groups['group1']);

                foreach ($groups['group1'] as $group1) {
                    $status = 'folder';
                    $tree->addnode('c' . $group1, 0, $group1, '', '', "setH1('Loading...');frajax('load', '" . $this->table . "',this.value); return false;", '', '', '', $status);
                }
            }

            /* Add categories to HKTree */
            if (isset($catrecords) && is_array($catrecords)) {
                for ($i = 0; $i < count($catrecords); $i++) {
                    $status = 'folder';
                    $cat_parent = !empty($catrecords[$i]['parent']) ? 'c'.$catrecords[$i]['parent'] : 0;
                    $tree->addnode('c' . $catrecords[$i]['id'], $cat_parent, $catrecords[$i]['display'], '', '', '', '', '', '', $status);
                }
            }

            /* Add items to HKTree */
            for ($i = 0; $i < count($records); $i++) {
                if (($records[$i]['categoryfield'] != "") and ($records[$i]['categoryfield'] != "0")) {
                    $parent = "c" . $records[$i]['categoryfield'];
                } elseif (isset($records[$i]['group1']) && $records[$i]['group1'] != '') {
                    $parent = "'" . strtolower('c' . $records[$i]['group1']) . "'";
                } elseif ($records[$i]['parent'] != "") {
                    $parent = "'" . strtolower($records[$i]['parent']) . "'";
                } else {
                    $parent = "0";
                }
                $link = $prefix . "/" . $this->table . "/" . $records[$i]['id'] . "/";

                if (!$golivefield && !$expiryfield) {
                    $status = '';
                } elseif ($golivefield && $expiryfield) {
                    $now = strtotime('now');
                    if (($now < $records[$i]['golive']) || (($now > $records[$i]['expiry']) && ($records[$i]['expiry'] > 0))) {
                        $status = 'expired';
                    } else {
                        $status = '';
                    }
                } else {
                    $status = '';
                }
                if (!empty($activefield) && (($records[$i]['active'] == 'no') || ($records[$i]['active'] == 'inactive') ||($records[$i]['active'] == '0') || ($records[$i]['active'] === 0) || ($records[$i]['active'] == ''))) {
                    $status = 'expired';
                }

                /* check permissions */
                global $_USERGROUPS;
                $perms = new Jojo_Permissions();
                $perms->getPermissions($this->table, $records[$i]['id']);
                /*
                A quick hack so that the list type will show record in the list after a frajax save.
                Permissions are only checked on 'page' records, not article / faq / anything else records.
                */
                if (($this->table != 'page') || $perms->hasPerm($_USERGROUPS, 'view')) {
                    /* add the node */
                    $tree->addnode($records[$i]['id'], $parent, $records[$i]['display'], $link, '', "frajax('load', '" . $this->table . "', '" . $records[$i]['id'] . "'); return false;", '', $records[$i]['rollover'],'',$status);
                }
            }

            if ($selectednode !== false) $tree->selected = $selectednode;

            /* output the tree structure */
            if ($menutype == 'recursivePath') {
                return $tree->recursivePath();
            } elseif ($menutype == 'array') {
                return $tree->printout_array();
            } else {
                $html .= "<form id=\"jump\" name=\"jump\" method=\"post\" action=\"" . $prefix . "/" . $this->table . "/\">\n<select name=\"jumpid\" size=\"25\" style=\"width: 100%\" onchange=\"frajax('load', '" . $this->table . "',this.value); return false;\">\n";

                $html .= $tree->printout_select(10, $this->currentrecord);
                $html .= "</select>";
                $html .= "</form>\n";
            }

            return $html;
        } else {
            return "Unknown menu type selected, select a valid menu type in tabledata.";
        }
    }

    /* Converts the format of a 2D array from $arr[a][b] to $arr[b][a] - used for sorting the array*/
    function _transpose($arr)
    {
        $newarr = array();
        foreach ($arr as $keyx => $valx) {
            foreach ($valx as $keyy => $valy) {
                $newarr[$keyy][$keyx] = $valy;
            }
        }
        return $newarr;
    }

}