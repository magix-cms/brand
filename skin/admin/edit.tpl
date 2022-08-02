{extends file="layout.tpl"}
{block name='head:title'}{#edit_brand#|ucfirst}{/block}
{block name='body:id'}brand{/block}
{block name="stylesheets" append}
    {capture name="cssDatePicker"}{strip}
        /{baseadmin}/min/?f=
        {baseadmin}/template/css/bootstrap-datetimepicker.min.css
    {/strip}{/capture}
    {headlink rel="stylesheet" href=$smarty.capture.cssDatePicker media="screen"}
{/block}
{block name='article:header'}
    <h1 class="h2"><a href="{$smarty.server.SCRIPT_NAME}?controller={$smarty.get.controller}" title="Afficher la liste des ligues">{#brand#|ucfirst}</a></h1>
{/block}
{block name='article:content'}
    {if {employee_access type="edit" class_name=$cClass} eq 1}
    <div class="panels row">
        <section class="panel col-ph-12 col-md-12">
            {if $debug}
                {$debug}
            {/if}
            <header class="panel-header panel-nav">
                <h2 class="panel-heading h5">{#edit_brand#|ucfirst}</h2>
                <ul class="nav nav-tabs" role="tablist">
                    <li role="presentation" class="active"><a href="#general" aria-controls="general" role="tab" data-toggle="tab">{#text#}</a></li>
                    <li role="presentation"><a href="#image" aria-controls="image" role="tab" data-toggle="tab">{#image#}</a></li>
                    {*<li role="presentation"><a href="#child" aria-controls="child" role="tab" data-toggle="tab">{#child_page#}</a></li>*}
                </ul>
            </header>
            <div class="panel-body panel-body-form">
                <div class="mc-message-container clearfix">
                    <div class="mc-message"></div>
                </div>
                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane {if !$smarty.get.plugin}active{/if}" id="general">
                        {include file="form/edit.tpl" controller="brand"}
                    </div>
                    <div role="tabpanel" class="tab-pane" id="image">
                        {include file="form/img.tpl" controller="brand"}
                        {*<pre>{$page|print_r}</pre>*}
                    </div>
                    {*<div role="tabpanel" class="tab-pane tab-table" id="child">
                        <p class="text-right">
                            {#nbr_pages#|ucfirst}: {$pagesChild|count} <a href="{$smarty.server.SCRIPT_NAME}?controller={$smarty.get.controller}&amp;action=add&parent_id={$smarty.get.edit}" title="{#add_pages#}" class="btn btn-link">
                                <span class="fa fa-plus"></span> {#add_pages#|ucfirst}
                            </a>
                        </p>
                        {if $smarty.get.search}{$sortable = false}{else}{$sortable = true}{/if}
                        {include file="section/form/table-form-3.tpl" ajax_form=true idcolumn='id_bd' data=$pagesChild activation=true sortable=$sortable controller="brand"}
                    </div>*}
                </div>
                {*<pre>{$page|print_r}</pre>*}
            </div>
        </section>
    </div>
    {include file="modal/delete.tpl" data_type='brand' title={#modal_delete_title#|ucfirst} info_text=true delete_message={#delete_pages_message#}}
    {include file="modal/error.tpl"}
    {/if}
{/block}
{block name="foot" append}
    {include file="section/footer/editor.tpl"}
    {capture name="scriptForm"}{strip}
        /{baseadmin}/min/?f=
        libjs/vendor/jquery-ui-1.12.min.js,
        libjs/vendor/tabcomplete.min.js,
        libjs/vendor/livefilter.min.js,
        libjs/vendor/src/bootstrap-select.js,
        libjs/vendor/filterlist.min.js,
        {baseadmin}/template/js/table-form.min.js,
        {baseadmin}/template/js/img-drop.min.js,
        libjs/vendor/moment.min.js,
        libjs/vendor/datetimepicker/{iso}.js,
        libjs/vendor/bootstrap-datetimepicker.min.js,
        plugins/brand/js/admin.min.js
    {/strip}{/capture}
    {script src=$smarty.capture.scriptForm type="javascript"}
    <script type="text/javascript">
        $(function(){
            var controller = "{$smarty.server.SCRIPT_NAME}?controller={$smarty.get.controller}";
            if (typeof tableForm == "undefined")
            {
                console.log("tableForm is not defined");
            }else{
                tableForm.run(controller);
            }
            if (typeof brand === "undefined")
            {
                console.log("brand is not defined");
            }else{
                //pages.run(controller);
                brand.runAdd(iso);
            }
        });
    </script>
{/block}