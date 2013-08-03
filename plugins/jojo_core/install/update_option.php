<?php
/**
 *                    Jojo CMS
 *                ================
 *
 * Copyright 2008 Jojo CMS
 *
 * See the enclosed file license.txt for license information (LGPL). If you
 * did not receive this file, see http://www.fsf.org/copyleft/lgpl.html.
 *
 * @author  Michael Cochrane <mikec@jojocms.org>
 * @license http://www.fsf.org/copyleft/lgpl.html GNU Lesser General Public License
 * @link    http://www.jojocms.org JojoCMS
 */

$default_td['option'] = array(
        'td_name' => "option",
        'td_primarykey' => "op_name",
        'td_displayfield' => "op_name",
        'td_rolloverfield' => "op_value",
        'td_orderbyfields' => "op_name",
        'td_deleteoption' => "yes",
        'td_menutype' => "list",
    );

// Category Field
$default_fd['option']['op_category'] = array(
        'fd_name' => "Category",
        'fd_type' => "text",
        'fd_order' => "1",
    );

// Type Field
$default_fd['option']['op_type'] = array(
        'fd_name' => "Type",
        'fd_type' => "text",
        'fd_order' => "2",
    );

// Options Field
$default_fd['option']['op_options'] = array(
        'fd_name' => "Options",
        'fd_type' => "text",
        'fd_order' => "3",
    );

// Default Field
$default_fd['option']['op_default'] = array(
        'fd_name' => "Default",
        'fd_type' => "text",
        'fd_order' => "4",
    );

// Plugin Field
$default_fd['option']['op_plugin'] = array(
        'fd_name' => "Plugin",
        'fd_type' => "text",
        'fd_order' => "5",
    );

// Description Field
$default_fd['option']['op_description'] = array(
        'fd_name' => "Description",
        'fd_type' => "text",
        'fd_order' => "6",
    );

// Displayname Field
$default_fd['option']['op_displayname'] = array(
        'fd_name' => "Displayname",
        'fd_type' => "text",
        'fd_order' => "7",
    );

// Name Field
$default_fd['option']['op_name'] = array(
        'fd_name' => "Name",
        'fd_type' => "text",
        'fd_required' => "yes",
        'fd_size' => "40",
        'fd_order' => "8",
    );

// Value Field
$default_fd['option']['op_value'] = array(
        'fd_name' => "Value",
        'fd_type' => "textarea",
        'fd_rows' => "4",
        'fd_cols' => "30",
        'fd_order' => "9",
    );
