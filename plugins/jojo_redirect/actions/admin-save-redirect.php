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
 * @package jojo_redirect
 */

$frajax = new frajax();
$frajax->title = 'Save redirect - ' . _SITETITLE;
$frajax->sendHeader();
$frajax->scrollToTop();
$frajax->assign("h1", "innerHTML", 'Processing...');

$delete     = Jojo::getFormData('delete', false);
$update     = Jojo::getFormData('update', false);
$save       = Jojo::getFormData('save', false);

/* Delete logic */
if ($delete) {
    Jojo::deleteQuery("DELETE FROM {redirect} WHERE redirectid=? LIMIT 1", array($delete));
    $redirects = Jojo::selectQuery("SELECT * FROM {redirect} WHERE 1 ORDER BY rd_order, rd_from");
    $smarty->assign('redirects', $redirects);
    $frajax->assign("redirect-content", "innerHTML", $smarty->fetch('admin/redirects-inner.tpl'));
    $frajax->assign("h1", "innerHTML", 'Edit Redirects');
    $frajax->sendFooter();
    exit();
}

/* Save logic */
if ($update || $save) {
    if ($save) {
        $from       = Jojo::getFormData('from', '');
        $to         = Jojo::getFormData('to', '');
        $type       = Jojo::getFormData('type', 301);
        $order      = Jojo::getFormData('order', 0);
        $notes      = Jojo::getFormData('notes', '');
        $regex      = Jojo::getFormData('regex', '');
    } else {
        $from       = Jojo::getFormData('from_' . $update, '');
        $to         = Jojo::getFormData('to_' . $update, '');
        $type       = Jojo::getFormData('type_' . $update, 301);
        $order      = Jojo::getFormData('order_' . $update, 0);
        $notes      = Jojo::getFormData('notes_' . $update, '');
        $regex      = Jojo::getFormData('regex_' . $update, '');
    }
    /* error checking */
    $errors = array();
    if ($from == '') {
        $errors[] = 'Please enter a URL to redirect from';
    }
    if ($type == '') {
        $type = '301';
    }
    if ($order == '') {
        $order = 0;
    }
    if (count($errors)) {
        $frajax->alert(implode("\n", $errors));
        $frajax->assign("h1", "innerHTML", 'Errors found while saving');
    } else {
        if ($save) {
            /* create new redirect */
            $query = "INSERT INTO {redirect} SET rd_from=?, rd_to=?, rd_type=?, rd_order=?, rd_notes=?, rd_regex=?";
            $values = array($from, $to, $type, $order, $notes, $regex);
            Jojo::insertQuery($query, $values);
            $frajax->assign("h1", "innerHTML", 'Redirect Saved');
        } else {
            /* edit existing redirect */
            $query = "UPDATE {redirect} SET rd_from=?, rd_to=?, rd_type=?, rd_order=?, rd_notes=?, rd_regex=? WHERE redirectid=? LIMIT 1";
            $values = array($from, $to, $type, $order, $notes, $regex, $update);
            Jojo::updateQuery($query, $values);
            $frajax->assign("h1", "innerHTML", 'Redirect Updated');
        }
        $redirects = Jojo::selectQuery("SELECT * FROM {redirect} ORDER BY rd_order, rd_from");
        $smarty->assign('redirects', $redirects);
        $frajax->assign("redirect-content", "innerHTML", $smarty->fetch('admin/redirects-inner.tpl'));
    }
}
if (isset($_SESSION['redirects'])) unset($_SESSION['redirects']);
$frajax->sendFooter();