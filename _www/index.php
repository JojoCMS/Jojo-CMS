<?php
/**
 * All requests are run through index.php
 *
 *                    Jojo CMS
 *                ================
 *
 * Copyright 2007-2008 Harvey Kane <code@harveykane.com>
 * Copyright 2007-2008 Michael Holt <code@gardyneholt.co.nz>
 *
 * See the enclosed file license.txt for license information.
 *
 * @author  Harvey Kane <code@harveykane.com>
 * @author  Michael Cochrane <mikec@jojocms.org>
 * @author  Melanie Schulz <code@gardyneholt.co.nz>
 * @package jojo_core
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);
/* Do we have a config file and .htaccess in place? */
if (file_exists('config.php') && isset($_GET['uri'])) {
    /* Yes, start serving pages */
    require_once('config.php');

    /* Did the config file define _BASEDIR? */
    if (!defined('_BASEDIR')) {
        /* No, see if we can guess it */
        if (file_exists(dirname(__FILE__) . '/includes/jojo.php')) {
            /* Found jojo in this folder :-) */
            define('_BASEDIR', dirname(__FILE__));
        } else {
            echo '_BASEDIR is not defined. Please define this in config.php';
        }
    }
    require_once(_BASEDIR . '/includes/defaultconfig.php');
    require_once(_BASEDIR . '/includes/jojo.php');
    exit;
}

/* ensure we aren't on index.php (301 back to root if we are) */
$uri_parts = explode('?', basename($_SERVER['REQUEST_URI'])); //handle querystrings
if (($uri_parts[0] == 'index.php') && file_exists('config.php') && file_exists('.htaccess')) {
    $protocol = ((isset($_SERVER['HTTPS']) &&($_SERVER['HTTPS'] == 'on')) || getenv('SSL_PROTOCOL_VERSION')) ? 'https://' : 'http://';
    $actualurl = $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    if (isset($_SERVER["HTTP_X_FORWARDED_HOST"])) {
        $actualurl = $protocol . $_SERVER["HTTP_X_FORWARDED_HOST"] . $_SERVER['REQUEST_URI'];
    }
    $correcturl = preg_replace('%(.*)/index\\.php%im', '$1', $actualurl);
    header("HTTP/1.1 301 Moved Permanently");
    header("Location: $correcturl");
    exit;
}

/* Create contents for default htaccess file */
$htaccess = <<<EOHTACCESS
# Jojo .htaccess file.
# Version 1740
RewriteEngine On

RewriteBase REWRITEBASE

PHPDIRECTIVES

# Catch all requests
RewriteRule ^$ index.php?uri= [L]
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php?uri=$1
EOHTACCESS;

/* Add php directives if php running as apache module */
$phpdirectives = '';
if (function_exists('apache_get_modules') && in_array('mod_php5', apache_get_modules())) {
    $phpdirectives = "\n# Prevent PHP Session IDs showing up in the url
php_value session.use_only_cookies 1
php_value session.use_trans_sid 0
";
}
$htaccess = str_replace("\nPHPDIRECTIVES\n", $phpdirectives, $htaccess);

/* Set re-write base correctly */
$rewritebase = str_replace('index.php', '', $_SERVER['REQUEST_URI']);
$htaccess = str_replace('REWRITEBASE', $rewritebase, $htaccess);

$protocol = ((isset($_SERVER['HTTPS']) &&
            ($_SERVER['HTTPS'] == 'on')) ||
        getenv('SSL_PROTOCOL_VERSION')) ? 'https://' : 'http://';

/* Are the config files good? */
if (function_exists('apache_get_modules') && !in_array('mod_rewrite', apache_get_modules())) {
    /* Apache running without mod_rewrite */
    jojo_install_header();
    echo '<div class="configdiv"><h4>Apache Configuration</h4>';
    echo "\n<p>You must have the Apache mod_rewrite module enabled to run Jojo CMS on this server.\n";

    echo "<p>You will need to edit your /etc/apache2/apache2.conf or /etc/httpd/conf/httpd.conf or similar file and enable mod_rewrite. Within this file locate the line containing 'LoadModule rewrite_module' and ensure it is not commented out.<br/><br/> It should look similar to the line shown below, i.e. no '#' at the start of the line.</p>\n";
    echo "<textarea rows='20' cols='60'>...
#LoadModule proxy_http_module modules/mod_proxy_http.so
#LoadModule proxy_ftp_module modules/mod_proxy_ftp.so
LoadModule rewrite_module modules/mod_rewrite.so
LoadModule setenvif_module modules/mod_setenvif.so
#LoadModule speling_module modules/mod_speling.so
...
</textarea>
Please <a href=\"".$protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']."\">reload</a> this page to continue installation once you have updated the htaccess file.\n</div>\n";

    jojo_install_footer();
    exit;
} elseif (file_exists('.htaccess') && !strpos(file_get_contents('.htaccess'), '# Version 1740')) {
    /* htaccess file is out of date */
    jojo_install_header();

    echo '<div class="configdiv"><h4>.htaccess</h4>';
    echo "\n<p>Your htaccess file is out of date\n";

    echo "<p>This is the content for the current default htaccess. Please open the existing .htaccess and replace the content with the content show below into the existing file. Ensure you also copy the comments.</p>\n";
    echo '<textarea rows="20" cols="60">';
    echo $htaccess;
    echo "</textarea>\n";
    echo "Please <a href=\"".$protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']."\">reload</a> this page to continue installation once you have updated the htaccess file.\n</div>\n";

    jojo_install_footer();
    exit;
} elseif (!file_exists('.htaccess')) {
    /* Missing htaccess file. Create it now */
    jojo_install_header();

    $fp = @fopen(".htaccess", 'w' );
    if ($fp != false && fwrite($fp, $htaccess)) {
        /* create file automatically */
        fclose($fp);

        echo '<h1>.htaccess</h1>';
        echo "\n<p>The .htaccess file is required by Jojo on Apache installations. This file was not found on your web server, so <strong>Jojo has created a new .htaccess file</strong> for you based on the code below.\n";

        if (!chmod (".htaccess", 0755)) {
            echo '<p>Your config directory is writabable, this may a security risk. We recommend you change the permissions to 0755.</p>';
        }

        echo '<p>This is the content of the .htaccess file that was created in <strong>'.dirname(__FILE__).'</strong></p>'."\n";
        echo '<textarea class="code" rows="16" cols="60">';
        echo $htaccess;
        echo '</textarea>';
        echo '<p class="action">Please <a href="'.$protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'].'">reload</a> this page to continue installation. <button onclick="window.location=\''.$protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'].'\';">reload</button></div></p>';
    } else {
        /* can't write into the file -  request to create config file manually */
        echo '<h1>.htaccess</h1>';
        echo "\n<p>The .htaccess file is required by Jojo on Apache installations. This file does not already exist on your web server, and Jojo does not have write permissions to create the file for you. Security-wise, this is a very good thing, but it does mean you will have to create the .htaccess file manually..\n";
        echo '<p>Please create a new file called <strong>.htaccess</strong> in the same folder as Jojo\'s <strong>index.php</strong> and copy-paste the code below into this file.</p>'."\n";
        echo '<textarea class="code" rows="16" cols="60">';
        echo $htaccess;
        echo '</textarea>';
        echo '<p class="action">Please create a .htaccess file containing the above code in your web root folder, then <a href="'.$protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'].'">reload</a> this page to continue. <button onclick="window.location=\''.$protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'].'\';">reload</button></p>';
        echo '</div>';
    }

    jojo_install_footer();
    exit;
} else {
    /* No, start the install process */
    session_start();

    /* See if jojo is in this folder */
    $basedir = '';
    $basedirs = array();
    if (empty($_POST['basedir']) && file_exists(dirname(__FILE__) . '/includes/jojo.php')) {
        //$_POST['basedir'] = dirname(__FILE__);
        $basedir = dirname(__FILE__);
    } else {
        /* scan for Jojo folders one level up */
        $folders = @scandir(dirname(__FILE__).'/..');
        if (is_array($folders)) {
            foreach ($folders as $folder) {
                if (file_exists(dirname(__FILE__).'/../' . trim($folder, '/') . '/includes/jojo.php')) {
                    $basedirs[] = realpath(dirname(__FILE__).'/../' . trim($folder, '/'));
                    if (empty($basedir)) $basedir = realpath(dirname(__FILE__).'/../' . trim($folder, '/'));
                }
            }
        }
    }
    $basedir = rtrim($basedir);

    if (empty($_POST['basedir']) && empty($_SESSION['basedir'])) {
        jojo_install_header();
        echo '<h1>Jojo CMS installation</h1><p>We need to create a config file which contains all the file locations and database details for your Jojo website. This is stored in the web root, the same folder as index.php and .htaccess are located.</p>'."\n";
        echo '<form method="post" action="install">';
        echo '<div class="box"><h2>Jojo basedir</h2>'."\n".'<p>Enter the location of your core Jojo files.</p>'."\n";
        echo '<label for="basedir">Jojo Base Directory: </label><input type="text" size="45" id="basedir" name="basedir" value="'.$basedir.'" /> ';
        echo '</div>';
        echo '<p class="action">Please set the Jojo base directory above. <button type="submit" name="submit" value="Next" />Next</button></p>';
        echo '</form>';
        if (count($basedirs)) {
            echo '<div class="box"><h3>Note:</h3><p>Jojo has been detected in the following locations:</p>';
            echo '<ul>';
            foreach ($basedirs as $b) {
                echo '<li>'.$b.'</li>';
            }
            echo '</ul></div>';
        }
        jojo_install_footer();
        exit;
    } else {

        // replace double slash if exist
        $_POST['basedir'] = isset($_POST['basedir']) ? str_replace('\\\\' , '/' , $_POST['basedir']) : '';
        $basedir = isset($_SESSION['basedir']) ? $_SESSION['basedir'] : rtrim($_POST['basedir'], '/');

        /* Jojo base files found */
        if (file_exists($basedir . '/includes/jojo.php')) {
            $_SESSION['basedir'] = $basedir;
            include($basedir . '/includes/install.php');
            exit;
        }

        /* Could't file Jojo files there */
        jojo_install_header();
        echo '<h1>Jojo CMS installation</h1>';
        echo '<div class="errors"><b>Error:</b> Jojo files not detected in ' . htmlentities($basedir, ENT_QUOTES, 'UTF-8') . "</div>\n";
        echo '<p>We need to create a config file which contains all the file locations and database details for your Jojo website. This is stored in the web root, the same folder as index.php and .htaccess are located.</p>'."\n";
        echo '<form method="post" action="install">';
        echo '<div class="box"><h2>Jojo basedir</h2>'."\n".'<p>Enter the location of your core Jojo files.</p>'."\n";
        echo '<label for="basedir">Jojo Base Directory: </label><input type="text" size="45" id="basedir" name="basedir" value="'.$basedir.'" /> ';
        echo '</div>';
        echo '<p class="action">Please set the Jojo base directory above. <button type="submit" name="submit" value="Next" />Next</button></p>';
        echo '</form>';
        if (count($basedirs)) {
            echo '<div class="box"><h3>Note:</h3><p>Jojo has been detected in the following locations:</p>';
            echo '<ul>';
            foreach ($basedirs as $b) {
                echo '<li>'.$b.'</li>';
            }
            echo '</ul></div>';
        }
        jojo_install_footer();
    }
    exit;
}

/* Output the setup html header */
function jojo_install_header() {
    $version = '';
    if (!empty($_SESSION['basedir']) && file_exists($_SESSION['basedir'].'/version.txt')) {
        $version = file_get_contents($_SESSION['basedir'].'/version.txt');
    }
    if (defined('_BASEDIR') && file_exists(_BASEDIR.'/version.txt')) {
        $version = file_get_contents(_BASEDIR.'/version.txt');
    }
    header("Content-type: text/html; charset=utf-8");
    echo <<<EOHEADER
<html>
  <head>
    <title>Jojo CMS Installation</title>
    <style type="text/css">
        body {
          color: #333;
          margin: 0;
          padding: 0;
          font-family: verdana, arial, tahoma, sans-serif;
          line-height: 120%;
          font-size: 11px;
          text-align: center;
        }

        a {
          text-decoration: underline;
          color: #000;
        }

        #wrap {
          width: 760px;
          margin: 10px auto;
          text-align: left;
        }

        #header {
          color: #fff;
          text-align: right;
          color: #5E7FBB;
          height: 93px;
          border-bottom: 1px solid #eee;
          margin: 20px 0 0 0;
          font-size: 2em;
        }

        h1 {
          color: #4C5FA3;
          font-size: 1.9em;
          letter-spacing: 1px;
          margin-bottom: 20px;
          font-weight: normal;
        }

        h2 {
          color: #4C5FA3;
          font-size: 18px;
          margin: 0 0 10px 0;
          font-weight: 400;
        }

        h3 {
          color: #222;
          font-size: 14px;
          font-weight: bold;
          margin: 0;
        }

        h4 {
          color: #5E7FBB;
          font-weight: 700;
          margin-bottom: 8px;
          margin-top: 10px;
        }

        #logo {
          float: left;
          width: 175px;
          height: 83px;
          border: 0;
          background: transparent url(data:image/gif;base64,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) left top no-repeat;
        }

        .action {
          background: #4C5883;
          padding: 10px;
          margin: 10px 0;
          color: #fff;
          font-weight: bold;
        }

        .action a {
          color: #fff;
        }

        #content {
          width: 570px;
          padding: 0;
          float: left;
        }

        #next {
          width: 46px;
          height: 44px;
          background: transparent url(data:image/gif;base64,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) left top no-repeat;
          margin: 5px auto;
        }

        .errors {
          color: #fff;
          margin: 10px 0;
          padding: 10px;
          border: 1px solid #eee;
          background: #900;
          font-weight: bold;
          border: 2px solid #600;
        }

        .errors h2, .errors h3, .errors h4 {
          color: #fff;
        }

        #sidebar {
          width: 160px !important;
          width: 180px;
          float: right;
          background: #F7F7F7;
          border: 1px solid #EFEFEF;
          padding: 10px;
          margin: 10px 0;
        }

        .box {
          background: #F7F7F7;
          border: 1px solid #EFEFEF;
          padding: 10px;
          margin: 10px 0;
        }

        #version {
          width: 141px;
          width: 141px;
          height: 40px;
          padding: 5px 10px 0 90px;
          color: #fff;
          font-weight: bold;
          background: transparent url(data:image/gif;base64,R0lGODlhjQAoALMAAHyFpKCmvVhji3B5nPP097e8zdve5s/S3ufp7mRulMPH1quxxZSbtYiQrP///0xYgyH5BAAAAAAALAAAAACNACgAAAT/8MlJq7046827/2AojmRpnmiqrmzrvnAsz3Rt33iu73zv/xmHcAjYJAIKg5BwWDQ8Q6LmmFw2n8BTVFjEDA7bLSHACXct33B0nC2ZMQu12jDQvC1x+ZbeFt0rCnpqBHUYfxOBgmKFfVBbZxMFioOMFYcPkpOLjY5RkA8BagcBAAALCHOGjxShYaOlp6mcZasTAgRhDBUCmVu6Fn+3ubu9Ub+zQbUSrcYYxUIIF3/MQ8cVzw7RyMmeFEpRBxkCqFuVEn/fQ+EY42Hm2xN/CcMZ1EJklrXzvhr2Dvjw8nWTACBMAg0FtywAVithlIMZHA5ZGFCglGVhaIFjONCfRnUV/y1ymeBxgyuOF0va2bIu5Dll/gSY3KIA5UhQYWSujFLT5cuBDxhcqjAgDMB4tYQqu1B0y9GQfyQKoYjBHxYKd6Q6oHrBqs+fFyXgWqPzArkhBMoiBToWrdoKZ5e8DdgUKKYwPS1gK4DmD7a818Lw/ZroIYW6PNXyUgORQuEhjREPUaAYm4PGFRukQ6t3UIEAAQq0jcL1geYwBAKj/hx69ESXm51aaKcpigFvep7Sri3ktkswahDMfTDAtSIDaoGHEY7GuCDkPvPswVwhQWw9Bd5Kt019gvXa2b8qHYIgwPAKAeK6+iRhPDTzG9ILOsC+YlEDCgK829CggHIEo+wnwR19+QmIQX//BfjVggw26OCDEEYo4YQUVmjhhRhGAAA7) left top no-repeat;}
        }

        #sidebar li.selected {
          font-weight: bold;
        }

        div.clear {
          clear: both;
        }

        textarea.code {
          border: 1px dashed #4C5FA3;
          background: #f5f5f5;
          margin: 10px 0;
          padding: 10px;
          width: 570px;
        }

        form.form label {
          float: left;
          width: 150px;
          display: block;
          text-align: right;
          margin-right: 5px;
          margin-bottom: 3px;
        }

        form.form input {
          float: left;
          margin-bottom: 3px;
        }

        form.form br {
          clear: left;
          margin-bottom: 3px;
        }

        #tooltips {
          display: none;
          float: right;
          background: #B8F7B8;
          border: 1px solid #333;
          padding: 10px;
          margin: 0;
          width: 150px;
        }

        #setup_loading {
          width: 128px;
          height: 15px;
          margin-top: 10px;
          background: transparent url(data:image/gif;base64,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) left top no-repeat;
        }


    </style>

    <script type="text/javascript">
    function toolTip(tip)
    {
        if (tip) {
            document.getElementById('tooltips').innerHTML = tip;
            document.getElementById('tooltips').style.display = 'block';
        } else {
            document.getElementById('tooltips').style.display = 'none';
        }
    }
    </script>
  </head>
<body>
<div id="wrap">
<div id="header"><div id="logo"></div>Jojo CMS Installation</div>
EOHEADER;


    echo '<div id="sidebar">
      <h3>License</h3>
      <p>Jojo CMS is licensed under the <a href="http://www.jojocms.org/license/" target="_BLANK">Lesser GPL</a>.</p>

      <h3>Support</h3>
      <p>If you have trouble installing Jojo, please use the <a href="http://www.jojocms.org/forums/" target="_BLANK">Jojo forums</a> as the first point of contact. <a href="http://www.jojocms.org/contact/" target="_BLANK">Paid support</a> is also available upon request.</p>
      <div id="version">'.$version.'</div>
    </div>

    <div id="content">';
}

/* Output the setup html footer */
function jojo_install_footer() {
    echo "</div>\n\n";
    echo "</div>\n";
    echo "</body>\n";
    echo "</html>\n";
    //unlink('.htaccess');
}