{literal}
<script type="text/javascript">
/*<![CDATA[*/
    //_editor_url  = document.location.href.replace(/wysiwyg-interface\/xinha\.php.*/, 'xinha/')
    _editor_url  = '{/literal}{$SITEURL}{literal}/external/xinha/';
    _editor_lang = "en";
/*]]>*/
</script>

<!-- Load up the actual editor core -->
<script type="text/javascript" src="{/literal}{$SITEURL}{literal}/external/xinha/XinhaCore.js"></script>


<script type="text/javascript">
/*<![CDATA[*/
    var xinha_editors = null;
    var xinha_init    = null;
    var xinha_config  = null;
    var xinha_plugins = null;
    var xinha_editor_names = null;

    xinha_plugins = xinha_plugins ? xinha_plugins :
      [
        /* Load plugins */
        {/literal}
        {foreach from=$xinha_plugins item=xinha_plugin}
        '{$xinha_plugin}', 
        {/foreach}
        {literal}
       ];

    // This contains the names of textareas we will make into Xinha editors
    xinha_editors = xinha_editors ? xinha_editors :
      [
        {/literal}{foreach name=wysiwyg from=$wysiwyg_editors item=editor}'fm_{$editor}_xinha'{if !$smarty.foreach.wysiwyg.last}, {/if}{/foreach}{literal}
      ];


    Xinha.makeVisibleEditors = function(xinha_editors, xinha_config, xinha_plugins) {
        var visible_editors = [];
        for (var i in xinha_editors) {
            var editor_id = xinha_editors[i];
            if (typeof editor_id == "string") {
                if ($('#' + xinha_editors[i]).is(':visible') || parent.$('#' + xinha_editors[i]).is(':visible')) {
                    visible_editors.push(editor_id);
                }
            }
        }
        return Xinha.makeEditors(visible_editors, xinha_config, xinha_plugins);
    }

    function startVisibleXinhaEditors() {
        xinha_editors = Xinha.makeVisibleEditors(
             xinha_editors, xinha_config, xinha_plugins
        );
        Xinha.startEditors(xinha_editors);
    }
    
    xinha_init = xinha_init ? xinha_init : function()
    {
         // THIS BIT OF JAVASCRIPT LOADS THE PLUGINS, NO TOUCHING  :)
         if(!Xinha.loadPlugins(xinha_plugins, xinha_init)) return;

       xinha_config = xinha_config ? xinha_config : new Xinha.Config();
       
       xinha_config.Events.onBeforeSubmit = function(event) {
           {/literal}{foreach name=wysiwyg from=$wysiwyg_editors item=editor}
           var html = xinha_editors['fm_{$editor}_xinha'].getEditorContent();
           $('#fm_{$editor}').val(html);
           {/foreach}{literal}
           return true;
       }

        {/literal}{jojoHook hook="xinha_config_start"}{literal}/*  <?php Jojo::runHook('xinha_config_start'); ?> */
         
        {/literal}
        {if !$OPTIONS.xinha_allowstyling || $OPTIONS.xinha_allowstyling=='no'}
        xinha_config.toolbar =
        [
          ["popupeditor"],
          ["separator","formatblock","bold","italic","underline","strikethrough"],
          ["separator","subscript","superscript"],
          ["separator","justifyleft","justifycenter","justifyright","justifyfull"],
          ["separator","insertorderedlist","insertunorderedlist","outdent","indent"],
          ["separator","inserthorizontalrule","createlink","insertimage","inserttable"],
          ["separator","undo","redo"],
          ["separator","killword","clearfonts","removeformat","toggleborders","splitblock"],
          ["separator","htmlmode","showhelp","about"]
        ];
        {/if}
        {literal}
         
        xinha_config.stylistLoadStylesheet("{/literal}{$SITEURL}{literal}/css/styles.css");
        xinha_config.pageStyleSheets = ["{/literal}{$SITEURL}{literal}/css/styles.css", "{/literal}{$SITEURL}{literal}/css/xinha.css"];
        xinha_config.baseHref = "{/literal}{$SITEURL}{literal}/";
        xinha_config.sevenBitClean = false;
        xinha_config.height = '350px';
        xinha_config.formatblock =
          {
            "&mdash; format &mdash;": "",
            "Heading 1": "h1",
            "Heading 2": "h2",
            "Heading 3": "h3",
            "Heading 4": "h4",
            "Heading 5": "h5",
            "Heading 6": "h6",
            "Normal"   : "p",
            "Block Quote"  : "blockquote",
            "Formatted": "pre"
          };
        {/literal}
        {if $OPTIONS.xinha_strip_href == 'no'}
                xinha_config.stripBaseHref = false;
        {/if}
        {literal}

        if (xinha_config.ExtendedFileManager) {
                with (xinha_config.ExtendedFileManager)
                {
                    <?php

                    // define backend configuration for the plugin
                    $IMConfig = array();
                    $IMConfig['images_dir'] = _DOWNLOADDIR . '/images/';
                    $IMConfig['images_url'] = _SITEURL . '/downloads/images/';
                    $IMConfig['files_dir'] = _DOWNLOADDIR . '/files/';
                    $IMConfig['files_url'] = _SITEURL . '/downloads/files/';
                    $IMConfig['thumbnail_prefix'] = 't_';
                    $IMConfig['thumbnail_dir'] = 't';
                    $IMConfig['resized_prefix'] = 'resized_';
                    $IMConfig['resized_dir'] = '';
                    $IMConfig['tmp_prefix'] = '_tmp';
                    $IMConfig['view_type'] =  Jojo::getOption('xinha_viewtype','thumbview');
                    $IMConfig['allow_upload'] = true;
                    $IMConfig['max_filesize_kb_image'] = Jojo::getOption('max_imageupload_size','2000');
                    $IMConfig['max_filesize_kb_link'] = Jojo::getOption('max_fileupload_size','5000');

                    // Maximum upload folder size in Megabytes.
                    // Use 0 to disable limit
                    $IMConfig['max_foldersize_mb'] = 0;

                    $IMConfig['allowed_image_extensions'] = explode(',', Jojo::getOption('allowed_imageupload_extensions','jpg,gif,png'));
                    $IMConfig['allowed_link_extensions'] = explode(',', Jojo::getOption('allowed_fileupload_extensions','jpg,gif,pdf,ip,txt,doc,docx,ppt,pptx,psd,png,html,swf,mp3,mp4,xml,xls'));

                    require_once _BASEPLUGINDIR . '/jojo_core/external/xinha/contrib/php-xinha.php';
                    xinha_pass_to_php_backend($IMConfig);
                    ?>
                }
        }

        //xinha_editors = Xinha.makeEditors(xinha_editors, xinha_config, xinha_plugins);
        //Xinha.startEditors(xinha_editors);

        startVisibleXinhaEditors();
 
        window.onload = null;
    }
//window.onload = xinha_init;
Xinha._addEvent(window,'load', xinha_init)


/*]]>*/
</script>

{/literal}