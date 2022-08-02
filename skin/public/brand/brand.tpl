{extends file="layout.tpl"}
{block name="title"}{$brand.seo.title}{/block}
{block name="description"}{$brand.seo.description}{/block}
{block name='body:id'}brand{/block}
{block name="styleSheet"}
    {$css_files = ["lightbox","slider"]}
{/block}

{block name='article'}
    <article class="catalog" itemprop="mainContentOfPage">
        {block name='article:content'}
            <div class="container">
                <h1 itemprop="name">{$brand.name}</h1>
                <div class="text clearfix" itemprop="text">
                    {if isset($brand.img.name)}
                        <a href="{$brand.img.large.src}" class="img-zoom img-float float-right" title="{$brand.img.title}" data-caption="{$brand.img.caption}">
                            <figure>
                                {include file="img/img.tpl" img=$brand.img lazy=true}
                                {if $brand.img.caption}
                                    <figcaption>{$brand.img.caption}</figcaption>
                                {/if}
                            </figure>
                        </a>
                    {/if}
                    {$brand.content}
                </div>
            </div>
            {if !empty($products)}
                <div class="cat-products">
                    <div class="container">
                        <p class="h2">{#products#|ucfirst}</p>
                        <div class="vignette-list">
                            <div class="row row-center">
                                {include file="catalog/loop/product.tpl" data=$products classCol='vignette col-4 col-xs-3 col-sm-4 col-md-th col-lg-4 col-xl-3'}
                            </div>
                        </div>
                    </div>
                </div>
            {/if}
        {/block}
    </article>
{/block}