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
                        {foreach from=$jojo_admin_nav key=k item=n}<li{if $n.selected} class="current"{/if}><a {if !$n.subnav}href="{$n.url}"{/if} id="_{$k}" title="{$n.title}">{$n.label}</a></li>{if $n.subnav} 
                        <li class="subnavlist">
                        <div class="adminsubnav">
                            <ul id="adminsubnav_{$k}"{if $n.selected} class="current"{/if}>
                                {foreach from=$n.subnav item=s}<li{if $s.selected} class="current"{/if}><a href="{$s.url}" title="{$s.title}">{$s.label}</a></li>{if $s.subnav} 
                                <li class="subnavlist">
                                <div class="adminsubsubnav">
                                    <ul{if $s.selected} class="current"{/if}>
                                        {foreach from=$s.subnav item=t}<li{if $t.selectedpages} class="current"{/if}><a href="{$t.url}" title="{$t.title}">{$t.label}</a></li>
                                        {/foreach}
                                    </ul>
                                </div>
                                </li>
                                {/if}{/foreach}
                            </ul>
                        </div>
                        </li>
                        {/if}{/foreach}
                        <li><a href="{$SITEURL}/" title="Homepage" target="_blank">Site Home</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <h1 id="h1">{if $displayvalue}{$displayvalue|escape:"html"}{elseif $title}{$title|escape:"html"}{else}Admin{/if}</h1>
    {if $content}{$content}{/if}
    <div id="container">