{if !$templateoptions || $templateoptions.frajax || $isadmin}<iframe src="javascript:false;" name="frajax-iframe" id="frajax-iframe" style="display:none; height: 0; width: 0; border: 0;"></iframe>{/if}
{if $OPTIONS.analyticscodetype != 'async' && $OPTIONS.analyticscode && !$isadmin && !$adminloggedin && $OPTIONS.analyticsposition != 'top'}
 {include file="analytics.tpl"}
{elseif $OPTIONS.analyticscode != 'async' && $OPTIONS.analyticscode && $adminloggedin && $OPTIONS.analyticsposition != 'top'}
<!-- Google Analytics code not displayed when logged in as Admin -->
{/if}

{jojoHook hook="foot"}