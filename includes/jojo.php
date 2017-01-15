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

/* Include config files or begin install process */
if (!file_exists('config.php')) {
    header("Content-type: text/html; charset=utf-8");
    require(_BASEDIR . '/install.php');
    exit;
}

/* Include core code */
require_once(_BASEPLUGINDIR . '/jojo_core/classes/Jojo.php');

$maintimer = Jojo::timer();

/* Merge in any get variable from the reguest uri */
if ($str = strstr($_SERVER['REQUEST_URI'], '?')) {
    parse_str(substr($str, 1), $get);
    $_GET = array_merge($get, $_GET);
    $_REQUEST = array_merge($_REQUEST, $_GET);
}

/* Include the setup file if it has been requested */
if (strtolower(trim($_REQUEST['uri'], '/')) == 'setup') {
    require(_BASEDIR . '/includes/setup.php');
    exit;
}

ob_start(); // start the output buffer

/* If cache settings aren't set in config, get them from the database */
defined('_CONTENTCACHE') or define('_CONTENTCACHE', (Jojo::getOption('contentcache') == 'no' ? false : true));
defined('_CONTENTCACHETIME') or define('_CONTENTCACHETIME', Jojo::either(Jojo::getOption('contentcachetime'), 3600));
defined('_CONTENTCACHETIMERESOURCES') or define('_CONTENTCACHETIMERESOURCES', Jojo::either(Jojo::getOption('contentcachetime_resources'), 604800));
defined('_RESOURCEROOTCACHE') or define('_RESOURCEROOTCACHE', (Jojo::getOption('resource_rootcache') == 'yes' ? _WEBDIR : false));

/* Set the timezone */
if (function_exists('date_default_timezone_set')) {
    defined('_SITETIMEZONE') or define('_SITETIMEZONE', Jojo::getOption('sitetimezone'));
    if (_SITETIMEZONE) {
        @date_default_timezone_set(_SITETIMEZONE);
    }
}

$extension = Jojo::getFileExtension($_GET['uri']);
$resourcerequest = (boolean)(in_array($extension, array('jpg', 'jpeg', 'gif', 'png', 'svg', 'js', 'css', 'swf', 'woff', 'svg', 'eot', 'ttf')) && strpos($_GET['uri'],'/private/')===false);
/* check public cache */
if ($resourcerequest  && !Jojo::ctrlF5()) {
    $cachefile = _CACHEDIR.'/public/'. $_GET['uri'];
    if (Jojo::fileExists($cachefile)) {
        /* output data */
        Jojo_Plugin_Core::sendCacheHeaders(filemtime($cachefile), _CONTENTCACHETIMERESOURCES);
        $data = file_get_contents($cachefile);
        header('Content-type: ' . Jojo::getMimeType($cachefile));
        header('Content-Length: ' . strlen($data));
        header('Content-Disposition: inline;');
        header('Content-Transfer-Encoding: binary');
        echo $data;
        ob_end_flush(); // Send the output and turn off output buffering
        exit();
    }
    unset($cachefile);
}

/* Set php ini settings */
//These settings can be added to .htaccess, however Dreamhost (and possibly others) does not allow these.
//This forces session ids into a cookie, never the querystring. This is to avoid SEs indexing session ids, msn in particular.
//TODO: Should use ini_get to check settings first
@ini_set('session.use_only_cookies', 1);
@ini_set('session.use_trans_sid', 0);
ini_set('magic_quotes_gpc', 0);
ini_set('magic_quotes_runtime', 0);
ini_set('session.gc_maxlifetime', 86400);
if (extension_loaded('mbstring')) {
    mb_internal_encoding('UTF-8');
}
/* Error display */
if (_DEBUG) {
    /* Display useful errors for developer */
    error_reporting(E_ALL ^ E_NOTICE);
    ini_set('display_errors', 1);
} else {
    /* Don't display errors */
    error_reporting(0);
    ini_set('display_errors', 0);
}

/* _SITEURL can be defined in config file (preferred) or in database */
defined('_SITEURL') or define('_SITEURL', Jojo::getOption('siteurl')); //set _SITEURL constant from database
defined('_SECUREURL') or define('_SECUREURL', _SITEURL); 

if (Jojo::usingSslConnection()) {
    define('_PROTOCOL', 'https://');
    $issecure = true;
    define('_SECURESITEFOLDER', ltrim(str_replace(_PROTOCOL . ( isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '' ) , '' , _SECUREURL), '/')); //the folder in which the website resides, if not the root (eg https://www.foo.com/FOLDER/index.php will return "FOLDER")
} else {
    define('_PROTOCOL', 'http://');
    $issecure = false;
}

define('_SITEFOLDER',       ltrim(str_replace(_PROTOCOL . ( isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '' ) , '' , _SITEURL), '/')); //the folder in which the website resides, if not the root (eg http://www.foo.com/FOLDER/index.php will return "FOLDER")

$logindata = false;
$loginfailure = false;
$_USERID = false;
$_USERGROUPS = array('everyone');

/* For resource requests (images, js, css etc) bypass everything only needed for content page requests */
if (!$resourcerequest) {

    defined('_SESSIONHANDLER') or define('_SESSIONHANDLER', Jojo::getOption('enable_sessionhandler', false)); //use database session handler
    if (_SESSIONHANDLER) {
        @ini_set('session.save_handler', 'user');
        session_set_save_handler(array('Jojo_SessionHandler', 'open'),
                                 array('Jojo_SessionHandler', 'close'),
                                 array('Jojo_SessionHandler', 'read'),
                                 array('Jojo_SessionHandler', 'write'),
                                 array('Jojo_SessionHandler', 'destroy'),
                                 array('Jojo_SessionHandler', 'gc'));
    }
    if ($issecure) {
        session_set_cookie_params(0, '/' . _SECURESITEFOLDER);
    } else {
        session_set_cookie_params(0, '/' . _SITEFOLDER);
    }

    /* if sid is set in the GET request on a secure URL, restore this session (previously started on non-secure site). Only applies if secure domain != non-secure domain */
    if ($issecure && !empty($_GET['sid']) && (_SITEURL != _SECUREURL) && (str_replace('http://', '', _SITEURL) != str_replace('https://', '', _SECUREURL))) {
        /* load the session with the specified session id */
        session_id($_GET['sid']);
        session_name('jojo');
        session_start();
        $_SESSION['secure_session_started'] = true;
        /* redirect to strip the session id from the URL */
        $redirect = preg_replace('/(.*)(\?|&)sid=[^&]+?(&)(.*)/i', '$1$2$4', _PROTOCOL.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'] . '&');
        $redirect = substr($redirect, 0, -1);
        Jojo::redirect($redirect);
    }
    session_name('jojo');
    session_start();

    /* Redirect to Mobile-only version of site */
    if (isset($_POST['set_mobile'])) {
        $set_mobile = (boolean)($_POST['set_mobile'] == '1');
        Jojo::setMobile($set_mobile);
        Jojo::redirect(_PROTOCOL . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
    }

    /* Authentication */
     /* Check for someone logging in  */
    if (Jojo::getFormData('username', false) && $authClass = Jojo::getFormData('_jojo_authtype', false)) {
        // TODO: clean this input
        $authClass = 'Jojo_Auth_' . $authClass;
        $logindata = call_user_func(array($authClass, 'authenticate'));
        if (is_array($logindata) && isset($logindata['userid'])) {
            $_SESSION['userid'] = $logindata['userid'];
        } else {
            $loginfailure = $logindata;
        }
    } else {
    /* Setup global variables for already logged in user */
        $logindata = Jojo::authenticate();
    }

    // page cache settings
    $dynamic_url    = $_SERVER['REQUEST_URI']; // requested dynamic page (full url)
    $cache_file     = _CACHEDIR . '/public/html' . $dynamic_url .'index.html'; // construct a cache file
    /* Check for cached copy of page and display if not expired */
    if (!(isset($_USERID) && $_USERID) && _CONTENTCACHE && !Jojo::ctrlF5() && count($_POST)==0 && file_exists($cache_file)) { //check cache copy exists 
            Jojo_Plugin_Core::sendCacheHeaders(filemtime($cache_file), _CONTENTCACHETIME);
            readfile($cache_file); //read Cache file
            if (strpos($dynamic_url, '/rss/')!==false) {
                header('Content-type: application/rss+xml');
                header('Content-Length: ' . strlen(ob_get_contents()));
                ob_end_flush(); //Flush and turn off output buffering
            } elseif (strpos($dynamic_url, '/pdf/')!==false) {
                $uriparts = explode('/', trim($dynamic_url,'/'));
                $pdftitle = array_pop($uriparts);
                header('Content-type: application/pdf');
                header('Content-Disposition: attachment; filename=' . ucwords(str_replace(array('-','_'), ' ', $pdftitle)) . '.pdf');
                header('Content-Length: ' . strlen(ob_get_contents()));
                ob_end_flush(); //Flush and turn off output buffering
            } else{
                header('Content-type: text/html; charset=' . (isset($charset) && $charset != '' ? $charset : 'utf-8'));
                header('Content-Length: ' . strlen(ob_get_contents()));
                ob_end_flush(); //Flush and turn off output buffering
                echo '<!-- cached page - ' . date('l jS \of F Y h:i:s A', filemtime($cache_file)) . ', Page : ' . $dynamic_url . ' -->';
            }
            exit();
    } elseif (Jojo::ctrlF5() && file_exists($cache_file)){
            unlink($cache_file);
    }

}

/* Work out the Site URI with respect to the Site URL */
if (preg_match('%^/?' . _SITEFOLDER . '/?(.*)%', $_SERVER['REQUEST_URI'], $regs)) {
    $fullSiteUri = trim($regs[1], '/');
} elseif (Jojo::usingSSLConnection() && preg_match('%^/?' . _SECURESITEFOLDER . '/?(.*)%', $_SERVER['REQUEST_URI'], $regs)) {
    $fullSiteUri = trim($regs[1], '/');
} else {
    $fullSiteUri = trim($_SERVER['REQUEST_URI'], '/');
}

/* Remove the subsection code off the front of the URI if this is a multi-sectioned site */
$mldata = Jojo::getMultiLanguageData();
$uri = $fullSiteUri;
/* Find the first part of the uri */
$urlParts = explode('/', $uri);
$urlPrefix = $urlParts[0];

if (isset($mldata['roots'][$urlPrefix])) {
    /* Check if the prefix is a section short code */
    $uri = (string)substr($uri, strlen($urlPrefix));
    $uri = trim($uri, '/');
} elseif ($l = array_search($urlPrefix, $mldata['longcodes'])) {
    /* Check if the prefix is a section long code */
    $uri = (string)substr($uri, strlen($urlPrefix));
    $uri = trim($uri, '/');
}

define('_SITEURI', $uri); // Site URI without the language prefix
define('_FULLSITEURI', $fullSiteUri); // Site URI including the langauge prefix

//relative url is the path from the base url eg for http://foo.com/myfolder/articles/23/foo-bar/ this would be articles/23/foo-bar
$relativeurl = (_SITEFOLDER!='') ? ltrim(str_replace(_SITEFOLDER . '/', '', $_SERVER['REQUEST_URI']) , '/') : ltrim($_SERVER['REQUEST_URI'], '/');
define('_RELATIVE_URL', $relativeurl );
define('_MULTILANGUAGE', (boolean)( isset($mldata['roots']) && count($mldata['roots'])>1 ));

/* Include plugin api.php's so filters and hooks get added */
if (_DEBUG || Jojo::ctrlF5() || !file_exists(_CACHEDIR . '/api.txt')) {
    $all = '<?php ';
    foreach (Jojo::listPlugins('api.php') as $pluginfile) {
        $code = trim(file_get_contents($pluginfile));
        if ($code) {
            $all .= '?>' . $code;
            include($pluginfile);
        }
    }

    /* Strip out all the stuff we don't need in api.txt */
    $all = str_replace(array('?><?php', "\r"), '', $all);
    $all = preg_replace('#\$_provides\[\'fieldTypes\'\](.*);#Ums', '', $all);
    $all = preg_replace('#\$_provides\[\'pluginClasses\'\](.*);#Ums', '', $all);
    $all = preg_replace('#/\*(.*)\*/#Ums', '', $all);
    $all = preg_replace('#\$_options\[\](.*);#Ums', '', $all);
    //$all = preg_replace('#\n(\n)+#', "\n", $all); //this line causing connection reset error on WAMP

    /* Cache all table exists calls */
    $all = preg_replace_callback('#Jojo::tableExists\(\'([a-z0-9_]*)\'\)#Ums',
                                create_function('$matches', 'return Jojo::tableExists($matches[1]) ? "true" : "false";'),
                                $all);

    file_put_contents(_CACHEDIR . '/api.txt', $all);
} else {
    include(_CACHEDIR . '/api.txt');
}

if (!$resourcerequest) {

    define('_SITENAME',         Jojo::getOption('sitetitle'));
    define('_NONSECUREURL',     Jojo::getOption('siteurl'));
    define('_CONTACTNAME',      Jojo::either(Jojo::getOption('contactname'), Jojo::getOption('fromname'), Jojo::getOption('webmastername'))); //used for contact form
    define('_CONTACTADDRESS',   Jojo::either(Jojo::getOption('contactaddress'), Jojo::getOption('fromaddress'), Jojo::getOption('webmasteraddress'))); //used for contact form
    define('_FROMNAME',         Jojo::either(Jojo::getOption('fromname'), Jojo::getOption('sitetitle')));
    define('_FROMADDRESS',      Jojo::getOption('fromaddress'));
    define('_WEBMASTERNAME',    Jojo::getOption('webmastername'));
    define('_WEBMASTERADDRESS', Jojo::getOption('webmasteraddress'));
    define('_SITETITLE',        Jojo::getOption('sitetitle'));
    define('_SHORTTITLE',       Jojo::getOption('shorttitle'));

    /* if no assets set, use siteurl/secureurl to make resource links absolute for browsers that don't understand base href) */
    if ($issecure ) {
        $ASSETS[] = _SECUREURL . '/';
    } else {
        /* define assets array */
        $ASSETS = array();
        foreach (explode("\n", Jojo::getOption('assetdomains')) as $a) {
            if (trim($a)) {
                $ASSETS[] = trim($a) . '/';
            }
        }
        if (empty($ASSETS)) {
            $ASSETS[] = _SITEURL . '/';
        }
    }
    /* Initialise template engine */
    $templateEngine = Jojo::getOption('templateengine', 'smarty');
    switch ($templateEngine) {
        case 'smarty':
            require_once(_BASEPLUGINDIR . '/jojo_core/external/smarty/Smarty.class.php');

            $smarty = new Smarty();
            $smarty->compile_dir  = _CACHEDIR . '/smarty/templates_c';
            $smarty->cache_dir    = _CACHEDIR . '/smarty/cache';
            $smarty->default_resource_type = 'jojo';
            $smarty->register_resource('jojo', array(
                                    array('Jojo', 'smarty_getTemplate'),
                                    array('Jojo', 'smarty_getTimestamp'),
                                    array('Jojo', 'smarty_getSecure'),
                                    array('Jojo', 'smarty_getTrusted'))
                                    );
            break;

        case 'dwoo':
        default:
            require_once(_BASEPLUGINDIR . '/jojo_core/external/dwoo/dwooAutoload.php');
            require_once(_BASEPLUGINDIR . '/jojo_core/classes/jojo_dwoo_smarty_adapter.php');
            $smarty = new Jojo_Dwoo_Smarty_Adapter();
            $smarty->show_compat_errors = true;
            $smarty->compile_dir  = _CACHEDIR . '/dwoo/templates_c';
            $smarty->cache_dir    = _CACHEDIR . '/dwoo/cache';
            $smarty->setCharset('utf-8');

            $smarty->template_dir = array_reverse(Jojo::listPlugins('templates', true));
            break;
    }
    $smarty->register_function('jojoHook', array('Jojo', 'runSmartyHook'));
    $smarty->register_function('jojoAsset', array('Jojo', 'runSmartyAssetHook'));
    if (_DEBUG) {
        /* Tell smarty not to cache stuff - can cause problems when swapping themes */
        $smarty->force_compile = true;
    }

    $smarty->assign('OPTIONS',          Jojo::getOptions());
    $smarty->assign('sitetitle',        _SITETITLE);
    $smarty->assign('QUERY_STRING',     $_SERVER['QUERY_STRING']);
    $smarty->assign('REQUEST_URI',      $_SERVER['REQUEST_URI']);
    $smarty->assign('SITENAME',         _SITENAME);
    $smarty->assign('SITEURL',          _SITEURL);
    $smarty->assign('SITEURI',          _SITEURI);
    $smarty->assign('SECUREURL',        _SECUREURL);
    $smarty->assign('NONSECUREURL',     _NONSECUREURL);
    $smarty->assign('RELATIVE_URL',     _RELATIVE_URL);
    $smarty->assign('issecure',         $issecure);
    $smarty->assign('ADMIN',            _ADMIN);
    $smarty->assign('SITEFOLDER',       _SITEFOLDER);
    if (!$issecure) $smarty->assign('NEXTASSET',        $ASSETS);
    $smarty->assign('MULTILANGUAGE',        _MULTILANGUAGE);
    $smarty->assign('IS_MOBILE',            Jojo::isMobile());

    /* User is logged in */
    $smarty->assign('loggedIn', (boolean)(is_array($logindata)));
    $smarty->assign('userrecord', $logindata);
    if (in_array('admin',$_USERGROUPS)) {
       $smarty->assign('adminloggedin', true);
       // allow admins to see hidden pages
       $_SESSION['showhidden'] = true;
    }
    $smarty->assign('loginmessage', $loginfailure);

    /* After login hook */
    if ( isset($_SESSION['loggingin']) && $_SESSION['loggingin']) {
        Jojo::runHook('action_after_login');
        unset($_SESSION['loggingin']);
    }
    if ( isset($_SESSION['loggingout']) && $_SESSION['loggingout']) Jojo::runHook('action_after_logout');

} 

/* Parse the url out into bits */
Jojo::runHook('jojo_before_parsepage');
$data = Jojo::parsepage(_FULLSITEURI);

try {
    $page = Jojo_Plugin::getPage($data);
} catch (Jojo_Exception_IncludeFile $e) {
    include $e->getFileToInclude();
    exit;
}

/* If page not found or is a malformed resource request, return 404 */
if ($page===false || $resourcerequest) {
    include(_BASEPLUGINDIR . '/jojo_core/404.php');
}

/* If the URL is not what we expected, 301 redirect the visitor */
$actualurl = _PROTOCOL . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
if (isset($_SERVER["HTTP_X_FORWARDED_HOST"])) {
    $actualurl = _PROTOCOL . $_SERVER["HTTP_X_FORWARDED_HOST"] . $_SERVER['REQUEST_URI'];
}
$correcturl = $page->getCorrectUrl();

if (($page->id == 1) && (rtrim($correcturl,'/') != rtrim($actualurl,'/')))  {
    Jojo::redirect($correcturl);
} elseif ($correcturl != $actualurl) {
    Jojo::redirect($correcturl);
    ob_end_flush(); // Send the output and turn off output buffering
    exit();
}

$smarty->assign('correcturl', $correcturl);

/* Set up a memcache instance if it is available */
$mCache = false;
if (class_exists('Memcache') && Jojo::getOption('contentcachetime_memcache', 600)) {
    $mCache = new Memcache();
    if (!$mCache->connect('localhost', 11211))  { $mCache = false; }
}

/* Enable GZIP */
if (Jojo::getOption('enablegzip', false) == 1 && strpos($_SERVER['REQUEST_URI'], '/actions/') === false) {
    Jojo::gzip();
}

/* Custom code from all plugins and theme */
$templateoptions = array();
foreach (Jojo::listPlugins('global.php') as $pluginfile) {
    include($pluginfile);
}
$smarty->assign('templateoptions', $templateoptions);

/* Display login form if required to view this page */
if (!$page->perms->hasPerm($_USERGROUPS, 'view')) {
    if (isset($username)) $smarty->assign('username', $username);
    if (isset($password)) $smarty->assign('password', $password);
    if (isset($remember)) $smarty->assign('remember', $remember);
    if (isset($referer)) $smarty->assign('referer', $referer);
    $smarty->assign('content', $smarty->fetch('login.tpl'));
    $html = $smarty->fetch('template.tpl');
    Jojo::runHook('after_fetch_template');
    /* Allow output to be filtered */
    $html = Jojo::applyFilter('output', $html);
    $html = str_replace('##', '', $html);
    $html = preg_replace('/<a([^>]*?)href=["\']\\[#\\]([a-z0-9-_]*)?["\']([^>]*?)>/i', '<a$1href="#$2"$3>', $html);
    /* Output the html to the browser */
    header('Content-Length: ' . strlen($html));
    echo $html;
    ob_end_flush(); // Send the output and turn off output buffering
    exit();
}

/* Store the search terms used if the refered is external */
if (!isset($_SESSION['referer']) && isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'],_SITEURL) === false) {
    $_SESSION['referer'] = $_SERVER['HTTP_REFERER'];
    require_once(_BASEPLUGINDIR . '/jojo_core/external/lgf-searchReferencesV2/lgf-search-Functions.php');
    $keywords = ExtractKeywords3($_SESSION['referer']);
    $_SESSION['referer_searchphrase'] = is_array($keywords) ? implode(' ', $keywords) : '';
    setcookie('referer_searchphrase', $_SESSION['referer_searchphrase'], 0);
}
if (isset($_SESSION['referer_searchphrase'])) {
    setcookie('referer_searchphrase', $_SESSION['referer_searchphrase'], 0);
}

/* Assign all page fields to smarty */
foreach($page->page as $key => $value) {
    $smarty->assign($key, $value);
}
// Set the html language for the page
$languagedata = Jojo::getPageHtmlLanguage();
$smarty->assign ('pg_htmllang', $languagedata['languageid'] );

$charset = $languagedata['charset'] ?: 'utf-8';
$direction = $languagedata['direction'];
if ($direction == 'rtl') {
    $smarty->assign('rtl', true);
}

if ($templateEngine == 'dwoo') {
    $smarty->setCharset($charset);
}
$smarty->assign('charset', $charset);

if(substr($page->page [ 'pg_url' ],-4,4)=='.txt') {
  header('Content-type: text/plain; charset=' . $charset);
} else {
  header("Content-type: text/html; charset=" . $charset);
}
/* Set the content variables */
$content = $page->getContent();
$content = Jojo::applyFilter('jojo:content', $content); //plugins - add any additional elements to the $content array here

if (isset($content['header']) && $content['header']==404) {
    include(_BASEPLUGINDIR . '/jojo_core/404.php');
    ob_end_flush(); // Send the output and turn off output buffering
    exit;
}

/* Create metakeywords if this is a public page */
if ($page->perms->hasPerm('everyone', 'view')) {
    $content['metakeywords'] = Jojo::getMetaKeywords($content['metakeywords'] .' ' . $content['seotitle'] . ' ' . $content['title'] . ' ' . $content['content']);
}

$robots_index = true;
$robots_follow = true;
if (isset($content['index']) && !$content['index']) $robots_index = false;
if (isset($content['followfrom']) && !$content['followfrom']) $robots_follow = false;
$smarty->assign('robots_index', $robots_index);
$smarty->assign('robots_follow', $robots_follow);
/* Assign all page variables to smarty */
foreach($content as $k => $v) {
    $v = Jojo::applyFilter($k, $v);
    if ($k != 'index') $smarty->assign($k, $v); //do not assign a variable called 'index' in case it is already used elsewhere
}


/* Make a nice title for the page including the branding, depending on the user options */
$displaytitle = !empty($content['seotitle']) ? $content['seotitle'] : $content['title'];

if (Jojo::getOption('shorttitlebranding') == 'yes') {
    /* get the lengths of title elements */
    $titlelength = array();
    $titlelength['base'] = strlen($displaytitle);
    $titlelength['branding'] = strlen(_SITETITLE);
    $titlelength['shortbranding'] = (_SHORTTITLE != '') ? strlen(_SHORTTITLE) : strlen(_SITETITLE);
    $titlelength['padding'] = 3; // = strlen(' | ')

    if (($titlelength['base'] + $titlelength['padding'] + $titlelength['branding']) <= 70) {
        /* base + full branding is less than 70 */
        $brandingtitle = _SITETITLE;
    } elseif (($titlelength['base'] + $titlelength['padding'] + $titlelength['shortbranding']) <= 70) {
        /* base + shortened branding is less than 70 */
        $brandingtitle = _SHORTTITLE;
    } elseif (($titlelength['base']) <= 70) {
        /* base + no branding is less than 70 */
        $brandingtitle = false;
    } else {
        /* base + full branding is less than 70 */
        $brandingtitle = _SITETITLE;
    }
} else {
    $brandingtitle = _SITETITLE;
}

/* Append or Prepend the site title based on user options (unless it's the same as the page title) */
if ($displaytitle==$brandingtitle) {
} elseif (Jojo::getOption('titlebranding') == 'first' && $brandingtitle) {
    $displaytitle = $brandingtitle . Jojo::getOption('title_separator', ' | ') . $displaytitle;
} elseif (Jojo::getOption('titlebranding') == 'last' && $brandingtitle) {
    $displaytitle = $displaytitle . Jojo::getOption('title_separator', ' | ') . $brandingtitle;
}
$smarty->assign('displaytitle', $displaytitle);

/* Pass details of breadcumbs and options to smarty for templates to use */
$smarty->assign('numbreadcrumbs', count($content['breadcrumbs']));

/* Add quick edit if we have edit privilages for this page */
if ($page->perms->hasPerm($_USERGROUPS, 'edit')) {
  $smarty->assign('showquickedit', true);
  $smarty->assign('qid', $page->qid);
  $smarty->assign('qt', $page->qt);
}

/* decide if we need to link to the handheld CSS file */
$handheld = false;
foreach (Jojo::listPlugins('css/handheld.css') as $pluginfile) {
    $handheld = true;
    break;
}
$css_handheld = Jojo::getOption('css-handheld');
if (!empty($css_handheld)) $handheld = true;
$smarty->assign('include_handheld_css', $handheld);

/* decide if we need to link to the print CSS file */
$print = false;
foreach (Jojo::listPlugins('css/print.css') as $pluginfile) {
    $print = true;
    break;
}
$css_print = Jojo::getOption('css-print');
if (!empty($css_print)) $print = true;
$smarty->assign('include_print_css', $print);

$smarty->assign('GENERATIONTIME', Jojo::timer($maintimer));

/* Include default menu */
if ($templateoptions['menu']) {
    $smarty->assign('menu', $page->getMenu('main', Jojo::getOption('mainnavdepth', 3)));
    $smarty->assign('footernav', $page->getMenu('footer', Jojo::getOption('footernavdepth', 1)));
}
Jojo::runHook('before_fetch_template');

if ((boolean)(Jojo::getOption('ogdata', 'no')=='yes') && $content['ogtags']) {
    /* Get OpenGraph header data from plugins */
    $ogdata = $content['ogtags'];
    $smarty->assign('ogdata', $ogdata);
    $ogmetatags = $smarty->fetch('ogmetatags.tpl');
    $smarty->assign('ogmetatags', $ogmetatags);
    $smarty->assign('ogxmlns', (Jojo::getOption('doctype', 'xhtml')!='html5' ? 'xmlns:og="http://ogp.me/ns#"' . ( isset($ogdata['fb_admins']) || isset($ogdata['fb_app_id']) ? ' xmlns:fb="https://www.facebook.com/2008/fbml"' : '') : ' prefix="og: http://ogp.me/ns#' . ( isset($ogdata['fb_admins']) || isset($ogdata['fb_app_id']) ? ' fb: http://www.facebook.com/2008/fbml' : '') . '"' ));
}

/* Fetch custom head from all the plugins and themes */
$customhead = '';
foreach (Jojo::listPlugins('templates/customhead.tpl') as $pluginfile) {
    $customhead .= $smarty->fetch($pluginfile);
}
$smarty->assign('customhead', $customhead);

/* Fetch custom foot from all the plugins and themes */
$customfoot = '';
foreach (Jojo::listPlugins('templates/customfoot.tpl') as $pluginfile) {
    $customfoot .= $smarty->fetch($pluginfile);
}
$smarty->assign('customfoot', $customfoot);

/* Include page template */
if (isset($isadmin) && $isadmin) {
    $html = $smarty->fetch('template-admin.tpl');
} else {
    $html = $smarty->fetch('template.tpl');
}

Jojo::runHook('after_fetch_template');

/* Allow output to be filtered */
$html = Jojo::applyFilter('output', $html);
$html = str_replace('##', '', $html);
$html = preg_replace('/<a([^>]*?)href=["\']\\[#\\]([a-z0-9-_]*)?["\']([^>]*?)>/i', '<a$1href="#$2"$3>', $html);

header('Content-Length: ' . strlen($html));
echo $html;

/* Cache the page */
if (_CONTENTCACHE && !Jojo::noCache() && !$_USERID && count($_POST) == 0 && $page->page['pg_contentcache'] != 'no') {
    Jojo::RecursiveMkdir( dirname($cache_file)); // create folder structure
    $data = ob_get_contents();
    file_put_contents($cache_file, $data);
/* or wipe the cache file if it's been set to no cache since */
} elseif (Jojo::noCache() && file_exists($cache_file)){
        unlink($cache_file);
}

/* Output the html to the browser */
ob_end_flush(); //Flush and turn off output buffering


/* run any auto-maintenance tasks */
Jojo::runHook('jojo_maintenance', array());
$lastmaintenence = Jojo::getOption('last_maintenance');
if (time() > ($lastmaintenence + 86400)) { //60 * 60 * 24 = 86400 seconds
    foreach (Jojo::listPlugins('includes/maintenance.php') as $pluginfile) {
        include($pluginfile);
    }
    /* mark the maintenance as 'done' for today */
    Jojo::updateQuery("UPDATE {option} SET `op_value`='".time()."' WHERE `op_name`='last_maintenance'");
}
