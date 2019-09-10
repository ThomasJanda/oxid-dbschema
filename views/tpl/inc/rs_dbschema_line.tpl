[{*
from_table
from_column
to_table
to_column
textfrom
textto
*}]
[{assign var=id value=$from_table|cat:"__"|cat:$from_column|cat:"|"|cat:$to_table|cat:"__"|cat:$to_column}]
[{assign var=id value=$id|md5}]
<svg id="[{$id}]"
     class="polyline"
     data-from_table="[{$from_table}]"
     data-from_column="[{$from_column}]"
     data-to_table="[{$to_table}]"
     data-to_column="[{$to_column}]">
    <path class="line" />
</svg>
<svg class="text" id="[{$id}]_fromtext" data-from_table="[{$from_table}]" data-to_table="[{$to_table}]">
    <text x="10" y="14" text-anchor="middle">[{$from_text}]</text>
</svg>
<svg class="text" id="[{$id}]_totext" data-from_table="[{$from_table}]" data-to_table="[{$to_table}]">
<text x="10" y="14" text-anchor="middle">[{$to_text}]</text>
</svg>