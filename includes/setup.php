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
 * @author  Tom Dale <tom@zero.co.nz>
 * @license http://www.fsf.org/copyleft/lgpl.html GNU Lesser General Public License
 * @link    http://www.jojocms.org JojoCMS
 * @package jojo_core
 */

/* Should we be here? */
if (!isset($isAdmin) || !$isAdmin) {
    if (!defined('_MASTERPASS') || strlen(_MASTERPASS) < 6) {
        jojo_install_header();
        echo '<div class="errors"><h2>_MASTERPASS Error</h2>Please define the _MASTERPASS constant in config.php and ensure it is at least 6 characters in length.</div>';
        jojo_install_footer();
        exit();
    }

    /* Store password in session if we have it */
    session_start();
    if (isset($_POST['simplepass'])) {
      $_SESSION['simplepass'] = $_POST['simplepass'];
      header('location: ' . $_SERVER['REQUEST_URI']);
      exit();
    }

    /* Check we have correct password, else display the login screen */
    if ((!isset($_SESSION['simplepass']) || $_SESSION['simplepass'] != _MASTERPASS) && (!isset($_COOKIE['simplepass']) || $_COOKIE['simplepass'] != _MASTERPASS)) {
        jojo_install_header();
        echo '<h1 id="h1">Jojo Setup</h1><p>The Jojo setup process requires authentication.</p>'."\n";
        echo '<div class="well">'."\n";
        echo "<form method=\"post\" class=\"form\" action=\"".$_SERVER['REQUEST_URI']."\">\n";
        echo '<h2>Enter password</h2>
        <div class="form-group">
            <label for="simplepass">Password:</label><input class="form-control" type="password" size="20" id="simplepass" name="simplepass" />
        </div>
        <input class="btn btn-default" type="submit" name="submit" value="Run Setup" />'
        ;
        echo "</form>
        </div>
        <p><em>If you are unsure of the password, please check the <strong>_MASTERPASS</strong> setting in your config.php file.</em></p>";
        jojo_install_footer();
        exit();
    }

    /* Output HTML header */
    jojo_install_header();
}

/* Turn error reporting on */
ini_set('max_execution_time', 900);
error_reporting(E_ALL);

/* Remove cached plugin/theme files */
if (file_exists(_CACHEDIR . '/listPlugins.txt')) {
    unlink(_CACHEDIR . '/listPlugins.txt');
}
if (file_exists(_CACHEDIR . '/listThemes.txt')) {
    unlink(_CACHEDIR . '/listThemes.txt');
}
if (file_exists(_CACHEDIR . '/api.txt')) {
    unlink(_CACHEDIR . '/api.txt');
}

/* Ensure default folders exist */
$_folders = array();
$_folders[_PLUGINDIR] = 'My Site Plugin Directory';
$_folders[_THEMEDIR] = 'My Site Theme Directory';
$_folders[_DOWNLOADDIR] = 'Download Directory';
$_folders[_DOWNLOADDIR . '/images'] = 'Default image upload folder for Xinha';
$_folders[_DOWNLOADDIR . '/files'] = 'Default file upload folder for Xinha';
$_folders[_CACHEDIR] = 'Cache Directory';
$_folders[_CACHEDIR . '/smarty/templates_c'] = 'Smarty Template Cache Directory';
$_folders[_CACHEDIR . '/smarty/cache'] = 'Smarty Cache Directory';
$_folders[_CACHEDIR . '/dwoo/templates_c'] = 'Dwoo Template Cache Directory';
$_folders[_CACHEDIR . '/dwoo/cache'] = 'Dwoo Cache Directory';

foreach($_folders as $folder => $name) {
    $res = Jojo::RecursiveMkdir($folder);
    if ($res === true) {
        echo "Created folder: $name ($folder)<br/>";
    } elseif($res === false) {
        echo "Could not automatically create $folder folder on the server. Please create this folder and assign 777 permissions.";
    }
}

/* create an empty array for each table to hold indexes - plugins will add to this array before it is processed */
global $_db;
$indexes = array();
Jojo::_connectToDB();
$tables = $_db->getAssoc('SHOW FULL TABLES');
foreach ($tables as $tblname => $tbltype)  {
    if (_TBLPREFIX) {
        if (strpos($tblname, _TBLPREFIX) !== 0) continue;
        $tblname = str_replace(_TBLPREFIX, '', $tblname);
    }
    $indexes[$tblname] = array();
}

/* do we have any SQL to run before setup? */
$sql = (count($indexes)) ? Jojo::getFormData('sql','') : false;
if (!empty($sql)) {
    echo 'Running SQL: '.$sql."<br />\n";
    Jojo::structureQuery($sql);
}

/* delete any orphaned options as specified */
if (isset($_POST['delete_orphaned_options'])) {
    foreach ($_POST['orphaned_options'] as $o) {
        echo "Removing orphaned option <b>$o</b> from database<br/>";
        Jojo::removeOption($o);
    }
}

/* delete any orphaned pages as specified */
if (isset($_POST['delete_orphaned_pages'])) {
    foreach ($_POST['orphaned_pages'] as $p) {
        $data = Jojo::selectQuery("SELECT pg_title FROM {page} WHERE pageid = ?", array($p));
        echo "Removing orphaned page <b>". Jojo::htmlspecialchars($data[0]['pg_title'])."</b> from database<br/>";
        Jojo::deleteQuery("DELETE FROM {page}  WHERE pageid = ? LIMIT 1", array($p));
    }
}

/* Default Tabledata settings */
$default_td = array();

echo '<h1 id="h1">Running setup<div id="setup_loading"></div></h1>
<p>The Jojo setup script is an important part of the system. It applies version upgrades to the database, refreshes the cache, and performs other important housekeeping tasks. It is highly recommended that you run setup after every Jojo upgrade, and after adding any new files to plugins.</p><p>Consider running setup to be the equivalent of restarting Windows - it will fix all manner of problems, and is a good thing to do before seeking support.</p><p>If you do not see a "Setup Complete" message at the bottom of the page, it means the setup process has failed, which is usually due to a faulty install script in a plugin. The resulting error message should give some indication as to which plugin is responsible.</p>';

/* On first run, ensure database is set to utf8 as a default collation */
if (!$indexes) {
    $query = "ALTER DATABASE " . _DBNAME . " DEFAULT CHARACTER SET utf8 DEFAULT COLLATE utf8_general_ci;";
    echo 'Running SQL: '.$query."<br />\n";
    $_db->Execute($query);
}

/* Install the basics first so that the autoloading works */
include(_BASEPLUGINDIR . '/jojo_core/install/install_theme.php');
include(_BASEPLUGINDIR . '/jojo_core/install/install_plugin.php');

/* Include all the php files in the install folder of the core plugin */
$dir = _BASEPLUGINDIR . '/jojo_core/install/';
$handle  = opendir($dir);
while (false !== ($filename = readdir($handle))) {
    if ($filename[0] != '.' && strpos($filename, '.php') != false) {
        require_once($dir . $filename);
    }
}

/* Ensure core plugin is enabled */
Jojo::updateQuery("REPLACE INTO {plugin} ( `name` , `active`, `priority` ) VALUES ( 'jojo_core', 'yes', '100');");

/* Ensure a theme is enabled */
$themes = Jojo::selectQuery("SELECT * FROM {theme} WHERE active = 'yes'");
if (!count($themes)) {
    /* Enabled default theme */
    $currenttheme = '2column'; //default theme
    Jojo::updateQuery("REPLACE INTO {theme} ( `name` , `active` ) VALUES ( '".$currenttheme."', 'yes');");
} else {
    $currenttheme = $themes[0]['name'];
}

/* Create array of enabled plugins */
if (Jojo::tableexists('plugin')) {
    $PLUGINS = array();
    $data = Jojo::selectQuery("SELECT * FROM {plugin} WHERE active = 'yes' ORDER BY priority DESC");
    foreach ($data as $plugin) {
        $PLUGINS[] = $plugin['name'];
    }
}

/* Run any code in the 'install' folder of enabled plugins */
foreach ($PLUGINS as $plugin) {
    if (false !== ($files = Jojo::scanDirectory(_BASEPLUGINDIR . '/' . $plugin . '/install'))) {
        foreach ($files as $filename) {
            if ($filename && $filename[0] != '.' && strpos($filename, '.php') != false) {
                require_once(_BASEPLUGINDIR . '/' . $plugin . '/install/' . $filename);
            }
        }
    }

    if (defined('_ALTPLUGINDIR')) {
        if (false !== ($files = Jojo::scanDirectory(_ALTPLUGINDIR . '/' . $plugin . '/install'))) {
            foreach ($files as $filename) {
                if ($filename && $filename[0] != '.' && strpos($filename, '.php') != false) {
                    require_once(_ALTPLUGINDIR . '/' . $plugin . '/install/' . $filename);
                }
            }
        }
    }

    if (false !== ($files = Jojo::scanDirectory(_PLUGINDIR . '/' . $plugin . '/install'))) {
        foreach ($files as $filename) {
            if ($filename && $filename[0] != '.' && strpos($filename, '.php') != false) {
                require_once(_PLUGINDIR . '/' . $plugin . '/install/' . $filename);
            }
        }
    }
}
/* Run any code in the 'install' folder of current theme */
if (false !== ($files = Jojo::scanDirectory(_BASETHEMEDIR . '/' . $currenttheme . '/install'))) {
    foreach ($files as $filename) {
        if ($filename && $filename[0] != '.' && strpos($filename, '.php') != false) {
            require_once(_BASETHEMEDIR . '/' . $currenttheme . '/install/' . $filename);
        }
    }
}

if (false !== ($files = Jojo::scanDirectory(_THEMEDIR . '/' . $currenttheme . '/install'))) {
    foreach ($files as $filename) {
        if ($filename && $filename[0] != '.' && strpos($filename, '.php') != false) {
            require_once(_THEMEDIR . '/' . $currenttheme . '/install/' . $filename);
        }
    }
}

/* Populate options table with all options from all plugins */
$allOptions = Jojo::getOptions(true);
if (Jojo::tableexists('option')) {
    foreach (Jojo::listPlugins('api.php') as $file) {
        $_options = array();
        include($file);
        $plugin = basename(dirname($file));
        foreach($_options as $o) {
            $data = Jojo::selectQuery("SELECT * FROM {option} WHERE op_name = ?", $o['id']);
            if (!count($data)) {
                /* create a new option */
                Jojo::insertQuery("INSERT INTO {option} SET op_name=?, op_value=?, op_type=?, op_options=?, op_default=?, op_plugin=?, op_category=?, op_displayname=?, op_description=?",
                        array(
                            $o['id'],
                            $o['default'],
                            $o['type'],
                            $o['options'],
                            $o['default'],
                            $plugin,
                            $o['category'],
                            $o['label'],
                            $o['description']
                        )
                    );
            } else {
                /* update the metadata on the options */
                Jojo::insertQuery("UPDATE {option} SET  op_type=?, op_options=?, op_default=?, op_plugin=?, op_category=?, op_displayname=?, op_description=? WHERE op_name=?",
                        array(
                            $o['type'],
                            $o['options'],
                            $o['default'],
                            $plugin,
                            $o['category'],
                            $o['label'],
                            $o['description'],
                            $o['id']
                        )
                    );
            }
            unset($allOptions[$o['id']]);
        }
    }
}

/* Remove orphaned options */
if (count($allOptions)) {
    echo '<form method="post" id="orphaned-options">';
    echo '<h3>Orphaned options</h3>
    <p>The following options don\'t appear to be required by Jojo, and in most cases they can be deleted. Before deleting however, please ensure that the options aren\'t required by any custom plugins. If in doubt, there is no harm in leaving these options alone so please do not delete options unless you are sure they are no longer needed.</p>';
    foreach($allOptions as $n => $v) {
        /* PLEASE don't delete old options automatically. Some of them are VERY important. */
        echo "<label style=\"float: none; display: inline\"><input style=\"float: none; display: inline; width: auto;\" type=\"checkbox\" name=\"orphaned_options[]\" value=\"$n\" /> Orphaned option found <b>$n</b></label><br/>";
    }
    echo '<input class="btn btn-default btn-xs" type="button" onclick="var optionsForm = document.getElementById(\'orphaned-options\'); for (i = 0; i < optionsForm.length; i++) {optionsForm.elements[i].checked = true;} return false;" value="Select all" /> &nbsp; ';
    echo '<input class="btn btn-default" type="submit" name="delete_orphaned_options" value="Delete selected" /></form>';
}


/* Run jojo_core setup so constants are available like $_ADMIN_ROOT_ID */
include(_BASEPLUGINDIR . '/jojo_core/setup.php');

/* Include plugin setup actions */
foreach (Jojo::listPlugins('setup.php', 'all', false, true) as $pluginfile) {
    if ($pluginfile != _BASEPLUGINDIR . '/jojo_core/setup.php') {
        include($pluginfile);
    }
}

/* Check for orphaned pages */
$pages = Jojo::selectAssoc("SELECT pageid as id, pageid, pg_title, pg_link, pg_parent FROM {page}");
$html = '';
foreach($pages as $page) {
    if ($page['pg_link'] != '') {
        $classname = strtolower($page['pg_link']);
        if (!class_exists($classname)) {
            $html .= "<label style=\"float: none; display: inline\"><input style=\"float: none; display: inline; width: auto;\" type=\"checkbox\" name=\"orphaned_pages[]\" value=\"".$page['pageid']."\" /> Dead Link page found (plugin missing or uninstalled)<b> ".Jojo::htmlspecialchars($page['pg_title'])."</b> (ID: ".$page['pageid'].")</label><br/>";
        }
    }
    if ($page['pg_parent'] != 0 && !isset($pages[$page['pg_parent']]) ) {
        $html .= "<label style=\"float: none; display: inline\"><input style=\"float: none; display: inline; width: auto;\" type=\"checkbox\" name=\"orphaned_pages[]\" value=\"".$page['pageid']."\" /> Orphaned page found (parent set but missing or moved)<b> ".Jojo::htmlspecialchars($page['pg_title'])."</b> (ID: ".$page['pageid'].")</label><br/>";
    }
}
if (!empty($html)) {
    echo '<form method="post" id="orphaned-pages">';
    echo '<h3>Orphaned pages</h3>
    <p>The following pages don\'t appear to be required by Jojo, and in most cases they can be deleted. Before deleting however, please ensure that the pages aren\'t required by any custom plugins.</p>';
    echo $html;
    echo '<input class="btn btn-default btn-xs" type="button" onclick="var pagesForm = document.getElementById(\'orphaned-pages\'); for (i = 0; i < pagesForm.length; i++) {pagesForm.elements[i].checked = true;} return false;" value="Select all" /> &nbsp; ';
    echo '<input class="btn btn-default" type="submit" name="delete_orphaned_pages" value="Delete selected" /></form>';
}

/* Turn off caching of dynamic pages */
$pages = Jojo::selectQuery("SELECT * FROM {page}  WHERE pg_contentcache = 'auto' AND pg_link!=''");
for ($i=0; $i<count($pages); $i++) {
  if (_DEBUG) {
      echo "Set cache to <b>no</b> for dynamic page <b>" . $pages[$i]['pg_title']."</b><br />";
  }
  Jojo::updateQuery("UPDATE {page} SET pg_contentcache = 'no' WHERE pageid=" . $pages[$i]['pageid']." LIMIT 1");
}

// BROKEN needs to recurse: Set NOINDEX flag for all admin pages so they dont get included in google sitemap
$data = Jojo::selectQuery("SELECT * FROM {page}  WHERE pg_index = 'yes' AND pg_link = 'Jojo_Plugin_Admin'");
for ($i=0; $i<count($data); $i++) {
    if (_DEBUG) {
        echo "Setting <b>index</b> flag to 'No' on <b>" . $data[$i]['pg_title']."</b><br />";
    }
    Jojo::updateQuery("UPDATE {page} SET pg_index = 'no' WHERE pageid = ? LIMIT 1", array($data[$i]['pageid']));
}


$existingtables = array();
$existingfields = array();

/* ensure the previous command gives us a tables array */
if (!$tables) {
    $tablestemp = $_db->MetaTables('TABLES');
    $tables = array();
    foreach ($tablestemp as $t) {
        $tables[$t] = true;
    }
}

foreach ($tables as $tblname => $tbltype)  {
    if ($tblname == 'adodb_logsql') {
        /* Hide adodb log table */
        continue;
    }
    if (_TBLPREFIX) {
        if (strpos($tblname, _TBLPREFIX) !== 0) {
            /* Not our prefix, another install's table */
            continue;
        }
        $tblname = str_replace(_TBLPREFIX, '', $tblname);
    }

    /* Track what tables we've found */
    $existingtables[] = $tblname;

    /* Look for TABLE in TABLEDATA - create if it doesn't exist */
    $results = Jojo::selectQuery("SELECT * FROM {tabledata} WHERE td_name = ?", $tblname);
    if (count($results) == 0)   {
        /* Create Record */
        $query = "INSERT INTO {tabledata} SET td_name = ?, td_primarykey = ?";
        $primaryKeys = $_db->MetaPrimaryKeys($tblname);
        if ($primaryKeys && count($primaryKeys) == 1) {
            $values = array($tblname, $primaryKeys[0]);
        } else {
            $values = array($tblname, $tblname . 'id');
        }
        Jojo::insertQuery($query, $values);
    }

    /* Check all fields in the table */
    $fields = Jojo::selectQuery(sprintf("SHOW COLUMNS FROM {%s}", $tblname));
    $fieldlist = array(); //a simple array of field names, used for comparing indexes later
    foreach ($fields as $field) {
        $fieldname = $field["Field"];
        $fieldlist[] = $fieldname;
        $fieldtype = $field["Type"];
        $fieldsize = 0;  //eg INT(11) would be 11. VARCHAR(255) would be 255
        $fielddefault = $field["Default"];

        /* Track what fields we've found */
        $existingfields[] = $tblname . '.' . $fieldname; //add this field to the list of fields that exist

        /* See if the field is already in field data */
        $query = "SELECT * FROM {fielddata} WHERE fd_table = ? AND fd_field = ?";
        $values = array($tblname, $fieldname);
        $res = Jojo::selectQuery($query, $values);

        if (count($res) == 0) {
            /* Create record */
            $name = $fieldname;
            $options = "";

            /* Work out the field type */
            if (preg_match("/^varchar/", $fieldtype)) {
                //Check for VARCHAR
                $type = "text";
            } elseif (preg_match("/^text/", $fieldtype)) {
                //Check for TEXT
                $type = "textarea";
            } elseif (preg_match("/^int/", $fieldtype)) {
                //Check for INTEGER
                $type = "integer";
            } elseif (preg_match("/^enum/", $fieldtype)) {
                //Check for ENUM
                $data = str_replace(array("enum", "'", "(", ")"), "", $fieldtype);
                $data = explode(',', $data);
                $options = array();
                foreach ($data as $value) {
                    $options[] = $value . ":" . ucfirst($value);
                }

                if (count($options) < 4) {
                    $type = "radio";
                } else {
                    $type = "combobox";
                }
                $options = implode("\r\n",$options);
            } else {
                $type = "text";
            }
            $required = "no";

            /* Insert new record into database */
            $query = "INSERT INTO
                        {fielddata}
                      SET
                        fd_table = ?, fd_field = ?, fd_sqltype = ?,
                        fd_name = ?, fd_required = ?, fd_type = ?,
                        fd_options = ?, fd_default = ?";
            $values = array(
                        $tblname, $fieldname, $fieldtype,
                        $fieldname, $required, $type,
                        $options, (string)$fielddefault
                        );
            $result2 = Jojo::insertQuery($query, $values);

        } elseif (preg_match("/^enum/", $fieldtype) && $fieldtype != $res[0]['fd_sqltype']) {
            /* Extra options are often added to enum fields - this code ensures the extra options are added to the cms */
            $data = str_replace(array("enum", "'", "(", ")"), "", $fieldtype);
            $data = explode(',', $data);
            $options = array();
            foreach ($data as $value) {
                $options[] = $value . ":" . ucfirst($value);
            }

            if (count($options) < 4) {
                $type = "radio";
            } else {
                $type = "combobox";
            }
            $options = implode("\r\n",$options);

            /* Update field data */
            $query = "UPDATE
                        {fielddata}
                      SET
                        fd_sqltype = ?, fd_options = ? WHERE fielddataid = ? LIMIT 1";
            $values = array($fieldtype, $options, $res[0]['fielddataid']);
            Jojo::updateQuery($query, $values);
        } elseif ($fieldtype != $res[0]['fd_sqltype']) {
            /* Update the sqltype for other fields, but don't bother updating other details (eg for ENUMs) */
            $query = "UPDATE {fielddata} SET fd_sqltype = ? WHERE fielddataid = ?";
            $values = array($fieldtype, $res[0]['fielddataid']);
            Jojo::updateQuery($query, $values);
        }
    }

    /* setup indexes for the table */
    echo '<h4>Checking indexes for '.$tblname.'...</h4>';

    /* Get the existing indexes */
    $table_indexes = Jojo::selectQuery("SHOW INDEXES FROM {".$tblname."}");
    $existingIndexes = array();
    foreach ($table_indexes as $row2) {
        if (!isset($existingIndexes[$row2['Key_name']])) {
            $existingIndexes[$row2['Key_name']] = $row2['Column_name'];
        } else {
            $existingIndexes[$row2['Key_name']] = (array)$existingIndexes[$row2['Key_name']];
            $existingIndexes[$row2['Key_name']][] = $row2['Column_name'];
        }
    }
    /* list indexes */
    foreach ($existingIndexes as $k => $v) {
        $v_str = (is_array($v)) ? implode(', ', $v) : $v;
        echo $k .' - <em>' . $v_str.'</em><br />'."\n";
    }

    if (isset($indexes[$tblname])) {
        /* Check for any missing indexes */
        foreach ($indexes[$tblname] as $i) {
            if (in_array($i, $existingIndexes)) {
                continue;
            }
            if (!is_array($i) && (!in_array($i, $fieldlist))) {
                echo "<span style='color:red'>Field $i does not exist</span><br />\n";
                continue;
            } else if (is_array($i)) {
                $continue = false;
                foreach ($i as $index_field) {
                    if (!in_array($index_field, $fieldlist)) {
                        echo "<span style='color:red'>Field $index_field does not exist</span><br />\n";
                        $continue = true;
                    }
                }
                if ($continue) $continue;
            }
            echo "Index on `" . implode((array)$i, '` and `') . '`';
            echo "<span style='color:orange'>missing</span><br />\n";
            $sql = "ALTER TABLE {".$tblname."} ADD INDEX (`" . implode((array)$i, '`, `') . '`);';
            echo ".Executing Query: <span style='color:blue'>$sql</span><br />\n";
            $res = Jojo::structureQuery($sql);
            if ($res) echo "<span style='color:green'>Done</span><br />\n";
        }
    }

}

/* Delete TABLEDATA entries for tables that no longer exist... */
$tabledatas = Jojo::selectQuery("SELECT tabledataid, td_name FROM {tabledata}");
foreach ($tabledatas as $t) {
    if (!in_array($t['td_name'], $existingtables)) {
        Jojo::deleteQuery("DELETE FROM {tabledata} WHERE tabledataid = ? LIMIT 1", $t['tabledataid']);
        Jojo::deleteQuery("DELETE FROM {fielddata} WHERE fd_table = ?", $t['td_name']);
    }
}

/* Delete FIELDDATA entries for fields that no longer exist */
$fielddatas = Jojo::selectQuery("SELECT fielddataid, fd_field, fd_table FROM {fielddata} WHERE 1");
foreach ($fielddatas as $f) {
    if (!in_array($f['fd_table'].'.' . $f['fd_field'], $existingfields)) {
        Jojo::deleteQuery("DELETE FROM {fielddata} WHERE fielddataid=? LIMIT 1", $f['fielddataid']);
    }
}

/* Map common field names to field type */
$_typeMap = array(
    //'url' => 'url', //this temporarily disabled as it keeps reverting 'internalurl' fields back to 'url'
    'web' => 'url',
    'website' => 'url',
    'email' => 'email',
    'birthday' => 'birthday',
    'email address' => 'email',
    'image' => 'fileupload',
    'image1' => 'fileupload',
    'image2' => 'fileupload',
    'image3' => 'fileupload',
    'image4' => 'fileupload',
    'image5' => 'fileupload',
    'image6' => 'fileupload',
    'image7' => 'fileupload',
    'image8' => 'fileupload',
    'image9' => 'fileupload',
    'image10' => 'fileupload',
    'permissions' => 'permissions',
);

/* Map common field names to friendly names */
$_nameMap = array(
    'desc' => 'Description',
    'categoryid' => 'Category',
    'datetime' => 'Date / Time',
    'autoupdate' => 'Auto Update',
    'help' => 'Help Info',
    'readonly' => 'Read Only',
    'tabname' => 'Tab Name',
    'menutitle' => 'Menu Title',
    'seotitle' => 'SEO Title',
    'menushow' => 'Show on Menu',
    'firstname' => 'First Name',
    'lastname' => 'Last Name',
    'desc' => 'Description',
    'metadesc' => 'Meta Description',
    'id' => 'ID',
    'url' => 'URL',
);

/* Tidy up Table data based on values in autoupdate_tablename.php files */
$tabledatas = Jojo::selectQuery("SELECT * FROM {tabledata}");
foreach ($tabledatas as $t) {
    $tablename = $t['td_name'];

    /* Are we allowed to automatically update this table? */
    if (strtolower($t['td_autoupdate']) == 'yes') {
        /* See if anything needs changing */
        $toUpdate = array();
        foreach ($t as $k => $v) {
            if ((isset($default_td[$tablename][$k])) && ($default_td[$tablename][$k] != $v)) {
                $toUpdate[$k] = $default_td[$tablename][$k];
            }
        }

        /* Update database if needed */
        if (count($toUpdate) > 0) {
            $query = "UPDATE {tabledata} SET";
            $values = array();
            $gap = '';
            foreach($toUpdate as $k => $v) {
                $query .= "$gap $k = ?";
                $gap = ',';
                $values[] = $v;
            }
            $query .= " WHERE tabledataid = ? LIMIT 1";
            $values[] = $t['tabledataid'];
            Jojo::updateQuery($query, $values);
            if (_DEBUG) {
                echo "Autoupdate Table Data for table: <b>" . $tablename . "</b><br />";
            }
        }
    }

    /* Check all field datas are up to date based on autoupdate_tablename.php */
    $fielddatas = Jojo::selectQuery("SELECT * FROM {fielddata} WHERE fd_table = ?", $t['td_name']);
    foreach($fielddatas as $f) {
        /* Store un-editied data */
        $original = $f;

        /* Update the fielddata entry if AutoUpdate is enabled */
        if ($f['fd_autoupdate'] == 'yes') {
            /* See if there is anything to update */
            foreach ($f as $k => $v) {
                if ( isset($default_fd[$tablename][$f['fd_field']][$k]) &&
                     $default_fd[$tablename][$f['fd_field']][$k] != $v) {

                    /* Update values */
                    $f[$k] = $default_fd[$tablename][$f['fd_field']][$k];
                }
            }

        }

        /* Remove prefix from field name to use as a default */
        if ($f['fd_field'] == $f['fd_name']) {
            /* No default set so create one */
            $newname = ucfirst(preg_replace("/^[a-z][a-z]_/", "", $f['fd_name']));
            if ($newname != $f['fd_name']) {
                /* Update values */
                $f['fd_name'] = $newname;
            }
        }

        /* Map field name to default field type */
        if (isset($_typeMap[strtolower($f['fd_name'])]) &&
                ($f['fd_type'] != $_typeMap[strtolower($f['fd_name'])]) &&
                !isset($default_fd[$tablename][$f['fd_field']])) {
            /* Update values */
            $f['fd_type'] = $_typeMap[strtolower($f['fd_name'])];
        }

        /* Put permissions on permissions tab if not already on a tab */
        if ($f['fd_type'] == 'permissions' && $f['fd_tabname'] == ''){
            /* Update values */
            $f['fd_tabname'] = 'Permissions';
        }

        /* If this is the table's display field then make it compulsary */
        if (isset($t['td_display']) && $f['fd_field'] == $t['td_display'] && $f['fd_required'] == 'no') {
            /* Update values */
            $f['fd_required'] = 'yes';
        }

        /* If this is the table's primary key field rename it to ID and make it readonly */
        if ($f['fd_field'] == $t['td_primarykey'] && $f['fd_field'] == $f['fd_name']) {
            /* Update values */
            $f['fd_name'] = 'ID';
            $f['fd_type'] = 'fd_options';
            $f['fd_order'] = 0;
        }

        /* Map common field names to fiendly names */
        if (isset($_nameMap[strtolower($f['fd_name'])]) && $f['fd_name'] != $_nameMap[strtolower($f['fd_name'])]) {
            /* Update values */
            $f['fd_name'] = $_nameMap[strtolower($f['fd_name'])];
        }

        /* Update database if needed */
        $query = "UPDATE {fielddata} SET";
        $values = array();
        $gap = '';
        foreach($f as $k => $v) {
            if ($v != $original[$k]) {
                $query .= "$gap $k = ?";
                $gap = ',';
                $values[] = $v;
            }
        }
        if (count($values) > 0) {
            $query .= " WHERE fielddataid = ? LIMIT 1";
            $values[] = $f['fielddataid'];
            Jojo::updateQuery($query, $values);
            if (_DEBUG) {
                echo "Autoupdated Field Data for <b>" . $tablename . "-&gt;" . $f['fd_field'] . "</b><br />";
            }
        }
    }
}

/* Autodetect SITEURL */
$data = Jojo::selectQuery("SELECT * FROM {option} WHERE op_name = 'siteurl'");
if (isset($_GET['resetlocation']) || $data[0]['op_value'] == '') {
    $newsiteurl = rtrim('http://' . $_SERVER['HTTP_HOST'] . str_replace("\\", '/', dirname($_SERVER['PHP_SELF'])),'/');
    echo "Setting <b>SITEURL</b> to <b>" . $newsiteurl . "</b><br />";
    Jojo::updateQuery("UPDATE {option} SET op_value = ? WHERE op_name = 'siteurl' LIMIT 1", array($newsiteurl));
}

// Do some housekeeping checks here.
if (!file_exists(_DOWNLOADDIR)) {
    echo '<div class="error"><font color="red">WARNING: <b>' . _DOWNLOADDIR . '</b> does not exist - please create this folder</font></div>';
}

/* Pages will hang if they have themself as the parent */
if (Jojo::tableexists('page')) {
    $pages = Jojo::selectQuery("SELECT * FROM {page}  WHERE pageid=pg_parent");
    for ($i=0; $i<count($pages); $i++) {
        echo "Page <b>" . $pages[$i]['pg_title']."</b> references itself as parent - changing to top level item<br />";
        Jojo::updateQuery("UPDATE {page} SET pg_parent=0 WHERE pageid = ? LIMIT 1", array($pages[$i]['pageid']));
    }
}

/* Add option values from install to the database */
if (!empty($_SESSION['sitetitle'])) {
    Jojo::updateQuery("UPDATE {option} SET op_value = ? WHERE op_name = 'sitetitle' LIMIT 1", $_SESSION['sitetitle']);
}
if (!empty($_SESSION['webmastername'])) {
    Jojo::updateQuery("UPDATE {option} SET op_value = ? WHERE op_name = 'webmastername' LIMIT 1", $_SESSION['webmastername']);
}
if (!empty($_SESSION['webmasteremail'])) {
    Jojo::updateQuery("UPDATE {option} SET op_value = ? WHERE op_name = 'webmasteraddress' LIMIT 1", $_SESSION['webmasteremail']);
}

/* update edit/tablename to admin/edit/tablename or xxxxx/edit/tablename (based on Jojo_Plugin_Admin) */
$data = Jojo::selectQuery("SELECT * FROM {page}  WHERE pg_link='Jojo_Plugin_Admin'");
if (count($data)) {
    $adminurl = $data[0]['pg_url'];
    $data = Jojo::selectQuery("SELECT * FROM {page}  WHERE pg_link='edit.php'");
    foreach ($data as $row) {
        Jojo::updateQuery("UPDATE {page} SET pg_link='Jojo_Plugin_Admin_Edit', pg_url='".$adminurl."/".$row['pg_url']."' WHERE pageid=".$row['pageid']." LIMIT 1");
    }
}

/* Warn about redirects plugin change */
if (Jojo::tableexists('redirect') && Jojo::selectRow('SELECT * FROM {redirect}') && !Jojo::selectRow('SELECT * FROM {plugin} where name = "jojo_redirect" AND active = "yes"')) {
    echo "<h3 style='color:red'>Redirect plugin</h3>
    <p>The redirect feature has now been moved into a seperate plugin. You have redirects in the database but the plugin is not currently installed. Go to the <a href='" . Jojo::getOption('siteurl','',true) . "/admin/plugins/'>Manage Plugins</a> and install the <em>Jojo Redirect</em> plugin to make redirects work again.</p>";
}

/* Plugin scanner to look for obvious errors in plugins */
include(_BASEPLUGINDIR . '/jojo_core/install/plugin-scanner.php');

/* Output HTML footer */
if (!isset($isAdmin) || !$isAdmin) {
    echo '<h2>Setup complete</h2>
    <p>Go to the <a class="btn btn-primary" href="' . Jojo::getOption('siteurl','',true) . '">Site Home</a> or <a class="btn btn-primary" href="' . Jojo::getOption('siteurl','',true) . '/admin/">Site Admin</a> to configure the install.</p>
    <div class="well">
        <h2>Want to reset location?</h2>
        <p>In order to move the location of a website, please follow these steps.<ul><li>Edit the <strong>_SITEURL</strong> constant in config.php to reflect the new location (you may need to create this line if it does not already exist).</li><li>Edit the <strong>RewriteBase</strong> line of .htaccess to reflect the new location.</li><li><strong>Run setup</strong> again (refresh this page).</li></ul></p>
    </div>';
    echo '<script type="text/javascript">document.getElementById(\'h1\').innerHTML = \'Setup Complete\';</script>';
    jojo_install_footer();
}

/* Remove cached plugin/theme files */
if (file_exists(_CACHEDIR . '/listPlugins.txt')) {
    unlink(_CACHEDIR . '/listPlugins.txt');
}
if (file_exists(_CACHEDIR . '/listThemes.txt')) {
    unlink(_CACHEDIR . '/listThemes.txt');
}
if (file_exists(_CACHEDIR . '/api.txt')) {
    unlink(_CACHEDIR . '/api.txt');
}
