{literal}
<script type="text/javascript">
/* onclick event for left links, hides all category divs then shows the selected one */
$(document).ready(function(){
$('#option-categories a').click(function(){
    $('.category').hide();
    $('#'+'category-'+$(this).html().replace(/\s/g,"-")).show('slow');
    $('#option-categories a').removeClass('selected');
    $(this).addClass('selected');
return false;
});
});
</script>
{/literal}