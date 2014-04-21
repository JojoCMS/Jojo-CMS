{include file="admin/header.tpl"}
<div id="event-log">
<div class="row">
    <div class="col-md-4">
        <p>Export selected form's submissions as a CSV file<br />
        <form action='' method="post">
            <div class="input-group input-group-sm">
                <select class="form-control" name="form_id" id="form_id">
                        {foreach from=$forms item=f}
                        <option value="{$f.form_id}">{$f.form_name}</option>
                        {/foreach}
                </select>
                <span class="input-group-btn"><button type="submit" name="submit" class="btn btn-default btn-sm">Export</button></span>
            </div>
        </form>
        </p>
    </div>
</div>
<table class="sortabletable table table-bordered table-striped">
    <thead>
        <tr>
            <th></th>
            <th>ID</th>
            <th>Submitted</th>
            <th>From</th>
            <th>Subject</th>
            <th>To</th>
            <th>Description</th>
        </tr>
    </thead>
    <tbody>
{foreach from=$log item=e}
        <tr>
            <td>{if !$e.success}<span class="glyphicon glyphicon-exclaimation-sign text-danger" title="Sending failed!"></span>{/if}<a href="#" onclick="$.post('{$correcturl}', {ldelim} removeid: '{$e.formsubmissionid}' {rdelim}); window.location.reload();" title="Remove"><span class="glyphicon glyphicon-remove-circle text-danger"></span></a><br />
</td>
            <td>{$e.formsubmissionid}</td>
            <td>{$e.friendlydate}</td>
            <td>{$e.from_name}<br />{$e.from_email}</td>
            <td>{$e.subject}</td>
            <td>{$e.to_email}</td>
            <td id="desc_{$e.formsubmissionid}">
            <span class="short">{$e.shortdesc} <a href="#" onclick="$('#desc_{$e.formsubmissionid} .full').show();$('#desc_{$e.formsubmissionid} .short').toggle();return false;"><span class="glyphicon glyphicon-plus-sign"></span></a></span>
            <span class="full" style="display:none;">{$e.desc}<br /><a href="#" onclick="$('#desc_{$e.formsubmissionid} .full').toggle();$('#desc_{$e.formsubmissionid} .short').toggle();return false;"><span class="glyphicon glyphicon-minus-sign"></span></a></span>
            </td>
        </tr>
{/foreach}
    </tbody>
</table>
</div>
{include file="admin/footer.tpl"}