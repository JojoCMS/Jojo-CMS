<!-- [Google Analytics] -->
    <script{if !$htmldoctype} type="text/javascript"{/if}>
    /*<![CDATA[*/
{if $OPTIONS.analyticscodetype=='universal'}{literal}
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');{/literal}
  ga('create', '{$OPTIONS.analyticscode}', '{if $issecure}{$SITEURL|replace:"https://":""|replace:"www.":""}{else}{$SITEURL|replace:"http://":""|replace:"www.":""}{/if}');
  ga('send', 'pageview');
{elseif $OPTIONS.analyticscodetype=='async'}
    var _gaq = [];
    _gaq.push(['_setAccount', '{$OPTIONS.analyticscode}']{if $OPTIONS.crossdomainanalytics=="yes"}, ['_setDomainName', 'none'], ['_setAllowLinker', true]
    {/if}{if $sent and $contactFrom_tracking_analytics}, ['_trackPageview',"{$contactFrom_tracking_analytics}"]
    {elseif $sent and $OPTIONS.contact_tracking_code_analytics}, ['_trackPageview',"{$OPTIONS.contact_tracking_code_analytics}"]
    {else}, ['_trackPageview']{/if});{if $success and $google_ecommerce}
    {$google_ecommerce}{/if}
    (function() {literal}{{/literal}
        var ga = document.createElement('script');{if !$htmldoctype} ga.type = 'text/javascript';{/if} ga.async = true;
        ga.src = '{if $issecure}https://ssl.{else}http://www.{/if}google-analytics.com/ga.js';
        var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
    {literal}}{/literal})();
{else}{* old Google analytics code *}
    var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
    document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
    /*]]>*/
    </script>
    <script type="text/javascript">
        /*<![CDATA[*/
    {literal}try {{/literal}
        var pageTracker = _gat._getTracker("{$OPTIONS.analyticscode}");
    {if $OPTIONS.crossdomainanalytics=="yes"}
        pageTracker._setDomainName("none");
        pageTracker._setAllowLinker(true);
    {/if}
    {jojoHook hook="analytics_trackPageview"}
    {if $sent && $contactFrom_tracking_analytics}
        pageTracker._trackPageview({$contactFrom_tracking_analytics});
    {elseif $sent && $OPTIONS.contact_tracking_code_analytics}
        pageTracker._trackPageview("{$OPTIONS.contact_tracking_code_analytics}");
    {else}
        pageTracker._trackPageview();
    {/if}
    {if $success and $google_ecommerce}
       {$google_ecommerce}
    {/if}
    {literal}} catch(err) {}{/literal}
{/if}
    /*]]>*/
    </script>{if $sent && $OPTIONS.contact_tracking_code}
    {$OPTIONS.contact_tracking_code}
    {/if}