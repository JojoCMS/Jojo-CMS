{include file="doctype.tpl"}<head>
    {if $htmldoctype}<meta charset="{if $charset}{$charset}{else}utf-8{/if}" />{else}<meta http-equiv="content-Type" content="text/html; charset={if $charset}{$charset}{else}utf-8{/if}" />{/if}
    <!-- [[CACHE INFORMATION]] --><!-- Page generation time: {$GENERATIONTIME|round:3}s{if $pageid}; PageID: *{$pageid}* {/if}-->
    <title>{if $displaytitle}{$displaytitle}{/if}</title>
    <base href="{if $issecure}{$SECUREURL}{else}{$SITEURL}{/if}/" />
    {if $htmldoctype}<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    {/if}{if $metadescription}<meta name="description" content="{$metadescription}" />
    {elseif $pg_metadesc}<meta name="description" content="{$pg_metadesc}" />
    {/if}{if $pg_metakeywords}<meta name="keywords" content="{$pg_metakeywords}" />
    {/if}<meta name="generator" content="Jojo CMS http://www.jojocms.org" />
    <link rel="canonical" href="{if $canonical}{$canonical}{else}{$correcturl}{/if}" />
    {if !$robots_index || !$robots_follow || $isadmin}<meta name="robots" content="{if !$robots_index || $isadmin}no{/if}index, {if !$robots_follow || $isadmin}no{/if}follow" />{/if}{if $ogmetatags && !$isadmin}
    {$ogmetatags}{/if}{if $isadmin}
    <link rel="stylesheet" type="text/css" href="{cycle values=$NEXTASSET}css/jpop.css" />
    <link type="text/css" rel="stylesheet" href="{cycle values=$NEXTASSET}css/admin.css?v=3" />
    <link rel="stylesheet" href="{cycle values=$NEXTASSET}css/admin-print.css" type="text/css" media="print" />
    <link rel="stylesheet" type="text/css" href="external/anytime/anytimec.css" />
    <link rel="stylesheet" type="text/css" href="external/markitup/skins/markitup/style.css" />
    <link rel="stylesheet" type="text/css" href="external/markitup/sets/html/style.css" />
    <link rel="stylesheet" type="text/css" href="external/markitup/sets/bbcode/style.css" />
    <!--[if IE]> <link type="text/css" rel="stylesheet" href="{cycle values=$NEXTASSET}css/admin_ie.css" /> <![endif]-->
    {else}{* Manage the Open Directory Project and Yahoo Directory options *}{if $OPTIONS.googleplus_link}
    <link href="{$OPTIONS.googleplus_link}" rel="publisher" />{/if}{if $pageid == 1 }{if $OPTIONS.robots_opd == "yes" && $OPTIONS.robots_ydir == "yes"}<meta name="robots" content="noopd, noydir" />
    {elseif $OPTIONS.robots_opd == "yes"}<meta name="robots" content="noopd" />
    {elseif $OPTIONS.robots_ydir == "yes"}<meta name="slurp" content="noydir" />{/if}{/if}{* end of the OPD and Ydir section*}
    <link rel="stylesheet" type="text/css" href="{cycle values=$NEXTASSET}css/styles.css{if $DEBUG}?r={math equation='rand(1000,10000)'}{/if}" />{if $include_print_css}
    <link rel="stylesheet" type="text/css" href="{cycle values=$NEXTASSET}css/print.css" media="print" />{/if}{if $include_handheld_css}
    <link rel="stylesheet" type="text/css" href="{cycle values=$NEXTASSET}css/handheld.css" media="handheld" />{/if}{if $rtl}
    <link type="text/css" rel="stylesheet" href="{cycle values=$NEXTASSET}css/rtl.css" />{/if}
    {/if}{if $rssicon}{foreach from=$rssicon key=k item=v}<link rel="alternate" type="application/rss+xml" title="{$k}" href="{$v}" />
    {/foreach}{elseif $rss}<link rel="alternate" type="application/rss+xml" title="{$sitetitle} RSS Feed" href="{$rss}" />
    {elseif !$templateoptions || $templateoptions.rss}<link rel="alternate" type="application/rss+xml" title="{$sitetitle} RSS Feed" href="{$SITEURL}/articles/rss/" />
    {/if}{if $modernizr}<script src="{$SITEURL}/external/modernizr-1.6.min.js"></script>
    {/if}{if $jqueryhead || $isadmin}<script {if !$htmldoctype}type="text/javascript" {/if}src="{if $OPTIONS.googleajaxlibs == "yes"}http{if $issecure}s{/if}://ajax.googleapis.com/ajax/libs/jquery/1.4.4/jquery.min.js{else}{cycle values=$NEXTASSET}external/jquery/jquery-1.4.4.min.js{/if}"></script>
    {/if}{if $commonhead || $isadmin}<script{if !$htmldoctype} type="text/javascript"{/if} src="{cycle values=$NEXTASSET}js/common.js"></script>
    {/if}{if $head}{$head}{/if}{if $css}
    <style type="text/css">
        {$css}
    </style>
    {/if}{if $isadmin}
    <script type="text/javascript" src="{cycle values=$NEXTASSET}js/jpop.js"></script>
    <script type="text/javascript" src="external/anytime/anytimec.js"></script>
    <script type="text/javascript" src="external/markitup/jquery.markitup.pack.js"></script>
    <script type="text/javascript" src="external/markitup/sets/html/set.js"></script>
    <script type="text/javascript" src="external/markitup/sets/bbcode/set.js"></script>
    <script type="text/javascript" src="{cycle values=$NEXTASSET}js/admin.js?v=3"></script>
    {else}{if $OPTIONS.analyticscode && !$isadmin && !$adminloggedin && $OPTIONS.analyticsposition == 'top'}
    {include file="analytics.tpl"}
    {/if}{/if}{if $customhead}
    {$customhead}
    {/if}{jojoHook hook="customhead"}</head>
