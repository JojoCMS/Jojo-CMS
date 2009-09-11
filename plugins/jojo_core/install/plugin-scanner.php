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

if (!function_exists('_plugin_scanner_scandir_recursive')) {
    function _plugin_scanner_scandir_recursive($path) {
        $list = array();
        if (!is_dir($path)) {
            return $list;
        }
        $directory = @opendir("$path"); // @-no error display
        while ($file = @readdir($directory)) {
            $f = $path . '/' . $file;
            if ($file[0] == '.') {
                /* Skip dot files (eg ., .., .svn) */
                continue;
            }
            if (strpos($f, '/external/')) {
                /* Skip external code */
                continue;
            }
            $f = preg_replace('/(\/){2,}/','/',$f); //replace double slashes
            if(is_file($f)) {
                $list[] = $f;
            } elseif(is_dir($f)) {
                $list = array_merge($list, _plugin_scanner_scandir_recursive($f));   //RECURSIVE CALL
            }
        }
        @closedir($directory);
        return $list;
    }
}

/* Get a list of all the files in the different folders */
$plugins     = _plugin_scanner_scandir_recursive(_PLUGINDIR);
$themes      = _plugin_scanner_scandir_recursive(_THEMEDIR);
$baseplugins = _plugin_scanner_scandir_recursive(_BASEPLUGINDIR);
$basethemes  = _plugin_scanner_scandir_recursive(_BASETHEMEDIR);
$files = array_merge($plugins, $themes, $baseplugins, $basethemes);

/* Check all the files */
$warnings = array();
foreach ($files as $filename) {
    if (basename($filename) == 'plugin-scanner.php') {
        /* Dont check ourself */
        continue;
    }

    /* PHP fixes */
    if ((Jojo::getFileExtension($filename) == 'php') && filesize($filename)) {
        $contents = file_get_contents($filename);

        if (preg_match('/\$NUMROWS/', $contents)) {
            $warnings[] = $filename.' - $NUMROWS found - count() the return array instead.';
        }

        if (preg_match('/\$INSERTID/', $contents)) {
            $warnings[] = $filename.' - $INSERTID found - Jojo::insertQuery() returns the id of the new row, use this instead.';
        }

        if (preg_match('/no-form-injection\.inc\.php/', $contents)) {
            $warnings[] = $filename.' - no-form-injection.inc.php found - remove this and replace with Jojo::noFormInjection().';
        }

        if (preg_match('/new\ eventlog/i', $contents)) {
            $warnings[] = $filename.' - new eventlog found - this class has been renamed to Jojo_Eventlog.';
        }

        if (preg_match('/eventlog\.class\.php/', $contents)) {
            $warnings[] = $filename.' - eventlog.class.php found - this class is auto included, remove the include line now.';
        }

        if (preg_match('#classes/hktree#', $contents)) {
            $warnings[] = $filename.' - hktree.class.php found - this class has moved and is auto included, remove the include line now.';
        }

        if (preg_match('#strtotimenormal#i', $contents)) {
            $warnings[] = $filename.' - strtotimenormal found - this function has been remove, use Jojo::strToTimeUK() instead, they are identical.';
        }

        if (preg_match('/php-captcha\.inc\.php/', $contents) && basename($filename) != 'Jojo.php') {
            $warnings[] = $filename.' - php-captcha.inc.php found - this class is auto included, remove the include line now.';
        }

        if (preg_match('/bbconverter\.class\.php/', $contents) && basename($filename) != 'Jojo.php') {
            $warnings[] = $filename.' - bbconverter.class.php found - this class is auto included, remove the include line now.';
        }
    }

    /* Template Fixes */
    /*
    if ((Jojo::getFileExtension($filename) == 'tpl') && filesize($filename)) {
        $contents = file_get_contents($filename);
    }
    */
}

foreach ($warnings as $warning) {
    echo $warning . '<br />';
}