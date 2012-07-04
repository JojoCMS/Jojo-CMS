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

class Jojo_Plugin_Admin_Contactlog extends Jojo_Plugin
{
    function _getContent()
    {
        global $smarty, $_USERGROUPS;
        Jojo_Plugin_Admin::adminMenu();

        $smarty->assign('title',  "Contact Log");

        if (isset($_POST['removeid']) &&  $_POST['removeid']) {
            $removeid = $_POST['removeid'];
            Jojo::deleteQuery("DELETE FROM {formsubmission} WHERE formsubmissionid=$removeid");
        }
        $forms = Jojo::selectQuery("SELECT * FROM {form}");
        $logs = Jojo::selectQuery("SELECT * FROM {formsubmission} ORDER BY submitted DESC LIMIT 1000");
        foreach ($logs as &$e) {
            $e['friendlydate'] = strftime('%x %T', $e['submitted']);
            $fields = unserialize($e['content']);
            $message  = '';
            foreach ($fields as $f) {
                if (isset($f['displayonly'])) { continue; };
                if ($f['type'] == 'note') { continue; };
                if ($f['type'] == 'heading') {
                    $message .=  "\r\n" . $f['value'] . "\r\n";
                    for ($i=0; $i<strlen($f['value']); $i++) {
                        $message .= '-';
                    }
                    $message .= "\r\n";
                } else {
                    $message .= ($f['showlabel'] ? $f['display'] . ': ' : '' ) . ($f['type'] == 'textarea' || $f['type'] == 'checkboxes' ? "\r\n" . $f['value'] . "\r\n" : $f['value'] . "\r\n" );
                }
            }
            $e['shortdesc'] = str_replace("\r\n", ', ', substr(trim(htmlspecialchars($message, ENT_COMPAT, 'UTF-8', false)), 0, 100));
            $e['desc'] = nl2br(trim(htmlspecialchars($message, ENT_COMPAT, 'UTF-8', false)));
        }
        if (isset($_POST['submit'])) {
            $sent = $this->exportLogs();
        }
        $smarty->assign('forms', $forms);
        $smarty->assign('log', $logs);

        $content['content'] = $smarty->fetch('admin/contactlog.tpl');

        return $content;
    }

    function exportLogs()
    {
        /* Check for form injection attempts */
        Jojo::noFormInjection();
        if (isset($_POST['form_id'])) {

            $selectedlogs = Jojo::selectQuery("SELECT fs.formsubmissionid AS ID, fs.submitted, fs.content, f.form_name FROM {formsubmission} fs LEFT JOIN {form} f ON (fs.form_id=f.form_id) WHERE f.form_id=? AND `success`=1 ORDER BY submitted DESC", array(addslashes($_POST['form_id'])));
            if (!count($selectedlogs)) { return true; }
            $formname = $selectedlogs[0]['form_name'];
            foreach ($selectedlogs as $k => $s) {
                $selectedlogs[$k]['submitted'] = strftime('%F %T', $s['submitted']);
                $data = unserialize($s['content']);
                foreach ($data as $d) {
                    if ($d['type']!='note') {
                        $selectedlogs[$k][$d['display']] = trim(str_replace(array("\n", "\r"), ' ', $d['value']));
                    }
                }
                unset($selectedlogs[$k]['content']);
                unset($selectedlogs[$k]['form_name']);
            }

            $output = '';
            $c=0;
            foreach($selectedlogs AS $array) {
                $val_array = array();
                $key_array = array();
                foreach($array AS $key => $val) {
                    $key_array[] = $key;
                    $val = str_replace('"', '""', $val);
                    $val_array[] = "\"$val\"";
                }
                if($c == 0) {
                    $output .= implode(",", $key_array)."\n";
                }
                $output .= implode(",", $val_array)."\n";
                $c++;
            }

            header('Content-type: text/csv'."\r\n");
            header('Content-disposition: attachment; filename="' . Jojo::cleanURL($formname) . '.csv"'."\r\n");
            header("Pragma: no-cache"."\r\n");
            header("Expires: 0"."\r\n");
            header("Content-length: ".strlen($output)."\r\n");
            echo $output;
            exit;
        }
    }
}
