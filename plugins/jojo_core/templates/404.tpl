<?xml version="1.0" encoding="utf-8" ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
   "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title>404 - Page Not found - {$sitetitle}</title>
<base href="{if $issecure}{$SECUREURL}{else}{$SITEURL}{/if}/" />

<script type="text/javascript">
{literal}<!--
function xyz(c,a,b,s) {
    var s = (s == null) ? true : s;
    var o = '';
    var m = '';
    var m2 = ':otliam';
    for (i = 0; i <= b.length; i++) {o = b.charAt (i) + o;}
    b = o;
    for (i = 0; i <= m2.length; i++) {m = m2.charAt (i) + m;}
    if (!s) {m = '';}
    return m + a + unescape('%'+'4'+'0') + b + '.' + c;
}

var GOOG_FIXURL_LANG = 'en';
var GOOG_FIXURL_SITE = '{/literal}{if $issecure}{$SECUREURL}{else}{$SITEURL}{/if}{literal}';


{/literal}-->
</script>

<style type="text/css">
{literal}

body, html {
  color: #444;
  background: #fff;
  font-family: arial, sans-serif;
  text-align: center;
}

#wrap {
  width: 600px;
  margin-left: auto;
  margin-right: auto;
  padding: 30px;
  text-align: left;
}

#logo {
  margin-bottom: 20px;
  border: 0;
}

h1 {
}

p {
  clear: both;
  border-top: 1px solid #ccc;
  padding: 20px 0;
  margin: 20px 0;
}

a {
  color: #000;
  text-decoration: none;
}

a:hover {
  text-decoration: underline;
}

li {
  list-style-type: square;
}
#goog-wm {
}

#goog-wm h3.closest-match {
}

#goog-wm h3.closest-match a {
}

#goog-wm h3.other-things {
}

#goog-wm ul li {
}

#goog-wm li.search-goog {
  display: block;
}
{/literal}
</style>

</head>
<body>

<div id="wrap">
    <a href="{$SITEURL}"><img id="logo" src="images/v6000/logo.png" alt="" title="Return to the homepage" /></a>
    <h1>404 Page Not found</h1>
    <p>The page you have requested cannot be found. You may be able to find what you were looking for by following the links on our <a href="{$SITEURL}/">homepage</a>.</p>
    <h3>Site Links</h3>
    <ul>
        <li><a href="{$SITEURL}/">Home</a></li>
        <li><a href="sitemap/">Sitemap</a></li>
        <li><a href="contact/">Contact</a></li>
    </ul>

    <script type="text/javascript" src="http://linkhelp.clients.google.com/tbproxy/lh/wm/fixurl.js"></script>

    <p>If you believe this page should not be returning an error, please help us fix the problem by contacting the webmaster - <a href="contact/" onmouseover="this.href={$obfuscatedemail_mailto}"><span id="e-3987"></span></a>
    <noscript><a href="contact/" title="contact/">Email</a></noscript></p>

</div>
<script type="text/javascript" language="javascript">
    document.getElementById('e-3987').innerHTML = {$obfuscatedemail};
</script>

</body>
</html>
