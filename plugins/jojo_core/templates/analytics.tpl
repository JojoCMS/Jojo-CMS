    <!-- [Google Analytics] -->
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
{/if}
