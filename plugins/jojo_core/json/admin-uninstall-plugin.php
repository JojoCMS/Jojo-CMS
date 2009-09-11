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

/* Ensure users of this function have access to the admin page */
$page = Jojo_Plugin::getPage(Jojo::parsepage('admin/plugins'));
if (!$page->perms->hasPerm($_USERGROUPS, 'view')) {
    echo json_encode(
            array('result' => false,
                  'message' => "You do not have permission to use this function"));
    exit;
}

$plugin = basename(Jojo::getFormData('plugin', ''));
if (!$plugin) {
    echo json_encode(
            array('result' => false,
                  'message' => "No plugin selected."));
    exit;
}

/* Disable this plugin */
Jojo::insertQuery("DELETE FROM {plugin} WHERE name = ?", $plugin);

/* Run the plugin's uninstall.php */
ob_start();
if (file_exists(_BASEPLUGINDIR . '/' . $plugin . '/uninstall.php')) {
    include (_BASEPLUGINDIR . '/' . $plugin . '/uninstall.php');
} elseif (file_exists(_PLUGINDIR . '/' . $plugin . '/uninstall.php')) {
    include (_PLUGINDIR . '/' . $plugin . '/uninstall.php');
}
ob_end_clean();

/* Clear cache files */
if (Jojo::fileExists(_CACHEDIR . '/api.txt')) {
    unlink(_CACHEDIR . '/api.txt');
}
if (Jojo::fileExists(_CACHEDIR . '/listPlugins.txt')) {
    unlink(_CACHEDIR . '/listPlugins.txt');
}
if (Jojo::fileExists(_CACHEDIR . '/listThemes.txt')) {
    unlink(_CACHEDIR . '/listThemes.txt');
}

/* Clear all compiled templates */
foreach (Jojo::scanDirectory(_CACHEDIR . '/smarty/templates_c') as $filename) {
    if (Jojo::fileExists(_CACHEDIR . '/smarty/templates_c/' . $filename)) {
        unlink(_CACHEDIR . '/smarty/templates_c/' . $filename);
    }
}
foreach (Jojo::scanDirectory(_CACHEDIR . '/dwoo/templates_c') as $filename) {
    if (Jojo::fileExists(_CACHEDIR . '/dwoo/templates_c/' . $filename)) {
        unlink(_CACHEDIR . '/dwoo/templates_c/' . $filename);
    }
}

/* Let the user know */
echo json_encode(
        array('result' => true,
              'message' => ucwords(str_replace('_', ' ', $plugin)) . " uninstalled."));
exit;

