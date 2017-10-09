{if !$templateoptions || $templateoptions.frajax || $isadmin}<iframe src="javascript:false;" name="frajax-iframe" id="frajax-iframe" style="display:none; height: 0; width: 0; border: 0;"></iframe>
{/if}{if $OPTIONS.jquery_head=='no' && !$isadmin}<script src="https://ajax.googleapis.com/ajax/libs/jquery/{if $isadmin}1.9.1{elseif $OPTIONS.jquery_version}{$OPTIONS.jquery_version}{else}1.9.1{/if}/jquery.min.js"></script>{if $OPTIONS.jquery_ui=='yes'} 
<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.3/jquery-ui.min.js" defer></script>{/if}
{/if}{if $OPTIONS.commonjs_head=='no' && !$isadmin}<script>
    var siteurl = '{$SITEURL}';
    var secureurl = '{$SECUREURL}';
</script>
<script src="{$RESOURCEURL}/js/common{if $jsmodtime}{$jsmodtime}{/if}.js" defer></script>
{/if}{if !$isadmin}{if !$templateoptions || $templateoptions.dateparse}<script src="{$RESOURCEURL}/js/dateparse.js" defer></script>
{/if}{if $js || $documentready || $javascript}<script>
    /* <![CDATA[ */
    {if $documentready}{$documentready}
    {/if}{if $js}{$js}
    {/if}{if $javascript}{$javascript}{/if}
    /* ]]> */
</script>{/if}{if $OPTIONS.analyticscode && !$isadmin && !$adminloggedin && $OPTIONS.analyticsposition != 'top'}
{include file="analytics.tpl"}{/if}{if $OPTIONS.captcha_recaptcha=="yes"}<script src='https://www.google.com/recaptcha/api.js' defer></script>
{/if}{if $customfoot}
{$customfoot}{/if}{if $OPTIONS.customfoot}
{$OPTIONS.customfoot}{/if}{jojoHook hook="customfoot"}{jojoHook hook="foot"}
{/if}
<!-- [[CACHE INFORMATION]] --><!-- Page generation time: {$GENERATIONTIME|round:3}s{if $pageid}; PageID: *{$pageid}* {/if}-->
