{include file="doctype.tpl"}<!--[if IE]><![endif]-->
<head>
    <title>{if $displaytitle}{$displaytitle}{/if}</title>
    <base href="{if $issecure}{$SECUREURL}{else}{$SITEURL}{/if}/" />
    <!-- [[CACHE INFORMATION]] --><!-- Page generation time: {$GENERATIONTIME|round:3}s{if $pageid}; PageID: *{$pageid}* {/if}-->
    
    {if $htmldoctype}<meta charset="{if $charset}{$charset}{else}utf-8{/if}" />{else}<meta http-equiv="content-Type" content="text/html; charset={if $charset}{$charset}{else}utf-8{/if}" /> 
    {/if}{if $htmldoctype}<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <meta name="viewport" content="width=device-width{if !$OPTIONS.initialscale || $OPTIONS.initialscale=='yes'}, initial-scale=1.0{/if}" />
    {/if}{if $metadescription}<meta name="description" content="{$metadescription}" />
    {elseif $pg_metadesc}<meta name="description" content="{$pg_metadesc}" />
    {/if}{if $pg_metakeywords}<meta name="keywords" content="{$pg_metakeywords}" />
    {/if}<meta name="generator" content="Jojo CMS http://www.jojocms.org" />{if $canonical || $correcturl} 
    <link rel="shortcut icon" type="image/x-icon" href="{if $issecure}{$SECUREURL}{else}{$SITEURL}{/if}/favicon.ico" />
    <link rel="canonical" href="{if $canonical}{$canonical}{else}{$correcturl}{/if}" /> {/if}
    {if !$robots_index || !$robots_follow || $isadmin}<meta name="robots" content="{if !$robots_index || $isadmin}no{/if}index, {if !$robots_follow || $isadmin}no{/if}follow" />
    {/if}{if $ogmetatags && !$isadmin} 
    {$ogmetatags}
    {/if}{if $isadmin}<link rel="stylesheet" type="text/css" href="{cycle values=$NEXTASSET}{jojoAsset file="css/admin.css"}" />
    {else}{* Manage the Open Directory Project and Yahoo Directory options *}{if $OPTIONS.googleplus_link} 
    <link href="{$OPTIONS.googleplus_link}" rel="publisher" />{/if}{if $pageid == 1 }{if $OPTIONS.robots_odp == "yes" && $OPTIONS.robots_ydir == "yes"}<meta name="robots" content="noodp, noydir" />
    {elseif $OPTIONS.robots_odp == "yes"}<meta name="robots" content="noodp" />
    {elseif $OPTIONS.robots_ydir == "yes"}<meta name="slurp" content="noydir" />{/if}{/if}{* end of the ODP and Ydir section*}
    <link rel="stylesheet" type="text/css" href="{cycle values=$NEXTASSET}{jojoAsset file="css/styles.css"}" />{if $rtl}
    <link rel="stylesheet" type="text/css" href="{cycle values=$NEXTASSET}{jojoAsset file="css/rtl.css"}" />{/if}
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
        <link rel="stylesheet" type="text/css" href="{cycle values=$NEXTASSET}{jojoAsset file="css/handheld.css"}" media="handheld" />{/if}{if $htmldoctype} 
        <script src="{if $OPTIONS.googleajaxlibs == "yes"}http{if $issecure}s{/if}://html5shiv.googlecode.com/svn/trunk/html5.js{else}{cycle values=$NEXTASSET}external/html5shiv.min.js{/if}"></script>
        <script src="{cycle values=$NEXTASSET}external/respond/dest/respond.min.js"></script>
    {/if}<![endif]-->
    {if $modernizr != 'no'}<script src="{$SITEURL}/external/modernizr.min.js"></script>
    {/if}{if $jqueryhead || $isadmin}<script {if !$htmldoctype}type="text/javascript" {/if}src="{if $OPTIONS.googleajaxlibs == "yes"}http{if $issecure}s{/if}://ajax.googleapis.com/ajax/libs/jquery/{if $isadmin}1.9.1{elseif $OPTIONS.jquery_version}{$OPTIONS.jquery_version}{else}1.9.1{/if}/jquery.min.js{else}{cycle values=$NEXTASSET}external/jquery/jquery-{if $isadmin}1.9.1{elseif $OPTIONS.jquery_version}{$OPTIONS.jquery_version}{else}1.9.1{/if}.min.js{/if}"></script>
    {/if}{if $jqueryhead && $OPTIONS.jquery_ui=='yes'}<script {if !$htmldoctype}type="text/javascript" {/if}src="{if $OPTIONS.googleajaxlibs == "yes"}http{if $issecure}s{/if}://ajax.googleapis.com/ajax/libs/jqueryui/1.8.18/jquery-ui.min.js{else}{cycle values=$NEXTASSET}external/jquery/jquery.ui.core.min.js{/if}"></script>
    {/if}{if $commonhead && !$isadmin}<script{if !$htmldoctype} type="text/javascript"{/if} src="{cycle values=$NEXTASSET}{jojoAsset file="js/common.js"}"></script>
    {/if}{if $head}{$head}
    {/if}{if $isadmin}<script type="text/javascript" src="{cycle values=$NEXTASSET}{jojoAsset file="js/commonadmin.js"}"></script>
    {else}{if $OPTIONS.analyticscode && !$isadmin && !$adminloggedin && $OPTIONS.analyticsposition == 'top'}{include file="analytics.tpl"}
    {/if}{/if}{if $customhead} 
    {$customhead}
    {/if}{jojoHook hook="customhead"}
</head>
