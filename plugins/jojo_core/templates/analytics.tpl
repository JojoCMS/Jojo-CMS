<!-- [Google Analytics] -->
{if $OPTIONS.analyticscodetype=='async'}
        <script type="text/javascript">/*<![CDATA[*/
            var _gaq = _gaq || [];
            _gaq.push(['_setAccount', '{$OPTIONS.analyticscode}']);
{if $OPTIONS.crossdomainanalytics=="yes"}
            _gaq.push(['_setDomainName', 'none']);
            _gaq.push(['_setAllowLinker', true]);
{/if}{if $sent and $OPTIONS.contact_tracking_code_analytics}
            _gaq.push(['_trackPageview',"{$OPTIONS.contact_tracking_code_analytics}"]);
{else}
            _gaq.push(['_trackPageview']);
{/if}{if $success and $google_ecommerce}
            {$google_ecommerce}
{/if}
            (function() {literal}{{/literal}
                var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
                ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
                var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
            {literal}}{/literal})();
        /*]]>*/</script>{$OPTIONS.contact_tracking_code}
{else}
{* old Google analytics code *}
<script type="text/javascript">
        /*<![CDATA[*/
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
{if $sent and $OPTIONS.contact_tracking_code_analytics}
    pageTracker._trackPageview("{$OPTIONS.contact_tracking_code_analytics}");
{else}
    pageTracker._trackPageview();
{/if}
{if $success and $google_ecommerce}
   {$google_ecommerce}
{/if}
{literal}} catch(err) {}{/literal}
    /*]]>*/
    </script>
{if $sent && $OPTIONS.contact_tracking_code}
    {$OPTIONS.contact_tracking_code}
{/if}{/if}