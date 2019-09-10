[{assign var=tables value=$oView->getTables()}]
[{foreach from=$tables item=table}]
    <div class="navitable">
        <input type="checkbox" name="table" value="[{$table}]"> [{$table}]
    </div>
[{/foreach}]