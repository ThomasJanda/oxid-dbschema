[{foreach from=$relations item=data}]
    [{include file="inc/rs_dbschema_line.tpl"
    from_table=$data.from_table
    from_column=$data.from_column
    from_text=$data.from_text
    to_table=$data.to_table
    to_column=$data.to_column
    to_text=$data.to_text
    }]
[{/foreach}]
