{if $product.sameBrand}
    {brand_conf}
    <p class="h2">{#same_brand#|ucfirst}</p>
    <div class="vignette-list">
        <div class="row" itemprop="mainEntity" itemscope itemtype="http://schema.org/ItemList">
            {include file="catalog/loop/product.tpl" data=$product.sameBrand classCol='vignette col-12 col-xs-6 col-md-4'}
        </div>
    </div>
{/if}