[{*
table
*}]
[{assign var=left value=$left|default:10}]
[{assign var=top value=$top|default:10}]
[{assign var=width value=$width|default:"-1"}]
[{assign var=height value=$height|default:"-1"}]

[{assign var=calcHeight value=0}]
[{if $height=="-1"}]
    [{assign var=calcHeight value=1}]
    [{assign var=height value=35}]
[{/if}]
[{capture name="columns"}]
    [{assign var=columns value=$oView->getColumns($table)}]
    [{foreach from=$columns item=data}]
        <div class="column" style="[{if $data.primary}]text-decoration:underline;[{/if}][{if $data.foreign!==false}]font-style:italic; [{/if}]"  id="[{$table}]__[{$data.title}]" >
            [{$data.title}]
            <span [{if $data.type2!=""}] class="tooltip" title="[{$data.type2}]"[{/if}]>
                ([{$data.type}])
            </span>
            [{if $data.comment!=""}]<span class="tooltip right" title="[{$data.comment}]">?</span>[{/if}]
        </div>

        [{if $calcHeight==1}]
            [{math equation="x + 21" x=$height assign=height}]
        [{/if}]
    [{/foreach}]
[{/capture}]
[{if $calcHeight==1}]
    [{if $height>400}]
        [{assign var=height value=400}]
    [{/if}]
[{/if}]

<div id="[{$table}]" class="table" style="left:[{$left}]px; top:[{$top}]px; [{if $width!="-1"}]width:[{$width}]px; [{/if}] [{if $height!="-1"}]height:[{$height}]px; [{/if}]">
    <div class="header">
        [{$table}]
        [{assign var=comment value=$oView->getTableComment($table)}]
        [{if $comment!=""}]<span class="tooltip right" title="[{$comment}]">?</span>[{/if}]
    </div>
    <div class="content">
        [{$smarty.capture.columns}]
    </div>
    <div class="resizer"></div>
</div>