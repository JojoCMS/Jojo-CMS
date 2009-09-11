<script type="text/javascript" src="js/password-strength.js"></script>
<script type="text/javascript">
/* <![CDATA[ */
function checkme()
{literal}{
  var errors=new Array();
  var i=0;
  if (document.getElementById('oldp').value == '') {errors[i++]='Old password is a required field';}
  if (document.getElementById('newp').value == '') {errors[i++]='New password is a required field';}
  if (document.getElementById('newp2').value == '') {errors[i++]='New password confirmation is a required field';}
  if ((document.getElementById('newp').value != document.getElementById('newp2').value) && (document.getElementById('newp').value != '') && (document.getElementById('newp2').value != '')) {errors[i++]='Password does not match password confirmation';}
  if ((document.getElementById('oldp').value == document.getElementById('newp').value) && (document.getElementById('oldp').value != '') && (document.getElementById('newp').value != '')) {errors[i++]='Old password is the same as new password';}
  if ((document.getElementById('newp').value != '') && (!document.getElementById('newp').value.match(/[0-9]/i))) {errors[i++]='Passwords must contain at least one number';}
  if ((document.getElementById('newp').value != '') && (!document.getElementById('newp').value.match(/.{8,20}/i))) {errors[i++]='Passwords must be between 8 and 20 characters';}
  if (errors.length==0) {
    return(true);
  } else {
    alert(errors.join("\n"));
    return(false);
  }
}
{/literal}
/* ]]> */
</script>