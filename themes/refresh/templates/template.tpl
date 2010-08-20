{include file="head.tpl"}
<body>
    <!-- wrap starts here -->
    <div id="wrap"><!--header -->
        <div id="header">
            <div id="logo-text"><a href="{$SITEURL}/" title="Back to Homepage">{$sitetitle}</a></div>
            <form name="search" class="search" method="post" action="search/">
              <input class="searchbox" type="text" name="q" value="Search" onclick="if(this.value=='Search')this.value='';" onblur="if(this.value=='')this.value='Search';"/>
              <input type="image" src="images/searchsubmit.gif" name="Submit" class="searchinput"/><br />
              <a class="searchwhitetext" href="search/">Advanced search</a>
            </form>
        </div>

    <!-- menu -->
    <div id="menu">
        <ul>
        {foreach from=$mainnav item=n}
            <li{if in_array($n.pageid, $selectedpages)} id="current"{/if}><a href="{$n.url}" title="{$n.title|escape:"html"}"{if $n.pg_followto=='no'} rel="nofollow"{/if}>{$n.label}</a></li>
        {/foreach}
        </ul>
    </div>

    <!-- content-wrap starts here -->
    <div id="content-wrap">
        <div id="sidebar">
            {if $subnav}
            <h2>Sub Pages</h2>
            <div class="left-box">
                <ul class="sidemenu">
                   {foreach from=$subnav item=n}
                    <li><a href="{$n.url}" title="{$n.title|escape:"html"}">{$n.label}</a></li>
                   {/foreach}
                </ul>
            </div>
            {/if} {include file="article-summary.tpl"}</div>

    <div id="main">
        <a name="body"></a>
        <!-- [Breadcrumb Navigation] -->
        {if $numbreadcrumbs > 1}
        <div id="breadcrumbs">
    			{foreach from=$breadcrumbs item=bc name=bc}
    				{if $smarty.foreach.bc.index == ($numbreadcrumbs-1)}
    				                {$bc.name|escape:"htmlall":$charset}
    				{else}
    				<a href="{$bc.url}" title="{$bc.rollover|escape:"html":$charset}">{$bc.name|escape:"html":$charset}</a> &gt;
    				{/if}
    			{/foreach}
        </div>
        {/if}
        <!-- [End Breadcrumb Navigation] -->

        <h1 id="h1">{$title}</h1>
        <div id="content">
        {include file="content.tpl"}
        </div>
    </div>

    <!-- content-wrap ends here --></div>

    <!--footer starts here-->
    <div id="footer">
        <p>&copy; {$smarty.now|date_format:"%Y"} <strong>{$sitetitle}</strong> | Design by: <a
        href="http://www.styleshout.com/">styleshout</a> |
        {***********************************************************
        About the "Powered by Jojo CMS" link
        ====================================
        Hundreds of hours of work have gone into producing Jojo, and many more
        hours are being spent improving and maintaining the system. We give
        Jojo away completely free, but we do kindly ask that you keep the link
        below intact as a sign of good faith. While there is no legal requirement
        within the license to keep the link, it's one of the few ways we get "paid"
        for our work, and it costs you nothing to leave it there.
        If you are still wanting to remove the link, please consider one of the
        following options before deleting the link outright...

        - providing a simple homepage only link instead of a sitewide link
        - placing the link on a subpage of the site (we prefer homepage links,
        but don't insist)
        - adding rel="nofollow' to the link to prevent any pagerank from passing,
        but visitors can still follow the link
        - donating US$100 (or a donation of your choice) towards the project

        - http://www.jojocms.org/donate/

        ***********************************************************} Powered by:
        <a href="http://www.jojocms.org/">Jojo CMS</a> | Valid
        <a href="http://validator.w3.org/check?uri=referer">XHTML</a> |
        <a href="http://jigsaw.w3.org/css-validator/check/referer">CSS</a><br />

            {foreach from=$footernav item=n name=footer}
            <a {if $smarty.foreach.footer.first}class='first-child'{/if} href="{$n.url}" title="{$n.title|escape:"html"}"{if $n.pg_followto=='no'} rel="nofollow"{/if}>{$n.label}</a>
            {if !$smarty.foreach.footer.last}|{/if}
            {/foreach}
        </p>
    </div>

    <!-- wrap ends here --></div>
    {include file="foot.tpl"}
</body>
</html>