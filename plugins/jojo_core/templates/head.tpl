{include file="doctype.tpl"}<!--[if IE]><![endif]-->
<head>
    <title>{if $displaytitle}{$displaytitle}{/if}</title>
    <base href="{$SITEURL}/" />    
    <meta charset="{if $charset}{$charset}{else}utf-8{/if}" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width{if !$OPTIONS.initialscale || $OPTIONS.initialscale=='yes'}, minimum-scale=1.0, maximum-scale=1.0{/if}" />
    {if $metadescription}<meta name="description" content="{$metadescription}" />
    {elseif $pg_metadesc}<meta name="description" content="{$pg_metadesc}" />
    {/if}{if $pg_metakeywords}<meta name="keywords" content="{$pg_metakeywords}" />
    {/if}<meta name="generator" content="Jojo CMS http://www.jojocms.org" />{if $canonical || $correcturl} 
    <link rel="shortcut icon" type="image/x-icon" href="{$SITEURL}/favicon.ico" />
    <link rel="canonical" href="{if $canonical}{$canonical}{else}{$correcturl}{/if}" /> {/if}
    {if !$robots_index || !$robots_follow || $isadmin}<meta name="robots" content="{if !$robots_index || $isadmin}no{/if}index, {if !$robots_follow || $isadmin}no{/if}follow" />
    {/if}{if $ogmetatags && !$isadmin} 
    {$ogmetatags}
    {/if}{if $OPTIONS.css_typekit}<script>!function(){ldelim}var e={ldelim}kitId:"{$OPTIONS.css_typekit}"{literal},scriptTimeout:3e3},t=document.getElementsByTagName("html")[0];t.className+=" wf-loading";var a=setTimeout(function(){t.className=t.className.replace(/(\s|^)wf-loading(\s|$)/g," "),t.className+=" wf-inactive"},e.scriptTimeout),c=!1,s=document.createElement("script");s.src="https://use.typekit.net/"+e.kitId+".js",s.type="text/javascript",s.async="true",s.onload=s.onreadystatechange=function(){var t=this.readyState;if(!(c||t&&"complete"!=t&&"loaded"!=t)){c=!0,clearTimeout(a);try{Typekit.load(e)}catch(s){}}};var i=document.getElementsByTagName("script")[0];i.parentNode.insertBefore(s,i)}();{/literal}</script>
    {/if}{if $OPTIONS.css_fontawesome}<script src="https://use.fontawesome.com/{$OPTIONS.css_fontawesome}.js" defer></script>
    {/if}{if $isadmin}<link rel="stylesheet" type="text/css" href="{$RESOURCEURL}/css/admin{if $cssmodtime}{$cssmodtime}{/if}.css" />
    {else}{* Manage the Open Directory Project and Yahoo Directory options *}{if $OPTIONS.googleplus_link} 
    <link href="{$OPTIONS.googleplus_link}" rel="publisher" />{/if}{if $pageid == 1 }{if $OPTIONS.robots_odp == "yes" && $OPTIONS.robots_ydir == "yes"}<meta name="robots" content="noodp, noydir" />
    {elseif $OPTIONS.robots_odp == "yes"}<meta name="robots" content="noodp" />
    {elseif $OPTIONS.robots_ydir == "yes"}<meta name="slurp" content="noydir" />{/if}{/if}{* end of the ODP and Ydir section*}
    {if $inlinecss}<style>{$inlinecss}</style>
    {else}<link rel="stylesheet" type="text/css" href="{$RESOURCEURL}/css/styles{if $cssmodtime}{$cssmodtime}{/if}.css" />{/if}{if $rtl}
    <link rel="stylesheet" type="text/css" href="{$RESOURCEURL}/css/rtl.css" />{/if}
    {/if}{if $css}
    <style type="text/css">
        {$css}
    </style>
    {/if}
    {if $rssicon}{foreach from=$rssicon key=k item=v}<link rel="alternate" type="application/rss+xml" title="{$k}" href="{$v}" />
    {/foreach}{elseif $rss}<link rel="alternate" type="application/rss+xml" title="{$sitetitle} RSS Feed" href="{$rss}" />
    {elseif !$templateoptions || $templateoptions.rss}<link rel="alternate" type="application/rss+xml" title="{$sitetitle} RSS Feed" href="{$SITEURL}/articles/rss/" />
    {/if}<!--[if lt IE 9]>{if $include_print_css} 
        <link rel="stylesheet" type="text/css" href="{cycle values=$NEXTASSET}{jojoAsset file="css/print.css"}" media="print" />{/if}{if $include_handheld_css} 
        <link rel="stylesheet" type="text/css" href="{cycle values=$NEXTASSET}{jojoAsset file="css/handheld.css"}" media="handheld" />{/if}
        <script src="https://cdn.jsdelivr.net/g/html5shiv,respond"></script>
    <![endif]-->
    {if $modernizr != 'no'}<script src="{$RESOURCEURL}/external/modernizr.min.js" async></script>
    {/if}{if $OPTIONS.jquery_head=='yes' || $isadmin}<script src="https://ajax.googleapis.com/ajax/libs/jquery/{if $isadmin}1.9.1{elseif $OPTIONS.jquery_version}{$OPTIONS.jquery_version}{else}1.9.1{/if}/jquery.min.js"></script>{if $OPTIONS.jquery_ui=='yes'} 
    <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.3/jquery-ui.min.js"{if !$isadmin} defer{/if}></script>{/if}
    {/if}{if $OPTIONS.commonjs_head=='yes' && !$isadmin}<script>
          var siteurl = '{$SITEURL}';
          var secureurl = '{$SECUREURL}';
    </script>
    <script src="{$RESOURCEURL}/js/common{if $jsmodtime}{$jsmodtime}{/if}.js" defer></script>
    {/if}{if $head}{$head}
    {/if}{if $isadmin}<script src="{$RESOURCEURL}/js/commonadmin{if $jsmodtime}{$jsmodtime}{/if}.js"></script>
    {else}{if $OPTIONS.analyticscode && !$isadmin && !$adminloggedin && $OPTIONS.analyticsposition == 'top'}{include file="analytics.tpl"}
    {/if}{/if}{if $customhead}{$customhead}
    {/if}{jojoHook hook="customhead"}
    {if $OPTIONS.facebookpixelid}{literal}<!-- Facebook Pixel Code -->
<script>
!function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?
n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;
n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;
t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,
document,'script','//connect.facebook.net/en_US/fbevents.js');
{/literal}
// Insert Your Facebook Pixel ID below. 
fbq('init', '{$OPTIONS.facebookpixelid}');
fbq('track', 'PageView');
</script>
<!-- Insert Your Facebook Pixel ID below. --> 
<noscript><img height="1" width="1" style="display:none"
src="https://www.facebook.com/tr?id={$OPTIONS.facebookpixelid}&amp;ev=PageView&amp;noscript=1"/></noscript>
<!-- End Facebook Pixel Code -->
{/if}
</head>
