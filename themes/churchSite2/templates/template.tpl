
{include file="head.tpl"}
<body>
<div id="wrap">
	<div id="header">
  		<div id="logo-text"><a href="{$basedir}" alt="Back to Homepage" title="Back to Homepage">{$sitetitle}</a></div>
  		<div id="header-form">
  		<form class="search" method="post" action="search/">
		<p><input class="textbox" type="text" name="q" value="{$keywords}" />
		<input class="button" type="submit" name="Submit" value="Search" /></p>
		</form>
		</div>
	</div>

	<div id="menu">
		<ul>
			{foreach from=$nav item=n}
				<li {if $n.pageid== $pageid} id="current"{/if}><a href="{$n.url}"
				title="{if $n.pg_desc}{$n.pg_desc|escape:"html"}{else}{$n.pg_title|escape:"html"}{/if}">{if
				$n.pg_menutitle}{$n.pg_menutitle}{else}{$n.pg_title}{/if}</a></li>
			{/foreach}
		</ul>
	</div>

<div id="content-wrap">
		<div id="leftpic">
			<div id="leftsidebar">
				{if $subnav}
				<h2>Subnavigation</h2>
				<div id="leftsidebartext">
				<ul>
					{foreach from=$subnav item=n}
					<li><a href="{$n.url}" onfocus="this.blur()" title="{if $n.pg_desc}{$n.pg_desc|escape:"html"}{else}{$n.pg_title|escape:"html"}{/if}">
				 	<span class="title">{if $n.pg_menutitle}{$n.pg_menutitle}{else}{$n.pg_title}{/if}</span>
				  	<span class="desc">{if $n.pg_desc}{$n.pg_desc|escape:"html"}{/if}</span>
				 	</a></li>
					{/foreach}
				</ul>
				</div>
				{/if}
				<h3>Contact Information</h3>
				<div id="leftsidebartext1">
				Add your contact information in this field, if you want!
				</div>
			</div>
		</div>

		<div id="main">
			<div id="middlepic"></div>
			<div id="content">
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
				{include file="content.tpl"}
			</div>
		</div>

		<div id="rightpic">
			<div id="rightsidebar">
			{include file="article-summary.tpl"}
			</div>
		</div>
	<br style="clear:both;">
	<div id="footer">
		<div id="footer_text">
		<p>&copy; 2006 <strong>{$sitetitle}</strong> | Design by: <a
			href="http://refueled.net">RFDN</a> |
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


{include file="foot.tpl"}
</body>
</html>