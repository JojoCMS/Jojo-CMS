{include file="admin/header.tpl"}

<div class="admin-home row">

<div class="col-md-6">

    {if !($browser == 'mozilla' || $browser == 'webkit' ) }
    <div class="admin-home-item-left">

        <h3>Browser Warning!</h3>
        <p>Jojo CMS Admin has only been tested in the <a href="http://www.getfirefox.com" target="_BLANK">Firefox</a>, <a href="http://www.google.com/chrome" target="_BLANK">Chrome</a> and <a href="www.apple.com/safari/" target="_BLANK">Safari</a> browsers. There are several known issues with Internet Explorer in particular which will prevent some features of the admin area from working.</p>

        <p>You are currently using the <strong>{$browser}</strong> browser</p>

        <p>We recommend you download the latest version of <a href="http://www.getfirefox.com">Firefox</a>, <a href="http://www.google.com/chrome" target="_BLANK">Chrome</a> or <a href="www.apple.com/safari/" target="_BLANK">Safari</a>, all free, all good.</p>

    </div>
    {/if}

    <div class="admin-home-item-left">

        <h2>Welcome</h2>
        Welcome to the admin section of your website.{if $jojoversion} Currently running Jojo CMS {$jojoversion}{/if}.

    </div>

    <div class="admin-home-item-left">

        <h3>Help!</h3>
        <p>Documentation for Jojo CMS is available <a href="http://www.jojocms.org/docs/" target="_BLANK">online</a>.
        Support is also available for Jojo CMS in several forms...</p>
        <ul>
        <li>Contact your {if $OPTIONS.webmasteraddress}<a href="mailto:{$OPTIONS.webmasteraddress}">web developer</a>{else}web developer{/if} for direct support</li>
        <li>Read the <a href="http://www.jojocms.org/docs/" target="_BLANK">Jojo documentation</a></li>
        <li>Browse or search the <a href="http://www.jojocms.org/forums/" target="_BLANK">Forums</a></li>
        <li>Submit a <a href="https://github.com/JojoCMS/Jojo-CMS/issues?state=open" target="_BLANK">bug report</a></li>
        <li>Contact the Jojo team directly - <a href="http://www.jojocms.org/contact/">www.jojocms.org/contact/</a> (if posting in the <a href="http://www.jojocms.org/forums/" target="_BLANK">forums</a> is not appropriate)</li>
        </ul>

    </div>

</div>

<div class="col-md-5 offset1">

    <div class="admin-home-item-right">
        <h3>Edit Site Options</h3>
        <p>These options are for managing your website. Unless you know what these do, they are best left unchanged</p>

        <!-- [Content Cache] -->
        <h3>Content Cache: </h3>
        <div class="controls">
            <label for="content-cache-on" class="radio inline"><input type="radio" name="content-cache" id="content-cache-on" onclick="$('#savemsg_contentcache').hide().html('Saving...').show(); frajax('admin-set-options','contentcache','yes');"{if $OPTIONS.contentcache == 'yes'} checked="checked"{/if} />On</label>
            <label for="content-cache-off" class="radio inline"><input type="radio" name="content-cache" id="content-cache-off" onclick="$('#savemsg_contentcache').hide().html('Saving...').show(); frajax('admin-set-options','contentcache','no');"{if $OPTIONS.contentcache == 'no'} checked="checked"{/if} />Off</label>
            <span id="savemsg_contentcache" style="color: red;" class="inline-help"></span>
        </div>
        <p>Caching your website's content makes it run a lot faster, and reduces load on the server. It should usually be left on, unless new features are being tested</p>

        <!-- [Empty Content Cache] -->
        <p><button class="btn btn-warning empty-cache" data-scope="html"/>Empty Page Cache</button></p>
        <p>Pages have a cache time of {$contentcachetime} - emptying the cache will ensure everyone will see fresh content immediately.</p>
        <p><button class="btn btn-warning empty-cache" data-scope="js"/>Empty JS Cache</button>
        <button class="btn btn-warning empty-cache" data-scope="css"/>Empty CSS Cache</button>
        <button class="btn btn-warning empty-cache" data-scope="external"/>Empty Externals Cache</button></p>
        <p>Resources (images, css, js etc) have a cache time of {$resourcecachetime} and should usually not need be cleared.</p>
        <p><button class="btn btn-danger empty-cache" data-scope="full"/>Clear Everything</button></p>

        <p>All options are available from the <a href="{$ADMIN}/options/">options page</a>.</p>
    </div>
</div>

</div>

{include file="admin/footer.tpl"}