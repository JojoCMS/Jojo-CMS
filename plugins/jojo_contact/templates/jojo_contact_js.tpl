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
if ({foreach from=$field.options item=option}(!document.getElementById('form_{$field.field}_{$option|replace:' ':'_'|replace:'$':''}').checked){if !$smarty.foreach.option.last} && {/if}{/foreach})
{literal}{{/literal}errors[i++]='{$field.display} is a required field';{literal}}{/literal}
{else}
if (document.getElementById('form_{$field.field}').value == '') {literal}{{/literal}errors[i++]='{$field.display} is a required field';{literal}}{/literal}
  {if $field.validation=='email'} else if (!validateEmail(document.getElementById('form_{$field.field}').value)) {literal}{{/literal}errors[i++]='{$field.display} is not a valid email format';{literal}}{/literal}{/if}
{/if}
{/if}
{/foreach}
{if $OPTIONS.contactcaptcha == 'yes'}
if (document.getElementById('CAPTCHA').value == '') {literal}{{/literal}errors[i++]='Please enter the CAPTCHA code (required to prevent spam)';{literal}}{/literal}
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