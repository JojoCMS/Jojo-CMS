    <script type="text/javascript" src="external/jsTree/_stable/jquery.tree.js"></script>
    <link rel="stylesheet" type="text/css" href="external/jsTree/_stable/themes/default/style.css" />

    <!-- required for xml data type -->
    <script type="text/javascript" src="external/jsTree/lib/sarissa.js"></script>
    <script type="text/javascript" src="external/jsTree/_stable/plugins/jquery.tree.xml_flat.js"></script>
    <script type="text/javascript" src="external/jsTree/_stable/plugins/jquery.tree.xml_nested.js"></script>

    <script type="text/javascript" src="external/jsTree/lib/jquery.metadata.js"></script>
    <script type="text/javascript" src="external/jsTree/_stable/plugins/jquery.tree.metadata.js"></script>

    <script type="text/javascript" src="external/jsTree/lib/jquery.hotkeys.js"></script>
    <script type="text/javascript" src="external/jsTree/_stable/plugins/jquery.tree.hotkeys.js"></script>

    <script type="text/javascript" src="external/jsTree/lib/jquery.cookie.js"></script>
    <script type="text/javascript" src="external/jsTree/_stable/plugins/jquery.tree.cookie.js"></script>

    <div id="treediv" class="treediv" style="min-height:400px; overflow-x:auto; border-right:1px solid #eeeeee;"></div>
    <script type="text/javascript">{literal}
        var canLoad = true;
        $(function() {
              $("#treediv").tree({
                data  : {
                  type  : "json",
                  async : true,
                  opts : {
                      method : 'GET',
                      url   : siteurl + "/json/admin-edit-nodes.php?table={/literal}{$table}{literal}"
                  }
                },
                rules : {
                    renameable: "none",
                    deletable: "none",
{/literal}{if $draggable}{literal}
                    draggable: "all",
                    dropable: "all"
{/literal}{else}{literal}
                    draggable: "none",
                    dropable: "none"
{/literal}{/if}{literal}
                },
                callback : {
                    onchange : function(NODE, TREE_OBJ) {
                        if (canLoad && !$(NODE).hasClass('locked') && NODE.id) {
                            frajax('load', '{/literal}{$table}{literal}', NODE.id);
                        }
                    }
{/literal}{if $draggable}{literal}
                    ,
                    onmove : function(NODE,REF_NODE,TYPE,TREE_OBJ) {
                        if (TYPE == 'inside') {
                            /* Insert last inside */
                            $.post('json/admin-edit-move.php',
                                   {table: '{/literal}{$table}{literal}', id: NODE.id, newParent: REF_NODE.id, order: 999}
                                   );
                        } else if (TYPE == "before") {
                            /* Before an existing node */
                            $.post('json/admin-edit-move.php',
                                   {table: '{/literal}{$table}{literal}', id: NODE.id, newParent: $(REF_NODE).attr('parentid'), order: parseInt($(REF_NODE).attr('pos'))}
                                   );
                        } else if (TYPE == "after") {
                            /* After an existing node */
                            $.post('json/admin-edit-move.php',
                                   {table: '{/literal}{$table}{literal}', id: NODE.id, newParent: $(REF_NODE).attr('parentid'), order: parseInt($(REF_NODE).attr('pos'))}
                                   );
                        }
                    }
{/literal}{else}{literal}
                    ,
                    beforemove: function(NODE,REF_NODE,TYPE,TREE_OBJ) {
                        /* No moving */
                        return false;
                    }
{/literal}{/if}{literal}
                },
                ui : {
                    context : []
                }
            });
        });
    {/literal}</script>
{if $draggable}<em style="color: #aaa; font-style: italic">Drag-drop to reorder</em>{/if}