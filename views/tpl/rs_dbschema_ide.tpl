<html>
<head>
    <title></title>
    <link rel="stylesheet" type="text/css" href="[{$oViewConf->getModuleUrl("rs-dbschema", "out/src/style/css.css")}]">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js"></script>
    <script type="text/javascript" src="[{$oViewConf->getModuleUrl("rs-dbschema", "out/src/js/js.js")}]"></script>
</head>
<body>

    <div id="popup_wait"></div>



    <div id="navi">
        <div class="clear">
            <div style="float:left; ">
                <button type="button" class="button new" id="button_new">New</button>
                <button type="button" class="button load" id="button_load_popup">Load</button>
                <button type="button" class="button save" id="button_save">Save</button>
            </div>
            <div style="float:right; ">
                <span style="font-weight:bold; " id="projectname"></span>
            </div>
        </div>
    </div>
    <div id="container">
        <div class="border"></div>
        <div class="helper"></div>
        [{*include file="inc/rs_dbschema_table.tpl" left=10 top=10 table="cpprovider_order"*}]
        [{*include file="inc/rs_dbschema_line.tpl"
            from_table="cpprovider_order"
            from_column="cpid"
            from_text="1"
            to_table="cpprovider_order_article"
            to_column="f_cpprovider_order"
            to_text="n"
        *}]
    </div>
    <div id="tables">
        [{include file="inc/rs_dbschema_tables.tpl"}]
    </div>

    <form id="form_tables_relations" action="[{ $oViewConf->getSelfLink() }]" method="post">
        [{ $oViewConf->getHiddenSid() }]
        <input type="hidden" name="cl" value="rs_dbschema_ide">
        <input type="hidden" name="fnc" value="getrelations">
    </form>
    <form id="form_tables_save" action="[{ $oViewConf->getSelfLink() }]" method="post">
        [{ $oViewConf->getHiddenSid() }]
        <input type="hidden" name="cl" value="rs_dbschema_ide">
        <input type="hidden" name="fnc" value="save">
    </form>
    <form id="form_tables_load_project_list" action="[{ $oViewConf->getSelfLink() }]" method="post">
        [{ $oViewConf->getHiddenSid() }]
        <input type="hidden" name="cl" value="rs_dbschema_ide">
        <input type="hidden" name="fnc" value="getprojectfiles">
    </form>
    <form id="form_tables_load" action="[{ $oViewConf->getSelfLink() }]" method="post">
        [{ $oViewConf->getHiddenSid() }]
        <input type="hidden" name="cl" value="rs_dbschema_ide">
        <input type="hidden" name="fnc" value="load">
    </form>

    <div id="popup_load">
        <h4>Load project</h4>
        <table>
            <tr>
                <td>Project</td>
                <td>
                    <select id="popup_load_project"></select>
                </td>
            </tr>
            <tr>
                <td><button type="button" class="load" id="button_load">Load</button></td>
                <td><button type="button" class="cancel" id="button_load_cancel">Cancel</button></td>
            </tr>
        </table>
    </div>

</body>
</html>