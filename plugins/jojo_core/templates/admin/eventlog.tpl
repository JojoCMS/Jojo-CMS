{include file="admin/header.tpl"}
<div id="event-log">
<p><a href="#" class="btn" onclick="$('.404').hide(); $('#hide404').hide(); $('#show404').show(); return false;" id="hide404" >Hide 404 errors</a><a href="#" class="btn" onclick="$('.404').show(); $('#show404').hide(); $('#hide404').show(); return false;" id="show404"  style="display:none;">Show 404 errors</a></p>
<table class="table">
    <thead>
        <tr>
            <th>Date / Time</th>
            <th>Code</th>
            <th>Short Description</th>
        </tr>
    </thead>
    <tbody>
{foreach item=e from=$log}
        <tr class="{$e.el_importance|replace:' ':'-'}{if $e.el_code=='404'} 404{/if}">
            <td title="{$e.el_datetime}">{$e.friendlydate}</td>
            <td>{$e.el_code}</td>
            <td>
                {if $e.el_desc}<a href="#" onclick="$('#desc_{$e.eventlogid}').show('fast');return false;"><img src="images/cms/icons/add.png" alt="more..." /></a>{/if}
                {$e.el_shortdesc}
{if $e.el_desc}
                <div style="font-weight:400;display:none;" id="desc_{$e.eventlogid}">
                    {$e.el_desc|nl2br}<br />
                    {if $e.el_uri}URI : {$e.el_uri}<br />{/if}
                    {if $e.el_referer}Referrer: {$e.el_referer}<br />{/if}
                    {if $e.el_userid}Userid: {$e.el_userid}{/if}{if $e.el_ip} IP: {$e.el_ip}{/if}{if $e.el_browser} Browser: {$e.el_browser}{/if}<br />
                </div>
{/if}
            </td>
        </tr>
{/foreach}
    </tbody>
</table>
</div>
{include file="admin/footer.tpl"}