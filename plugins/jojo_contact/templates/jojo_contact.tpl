{if $message}
<div class="message">{$message}</div>
{/if}
{if $content}{$content}{/if}
<form name="contactform" method="post" action="{$posturl}" onsubmit="return checkme()" class="contact-form">
<div>
{if $toaddresses}
    <label for="form_sendto">Send Enquiry To:</label>
    <select name="form_sendto" id="form_sendto">
        <option value="">Select</option>
{foreach item=to from=$toaddresses}
          <option value="{$to.email}">{$to.name}</option>
{/foreach}
    </select>&nbsp;*<br />
{/if}
    <p class="note">Required fields are marked *</p>
{foreach from=$fields key=k item=f }
    {assign var=x value=`$k-1`}
    {if $f.fieldset!='' && $f.fieldset!=$fields[$x].fieldset}<fieldset><legend>{$f.fieldset}</legend>{/if}
    {if !in_array($f.type,array('heading','note'))}<div><label for="form_{$f.field}">{if $f.display!=''}{$f.display}:{/if}</label>{/if}
    {if $f.type == 'textarea'}
    <textarea class="textarea" rows="{$f.rows|default:'10'}" cols="{$f.cols|default:'29'}" name="form_{$f.field}" id="form_{$f.field}">{$f.value}</textarea>{if $f.required}*{/if}</div>
    {elseif $f.type == 'checkboxes'}
    <div class="form-field">
{foreach from=$f.options item=fo }
        <input type="checkbox" class="checkbox" name="form_{$f.field}[{$fo}]" id="form_{$f.field}_{$fo|replace:' ':'_'|replace:'$':''}" value="{$fo}" /><label for="form_{$f.field}_{$fo}"> {$fo}</label><br />
{/foreach}
    </div></div>
    {elseif $f.type=='select'}
    <select name="form_{$f.field}" id="form_{$f.field}">
          <option value="">Select</option>
{foreach from=$f.options item=so}
          <option value="{$so|escape:'htmlall'}"{if $f.value == $so} selected="selected"{/if}>{$so}</option>
{/foreach}
    </select></div>
    {elseif $f.type=='heading'}{if in_array($f.size, array(1,2,3,4,5,6))}{$size=$f.size}{else}{$size=2}{/if}
    	<h{$size}>{$f.value}</h{$size}>
    {elseif $f.type=='note'}
    	<p>{$f.value}</p>
    {else}
    <input type="{$f.type}" class="{$f.type}" size="{$f.size}" name="form_{$f.field}" id="form_{$f.field}" value="{$f.value}" />
    {if $f.required}*{/if}</div>
    {/if}

    {if $f.description}<div class="form-field-description">{$f.description}</div>{/if}<br />
    {assign var=x value=`$k+1`}
    {if $f.fieldset!='' && $f.fieldset!=$fields[$x].fieldset}</fieldset>{/if}
{/foreach}

{if $OPTIONS.contactcaptcha == 'yes'}
    <br />
    <label for="CAPTCHA">Spam prevention:</label>
    <div class="form-field">
        <input type="text" class="text" size="8" name="CAPTCHA" id="CAPTCHA" value="" />*<br />
        Please enter the {$OPTIONS.captcha_num_chars|default:3} letter code below. This helps us prevent spam.<br />
        <img src="external/php-captcha/visual-captcha.php" width="200" height="60" alt="Visual CAPTCHA" /><br />
        <em>Code is not case-sensitive</em><br />
    </div>
{/if}
    <br />
    <div><label>&nbsp;</label><input type="submit" name="submit" value="Submit" class="button" onmouseover="this.className='button buttonrollover';" onmouseout="this.className='button'" /></div>
</div>
<br class="clear" />
</form>
