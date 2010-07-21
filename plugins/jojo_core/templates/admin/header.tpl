<div id="flash"></div>
<div id="bb-editor-overlay" onclick="hideBBEditor();" style="display:none"></div>
<div id="wysiwyg-editor-overlay" onclick="hideWysiwygEditor();" style="display:none"></div>
<div id="wrap">
    <div id="header">
        <div id="admintitle"><a href="{$ADMIN}/"><img id="adminlogo" src="{$adminlogourl|default:"images/cms/admin/logo.png"}" alt="" title="Admin Homepage" /></a><h2>{$OPTIONS.sitetitle} Site Administration</h2></div>
        <div id="menu-wrap{if $subnav && (in_array($pageid, $selectedpages) )}-{if $subsubnav && in_array($pageid, $selectedpages) }sub{/if}subnav{/if}" >
            <div id="menu">
                <div id="adminnav">
                    <ul>
{foreach from=$jojo_admin_nav key=k item=n}
                        <li {if in_array($n.pageid, $selectedpages) } class="current"{/if}><a {if !$n.subnav}href="{$n.url}"{/if} id="_{$k}" title="{if $n.pg_desc}{$n.pg_desc|escape:"html"}{else}{$n.pg_title|escape:"html"}{/if}">{if $n.pg_menutitle}{$n.pg_menutitle}{else}{$n.pg_title}{/if}</a></li>
{if $n.subnav}
                        <li class="subnavlist">
                        <div class="adminsubnav">
                            <ul id="adminsubnav_{$k}" {if in_array($n.pageid, $selectedpages) } class="current"{/if}>
{foreach from=$n.subnav item=s}
                            <li class="{if in_array($s.pageid, $selectedpages) } current{/if}"><a href="{$s.url}" title="{if $s.pg_desc}{$s.pg_desc|escape:"html"}{else}{$s.pg_title|escape:"html"}{/if}">{if $s.pg_menutitle}{$s.pg_menutitle}{else}{$s.pg_title}{/if}</a></li>
{if $s.subnav}
                            <li class="subnavlist">
                            <div class="adminsubsubnav">
                                <ul  {if in_array($s.pageid, $selectedpages) } class="current"{/if}>
{foreach from=$s.subnav item=t}
                                    <li class="{if in_array($t.pageid, $selectedpages) } current{/if}"><a href="{$t.url}" title="{if $t.pg_desc}{$t.pg_desc|escape:"html"}{else}{$t.pg_title|escape:"html"}{/if}">{if $t.pg_menutitle}{$t.pg_menutitle}{else}{$t.pg_title}{/if}</a></li>
{/foreach}
                                </ul>
                            </div>
                            </li>
{/if}
{/foreach}
                        </ul>
                    </div>
                    </li>
{/if}
{/foreach}
                     <li><a href="{$SITEURL}/" title="Homepage" target="_blank">Site Home</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <h1 id="h1">{if $displayvalue}{$displayvalue|escape:"html"}{elseif $title}{$title|escape:"html"}{else}Admin{/if}</h1>
    {if $content}{$content}{/if}
    <div id="container">