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

define('_READONLYSESSION', true);

/* gzipping may be faster although it means the data all comes at once. Lets try this... */
if (Jojo::getOption('enablegzip', false)) {
    Jojo::gzip();
}

$frajax = new frajax(true, true);
$frajax->title = 'Load record - ' . _SITETITLE;
$frajax->sendHeader();
$frajax->scrollToTop();
$frajax->assign('h1', 'innerHTML', 'Loading...');
$frajax->script('parent.$(".control-group").removeClass("error");');

$content = array();

$t = Jojo::getFormData('arg1');
$page = Jojo_Plugin::getPage(Jojo::parsepage('admin/edit/' . $t));
if (!$page->perms->hasPerm($_USERGROUPS, 'view')) {
    $frajax->alert('Access Denied. Trying reloading the page');
    exit();
}

$id = Jojo::getFormData('arg2', 0);

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


/* Update form values */
//$frajax->assign("h1", "innerHTML", 'Loading fields...');
$i = 0;
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
    $frajax->script('parent.$(".form-group").removeClass("has-error");');
    if ($f['type'] == 'unixdate') {
        $frajax->script("parent.$('#fm_".$fieldname."').AnyTime_noPicker();");
    }

    if ($f['type'] == 'texteditor') {
        /* find out if editor is html, bb, or other */
        $editortype = 'html';
        if (preg_match('/<!-- \\[editor:([a-z]*?)\\] -->/', $f['html'], $result)) {
        $editortype = $result[1];
        }
        $frajax->script("parent.$('#type_fm_".$fieldname."_html').prop('checked','');");
        $frajax->script("parent.$('#type_fm_".$fieldname."_bb').prop('checked','');");
        $frajax->script("parent.$('#type_fm_".$fieldname."_".$editortype."').prop('checked','checked');");
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
    /* update privacy field */
    if (!empty($f['flags']['PRIVACY'])) {
        if ((strtolower($f['privacy']) == 'y') || (strtolower($f['privacy']) == 'yes')) {
            $frajax->script("parent.$('#privacy_".$fieldname."').prop('checked','checked');");
        } else {
            $frajax->script("parent.$('#privacy_".$fieldname."').prop('checked','');");
        }

    }
}

//$table->getOption('displayvalue');
$frajax->assign("id", "value", $id);
$frajax->assign("h1", "innerHTML",  Jojo::htmlspecialchars(substr(Jojo::either($table->getOption('displayvalue'), "New " . $table->getOption('displayname')),0,100)));
$frajax->hide("message", "Fade", 1);
$frajax->hide("error", "Fade", 1);
if ($id == '') {
    $frajax->hide('btn_addsimilar', 'Fade', 1);
    $frajax->hide('btn_delete', 'Fade', 1);
    $frajax->hide('btn_addchild', 'Fade', 1);
} else {
    $frajax->show('btn_addsimilar', 'Appear', 1);
    $frajax->show('btn_delete', 'Appear', 1);
    $frajax->show('btn_addchild', 'Appear', 1);
}
$frajax->sendFooter();
