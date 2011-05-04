    {if !$templateoptions || $templateoptions.frajax || $isadmin}<iframe src="javascript:false;" name="frajax-iframe" id="frajax-iframe" style="display:none; height: 0; width: 0; border: 0;"></iframe>
    {/if}{if !$jqueryhead && !$isadmin}<script {if !$htmldoctype}type="text/javascript" {/if}src="{if $OPTIONS.googleajaxlibs == "yes"}http{if $issecure}s{/if}://ajax.googleapis.com/ajax/libs/jquery/1.4.4/jquery.min.js{else}"{cycle values=$NEXTASSET}external/jquery/jquery-1.4.2.min.js{/if}"></script>
    {/if}{if !$commonhead && !$isadmin}<script{if !$htmldoctype} type="text/javascript"{/if} src="{cycle values=$NEXTASSET}js/common.js"></script>
    {/if}{if !$templateoptions || $templateoptions.menu}
            <!--[if lte IE 7]>
            <script type="text/javascript">
            {literal}try {document.execCommand("BackgroundImageCache", false, true);} catch(err) {}{/literal}
            </script>
            <script type="text/javascript" src="{cycle values=$NEXTASSET}js/menu.js"></script>
            <![endif]-->
    {/if}{if !$templateoptions || $templateoptions.dateparse}<script type="text/javascript" src="{cycle values=$NEXTASSET}js/dateparse.js"></script>
    {/if}{if $js || $documentready || $javascript}
    <script{if !$htmldoctype} type="text/javascript"{/if}>
        /* <![CDATA[ */
        {if $documentready}{$documentready}
        {/if}{if $js}{$js}
        {/if}{if $javascript}{$javascript}{/if}
        /* ]]> */
    </script>
    {/if}{if $OPTIONS.analyticscode && !$isadmin && !$adminloggedin && $OPTIONS.analyticsposition != 'top'}
     {include file="analytics.tpl"}
    {/if}{if $customfoot}
{$customfoot}{/if}{jojoHook hook="customfoot"}{jojoHook hook="foot"}