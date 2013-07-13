<style type="text/css">
{literal}
#option-categories li {
  list-style-image: url('images/cms/admin/bullet2.gif');
}

#option-categories li a:focus {
  -moz-outline-style: none;
}

#option-categories li a.selected {
  font-weight: bold;
}

.options-title {
  background: transparent url('images/cms/admin/bg-menu-2.jpg') right bottom no-repeat;
  width: 100%;
  padding: 0 0 3px 10px;
  margin: 0 0 0 -10px;
  margin-bottom: 10px;
  margin-top: 15px;
}

.options-title h4 {
  display: inline;
  clear: none;
  margin: 0;
  padding: 0;
  width: 60%;
}

.options-title div {
  float: right;
  clear: none;
  color: red;
  width: 30%;
}

{/literal}
</style>
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