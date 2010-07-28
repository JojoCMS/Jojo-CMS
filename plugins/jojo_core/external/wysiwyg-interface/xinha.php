<!DOCTYPE html
     PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Xinha WYSIWYG Editor</title>
  <link rel="stylesheet" href="<?php echo _SITEURL ?>/css/styles.css" />
<script type="text/javascript">
/*<![CDATA[*/
    _editor_url  = document.location.href.replace(/wysiwyg-interface\/xinha\.php.*/, 'xinha/')
    _editor_lang = "en";
/*]]>*/
</script>

<!-- Load up the actual editor core -->
<script type="text/javascript" src="../xinha/XinhaCore.js"></script>
<script type="text/javascript">
/*<![CDATA[*/
    xinha_editors = null;
    xinha_init    = null;
    xinha_config  = null;
    xinha_plugins = null;

    // This contains the names of textareas we will make into Xinha editors
      xinha_plugins = xinha_plugins ? xinha_plugins :
      [
        /* Load plugins */
                  <?php 
                    $xinha_plugins = array (
                        'ContextMenu',
                        'Stylist',
                        'FindReplace',
                        'PasteText',
                        'ExtendedFileManager',
                        'TableOperations',
                        'InsertAnchor',
                        'HtmlEntities'
                    );
                    
                    $xinha_plugins = $sitemap = Jojo::applyFilter('xinha_plugins', $xinha_plugins);
                    
                    foreach ($xinha_plugins as $plugin) {
                        echo "'$plugin',\n";
                    }
                    ?>
       ];
     xinha_editors = xinha_editors ? xinha_editors :
      [
        'myTextArea'
      ];
    xinha_init = xinha_init ? xinha_init : function()
    {
         // THIS BIT OF JAVASCRIPT LOADS THE PLUGINS, NO TOUCHING  :)
         if(!Xinha.loadPlugins(xinha_plugins, xinha_init)) return;

       xinha_config = xinha_config ? xinha_config : new Xinha.Config();
        <?php Jojo::runHook('xinha_config_start'); ?>
        xinha_config.stylistLoadStylesheet("<?php echo _SITEURL ?>/css/styles.css");
        xinha_config.pageStyleSheets = ["<?php echo _SITEURL ?>/css/styles.css", "<?php echo _SITEURL ?>/css/xinha.css"];
        xinha_config.baseHref = "<?php echo _SITEURL ?>/";
        xinha_config.sevenBitClean = false;
	
        <?php 	$xinhastriphref = Jojo::getOption('xinha_strip_href','yes');
        if($xinhastriphref=='no'){ ?>	
                xinha_config.stripBaseHref = false;	
        <?php } ?>
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
            
                    $IMConfig['allowed_image_extensions'] = array("jpg","gif","png");
                    $IMConfig['allowed_link_extensions'] = array("jpg","gif","pdf","ip","txt","doc",
                                                                 "psd","png","html","swf",
                                                                 "xml","xls");
            
                    require_once _BASEPLUGINDIR . '/jojo_core/external/xinha/contrib/php-xinha.php';
                    xinha_pass_to_php_backend($IMConfig);
                    ?>
                }
        }
	
  xinha_editors = Xinha.makeEditors(xinha_editors, xinha_config, xinha_plugins);

  xinha_editors.myTextArea.config.width = '780px';
  xinha_editors.myTextArea.config.height = '460px';

  Xinha.startEditors(xinha_editors);
  window.onload = null;
}
window.onload = xinha_init;

//save changes back to parent textarea
function commit() {
    document.forms[0].submit();
}

function closing() {
   parent.$('#jpop_loading').hide();
   parent.$('#jpop_overlay').hide();
   parent.$('.jpop_content').hide();
   parent.$('select.jpop_select').removeClass('jpop_select');
}
/*]]>*/
</script>

</head>

<body>
<form onsubmit="parent.$('textarea[name=<?php if(isset($_GET['field'])) echo $_GET['field']; ?>]').val(this.myTextArea.value); parent.$('textarea[name=<?php if(isset($_GET['field'])) echo $_GET['field']; ?>]').trigger('change'); return false;">
<textarea id="myTextArea" name="myTextArea" rows="10" cols="80" style="width:100%"></textarea>
<div style="float:right">
    <input type="button" class="jojo-admin-edit-done"   value="Done"   onclick="commit(); closing(); return false;" />
    <input type="button" class="jojo-admin-edit-cancel" value="Cancel" onclick="closing(); return false;" />
</div>
</form>
<script type="text/javascript">
/*<![CDATA[*/
    <?php if (isset($_GET['field'])) {?>document.getElementById('myTextArea').value = parent.$('textarea[name=<?php echo $_GET['field']; ?>]').val();<?php } ?>
/*]]>*/
</script>
</body>
</html>