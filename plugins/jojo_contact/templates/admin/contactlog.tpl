{include file="admin/header.tpl"}
<div id="event-log">
Export selected form's submissions as a CSV file<br />
<form action='' method="post">
            <select name="form_id" id="form_id">
                {foreach from=$forms item=f}
                <option value="{$f.form_id}">{$f.form_name}</option>
                {/foreach}
        </select>
        <input type="submit" name="submit" value="Submit" class="button" onmouseover="this.className='button buttonrollover';" onmouseout="this.className='button'" /><br />
</form>
<table class="sortabletable">
    <thead>
        <tr>
            <th></th>
            <th>ID</th>
            <th>Submitted</th>
            <th>Subject</th>
            <th>By</th>
            <th>Description</th>
        </tr>
    </thead>
    <tbody>
{foreach from=$log item=e}
        <tr>
            <td>{if !$e.success}<img src="images/cms/icons/error.png" alt="Sending failed!" /> {/if}<a href="#" onclick="$.post('{$correcturl}', {ldelim} removeid: '{$e.formsubmissionid}' {rdelim}); window.location.reload();" title="Remove"><img src="images/cms/icons/delete.png" alt="Remove"  /></a><br />
</td>
            <td>{$e.formsubmissionid}</td>
            <td>{$e.friendlydate}</td>
            <td>{$e.subject}</td>
            <td>{$e.from_email}</td>
            <td id="desc_{$e.formsubmissionid}">
            <span class="short">{$e.shortdesc}<a href="#" onclick="$('#desc_{$e.formsubmissionid} .full').show();$('#desc_{$e.formsubmissionid} .short').toggle();return false;"><img src="images/cms/icons/add.png" alt="more..." /></a></span>
            <span class="full" style="display:none">{$e.desc}<br /><a href="#" onclick="$('#desc_{$e.formsubmissionid} .full').toggle();$('#desc_{$e.formsubmissionid} .short').toggle();return false;"><img src="images/cms/icons/less.png" alt="less..." /></a></span>
            </td>
        </tr>
{/foreach}
    </tbody>
</table>
</div>
{include file="admin/footer.tpl"}