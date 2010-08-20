{include file="head.tpl"}
<body>

    <div id="wrap">
        <div id="header">
            <a href="{$SITEURL}/" title="Back to Homepage">{$sitetitle}</a>
        </div>

    <!-- menu -->
    <div id="menu">
        <ul>
        {foreach from=$mainnav item=n}
            <li{if in_array($n.pageid, $selectedpages)} class="selected"{/if}><a href="{$n.url}" title="{$n.title|escape:"html"}"{if $n.pg_followto=='no'} rel="nofollow"{/if}>{$n.label}</a></li>
        {/foreach}
        </ul>
        <div class="clear"></div>
    </div>

    <div id="container">

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

        <h1>{$title}</h1>
        <div id="content">
        {include file="content.tpl"}
        </div>
    </div>

    <div id="sidebar">
            <h2>Sidebar</h2>
            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc non orci at lectus semper congue. Mauris dapibus gravida purus. Aenean molestie est tincidunt est. Nulla a metus. Duis eu quam. Vivamus mollis feugiat ipsum. Phasellus vel arcu. Nullam scelerisque velit quis sem. Duis porttitor. Phasellus porttitor massa pharetra ligula. In non erat. </p>
            <a href="http://www.jojocms.org"><img src="images/logo.png" alt="Jojo CMS" /></a>
        </div>


</div>

    <div id="footer">
        <p>&copy; {$smarty.now|date_format:"%Y"} <strong>{$sitetitle}</strong> |
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
        <a href="http://www.jojocms.org/">Jojo CMS</a><br />

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