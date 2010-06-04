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

Jojo::runHook('admin_action_start');

function refreshMenu($table, $t, $frajax) {
    $id = $table->getRecordID();

    $menutype = $table->getOption('menutype');
    if ($menutype == "auto" && ($table->getOption('parentfield') || $table->getOption('group1'))) {
        $menutype = "tree";
    } elseif ($menutype == "auto") {
        $menutype = 'list';
    }

    switch($menutype) {
        case 'tree':
            $tree = $table->createlist('recursivePath', true, 'admin/edit', $id);
            $script = '';
            if (isset($tree["$id "])) {
                /* Open all the nodes and select this item */
                $script = "**callback**";
                foreach(explode('/', $tree["$id "]) as $parentid) {
                    if (!$parentid) {
                        continue;
                    }
                    if ($parentid == $id) {
                        break;
                    }
                    $script = str_replace("**callback**", "\n parent.jQuery.tree.reference('treediv').open_branch('#$parentid', true, function() {**callback**});", $script);
                }
                $script = str_replace("**callback**", "\n parent.jQuery.tree.reference('treediv').select_branch('#$id');\n", $script);
            }
            $frajax->script(" parent.canLoad = false;\n parent.jQuery.tree.reference('treediv').close_all();\n parent.jQuery.tree.reference('treediv').refresh(); $script\n parent.canLoad = true;");
            break;

        case 'list':
            /* Update HTML list */
            $tree = $table->createlist($table->getOption('menutype'), true, _ADMIN.'/edit', $id);
            $frajax->assign('recordlist', 'innerHTML', $tree);
        break;
    }
}

$frajax = new frajax(true);
$frajax->title = 'Admin Action - ' . _SITETITLE;
$frajax->sendHeader();
$frajax->scrollToTop();
$frajax->assign("h1", "innerHTML", 'Processing...');

$content = array();

/* Ensure users of this function have access to the admin page */
$t = Jojo::getFormData('t');
$page = Jojo_Plugin::getPage(Jojo::parsepage('admin/edit/' . $t));
if (!$page->perms->hasPerm($_USERGROUPS, 'view')) {
    $frajax->alert('Access Denied. Trying reloading the page');
    exit();
}


$id = Jojo::getFormData('id', 0);

if (!$t) {
    $frajax->alert('Unable to open class');
    exit();
}

/* Create table object */
$table = &Jojo_Table::singleton($t);
if ($id > 0) {
    $table->getRecord($id);
}

/* for tables with varchar based primary keys */
$sqltype = Jojo::getMySQLType($table->getTableName(), $table->getOption('primarykey'));
if (strpos($sqltype,'varchar') !== false && $id != '') {
    $table->getRecord($id);
}

/* Save button pressed */
if (Jojo::getPost('btn_save', false) || Jojo::getPost('saveform', false)) {
    $errors = '';

    /* Retrieve all values from form and set the field values */
    foreach ($table->getFieldNames() as $fieldname) {
        if (Jojo::getFormData('fm_' . $fieldname, false) !== false) {
            $table->setFieldValue($fieldname, Jojo::getFormData('fm_' . $fieldname));
        }
    }

    /* Check for errors */
    $errors = $table->fieldErrors();
    if (is_array($errors)) {
        /* Error with one of the values */
        $frajax->script('parent.$("#error").html("<h4>Error</h4>The following errors were found...<br />' . implode('<br />', $errors).'").fadeIn("slow");');
        $frajax->script('parent.$("#message").fadeOut();');
        $frajax->assign("h1", "innerHTML", 'Save Error');
        exit();
    } else {
        /* Save record */
        $frajax->assign("h1", "innerHTML", 'Saving...');

        $res = $table->saveRecord();
        if ($res !== false) {
            /* Success message */
            $frajax->script('parent.$("#h1").html("Save successful.").show("fast");');
            $frajax->script('parent.$("#message").html("<h4>Jojo CMS</h4>'.$res.'").fadeIn("slow").fadeTo(5000, 1).fadeOut("slow");');

            /* Clear the content cache after saving */
            Jojo::clearCache();

            refreshMenu($table, $t, $frajax);

            $frajax->assign('id', 'value', $table->getRecordID());

            $frajax->script('parent.$("#btn_addsimilar").fadeIn("fast");');
            $frajax->script('parent.$("#btn_delete").fadeIn("fast");');
            $frajax->script('parent.$("#btn_copy").fadeIn("fast");');
            $frajax->script('parent.$("#btn_addchild").fadeIn("fast");');

            /* hook for plugins to do something after save is complete */
            Jojo::runHook('admin_action_after_save_'.$table->getTableName());
            Jojo::runHook('admin_action_after_save');


        } else {
            /* Error saving */
            $frajax->script('parent.$("#error").html("<h4>Error</h4> Saving failed").fadeIn("slow");');
            $frajax->assign("h1", "innerHTML", 'Save Error');
        }
    }

    /* Reload the record from the database */
    $table->getRecord($table->getRecordID());

    /* Update display */
    $frajax->assign("h1", "innerHTML",  Jojo::either($table->getOption('displayvalue'), "New " . $table->getOption('displayname')));

    /* Update form values */
    foreach ($table->getHTML('edit') as $fieldname => $f) {
        if ($f['type'] == 'texteditor') {
            $frajax->assign("fm_" . $fieldname, "value", $f['value']);
            $frajax->script('parent.setTextEditorContent("fm_' . $fieldname.'");');
        } else {
            $frajax->assign("wrap_" . $fieldname, "innerHTML", $f['html']);
            if (!empty($f['js'])) {
                $js = str_replace('$(', 'parent.$(', $f['js']); //the jQuery object does not exist in the iframe, so need to reference the parent.
                $frajax->script($js);
            }
        }
    }
}

/* Delete button pressed */
if (Jojo::getPost('btn_delete', false)) {
    Jojo::runHook('admin_action_pre_delete', array($table));
    if ($table->deleteRecord() == true) {
        Jojo::runHook('admin_action_delete_success', array($table));
        $frajax->redirect(_SITEURL . '/' . Jojo::getFormData('prefix') . '/' . $t . '/');
    } else {
        /* Error deleting */
        $frajax->script('parent.$("#error").html("<h4>Error</h4>Deleting failed").fadeIn("slow");');
        $frajax->assign("h1", "innerHTML", 'Delete Error');
    }
    exit();
}

/* Add Similar button pressed */
if (Jojo::getPost('btn_addsimilar', false)) {
    /* Clear primary key */
    $primaryKey = $table->getOption('primarykey');
    $table->setFieldValue($primaryKey, '');

    $frajax->script('parent.$("#message").html("<h4>Jojo CMS</h4>Please change the ' . $table->getOption('displayname') . ' fields as appropriate and press save to create a new ' . $table->getOption('displayname') . '.").fadeIn("slow");');
    $frajax->assign("h1", "innerHTML", 'Copy of ' . $table->getOption('displayvalue'));

    $allfields = $table->getHTML('edit');
    $start = Jojo::timer();
    $i = 0;
    foreach ($allfields as $fieldname => $f) {
        /* if execution is going to be especially slow, display some progress information */
        $i++;
        if (Jojo::timer($start) > 1) {
            $percent = floor($i / count($allfields) * 100);
            $frajax->assign("h1", "innerHTML", 'Loading ' . $percent . '%...');
            $start = Jojo::timer();
        }

        if ($f['type'] == 'texteditor') {
            /* find out if editor is html, bb, or other */
            $editortype = 'html';
            if (preg_match('/<!-- \\[editor:([a-z]*?)\\] -->/', $f['html'], $result)) {
                $editortype = $result[1];
            }
            $frajax->script("parent.$('#type_fm_" . $fieldname . "_html').attr('checked','');");
            $frajax->script("parent.$('#type_fm_" . $fieldname . "_bb').attr('checked','');");
            $frajax->script("parent.$('#type_fm_" . $fieldname . "_" . $editortype . "').attr('checked','checked');");
            $frajax->assign("fm_" . $fieldname, "value", $f['value']);
            if ($editortype=='bb') {
                $frajax->script("parent.$('#editor_" . $fieldname . "_html').hide(); parent.$('#editor_" . $fieldname . "_bb').show();");
            } else {
                $frajax->script("parent.$('#editor_" . $fieldname . "_bb').hide(); parent.$('#editor_" . $fieldname . "_html').show();");
            }
            $frajax->script('parent.setTextEditorContent("fm_' . $fieldname . '");');
        } else {
            $frajax->assign("wrap_" . $fieldname, "innerHTML", $f['html']);
            if (!empty($f['js'])) {
                $js = str_replace('$(', 'parent.$(', $f['js']); //the jQuery object does not exist in the iframe, so need to reference the parent.
                $frajax->script($js);
            }
        }
    }
    $frajax->assign("id", "value", '');

    $frajax->script('parent.$("#message").fadeOut("fast");');
    $frajax->script('parent.$("#btn_addsimilar").fadeOut("fast");');
    $frajax->script('parent.$("#btn_delete").fadeOut("fast");');
    $frajax->script('parent.$("#btn_addchild").fadeOut("fast");');

}

/* Add Child button pressed */
if (Jojo::getPost('btn_addchild', false)) {
    $table = new Jojo_Table($t);
    $table->setFieldValue($table->getOption('parentfield'), $id);

    $frajax->script('parent.$("#message").html("<h4>Jojo CMS</h4>New page added as a child to the previous page.").fadeIn("slow");');
    $frajax->assign("h1", "innerHTML", 'New Child');

    $allfields = $table->getHTML('edit');
    $start = Jojo::timer();
    foreach ($allfields as $fieldname => $f) {
        /* if execution is going to be especially slow, display some progress information */
        $i++;
        if (Jojo::timer($start) > 1) {
            $percent = floor($i / count($allfields) * 100);
            $frajax->assign("h1", "innerHTML", 'Loading '.$percent.'%...');
            $start = Jojo::timer();
        }

        if ($f['type'] == 'texteditor') {
            /* find out if editor is html, bb, or other */
            $editortype = 'html';
            if (preg_match('/<!-- \\[editor:([a-z]*?)\\] -->/', $f['html'], $result)) {
            $editortype = $result[1];
            }
            $frajax->script("parent.$('#type_fm_".$fieldname."_html').attr('checked','');");
            $frajax->script("parent.$('#type_fm_".$fieldname."_bb').attr('checked','');");
            $frajax->script("parent.$('#type_fm_".$fieldname."_".$editortype."').attr('checked','checked');");
            $frajax->assign("fm_" . $fieldname, "value", $f['value']);
            if ($editortype=='bb') {
                $frajax->script("parent.$('#editor_".$fieldname."_html').hide(); parent.$('#editor_".$fieldname."_bb').show();");
            } else {
                $frajax->script("parent.$('#editor_".$fieldname."_bb').hide(); parent.$('#editor_".$fieldname."_html').show();");
            }
            $frajax->script('parent.setTextEditorContent("fm_' . $fieldname.'");');
        } else {
            $frajax->assign("wrap_" . $fieldname, "innerHTML", $f['html']);
            if (!empty($f['js'])) {
                $js = str_replace('$(', 'parent.$(', $f['js']); //the jQuery object does not exist in the iframe, so need to reference the parent.
                $frajax->script($js);
            }
        }
    }

    $frajax->assign("id", "value", '');
    $frajax->script('parent.$("message").hide("fast");');
    $frajax->script('parent.$("#btn_addsimilar").fadeOut("fast");');
    $frajax->script('parent.$("#btn_delete").fadeOut("fast");');
    $frajax->script('parent.$("#btn_addchild").fadeOut("fast");');
}

$frajax->script('parent.addFocusEvents();');

$frajax->sendFooter();


/////////////////////////ISBLANK////////////////////////////////////////////
//Checks to see if $var is empty, blank, 0, "0", notset etc
function isblank($var)
{
    //TODO: decide if this should belong there
    if ( ($var == "") or ($var == NULL) or ($var == null) or (empty($var)) or ($var == "0") or (!isset($var)) ) {
        return true;
    } else {
        return false;
    }
}