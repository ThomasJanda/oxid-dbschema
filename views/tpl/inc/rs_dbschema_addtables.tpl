[{foreach from=$tables item=data}]
    [{include file="inc/rs_dbschema_table.tpl" table=$data.title left=$data.left top=$data.top width=$data.width height=$data.height}]
[{/foreach}]