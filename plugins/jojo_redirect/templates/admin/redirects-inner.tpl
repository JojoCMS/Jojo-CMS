<form method="post" action="actions/admin-save-redirect.php" target="frajax-iframe">
<table class="table table-striped">
<thead>
<tr>
  <th>From</th>
  <th>To</th>
  <th>Type</th>
  <th>Order</th>
  <th>Notes</th>
  <th>Use Regex?</th>
  <th>&nbsp;</th>
</tr>
</thead>
{foreach from=$redirects item=r}
<tr>
  <td><input class="form-control" type="text" size="30" name="from_{$r.redirectid}" id="from_{$r.redirectid}" value="{$r.rd_from}" autocomplete="off" /></td>
  <td><input type="text" class="form-control" size="30" name="to_{$r.redirectid}" id="to_{$r.redirectid}" value="{$r.rd_to}" autocomplete="off" /></td>
  <td><input type="text" class="form-control" size="3" name="type_{$r.redirectid}" id="type_{$r.redirectid}" value="{$r.rd_type}" autocomplete="off" /></td>
  <td><input type="text" class="form-control" size="3" name="order_{$r.redirectid}" id="order_{$r.redirectid}" value="{$r.rd_order}" autocomplete="off" /></td>
  <td><input type="text" class="form-control"  size="30" name="notes_{$r.redirectid}" id="notes_{$r.redirectid}" value="{$r.rd_notes}" autocomplete="off" /></td>
  <td><input type="checkbox" name="regex_{$r.redirectid}" id="regex_{$r.redirectid}" value="1"{if $r.rd_regex==1} checked="checked"{/if} /></td>
  <td><div class="btn-group"><button type="submit" class="btn btn-default" name="update" id="save_{$r.redirectid}"  value="{$r.redirectid}">Update</button><button type="submit" class="btn btn-default" name="delete" id="delete_{$r.redirectid}" value="{$r.redirectid}">Delete</button></div></td>
</tr>
{/foreach}
<thead>
<tr>
    <th colspan=7"><h4>Add a new redirect</h4></th>
</tr>
</thead>
<tr>
    <td><input class="form-control" type="text" size="30" name="from" id="from_0" value="" /></td>
    <td><input class="form-control" type="text" size="30" name="to" id="to_0" value="" /></td>
    <td><input class="form-control" type="text" size="3" name="type" id="type_0" value="" /></td>
    <td><input class="form-control" type="text" size="3" name="order" id="order_0" value="" /></td>
    <td><input class="form-control" type="text"  size="30" name="notes" id="notes_0" value="" /></td>
    <td><input type="checkbox" name="regex" id="regex_0" value="1" /></td>
    <td><button type="submit" class="btn btn-default" name="save" id="save_0" value="new">Save</button></td>
</tr>
</table>
</form>

<p class="help-block">If 'Use Regex?' is ticked From must be written as in regex form, with \ escaped \^$.|?*+()[ characters where they are intended literally. <br />
see <a href="http://www.regular-expressions.info/reference.html">http://www.regular-expressions.info/reference.html</a> for full syntax.</p>
<p class="help-block">The From regex can include a single () match that can be inserted into the To field by including empty brackets ()<br />
E.g. to redirect all requests for (something)/index.htm to (samesomething)/ <br />
From: (.+)/index\.htm &nbsp;&nbsp;To: ()/ &nbsp;&nbsp;Use Regex?: ticked</p>
<p class="note">Because redirects are stored in the session you will need to clear your current session in order to see the effects of any new redirects you add</p>