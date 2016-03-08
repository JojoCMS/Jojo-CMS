{if !$templateoptions || $templateoptions.frajax || $isadmin}<iframe src="javascript:false;" name="frajax-iframe" id="frajax-iframe" style="display:none; height: 0; width: 0; border: 0;"></iframe>
{/if}{if $OPTIONS.jquery_head=='no' && !$isadmin}<script {if !$htmldoctype}type="text/javascript" {/if}src="https://ajax.googleapis.com/ajax/libs/jquery/{if $isadmin}1.9.1{elseif $OPTIONS.jquery_version}{$OPTIONS.jquery_version}{else}1.9.1{/if}/jquery.min.js"></script>{if $OPTIONS.jquery_ui=='yes'} 
<script{if !$htmldoctype} type="text/javascript"{/if} src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.3/jquery-ui.min.js"></script>{/if}
<script{if !$htmldoctype} type="text/javascript"{/if}>
    if (typeof jQuery == 'undefined') {ldelim} 
        document.write(unescape("%3Cscript src='{cycle values=$NEXTASSET}external/jquery/jquery-{if $isadmin}1.9.1{elseif $OPTIONS.jquery_version}{$OPTIONS.jquery_version}{else}1.9.1{/if}.min.js'{if !$htmldoctype} type='text/javascript'{/if}%3E%3C/script%3E"));{if $OPTIONS.jquery_ui=='yes'} 
        document.write(unescape("%3Cscript src='{cycle values=$NEXTASSET}{jojoAsset file='external/jquery/jquery-ui.min.js'}'{if !$htmldoctype} type='text/javascript'{/if}%3E%3C/script%3E"));{/if}
    {rdelim}
</script>
{/if}{if $OPTIONS.commonjs_head=='no' && !$isadmin}<script{if !$htmldoctype} type="text/javascript"{/if}>
    var siteurl = '{$SITEURL}';
    var secureurl = '{$SECUREURL}';
</script>
<script{if !$htmldoctype} type="text/javascript"{/if} src="{cycle values=$NEXTASSET}{jojoAsset file="js/common.js"}"></script>
{/if}{if !$isadmin}{if !$templateoptions || $templateoptions.menu}
    <!--[if lte IE 7]>
    <script type="text/javascript">
    {literal}try {document.execCommand("BackgroundImageCache", false, true);} catch(err) {}{/literal}
    </script>
    <script type="text/javascript" src="{cycle values=$NEXTASSET}{jojoAsset file="js/menu.js"}"></script>
    <![endif]-->
{/if}{if !$templateoptions || $templateoptions.dateparse}<script type="text/javascript" src="{cycle values=$NEXTASSET}{jojoAsset file="js/dateparse.js"}"></script>
{/if}{if $js || $documentready || $javascript}<script{if !$htmldoctype} type="text/javascript"{/if}>
    /* <![CDATA[ */
    {if $documentready}{$documentready}
    {/if}{if $js}{$js}
    {/if}{if $javascript}{$javascript}{/if}
    /* ]]> */
</script>{/if}{if $OPTIONS.analyticscode && !$isadmin && !$adminloggedin && $OPTIONS.analyticsposition != 'top'}
{include file="analytics.tpl"}{/if}{if $OPTIONS.captcha_recaptcha=="yes"}<script src='https://www.google.com/recaptcha/api.js'></script>
{/if}{if $customfoot}
{$customfoot}{/if}{if $OPTIONS.customfoot}
{$OPTIONS.customfoot}{/if}{jojoHook hook="customfoot"}{jojoHook hook="foot"}
{/if}
<!-- [[CACHE INFORMATION]] --><!-- Page generation time: {$GENERATIONTIME|round:3}s{if $pageid}; PageID: *{$pageid}* {/if}-->
