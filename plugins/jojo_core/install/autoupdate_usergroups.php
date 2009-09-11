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

$default_td['usergroups'] = array(
        'td_name' => "usergroups",
        'td_primarykey' => "groupid",
        'td_displayfield' => "gr_name",
        'td_rolloverfield' => "gr_name",
        'td_orderbyfields' => "gr_name",
        'td_deleteoption' => "yes",
        'td_menutype' => "list",
    );

// Groupid Field
$default_fd['usergroups']['groupid'] = array(
        'fd_name' => "Groupid",
        'fd_type' => "text",
        'fd_required' => "yes",
        'fd_help' => "Lowercase, all one word",
        'fd_order' => "1",
    );

// Name Field
$default_fd['usergroups']['gr_name'] = array(
        'fd_name' => "Name",
        'fd_type' => "text",
        'fd_required' => "yes",
        'fd_size' => "30",
        'fd_help' => "A friendly name for the group - keep short and meaningful.",
        'fd_order' => "2",
    );
