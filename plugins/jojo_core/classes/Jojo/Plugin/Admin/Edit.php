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

class Jojo_Plugin_Admin_Edit extends Jojo_Plugin
{
    function _getContent()
    {

        global $smarty, $_USERGROUPS, $templateoptions;
        $content = array('javascript' => '');

        /* Make sure we are on http / https for viewing this page, as per the page setting */
        if ((_PROTOCOL == 'http://') && ($this->page['pg_ssl'] == 'yes'))
           Jojo::redirect(_SECUREURL.'/'.$this->page['pg_url'].'/');

        if ((_PROTOCOL == 'https://') && ($this->page['pg_ssl'] == 'no'))
           Jojo::redirect(_SITEURL.'/'.$this->page['pg_url'].'/');

        /* do not apply content variables to the edit pages. This was causing
        problems with the vars inside the textareas being replaced */
        Jojo::removeFilter('content', 'applyContentVars', 'jojo_core');

        $templateoptions['dateparse'] = true;
        $smarty->assign('templateoptions', $templateoptions);

        /* The the table and id */
        $urlParts = explode('/', _SITEURI);
        $lastPart = $urlParts[count($urlParts) - 1];
        if (count($urlParts) >= 2 && preg_match('%^([0-9]+)$%', $lastPart)) {
            /* Last part of url is all digits, assuming it's a record id */
            $id = $lastPart;
            $t = $urlParts[count($urlParts) - 2];
            $prefix = implode('/', array_splice($urlParts, 0, count($urlParts) - 2));
        } else {
            /* Last part isn't all digits, must be the table name */
            $id = 0;
            $t = $lastPart;
            $prefix = implode('/', array_splice($urlParts, 0, count($urlParts) - 1));
        }
        $smarty->assign('prefix', $prefix);

        if (!$t) {
            echo "Unable to open table";
            exit();
        }

        /* Create table object */
        $table = &Jojo_Table::singleton($t);
        if ($id > 0) {
            $table->getRecord($id);
            $smarty->assign('requested_id', $id);
        }

        /* Fetch list of tabs from fields */
        $data = Jojo::selectQuery("SELECT
                                    fd_tabname AS tabname
                                  FROM
                                    {fielddata}
                                  WHERE
                                    fd_table = ?
                                  ORDER BY
                                    fd_tabname",
                                  array($t));

        /* Build array of tabs from all the fields */
        foreach ($data as $i => $v) {
            $tabname = $v['tabname'];

            /* Add to array */
            if (!isset($tabnames[$tabname])) {
                $tabnames[$tabname] = array(
                                        'tabname' => $tabname,
                                        );
            }
        }

        /* Sort the tabs */
        ksort($tabnames);

        /* Let smarty know about the tab names */
        $_tabnames = array_values($tabnames);
        $smarty->assign('tabnames', $_tabnames);
        $smarty->assign('defaulttab', $_tabnames[0]['tabname']);

        /* Assign Table help to template */
        if ($table->getOption('help') != "") {
            $smarty->assign('help', $table->getOption('help'));
            $smarty->assign('numtabs', count($tabnames) + 1);
        } else {
            $smarty->assign('numtabs', count($tabnames));
        }

        Jojo_Plugin_Admin::adminMenu();

        /* Smarty Assignments */
        $smarty->assign('wysiwyg', Jojo::getOption('wysiwyg'));
        $smarty->assign('tablename', $table->getTableName());
        $smarty->assign('currentrecord', $table->getRecordID());
        $fieldsHTML = $table->getHTML('edit');

        // Let's search the FieldsHTML for body and body_code fields
        // Then escape the template [[ and ]] tags
        // This will stop smarty from interpreting these tags during the rendition of the page.
        $esc_on = "&#91;&#91;";
        $esc_off = "&#93;&#93;";
        foreach ( $fieldsHTML as $fname => &$fcontent) {
            if (($fname == 'pg_body') || ($fname == 'pg_body_code')) {
                $fcontent['html'] = preg_replace('#\[\[#', $esc_on, $fcontent['html']);
                $fcontent['html'] = preg_replace('#\]\]#', $esc_off, $fcontent['html']);
            }
        }

        $smarty->assign('fields', $fieldsHTML);

        $wysiwyg_editors = array();
        foreach ($fieldsHTML as $k => $f) {
            if ($f['type'] == 'bbeditor') {
                $smarty->assign('includebbeditor', true);
            } elseif ($f['type'] == 'wysiwygeditor') {
                $smarty->assign('includewysiwygeditor', true);
                $wysiwyg_editors[] = $k;
            } elseif ($f['type'] == 'texteditor') {
                $smarty->assign('includewysiwygeditor', true);
                $wysiwyg_editors[] = $k;
            }
            if (!empty($f['js'])) $content['javascript'] .= "\n".$f['js'];
        }
        $smarty->assign('wysiwyg_editors', $wysiwyg_editors);
        $smarty->assign('javascript', $content['javascript']);

        // Create button text
        if ($table->getRecordID() > 0) {
            $smarty->assign('savebutton', "Update");
        } else {
            $smarty->assign('savebutton', "Add");
        }
        if ($table->getOption('deleteoption') == "yes") {
            $smarty->assign('deletebutton', "Delete");
        }
        if ($table->getOption('addsimilar') == "yes") {
            $smarty->assign('addsimilarbutton', "Add Similar");
        }
        if ($table->getOption('parentfield') != "") {
            $smarty->assign('addchildbutton', "Add Child");
        }

        $smarty->assign('displayvalue',  Jojo::either($table->getOption('displayvalue'), "New " . $table->getOption('displayname')));
        $smarty->assign('displayname', $table->getOption('displayname'));

        $smarty->assign('recordlist', $table->createlist($table->getOption('menutype'), false, $prefix, $id));

        $smarty->assign('message', isset($message) ? $message : '');

        $smarty->assign('addnewlink', $prefix . "/" . $table->getTableName()."/");
        $smarty->assign('formactionlink', $prefix . "/" . $table->getTableName()."/");


        if ($this->perms->hasPerm($_USERGROUPS, 'add')) {
            $smarty->assign('addbutton', true);
        }

        /* check if Xinha JS is cached on server or not */
        if (!Jojo::fileexists(_CACHEDIR . '/external/xinha/XinhaCore.js')) {
            $smarty->assign('xinhatimeout', 10000);
        }

        /* prepare data for image widget */
        $this->tree = new hktree();
        $this->tree->addnode('', 0, 'downloads');


        /* Get list of folders for Image upload dropdown */
        $this->rec_scandir(_DOWNLOADDIR);
        
        $xinha_plugins = array (
                        'ContextMenu',
                        'Stylist',
                        'FindReplace',
                        'PasteText',
                        'ExtendedFileManager',
                        'TableOperations',
                        'InsertAnchor',
                        'HtmlEntities'
                    );

        $xinha_plugins = $sitemap = Jojo::applyFilter('xinha_plugins', $xinha_plugins);
        $smarty->assign('xinha_plugins', $xinha_plugins);

        $smarty->assign('foldertreeoptions',$this->tree->printout_select());

        $content['content'] = $smarty->fetch('admin/edit.tpl');
        $head               = array();
        $head[]             = $smarty->fetch('external/date_input_head.tpl');
        $head[]             = $smarty->fetch('admin/xinha_head.tpl');
        $head               = Jojo::applyFilter('admin_edit_head', $head); //allow plugins to add their piece
        $content['head']    = implode("\n", $head);

        // Now we restore our previously escaped tags to normal.
//        $content['content'] = ereg_replace('\*\*esc\*\*', '[[', $content['content']);
//        $content['content'] = ereg_replace('##esc##', ']]', $content['content']);

        return $content;
    }

    function getCorrectUrl()
    {
        $uri = Jojo::getAdminUri($_SERVER['REQUEST_URI']);

        //Assume the URL is correct
        return _PROTOCOL . $_SERVER['HTTP_HOST'] . $uri;
    }

    function rec_scandir($dir)
    {
       $files = array();
       if ( $handle = opendir($dir) ) {
          $shortdir = str_replace(_DOWNLOADDIR,'',$dir);
           while ( ($file = readdir($handle)) !== false ) {
               if ( $file != '..' && $file != '.' && $file != '.svn' ) {
                   if ( is_dir(rtrim($dir,'/') . "/" . $file) ) {
                           $fullpath = rtrim($dir,'/') . "/" . $file;
                           $shortfullpath = str_replace(_DOWNLOADDIR,'',$fullpath);
                       $this->rec_scandir(rtrim($dir,'/') . "/" . $file);
                       $this->tree->addnode(trim($shortfullpath), trim($shortdir,'/'), $file);
                       //echo "adding " . $fullpath . " in $dir<br />";
                   }
               }
           }
           closedir($handle);
       }
    }
}