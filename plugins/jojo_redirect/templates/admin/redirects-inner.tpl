<div>
  <span class="col_wide">From</span>
  <span class="col_wide">To</span>
  <span class="col_narrow">Type</span>
  <span class="col_narrow">Order</span>
  <span class="col_wide">Notes</span>
  <span class="col_narrow">Use Regex?</span>
  <span class="col_med">&nbsp;</span>
</div>
{section name=r loop=$redirects}
<div>
<form method="post" action="actions/admin-save-redirect.php" target="frajax-iframe">
  <span class="col_wide"><input type="hidden" name="redirectid" value="{$redirects[r].redirectid}" /><input type="text" size="30" name="from" id="from_{$redirects[r].redirectid}" value="{$redirects[r].rd_from}" autocomplete="off" /></span>
  <span class="col_wide"><input type="text" size="30" name="to" id="to_{$redirects[r].redirectid}" value="{$redirects[r].rd_to}" autocomplete="off" /></span>
  <span class="col_narrow"><input type="text" size="3" name="type" id="type_{$redirects[r].redirectid}" value="{$redirects[r].rd_type}" autocomplete="off" /></span>
  <span class="col_narrow"><input type="text" size="3" name="order" id="order_{$redirects[r].redirectid}" value="{$redirects[r].rd_order}" autocomplete="off" /></span>
  <span class="col_wide"><input type="text"  size="30" name="notes" id="notes_{$redirects[r].redirectid}" value="{$redirects[r].rd_notes}" autocomplete="off" /></span>
  <span class="col_narrow"><input type="checkbox" name="regex" id="regex_{$redirects[r].redirectid}" value="1"{if $redirects[r].rd_regex==1} checked="checked"{/if} /></span>
  <span class="col_med"><input type="submit" name="save" id="save_{$redirects[r].redirectid}" value="Update" /><input type="submit" name="delete" id="delete_{$redirects[r].redirectid}" value="Delete" /></span>
</form>
</div>
{/section}
<div style="clear:both;"></div>
<h3>Add a new redirect</h3>
<div>
<form method="post" action="actions/admin-save-redirect.php" target="frajax-iframe">
    <span class="col_wide">
        <input type="hidden" name="redirectid" value="0" />
        <input type="text" size="30" name="from" id="from_0" value="" />
    </span>
    <span class="col_wide"><input type="text" size="30" name="to" id="to_0" value="" /></span>
    <span class="col_narrow"><input type="text" size="3" name="type" id="type_0" value="" /></span>
    <span class="col_narrow"><input type="text" size="3" name="order" id="order_0" value="" /></span>
    <span class="col_wide"><input type="text"  size="30" name="notes" id="notes_0" value="" /></span>
    <span class="col_narrow"><input type="checkbox" name="regex" id="regex_0" value="1" /></span>
    <span class="col_med"><input type="submit" name="save" id="save_0" value="Save" /></span>
</form>
</div>
<div style="clear:both;"></div>

<p class="note">If 'Use Regex?' is ticked From must be written as in regex form, with \ escaped \^$.|?*+()[ characters where they are intended literally. <br />
see <a href="http://www.regular-expressions.info/reference.html">http://www.regular-expressions.info/reference.html</a> for full syntax.</p>
<p class="note">The From regex can include a single () match that can be inserted into the To field by including empty brackets ()<br />
E.g. to redirect all requests for (something)/index.htm to (samesomething)/ <br />
From: (.+)/index\.htm &nbsp;&nbsp;To: ()/ &nbsp;&nbsp;Use Regex?: ticked</p>
<p class="note">Because redirects are stored in the session you will need to clear your current session in order to see the effects of any new redirects you add</p>