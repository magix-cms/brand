{if !isset($vertical)}{$vertical = false}{/if}
{if is_array($brands) && $brands != null}
    <div class="brands-{if $vertical}v{else}h{/if}">
        {foreach $brands as $brand}
            <div class="brand">
                {strip}<picture>
                    <!--[if IE 9]><video style="display: none;"><![endif]-->
                    <source type="image/webp" sizes="{$brand.img['medium']['w']}px" srcset="{$brand.img['small']['src_webp']} {$brand.img['medium']['w']}w">
                    <source type="image/png" sizes="{$brand.img['medium']['w']}px" srcset="{$brand.img['small']['src']} {$brand.img['medium']['w']}w">
                    <!--[if IE 9]></video><![endif]-->
                    <img data-src="{$brand.img['medium']['src']}" alt="{$brand.name_bd}" width="{$brand.img['medium']['w']}" height="{$brand.img['medium']['h']}" class="img-responsive lazyload" />
                    </picture>{/strip}
                {if isset($brand.url) && !empty($brand.url)}
                    <a href="{$brand.url}" title="{$key.title_bd}" class="all-hover">{$brand.name_bd}</a>
                {/if}
            </div>
        {/foreach}
    </div>
{/if}