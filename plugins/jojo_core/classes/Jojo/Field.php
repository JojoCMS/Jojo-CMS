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

$classdir = str_replace('\\', '/',dirname(__FILE__));

class Jojo_Field
{
    protected $value;
    protected $table = false;

    public $error = '';

    public  $postdata = array(); //for storing the raw POST data submitted by the UI

    public  $fd_table;
    public  $fd_field;
    public  $fd_name;
    public  $fd_sqltype;
    public  $fd_type;
    public  $fd_units;
    public  $fd_options;
    public  $fd_readonly;
    public  $fd_required;
    public  $fd_showlabel;
    public  $fd_default;
    public  $fd_help;
    public  $fd_maxsize;

    public  $fd_flags;

    /**
     * Constructor
     */
    function __construct($fielddata = array())
    {
        if (count($fielddata)) {
            $this->fd_table = $fielddata['fd_table'];
            $this->fd_field = $fielddata['fd_field'];
            $this->fd_name = $fielddata['fd_name'];
            $this->fd_sqltype = $fielddata['fd_sqltype'];
            $this->fd_type = $fielddata['fd_type'];
            $this->fd_options = $fielddata['fd_options'];
            $this->fd_required = $fielddata['fd_required'];
            $this->fd_showlabel = $fielddata['fd_showlabel'];
            $this->fd_default = $fielddata['fd_default'];
            $this->fd_help = $fielddata['fd_help'];
            $this->fd_maxsize = $fielddata['fd_maxsize'];
            $this->fd_tabname = $fielddata['fd_tabname'];
            $this->fd_readonly = isset($fielddata['fd_readonly']) ? $fielddata['fd_readonly'] : 0;

            /* create an array of flags */
            if (!empty($fielddata['fd_flags'])) {
                $flags = explode(',', $fielddata['fd_flags']);
                /* remove any empty flags, otherwise populate the array */
                foreach ($flags as $k => $v) {
                    if (empty($v)) {
                        unset($this->fd_flags[$k]);
                    } else {
                        $this->fd_flags[$v] = true;
                    }
                }
            }

            //for many-to-many relationship fields
            $this->linktable = $fielddata['fd_m2m_linktable'];
            $this->linkitemid = $fielddata['fd_m2m_linkitemid'];
            $this->linkcatid = $fielddata['fd_m2m_linkcatid'];
            $this->cattable = $fielddata['fd_m2m_cattable'];
            $this->linktableorderfield = isset($fielddata['fd_m2m_linkorderfield']) ? $fielddata['fd_m2m_linkorderfield'] : 'order';

            //echo "VAL=" . $this->fd_default . "<br>";
            if (empty($this->value)) {
                $this->value = $this->fd_default;
            }
            //these vars apply to some datatypes, not others
            if ($fielddata['fd_rows'] != "") {
                $this->rows = $fielddata['fd_rows'];
            }
            if ($fielddata['fd_cols'] != "") {
                $this->cols = $fielddata['fd_cols'];
            }
            if ($fielddata['fd_size'] != "") {
                $this->fd_size = $fielddata['fd_size'];
            }
            if ($fielddata['fd_size'] != "") {
                $this->size = $fielddata['fd_size'];
            }
            //size as an alternative to fd_size
            if ($fielddata['fd_units'] != "") {
                $this->units = $fielddata['fd_units'];
                $this->fd_units = $fielddata['fd_units'];
            }
            if ($fielddata['fd_maxvalue'] != "") {
                $this->fd_maxvalue = $fielddata['fd_maxvalue'];
            }
            if ($fielddata['fd_minvalue'] != "") {
                $this->fd_minvalue = $fielddata['fd_minvalue'];
            }
        }
    }

    /**
     * Set the table object that field belongs to
     */
    function setTable($table)
    {
        $this->table = $table;
    }

    /**
     * Return the dislay name of this field
     */
    function getDisplayName()
    {
        return ($this->fd_name) ? $this->fd_name : $this->fd_field;
    }

    /**
     * Return the privacy status of an option of this field 'Y' and 'N' mean the value has been specifically set by the user. 'y' and 'n' mean the value is being set from site defaults
     */
    function getPrivacy()
    {
        $flags = $this->getOption('flags');
        if (empty($flags['PRIVACY'])) {
            return false; //privacy does not apply to this field
        }

        if ($this->table->getOption('privacyfield')) {
            $data = Jojo::selectRow("SELECT `".$this->table->getOption('privacyfield')."` AS privacy FROM {".$this->table->getTableName()."} WHERE `".$this->table->getOption('primarykey') . "` = ? LIMIT 1", array($this->table->getRecordID()));
            if (!empty($data['privacy'])) {
                $privacy = unserialize($data['privacy']);
                //if (!empty($privacy['private'][$this->fd_field])) {return 'Y';} //Privacy has been set in user preferences to YES
                if (in_array($this->fd_field, $privacy['private'])) {return 'Y';}
                //if (!empty($privacy['public'][$this->fd_field]))  {return 'N';} //Privacy has been set in user preferences to NO
                if (in_array($this->fd_field, $privacy['public'])) {return 'N';}
            }
            if (!empty($flags['PRIVATE'])) {
                return 'y'; //Field defaults to being private
            } else {
                return 'n'; //Field defaults to being public
            }
        }
        return false;
    }

    /**
     * Return the value of an option of this field
     */
    function getOption($option)
    {
        $option = 'fd_' . $option;
        return $this->$option;
    }

    /**
     * Set the value of this field
     */
    function setValue($newvalue)
    {
        $this->value = $newvalue;
        return true;
    }

    /**
     * Used to set the value of this field that was stored in the database
     */
    function setValueFromDB($value)
    {
        $this->value = $value;
        return true;
    }

    /**
     * Get the value of this field
     */
    function getValue()
    {
        return $this->value;
    }

    /**
     * Event called after record is saved
     */
    function afterSave()
    {
        return true;
    }

    /**
     * Override this function if POST data needs to be stored with the field object
     */
    function setPostData($data = false)
    {
        return true;
    }

    /**
     * Validate the value of this record. Return error if the entry is not valid.
     */
    function validate()
    {
        $this->checkValue();
        if ($this->error) {
            return $this->error;
        }
        return true;
    }

    /**
     * Get error message for this record if there is one
     */
    function getError()
    {
        return $this->error;
    }

    /**
     * Do serverside error checking
     */
    function checkValue()
    {
        return true;
    }

    /**
     * Event called when a record is deleted.
     * This method should do any additional cleanups for the record.
     * The main example would be deleting an uploaded file if the record is
     * deleted (in which the defining class would override this declaration)
     */
    function onDelete()
    {
        return true;
    }

    /**
     * Return the HTML version of this record in the appropriate mode
     */
    function getHTML($mode = 'view')
    {
        switch ($mode) {
            case 'edit':
                return $this->displayEdit();
                break;

            case 'hidden':
                return $this->displayHidden();
                break;

            case 'view':
            default:
                return $this->displayView();
        }
    }

    /**
     * Return the Javascript to run after the record is reloaded
     */
    function getJS()
    {
        return $this->displayJs();
    }

    /**
     * Return the HTML needed to display this field as editable
     */
    function displayedit()
    {
        return "";
    }

    /**
     * Return the HTML needed to display this field as hidden
     */
    function displayhidden()
    {
        return sprintf('<input type="hidden" name="fm_%s" value="%s">',
                $this->fd_field,
                $this->value);
    }

    /**
     * Return the value of this field for view
     */
    function displayview()
    {
        return $this->value;
    }

    /**
     * Return the javascript to be run after a record is loaded
     */
    function displayJs()
    {
        return '';
    }

    /* Does nothing for most field types
    */
    function getfilesize()
    {
        return 0;
    }

    //does nothing for most field types
    function getimagedimensions()
    {
        return 0;
    }

    //does nothing for most field types
    function getrelativeurl()
    {
        return 0;
    }

    //does nothing for most field types
    function getabsoluteurl()
    {
        return 0;
    }

    /**
     * Is this field blank?
     */
    function isblank()
    {
        return ($this->value == "");
    }

    /**
     * Ensure a field type class is included
     *
     * Used to include a class in the factory and for classes to include their
     * dependencies
     *
     * @param string $field   The type of concrete Jojo_Field subclass to
     *                        include.
     */
    static function includeFieldType($field)
    {
        $class = 'Jojo_Field_' . $field;
        if (!class_exists($class)) {
            /* Check plugins for the field */
            foreach (Jojo::listPlugins('field/' . $field . '.php') as $pluginfile) {
                require_once $pluginfile;
            }
        }
    }

    /**
     * Attempts to return a concrete Jojo_Field instance.
     *
     * @param string $field   The type of concrete Jojo_Field subclass to
     *                        return.
     * @return Jojo_Field      The newly created concrete Jojo_Field instance,
     *                        or PEAR_Error on error.
     */
    static function factory($field, $fielddata = array(), $table = false)
    {
        Jojo_Field::includeFieldType($field);

        $class = 'Jojo_Field_' . $field;
        if (class_exists($class)) {
            $field = new $class($fielddata);
        } else {
            $field = new Jojo_Field($fielddata);
        }

        if ($table) {
            $field->setTable($table);
        }

        return $field;
    }

    /**
     * Get an array of field names in the table that should be hidden by default
     *
     * this is for radio groups and list field types where selecting a certain value will hide / show another field in the table
     *
     */
    function getHiddenFields()
    {
        return array();
    }
}