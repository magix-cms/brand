{extends file="catalog/{$smarty.get.controller}/edit.tpl"}
{block name="plugin:content"}
    <div class="row">
    <form id="edit_cat_club" action="{$smarty.server.SCRIPT_NAME}?controller={$smarty.get.controller}&amp;action=edit&edit={$page.id_product}&plugin={$smarty.get.plugin}" method="post" class="validate_form col-ph-12 col-md-6 col-lg-4">
        <div class="form-group">
            <label for="brand">{#brand#}</label>
            <select name="brand[id]" id="brand" class="form-control">
                <option value="">Select a brand</option>
                {foreach $brands as $bd}
                    <option value="{$bd.id_bd}"{if isset($brand) && $brand.id_bd === $bd.id_bd} selected{/if}>{$bd.name_bd}</option>
                {/foreach}
            </select>
        </div>
        <button class="btn btn-main-theme pull-right" type="submit" name="action" value="edit">{#save#|ucfirst}</button>
    </form>
    </div>
{/block}