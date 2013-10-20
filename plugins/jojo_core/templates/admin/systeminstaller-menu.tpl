{if in_array('sysinstall', $_USERGROUPS)}
    <div id="systeminstaller_menu">
        <a id="si_menu_toggle" onclick="{literal}javascript:$(this).parent().toggleClass('visible');{/literal}"><img src="favicon.ico" /></a>
        <div id="si_inside">
            <h2>SYSTEM INSTALLER MENU</h2>

            {if $sysmenu.custom.links}
                <div class="si_section">
                    <h3>Links</h3>
                    {foreach from=$sysmenu.custom.links item=l key=t implode=", "}<a href="{$l}">{$t}</a><br />{/foreach}
                </div>
            {/if}

            <div class="si_section">
                <h3>This Page</h3>
                <div class="si_row"><span>PageID:</span> &nbsp;{$pageid}</div>
                <div class="si_row"><span>Link:</span> {if $pg_link}{$pg_link}{else}&lt;none&gt;{/if}</div>
            </div>

            {if $sysmenu.custom.user.fields}
                <div class="si_section">
                    <h3>This User</h3>
                    {foreach from=$sysmenu.custom.user.fields item=f key=k}
                        <div class="si_row"><span>{$k}:</span> &nbsp;{$userrecord.$f}</div>
                    {/foreach}
                </div>
            {/if}

            {foreach from=$sysmenu item=smenu key=sheading}{if $sheading !== "custom"}
                <div class="si_section">
                    <h3>{$sheading}</h3>
                    {foreach from=$smenu item=v key=k}
                        <div class="si_row"><span>{$k}:</span> &nbsp;{$v}</div>
                    {/foreach}
                </div>
            {/if}{/foreach}

            <div class="si_section">
                <h3 id="si_plugin_list_toggle" onclick="{literal}javascript:$('#si_plugin_list').slideToggle();{/literal}">
                    Plugins ({$sysmenu.custom.plugincount})
                </h3>
                <div id="si_plugin_list" style="display:none">
                    {foreach from=$sysmenu.custom.plugins item=pl}
                        {$pl}<br />
                    {/foreach}
                </div>
            </div>

        </div>
    </div>
    <link rel="stylesheet" href="{cycle values=$NEXTASSET}{jojoAsset file="css/systeminstaller_menu.css"}">
{/if}
