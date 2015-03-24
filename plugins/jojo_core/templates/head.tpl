{include file="doctype.tpl"}<!--[if IE]><![endif]-->
<head>
    <title>{if $displaytitle}{$displaytitle}{/if}</title>
    <base href="{if $issecure}{$SECUREURL}{else}{$SITEURL}{/if}/" />    
    {if $htmldoctype}<meta charset="{if $charset}{$charset}{else}utf-8{/if}" />{else}<meta http-equiv="content-Type" content="text/html; charset={if $charset}{$charset}{else}utf-8{/if}" /> 
    {/if}{if $htmldoctype}<meta http-equiv="X-UA-Compatible" content="IE=edge" />
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
    {/if}{if $OPTIONS.css_typekit}<script type="text/javascript">!function(){ldelim}var e={ldelim}kitId:"{$OPTIONS.css_typekit}"{literal},scriptTimeout:3e3},t=document.getElementsByTagName("html")[0];t.className+=" wf-loading";var a=setTimeout(function(){t.className=t.className.replace(/(\s|^)wf-loading(\s|$)/g," "),t.className+=" wf-inactive"},e.scriptTimeout),c=!1,s=document.createElement("script");s.src="//use.typekit.net/"+e.kitId+".js",s.type="text/javascript",s.async="true",s.onload=s.onreadystatechange=function(){var t=this.readyState;if(!(c||t&&"complete"!=t&&"loaded"!=t)){c=!0,clearTimeout(a);try{Typekit.load(e)}catch(s){}}};var i=document.getElementsByTagName("script")[0];i.parentNode.insertBefore(s,i)}();{/literal}</script>
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
        <script src="//cdn.jsdelivr.net/g/html5shiv,respond"></script>
    {/if}<![endif]-->
    {if $modernizr != 'no'}<script src="{$SITEURL}/external/modernizr.min.js"></script>
    {/if}{if $OPTIONS.jquery_head=='yes' || $isadmin}<script {if !$htmldoctype}type="text/javascript" {/if}src="//ajax.googleapis.com/ajax/libs/jquery/{if $isadmin}1.9.1{elseif $OPTIONS.jquery_version}{$OPTIONS.jquery_version}{else}1.9.1{/if}/jquery.min.js"></script>{if $OPTIONS.jquery_ui=='yes'} 
    <script{if !$htmldoctype} type="text/javascript"{/if} src="//ajax.googleapis.com/ajax/libs/jqueryui/1.11.3/jquery-ui.min.js"></script>{/if}
    <script{if !$htmldoctype} type="text/javascript"{/if}>
        if (typeof jQuery == 'undefined') {ldelim} 
            document.write(unescape("%3Cscript src='{cycle values=$NEXTASSET}external/jquery/jquery-{if $isadmin}1.9.1{elseif $OPTIONS.jquery_version}{$OPTIONS.jquery_version}{else}1.9.1{/if}.min.js'{if !$htmldoctype} type='text/javascript'{/if}%3E%3C/script%3E"));{if $OPTIONS.jquery_ui=='yes'} 
            document.write(unescape("%3Cscript src='{cycle values=$NEXTASSET}{jojoAsset file='external/jquery/jquery-ui.min.js'}'{if !$htmldoctype} type='text/javascript'{/if}%3E%3C/script%3E"));{/if}
        {rdelim}
    </script>
    {/if}{if $OPTIONS.commonjs_head=='yes' && !$isadmin}<script{if !$htmldoctype} type="text/javascript"{/if}>
          var siteurl = '{$SITEURL}';
          var secureurl = '{$SECUREURL}';
    </script>
    <script{if !$htmldoctype} type="text/javascript"{/if} src="{cycle values=$NEXTASSET}{jojoAsset file="js/common.js"}"></script>
    {/if}{if $head}{$head}
    {/if}{if $isadmin}<script type="text/javascript" src="{cycle values=$NEXTASSET}{jojoAsset file="js/commonadmin.js"}"></script>
    {else}{if $OPTIONS.analyticscode && !$isadmin && !$adminloggedin && $OPTIONS.analyticsposition == 'top'}{include file="analytics.tpl"}
    {/if}{/if}{if $customhead} 
    {$customhead}
    {/if}{jojoHook hook="customhead"}
</head>
