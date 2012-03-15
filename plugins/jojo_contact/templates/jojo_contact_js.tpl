function checkme()
{literal}{{/literal}
  var errors=new Array();
  var i=0;
{if $toaddresses}
  if (document.getElementById('form_sendto').value == '') {literal}{{/literal}errors[i++]='Send Enquiry To is a required field';{literal}}{/literal}
{/if}
{foreach from=$fields item=field}
{if $field.required}

{if  $field.type=='checkboxes'}
if ({foreach from=$field.options item=option name=options}
(!document.getElementById('form_{$field.field}_{$option|replace:' ':'_'|replace:'$':''}').checked){if !$smarty.foreach.options.last} && {/if}{/foreach})
{literal}{{/literal}errors[i++]='{$field.display} is a required field';{literal}}{/literal}

{elseif $field.type=='select'}
if (document.getElementById('form_{$field.field}').selectedIndex == 0)
{literal}{{/literal}errors[i++]='{$field.display} is a required field';{literal}}{/literal}

{elseif $field.type=='radio'}
if ({foreach from=$field.options item=option name=options}
(!document.getElementById('form_{$field.field}_{$option|replace:' ':'_'|replace:'$':''}').checked){if !$smarty.foreach.options.last} && {/if}{/foreach})
{literal}{{/literal}errors[i++]='{$field.display} is a required field';{literal}}{/literal}

{elseif $field.type=='emailwithconfirmation'}
if (document.getElementByiId('form_{$field.field}').value != document.getElementById('form_{$field.field}_confirmation').value) {literal}{{/literal}errors[i++]='{$field.display} must match confirmation field';{literal}}{/literal}

{else}
if (document.getElementById('form_{$field.field}').value == '') {literal}{{/literal}errors[i++]='{$field.display} is a required field';{literal}}{/literal}
  {if $field.validation=='email'} else if (!validateEmail(document.getElementById('form_{$field.field}').value)) {literal}{{/literal}errors[i++]='{$field.display} is not a valid email format';{literal}}{/literal}{/if}
{/if}
{/if}
{/foreach}



{if $option_new_database_method}
    {if $option_form_captcha}
    if (document.getElementById('CAPTCHA').value == '') {literal}{{/literal}errors[i++]='Please enter the CAPTCHA code (required to prevent spam)';{literal}}{/literal}
    {/if}
{else}
    {if $OPTIONS.contactcaptcha == 'yes'}
    if (document.getElementById('CAPTCHA').value == '') {literal}{{/literal}errors[i++]='Please enter the CAPTCHA code (required to prevent spam)';{literal}}{/literal}
    {/if}
{/if}

{literal}
if (errors.length==0) {
return(true);
} else {
alert(errors.join("\n"));
return(false);
}

}
{/literal}