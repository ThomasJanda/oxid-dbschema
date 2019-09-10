[{assign var=list value=$oView->getTables()}]
<div style="padding:5px; ">
    <input placeholder="search table" style="width:calc(100% - 10px); " id="tablelistsearch" type="text" name="phrase" value="">
</div>
<div id="tablelist">
    [{include file="inc/rs_dbschema_tablelist.tpl"}]
</div>
<form id="form_tables_addtable" action="[{ $oViewConf->getSelfLink() }]" method="post">
    [{ $oViewConf->getHiddenSid() }]
    <input type="hidden" name="cl" value="rs_dbschema_ide">
    <input type="hidden" name="fnc" value="addtable">
</form>
<form id="form_tables_refresh" action="[{ $oViewConf->getSelfLink() }]" method="post">
    [{ $oViewConf->getHiddenSid() }]
    <input type="hidden" name="cl" value="rs_dbschema_ide">
    <input type="hidden" name="fnc" value="refreshtablelist">
</form>
[{capture name="cpscript"}]
    [{capture}]<script>[{/capture}]

    $('#tablelistsearch').keyup(function(e){
        if(e.keyCode == 13)
        {
            displayWait();

            //refresh list
            var params = $('#form_tables_refresh').serialize();
            params += "&phrase="+$('#tablelistsearch').val();
            var url='[{ $oViewConf->getSelfLink() }]';
            $.ajax({
                method: "POST",
                url: url,
                data: params,
                async: false
            })
            .done(function( html ) {
                $('#tablelist').html(html);

                $('#container .table').each(function() {
                    var id=$(this).attr('id');
                    $('#tables .navitable input[value=' + id + ']').prop( "checked", true );
                });

                initTableList();

                hideWait();
            });
        }
    });

    function initTableList() {
        $('.navitable input').click(function () {

            displayWait();

            var v = $(this).attr('value');
            if ($(this).prop('checked')) {
                //remove all svgs
                $('#container svg').each(function () {
                    $(this).remove();
                });

                //add table
                var params = $('#form_tables_addtable').serialize();
                params += "&table=" + v;
                params += "&left=" + ($('#container').scrollLeft() + 10);
                params += "&top=" + ($('#container').scrollTop() + 10);
                var url = '[{ $oViewConf->getSelfLink() }]';
                $.ajax({
                    method: "POST",
                    url: url,
                    data: params,
                    async: false,
                })
                    .done(function (html) {
                        $('#container').append(html);
                    });

                initTables();
            }
            else {
                //remove table
                $('#container #' + v).remove();

                //remove all svgs
                $('#container svg').each(function () {
                    $(this).remove();
                });
                initTables();
            }

            hideWait();
        });
    }
    initTableList();

    [{capture}]</script>[{/capture}]
[{/capture}]
<script>[{$smarty.capture.cpscript}]</script>