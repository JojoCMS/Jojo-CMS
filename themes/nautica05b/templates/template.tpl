{include file="head.tpl"}
<body>
<!-- content - all container -->

<!-- #content: holds all except site footer - causes footer to stick to bottom -->
<div id="content">

  <!-- #header: holds the logo and top links -->
  <div id="header" class="width">

    <img src="images/logo.gif" alt="Your logo goes here"/>

    <ul>
        {foreach from=$nav item=n}
        <li {if $n.pageid== $pageid} id="current"{/if}><a href="{$n.url}"  title="{$n.title|escape:"html"}">{$n.label}</a></li>
        {/foreach}
     </ul>
  </div>
  <!-- #header end -->

  <!-- #headerImg: holds the main header image or flash -->
  <div id="headerImg" class="width"></div>

    <div id="submenu" class="width">{if $subnav}
        <ul>
        {foreach from=$subnav item=n}
            <li><a href="{$n.url}" onfocus="this.blur()"
            title="{if $n.pg_desc}{$n.pg_desc|escape:"html"}{else}{$n.pg_title|escape:"html"}{/if}">
            <span class="title">{if
            $n.pg_menutitle}{$n.pg_menutitle}{else}{$n.pg_title}{/if}</span> <span
            class="desc">{if $n.pg_desc}{$n.pg_desc|escape:"html"}{/if}</span> </a></li>
        {/foreach}
        </ul>
        {/if}
    </div>

    <!-- #page: holds the page content -->
    <div id="content-wrap"><!-- #columns: holds the columns of the page -->
        <div id="columns" class="widthPad">
            <div id="main">

            <!-- [Breadcrumb Navigation] -->
            {if $numbreadcrumbs > 1}
            <div id="breadcrumbs">
                {section name=bc loop=$breadcrumbs}
                    {if $smarty.section.bc.index == ($numbreadcrumbs-1)}
                    {$breadcrumbs[bc].name|escape:"htmlall":$charset}
                    {else}
                    <a href="{$breadcrumbs[bc].url}" title="{$breadcrumbs[bc].rollover|escape:"html":$charset}">{$breadcrumbs[bc].name|escape:"html":$charset}</a> &gt;
                    {/if}
                {/section}
            </div>
            {/if}
             <!-- [End Breadcrumb Navigation] -->

            <h1>{$title}</h1>
            {include file="content.tpl"}</div>

            <div id="sidebar">
                <div>
                <h2>Search</h2>
                <form class="search" method="post" action="search/">
                <p><input class="textbox" type="text" name="q" value="{$keywords}" />
                <input class="button" type="submit" name="Submit" value="Search" /></p>
                </form>
                </div>
            {include file="article-summary.tpl"}
            </div>
        </div>
    </div>

    <!--footer starts here-->
    <div id="footer">

    <div id="bg" class="width">
    <p>&copy; 2006 <strong>{$sitetitle}</strong> | Design by: <a
    href="http://www.oswd.org/design/information/id/3138">nautica</a> |
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

        {assign var=gap value=''} {foreach from=$footernav item=n} {$gap}
        <a href="{$n.url}" title="{if $n.pg_desc}{$n.pg_desc|escape:"html"}{else}{$n.pg_title|escape:"html"}{/if}">{if
        $n.pg_menutitle}{$n.pg_menutitle}{else}{$n.pg_title}{/if}</a> {assign
        var=gap value='&nbsp;|&nbsp;'} {/foreach}</p>
      </div>
      </div>
</div>
<!-- wrap ends here -->
{include file="foot.tpl"}
</body>
</html>