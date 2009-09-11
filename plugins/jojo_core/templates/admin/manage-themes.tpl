{include file="admin/header.tpl"}

<div id="message"></div>

{section name=p loop=$themes}
    <div class="theme-box{if $themes[p].status== 'active'} installed{/if}" title="{$themes[p].description|escape:'htmlall':$charset}">
      <h3>{$themes[p].name}</h3>
      <img src="images/w160/themes/{$themes[p].name}.jpg" alt="{$themes[p].name}" /><br />
      <button rel="{$themes[p].name}"><img class="icon" src="images/cms/icons/brick_add.png" alt="" id="theme-{$themes[p].name}-install" title="Install" /> {if $themes[p].status== 'active'}re{/if}install</button>

    </div>
{/section}

<script type="text/javascript">
{literal}
//Add a mouseover effect to themes
$('.theme-box').hover(
    function(){
        $(this).addClass('over')
    },
    function(){
        $(this).removeClass('over')
    }
);

$('.theme-box button').click(
    function(){
        $(this).parent().addClass('installing');
        $(this).replaceWith('<p>installing...</p>');
        $('div.theme-box button').fadeOut();
        $('div.theme-box').removeClass('installed');
        $.get(siteurl + '/json/admin-install-theme.php', {theme: $(this).attr('rel')}, function(data) {

                if (data.result) {
                    $('div.installing').addClass('installed');
                    $('div.installing img').hide();
                    $('div.installing p').html(data.message);
                } else {
                    alert('Error Installing theme: ' + data.message);
                }
        }, 'json');
        return false;
    }
);
{/literal}
</script>

{include file="admin/footer.tpl"}