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

/* Should we be here? */
if (file_exists('config/config.php') || file_exists('config.php')) {
    /* No, redirect to setup */
    header('Location: setup/');
    exit;
}

error_reporting(E_ALL & ~E_NOTICE);
ini_set('display_errors', 1);

/* Output html header */
jojo_install_header();

$errors = array();

$action = 'step2';

switch($action) {

    /* Step 2 - Site Settings */
    case 'step2':
        /* Check Step 2 Fields */
        $missing = false;
        $found  = false;
        $fields = array(
                'masterpass'     => '',
                'confirmpass'    => '',
                'sitetitle'      => '',
                'webmastername'  => '',
                'webmasteremail' => '',
                'admin'          => 'admin',
                    );

        foreach($fields as $f => $default) {
            if (!empty($_POST[$f])) {
                $$f = $_POST[$f];
                $_SESSION[$f] = $_POST[$f];
                $found = true;
            } elseif (!empty($_SESSION[$f])) {
                $$f = $_SESSION[$f];
                $found = true;
            } else {
                $$f = $default;
                $missing = true;
            }
        }

        /* User submitted some values so lets check them */
        if ($found) {
            if (empty($webmastername)) {
                $errors[] = 'Webmastername is empty';
            }
            if (empty($sitetitle)) {
                $errors[] = 'Site title is empty';
            }
            if (empty($webmasteremail)) {
                $errors[] = 'Webmasteradress is empty';
            } elseif(!preg_match('/^\w[-.\w]*@(\w[-._\w]*\.[a-zA-Z]{2,}.*)$/', $webmasteremail)) {
                $errors[] = 'Webmasteradress '.$webmasteremail.' is not valid';
            }
            if (empty($confirmpass)) {
                $errors[] = 'Password Confirmation is empty';
            }
            if (empty($masterpass)) {
                $errors[] = 'Master password is empty';
            }
            if ($masterpass != $confirmpass)  {
                $errors[] = 'The Confirmation Password isn\'t the same as the Masterpassword.';
            }

            if (!preg_match('/^[a-z0-9]*$/', $admin)) {
                $errors[] = 'The Admin URI needs to be lowercase alphanumeric with no spaces, special characters.';
            }

            if (count($errors)) {
                $errortext = implode('<br />',$errors);
                echo '<div class="errors"><h2>Errors</h2>' . $errortext . '</div>';
                $missing = true;
            }
        }

        /* Missing fields, show form */
        if ($missing) {
            echo '<h1>Site information</h1>'."\n";
            echo '<form class="form inline" method="post" action="install">'."\n";
            echo '<p>The following settings are required to run Jojo. These can be changed later from the "edit options" page.</p>';
            echo '<div class="well">
            <div class="form-group">
                <label for="sitetitle">Site Title: </label>
                <input class="form-control" type="text" size="30" id="sitetitle" name="sitetitle" value="'.$sitetitle.'" />
                 <div class="help-block">This should generally be the name of the business, or the name of the website. The site title will be appended to the end of the title of every page, and is used as the FROM in all emails sent by the website. </div>
            </div>
            <div class="form-group">
                <label for="webmastername">Webmaster Name: </label>
                <input class="form-control" type="text" size="30" id="webmastername" name="webmastername" value="'.$webmastername.'" />
                 <div class="help-block">The name of the person or company who will be managing this site.</div>
            </div>
            <div class="form-group">
                <label for="webmasteremail">Webmaster Email: </label>
                <input class="form-control" type="text" size="30" id="webmasteremail" name="webmasteremail" value="'.$webmasteremail.'" />
                 <div class="help-block">Used on 404 pages, and as the contact address for system reports</div>
           </div>
            <div class="form-group">
            <label for="masterpass">Master Password: </label>
            <input class="form-control" type="password" size="30" id="masterpass" name="masterpass" value="'.$masterpass.'" />
                <div class="help-block">This password is used to protect the Jojo setup process, and will also become the initial admin user password.</div>
            </div>
            <div class="form-group">
                <label for="confirmpass">Confirm Password: </label>
                <input class="form-control" type="password" size="30" id="confirmpass" name="confirmpass" value="'.$confirmpass.'" />
            </div>
            
            <div class="form-group">
                <label for="admin">Admin URI: </label>
                <input class="form-control" type="text" size="30" id="admin" name="admin" value="'.$admin.'" />
                <div class="help-block">The base address of the Jojo admin section. The default admin page will be www.example.com/admin/ - usually the default is fine, but you will need to change this if the /admin/ URI is already in use by the web hosting control panel.</div>
            </div>'."\n";
            echo '</div>';
            echo '<p class="action">Please complete the above fields then move on to the next step. <button class="btn btn-default" type="submit" name="submit" value="Next" />Next</button></p>';
            echo '</form>';
            echo ''."\n";
            break;
        }

    /* Step 3 - Create a config file */
    case 'step3':
        /* Check Step 2 Fields */
        $missing = false;
        $found = false;
        $protocol = ((isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on')) || getenv('SSL_PROTOCOL_VERSION')) ? 'https://' : 'http://';
        $fields = array(
                    'dbhost' => 'localhost',
                    'dbuser' => '',
                    'dbpass' => '',
                    'dbname' => '',
                    'webdir' => dirname($_SERVER['SCRIPT_FILENAME']),
                    'sitedir' => '',
                    'altplugindir' => '',
                    'siteurl' => preg_replace('%(.*?)/install/?%', '$1', $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']),
                        );

        foreach($fields as $f => $default) {
            if (!empty($_POST[$f])) {
                $$f = $_POST[$f];
                $_SESSION[$f] = $_POST[$f];
                $found = true;
            } elseif (!empty($_SESSION[$f])) {
                $$f = $_SESSION[$f];
                $found = true;
            } elseif ($f == 'dbpass') {
                $$f = '';
            } else {
                $$f = $default;
                $missing = true;
            }
        }

        /* User submitted some values so lets check them */
        if ($found) {
            $link = @mysql_connect($dbhost, $dbuser, $dbpass);
            if (!$link) {
                $errors[] = 'Could not connect to database: '. mysql_error();
            } else {
                $db_selected = @mysql_select_db($dbname, $link);
                if (!$db_selected) {
                    $errors[] = 'Could not select database ' . $dbname . ': ' . mysql_error();
                } else {
                    @mysql_query('ALTER DATABASE `' . $dbname . '` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci', $link);
                }
            }
            @mysql_close($link);

            if (!file_exists($basedir)) {
                $errors[] = 'Base Directory ' . $basedir . ' not found';
            }
            if (!file_exists($webdir)) {
                $errors[] = 'Web Dir ' . $webdir . ' not found';
            }
            if (!file_exists($sitedir)) {
                $errors[] = 'My Site Directory ' . $sitedir . ' not found';
            }
            if ($altplugindir && !file_exists($altplugindir)) {
                $errors[] = 'Shared Plugin Directory ' . $altplugindir . ' not found';
            }

            if (count($errors)) {
                $errortext = implode('<br />',$errors);
                echo '<div class="errors"><h2>Errors</h2>' . $errortext . '</div>';
                $missing = true;
            }
        }

        $webdir  = str_replace('\\', '/', $webdir);
        $basedir = str_replace('\\', '/', $basedir);
        $sitedir = str_replace('\\', '/', $sitedir);
        $altplugindir = str_replace('\\', '/', $altplugindir);
        $data = explode('/', $webdir);
        $suggested_mysite = '';
        for ($i=0; $i<(count($data)-1); $i++) {
            $suggested_mysite .= $data[$i].'/';
        }
        $suggested_mysite .= 'mysite';

        if (empty($mysitedir)) $mysitedir = $suggested_mysite;

        if ($missing) {
            echo '<h1>Create config file</h1><p>The config file contains </p>'."\n";
            echo '<form method="post" action="install" class="form inline">';
            echo '<div class="well">
            <h2>Database Details</h2>
            <p>These details should have been provided to you by your web host. Some hosts also let you create a database using their hosting control panel.</p>
            <div class="form-group">
                <label for="dbhost">Database host: </label><input class="form-control" type="text" size="30" id="dbhost" name="dbhost" value="'.$dbhost.'" />
           </div>
            <div class="form-group">
                <label for="dbname">Database name: </label><input class="form-control" type="text" size="30" id="dbname" name="dbname" value="'.$dbname.'" />
           </div>
            <div class="form-group">
                <label for="dbuser">Database username: </label><input class="form-control" type="text" size="30" id="dbuser" name="dbuser" value="'.$dbuser.'" />
           </div>
            <div class="form-group">
                <label for="dbpass">Database password: </label><input class="form-control" type="password" size="30" id="dbpass" name="dbpass" value="' . $dbpass . '" />
           </div>
            <h2>Site URL</h2>
            <p>The Site URL is the address of your homepage. Please include the http:// but do not include the trailing slash. (eg <strong>http://www.example.com</strong>)</p>
            <div class="form-group">
                <label for="siteurl">Site URL: </label><input class="form-control" type="text" size="45" name="siteurl" value="'.$siteurl.'" />
           </div>
            <p>The Secure URL is the SSL secured URL of your site (if you have one - leave blank if not). Please include the https:// but do not include the trailing slash. (eg <strong>https://www.example.com</strong>)</p>
            <div class="form-group">
                <label for="secureurl">Secure URL: </label><input class="form-control" type="text" size="45" name="secureurl" value="" />
           </div>
            <h2>File locations</h2>
            <p>The <strong>base directory</strong> and <strong>web directory</strong> should already be completed for you. Please check that these are correct.</p>
            <div class="form-group">
                <label for="webdir">Web directory: </label><input class="form-control" type="text" size="45" id="webdir" name="webdir" value="'.$webdir.'" />
           </div>
            <div class="form-group">
                <label for="basedir">Base Directory: </label><input class="form-control" type="text" size="45" id="basedir" name="basedir" value="'.$basedir.'" />
           </div>
            <div class="form-group">
                <label for="sitedir">My Site Directory: </label><input class="form-control" type="text" size="45" id="sitedir" name="sitedir" value="'.$mysitedir.'" />
                <div class="help-block">Please create the mysite folder manually before proceeding</div>
           </div>
            <div class="form-group">
                <label for="altplugindir">Shared Plugin Directory: </label><input class="form-control" type="text" size="45" id="altplugindir" name="altplugindir" value="'.$altplugindir.'" />
           </div>
            </div>
            <p class="action">Please complete the above fields and manually create the <strong>mysite</strong> folder, then proceed with the installation. <button class="btn btn-default" type="submit" name="submit" value="Create Config File">Next</button></p>';
            echo '</form>'."\n";
            echo '<div class="box"><h3>What is the mysite folder?</h3>';
            echo '<p>The <strong>mysite</strong> folder is used to keep all the site-specific files for your Jojo install, including plugins, themes, uploaded files and cached data. It is recommended that this is kept one-level above the web root folder (eg '.$suggested_mysite.'), however this may not always be possible depending on your hosting. An often easier solution is to create mysite as a subfolder of the web root (eg '.$webdir.'/mysite) however can be a security concern on some shared servers as parts of the mysite folder need to be writable.</p><p>Jojo will attempt to create the mysite folder in the location you have specified, however it is recommended that you create this folder manually before proceeding.</p>';
            echo '</div>';
            break;
        }

    case 'step4':
        /* Text for config file */
        $configText = <<<EOCONFIG
<?php

/* Database Connection */
define('_DBHOST', '%s');
define('_DBUSER', '%s');
define('_DBPASS', '%s');
define('_DBNAME', '%s');

/* Directory locations */
define('_SITEURL',   '%s');
define('_SECUREURL',   '%s');
define('_BASEDIR',   '%s');
define('_WEBDIR',    '%s');
define('_MYSITEDIR', '%s');

/* Shared plugin directory (if used) */
define('_ALTPLUGINDIR', '%s');

/* Debug Options - not recommended for production installations */
define('_DEBUG', false);

/* Master password */
define('_MASTERPASS', '%s');

/* Admin root */
define('_ADMIN', '%s');
EOCONFIG;

        if (!isset($_SESSION['dbpass'])) $_SESSION['dbpass'] = ''; //prevent notice error
        $configText = sprintf($configText,
                              $_SESSION['dbhost'], $_SESSION['dbuser'], $_SESSION['dbpass'], $_SESSION['dbname'],
                              rtrim($_SESSION['siteurl'], '/'), (isset($_SESSION['secureurl']) && $_SESSION['secureurl'] ?  rtrim($_SESSION['secureurl'], '/') : rtrim($_SESSION['siteurl'], '/')), rtrim(str_replace('\\', '/', $_SESSION['basedir']), '/'), rtrim($_SESSION['webdir'], '/'), rtrim($_SESSION['sitedir'], '/'), rtrim($_SESSION['altplugindir'], '/'),
                              $_SESSION['masterpass'], $_SESSION['admin']);

        /* If possible create config file automatically */
        require_once($_SESSION['basedir'] . '/plugins/jojo_core/classes/Jojo.php');
        $fp = @fopen($webdir . "/config.php", 'w' );

        if ($fp !== false && fwrite($fp, $configText)) {
            // create file automatically
            fclose($fp);

            echo '<h1>config.php</h1>'."\n";
            echo '<p>Your config file was created automatically in the webdirectory.</p>'."\n";
            echo '<form method="post" action="setup/">'."\n";
            echo '<p class="action">Jojo is now ready to start setting up the database. <button class="btn btn-default" type="submit" name="submit">Next</button>'."\n";
            echo '</p></form>';
            echo '<p>This is the content of config.php which has been created in the webroot directory.</p>'."\n";
            echo '<textarea class="code" rows="25" cols="60">';
            echo $configText;
            echo '</textarea>';
        } else {
            // can't write into the file -  request user to create config file manually
            echo '<h1>config.php</h1>'."\n";
            echo '<p>This is your config file. Please create a new file called <strong>'.$webdir.'/config.php</strong> and copy-paste the code below into this file.</p>'."\n";
            echo '<textarea class="code" rows="25" cols="60">';
            echo $configText;
            echo '</textarea>';
            echo '<p class="action"><form method="post" action="setup/">';
            echo '<button class="btn btn-default" type="submit" name="submit" value="Start Installation">Start Installation</button><br "style=clear:both" />'."\n";
            echo '</form></p>';
        }
}

jojo_install_footer();
