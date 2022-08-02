{extends file="layout.tpl"}
{block name="title"}{$root.seo.title}{/block}
{block name="description"}{$root.seo.description}{/block}
{block name='body:id'}brand{/block}

{block name='article'}
    <article class="container" itemprop="mainContentOfPage">
        {block name='article:content'}
            <h1 itemprop="name">{$root.name}</h1>
            {$root.content}
            {if !empty($brands)}
            <div class="vignette-list">
                <div class="row row-center" itemprop="mainEntity" itemscope itemtype="http://schema.org/ItemList">
                    {include file="brand/loop/brand.tpl" data=$brands small='true' classCol='brand col-full col-sm-4 col-md-5 col-lg-4 col-xl-3'}
                </div>
            </div>
            {/if}
        {/block}
    </article>
{/block}