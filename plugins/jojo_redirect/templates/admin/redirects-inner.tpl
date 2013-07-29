<form method="post" action="actions/admin-save-redirect.php" target="frajax-iframe">
<table class="table table-striped">
<tr>
  <th>From</th>
  <th>To</th>
  <th>Type</th>
  <th>Order</th>
  <th>Notes</th>
  <th>Use Regex?</th>
  <th>&nbsp;</th>
</tr>
{foreach from=$redirects item=r}
<tr>
  <td><input type="hidden" name="redirectid" value="{$r.redirectid}" /><input type="text" size="30" name="from" id="from_{$r.redirectid}" value="{$r.rd_from}" autocomplete="off" /></td>
  <td><input type="text" size="30" name="to" id="to_{$r.redirectid}" value="{$r.rd_to}" autocomplete="off" /></td>
  <td><input type="text" size="3" name="type" id="type_{$r.redirectid}" value="{$r.rd_type}" autocomplete="off" /></td>
  <td><input type="text" size="3" name="order" id="order_{$r.redirectid}" value="{$r.rd_order}" autocomplete="off" /></td>
  <td><input type="text"  size="30" name="notes" id="notes_{$r.redirectid}" value="{$r.rd_notes}" autocomplete="off" /></td>
  <td><input type="checkbox" name="regex" id="regex_{$r.redirectid}" value="1"{if $r.rd_regex==1} checked="checked"{/if} /></td>
  <td><input type="submit" class="btn btn-default" name="save" id="save_{$r.redirectid}" value="Update" /><input type="submit" class="btn btn-default" name="delete" id="delete_{$r.redirectid}" value="Delete" /></td>
</tr>
{/foreach}
<tr>
    <th colspan=7">Add a new redirect</th>
</tr>
<tr>
  <td>
        <input type="hidden" name="redirectid" value="0" />
        <input type="text" size="30" name="from" id="from_0" value="" />
    </td>
    <td><input type="text" size="30" name="to" id="to_0" value="" /></td>
    <td><input type="text" size="3" name="type" id="type_0" value="" /></td>
    <td><input type="text" size="3" name="order" id="order_0" value="" /></td>
    <td><input type="text"  size="30" name="notes" id="notes_0" value="" /></td>
    <td><input type="checkbox" name="regex" id="regex_0" value="1" /></td>
    <td><input type="submit" class="btn btn-default" name="save" id="save_0" value="Save" /></td>
</tr>
</table>
</form>

<p class="note">If 'Use Regex?' is ticked From must be written as in regex form, with \ escaped \^$.|?*+()[ characters where they are intended literally. <br />
see <a href="http://www.regular-expressions.info/reference.html">http://www.regular-expressions.info/reference.html</a> for full syntax.</p>
<p class="note">The From regex can include a single () match that can be inserted into the To field by including empty brackets ()<br />
E.g. to redirect all requests for (something)/index.htm to (samesomething)/ <br />
From: (.+)/index\.htm &nbsp;&nbsp;To: ()/ &nbsp;&nbsp;Use Regex?: ticked</p>
<p class="note">Because redirects are stored in the session you will need to clear your current session in order to see the effects of any new redirects you add</p>