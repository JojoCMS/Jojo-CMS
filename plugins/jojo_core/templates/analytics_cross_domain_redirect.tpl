<?xml version="1.0" encoding="utf-8" ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
   "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title>Redirecting...</title>
<meta name="robots" content="noindex,nofollow" />

{if $OPTIONS.analyticscodetype=='async'}
<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', '{$OPTIONS.analyticscode}']);
  _gaq.push(['_setDomainName', 'none']);
  _gaq.push(['_setAllowLinker', true]);
  _gaq.push(['_trackPageview']);

  _gaq.push(['_link', '{$redirect}']);

  (function() {literal}{{/literal}
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  {literal}}{/literal})();

</script>

{else}

{* old ga style Analytics *}

<script type="text/javascript">
var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
</script>
<script type="text/javascript">
var pageTracker = _gat._getTracker("{$OPTIONS.analyticscode}");
pageTracker._setDomainName("none");
pageTracker._setAllowLinker(true);
pageTracker._trackPageview();
pageTracker._link('{$redirect}');
</script>
{/if}

</head>
<body>
<h1>Redirecting...</h1>
{if $OPTIONS.analyticscodetype=='async'}
<p>If your browser does not automatically redirect, please use the <a href="{$redirect}"
onclick="_gaq.push(['_link', '{$redirect}']); return false;">direct link</a>.</p>

{else}
<p>If your browser does not automatically redirect, please use the <a href="{$redirect}" onclick="pageTracker._link(this.href); return false;">direct link</a>.</p>
{/if}

</body>
</html>