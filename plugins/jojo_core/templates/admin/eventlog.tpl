{include file="admin/header.tpl"}
<div id="event-log">
<div class="row">
        <form action='' method="post" class="form-inline">
            <div class="form-group">
                <button type="submit" name="submit" class="btn btn-default btn-sm">Clear</button>
                <select class="form-control input-sm" name="logdelete" id="logdelete">
                        {if $OPTIONS.eventlog_404=='yes'}<option value="404">404 Errors</option>{/if}
                        <option value="php">PHP Errors</option>
                        <option value="all">All</option>
                </select>
            </div>
        </form>
    </div>
</div>
{if $OPTIONS.eventlog_404=='yes'}<p><button class="btn btn-default btn-sm" onclick="$('.404').hide(); $('#hide404').hide(); $('#show404').show(); return false;" id="hide404" >Hide 404 errors</button><button class="btn btn-default btn-sm" onclick="$('.404').show(); $('#show404').hide(); $('#hide404').show(); return false;" id="show404"  style="display:none;">Show 404 errors</button></p>{/if}
<table class="table">
    <thead>
        <tr>
            <th>Date / Time</th>
            <th>Code</th>
            <th>Short Description</th>
        </tr>
    </thead>
    <tbody>
{foreach item=e from=$log}{if !($e.el_code=='404' && $OPTIONS.eventlog_404=='no')}
        <tr class="{$e.el_importance|replace:' ':'-'}{if $e.el_code=='404'} 404{/if}">
            <td title="{$e.el_datetime}">{$e.friendlydate}</td>
            <td>{$e.el_code}</td>
            <td>
                {$e.el_shortdesc} {if $e.el_desc}<a href="#" onclick="$('#desc_{$e.eventlogid}').show('fast');$(this).hide();return false;"><span class="glyphicon glyphicon-plus-sign"></span></a>{/if}
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
{/if}{/foreach}
    </tbody>
</table>
</div>
{include file="admin/footer.tpl"}