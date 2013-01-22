{include file="doctype.tpl"}<head>
    {if $htmldoctype}<meta charset="{if $charset}{$charset}{else}utf-8{/if}" />{else}<meta http-equiv="content-Type" content="text/html; charset={if $charset}{$charset}{else}utf-8{/if}" />{/if}<!-- MOBILE VERSION -->
    <!-- [[CACHE INFORMATION]] --><!-- Page generation time: {$GENERATIONTIME|round:3}s{if $pageid}; PageID: *{$pageid}* {/if}-->
    <title>{if $displaytitle}{$displaytitle}{/if}</title>
    <base href="{if $issecure}{$SECUREURL}{else}{$SITEURL}{/if}/" />
    {if $htmldoctype}<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    {/if}{if $metadescription}<meta name="description" content="{$metadescription}" />
    {elseif $pg_metadesc}<meta name="description" content="{$pg_metadesc}" />
    {/if}{if $pg_metakeywords}<meta name="keywords" content="{$pg_metakeywords}" />
    {/if}<meta name="generator" content="Jojo CMS http://www.jojocms.org" /> 
    
    <!--<meta name="viewport" content="width=device-width; initial-scale=1.0" />-->
    <meta name="viewport" content="width=device-width; initial-scale=1.0" /> <!-- , maximum-scale=1 -->
    <meta name="HandheldFriendly" content="true"/>
    <!--<meta name="MobileOptimized" content="320"/>-->
    <meta http-equiv="cleartype" content="on" />
    
    <!--  the favicon & iOS home screen icon are both 57x57 PNG's. Use a full URL file path for Android devices.  -->
<!--  <link rel="apple-touch-icon-precomposed" href="http://yoursite.com/apple-touch-icon.png"/>  -->
<!--  <link rel="icon" type="image/vnd.microsoft.icon" href="http://yoursite.com/favicon.png" />  -->
    
    {if $canonical || $correcturl}
    <link rel="canonical" href="{if $canonical}{$canonical}{else}{$correcturl}{/if}" /> {/if}
    {if !$robots_index || !$robots_follow || $isadmin}<meta name="robots" content="{if !$robots_index || $isadmin}no{/if}index, {if !$robots_follow || $isadmin}no{/if}follow" />{/if}{if $ogmetatags && !$isadmin}
    {$ogmetatags}{/if}{if $isadmin}
    {else}{* Manage the Open Directory Project and Yahoo Directory options *}{if $OPTIONS.googleplus_link}
    <link href="{$OPTIONS.googleplus_link}" rel="publisher" />{/if}{if $pageid == 1 }{if $OPTIONS.robots_odp == "yes" && $OPTIONS.robots_ydir == "yes"}<meta name="robots" content="noodp, noydir" />
    {elseif $OPTIONS.robots_odp == "yes"}<meta name="robots" content="noodp" />
    {elseif $OPTIONS.robots_ydir == "yes"}<meta name="slurp" content="noydir" />{/if}{/if}{* end of the ODP and Ydir section*}
    <link rel="stylesheet" type="text/css" href="{cycle values=$NEXTASSET}{jojoAsset file="css/styles.css"}" />
    <link rel="stylesheet" type="text/css" href="{cycle values=$NEXTASSET}{jojoAsset file="css/mobile.css"}" />{if $rtl}
    <link rel="stylesheet" type="text/css" href="{cycle values=$NEXTASSET}{jojoAsset file="css/rtl.css"}" />{/if}
    {/if}{if $rssicon}{foreach from=$rssicon key=k item=v}<link rel="alternate" type="application/rss+xml" title="{$k}" href="{$v}" />
    {/foreach}{elseif $rss}<link rel="alternate" type="application/rss+xml" title="{$sitetitle} RSS Feed" href="{$rss}" />
    {elseif !$templateoptions || $templateoptions.rss}<link rel="alternate" type="application/rss+xml" title="{$sitetitle} RSS Feed" href="{$SITEURL}/articles/rss/" />
    {/if}{if $modernizr != 'no'}<script src="{$SITEURL}/external/modernizr{if $modernizr != 'custom'}-1.6{/if}.min.js"></script>
    {/if}{if $jqueryhead || $isadmin}<script {if !$htmldoctype}type="text/javascript" {/if}src="{if $OPTIONS.googleajaxlibs == "yes"}http{if $issecure}s{/if}://ajax.googleapis.com/ajax/libs/jquery/{if $OPTIONS.jquery_version}{$OPTIONS.jquery_version}{else}1.4.4{/if}/jquery.min.js{else}{cycle values=$NEXTASSET}external/jquery/jquery-{if $OPTIONS.jquery_version}{$OPTIONS.jquery_version}{else}1.4.4{/if}.min.js{/if}"></script>
    {/if}{if $jqueryhead && $OPTIONS.jquery_ui=='yes'}<script {if !$htmldoctype}type="text/javascript" {/if}src="{if $OPTIONS.googleajaxlibs == "yes"}http{if $issecure}s{/if}://ajax.googleapis.com/ajax/libs/jqueryui/1.8.18/jquery-ui.min.js{else}{cycle values=$NEXTASSET}external/jquery/jquery.ui.core.min.js{/if}"></script>
    {/if}{if $commonhead || $isadmin}<script{if !$htmldoctype} type="text/javascript"{/if} src="{cycle values=$NEXTASSET}{jojoAsset file="js/common.js"}"></script>
    {/if}{if $head}{$head}{/if}{if $css}
    <style type="text/css">
        {$css}
    </style>
    {/if}{if $isadmin}
    {else}{if $OPTIONS.analyticscode && !$isadmin && !$adminloggedin && $OPTIONS.analyticsposition == 'top'}
    {include file="analytics.tpl"}
    {/if}{/if}{if $customhead}
    {$customhead}
    {/if}{jojoHook hook="customhead"}</head>
