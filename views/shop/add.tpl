{strip}
<script src="/systems/jquery/upload/jquery.fileupload.js"></script>
<script type="text/javascript" src="/systems/jquery/crop/jquery.imgareaselect.js"></script>
{literal}
<script>
</script>
{/literal}
<div class="container">
    include file="templates/html/_shop_menu.html"
    <div id="cms-goods">
        <div statisticBlock>
            <div>
                <div style="width:38.5%;display:inline-block;padding-right:2px;">
                    {foreach from=$statistic.title item=item}
                    <div class="group" style="text-align:right;margin-top: 1px;">{$item}</div>
                    {/foreach }
                </div>
                <div style="width:61.3%;display:inline-block;text-align:left;">
                    {foreach from=$statistic.value key=key item=item}
                    <div class="visibleShopParams group" data-param="{$key}" style="margin-top: 1px;">{$item}</div>
                    {/foreach }
                </div>
            </div>
        </div>
        {if $listGoods}
        <div class="shop_goods_lists">
            <div class="button_href">
                <a href="/shop/goods/add">Добавить товар</a>
            </div>
            <div class="shop_goods_filter_wrapper">
                <form method="POST" action="{$page.url}">
                    <input type="text" name="filter[id]" placeholder="Введите ID товара" value="{$filter.id}" class="shop_good_search">
                    <input type="text" name="filter[name]" placeholder="Введите навзвание товара" value="{$filter.name}" class="shop_good_search">
                    <select name="filter[status]">
                        <option value="">Все товары</option>
                        <option value="1" {if $filter.status == 1}selected{/if}>Отображаемые на сайте</option>
                        <option value="2" {if $filter.status == 2}selected{/if}>Не отображаемые на сайте</option>
                    </select>

                    <span class="filter_ok">Применить</span>

                </form>
            </div>
            <table>
                <tr>
                    <th>ID</th>
                    <th>Наименование</th>
                    <th>Цена на сайте</th>
                    <th>Дата добавления</th>
                    <th>Отображать на сайте</th>
                    <th>Статус</th>
                </tr>
                {foreach from=$listGoods item=item}
                <tr>
                    <td><a href="/shop/goods/{$item.id}">{$item.id}</a></td>
                    <td><a href="/shop/goods/{$item.id}"><b>{$item.producer_name}</b> {$item.name}</a></td>
                    <td><a href="/shop/goods/{$item.id}">{$item.price_out} р.</a></td>
                    <td><a href="/shop/goods/{$item.id}">{$item.date_create|datetime}</a></td>
                    <td><input type="checkbox" class="action_shop_checkbox" data-id = "{$item.id}" {if $item.status == 1}checked{/if} {if $item.confirm == -1}disabled{/if}></td>
                    <td>{$statusList[$item.confirm]}</td>
                </tr>

                {/foreach}
            </table>
            {if count($pages.pages) > 1}
            <div class="pages_wrapper">
                <b>Страницы:</b>
                {foreach from=$pages.pages item=item}
                {if $pages.current == $item}<span class="page active">{$item}</span>{else}<a class="page" href="{$page.url}{if $pageUrl}{$pageUrl}/{/if}{if $item > 1}?page={$item}{else}{/if}">{$item}</a>{/if}
                {/foreach}
            </div>
            {/if}
        </div>
        {else}
        <div class="item">
            <form action="" method="post">
                <div class="group">Основная информация</div>
                <table cellpadding="0" cellspacing="0" border="0" class="good-data">
                    <tr>
                        <td class="name">
                            <div>ID</div>
                        </td>
                        <td>{$good.id}</td>
                    </tr>
                    <tr class="hide">
                        <td class="name">
                            <div>Магазин<span class="i">*</span></div>
                        </td>
                        <td>
                            <input type="hidden" name="shop_id" value="{$good.shop_id}">
                            <input id="comission_id" type="hidden" value="{$good.comission_id}" />
                        </td>
                    </tr>
                    <tr class="grey">
                        <td class="name">
                            <div>Тип<span class="i">*</span></div>
                        </td>
                        <td>
                            <select name="type_id" class="list{if $error.type_id} error{/if}">
                                <option value="">--</option>
                                {foreach from=$goods_types item=item key=key}
                                <option value="{$key}"{if $smarty.post}{if $key == $smarty.post.type_id} selected{/if}{else}{if $key == $good.variations[0].tags[1009][0].id} selected{/if}{/if}>{$item}</option>
                                {/foreach}
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td class="name">
                            <div>Производитель</div>
                        </td>
                        <td>
                            <select name="producer_id" >
                                <option value="">--</option>
                                {foreach from=$producers item=item key=key}
                                <option value="{$key}" {if $smarty.post}{if $key == $smarty.post.producer_id} selected{/if}{else}{if $key == $good.variations[0].tags[1008][0].id} selected{/if}{/if}>{$item}</option>
                                {/foreach}
                            </select>
                        </td>
                    </tr>
                    <tr class="grey">
                        <td class="name">
                            <div>Артикул</div>
                        </td>
                        <td><input type="text" name="code" value="{if $smarty.post}{$smarty.post.code}{else}{$good.code}{/if}" maxlength="128" class="string" /></td>
                    </tr>
                    <tr>
                        <td class="name">
                            <div>Название<span class="i">*</span></div>
                        </td>
                        <td><input type="text" name="name" value="{if $smarty.post}{$smarty.post.name}{else}{$good.name}{/if}" maxlength="128" class="string max{if $error.name} error{/if}" /></td>
                    </tr>
                    <tr class="grey">
                        <td class="name">
                            <div>Описание</div>
                        </td>
                        <td>
                            <textarea name="description" class="text">{if $smarty.post}{$smarty.post.description}{else}{$good.description}{/if}</textarea>
                            <script>wysiwyg('description', '320')</script>
                        </td>
                    </tr>
                    <tr class="hide">
                        <td class="name">
                            <div>Кол-во в упаковке<span class="i">*</span></div>
                        </td>
                        <td>
                            <input id="count_pack" type="hidden" name="count_pack" value="{if $smarty.post}{$smarty.post.count_pack}{else}{$good.count_pack|default:1}{/if}" maxlength="4" class="string min{if $error.count_pack} error{/if}" />
                            <span class="value">шт.</span>
                        </td>
                    </tr>
                    <tr class="grey hide">
                        <td class="name">
                            <div>Кол-во минимальное<span class="i">*</span></div>
                        </td>
                        <td>
                            <input type="hidden" name="count_min" value="{if $smarty.post}{$smarty.post.count_min}{else}{$good.count_min|default:1}{/if}" maxlength="4" class="string min{if $error.count_min} error{/if}" />
                            <span class="value">шт.</span>
                        </td>
                    </tr>
                    <tr class="hide">
                        <td class="name">
                            <div>Ссылка</div>
                        </td>
                        <td><input type="hidden" name="link" value="{if $smarty.post}{$smarty.post.link}{else}{$good.link}{/if}" maxlength="128" class="string max" /></td>
                    </tr>
                    <tr class="grey hide">
                        <td class="name">
                            <div>Вес<span class="i">*</span></div>
                        </td>
                        <td>
                            <input type="hidden" name="weight_id" value="1000000005" />
                            <!--
                            <select name="weight_id" class="list min">
                                <option value="">--</option>
                                {foreach from=$goods_weights item=item key=key}
                                <option value="{$key}"{if $smarty.post}{if $key == $smarty.post.weight_id} selected{/if}{else}{if $key == $good.variations[0].tags[1010][0].id} selected{/if}{/if}>{$item}</option>
                                {/foreach}
                            </select>
                            -->
                        </td>
                    </tr>
                    <tr>
                        <td class="name">
                            <div>Страна</div>
                        </td>
                        <td>
                            <select name="country_id" class="list min">
                                <option value="">--</option>
                                {foreach from=$countries item=item key=key}
                                <option value="{$key}"{if $smarty.post}{if $key == $smarty.post.country_id} selected{/if}{else}{if $key == $good.variations[0].tags[1007][0].id} selected{/if}{/if}>{$item}</option>
                                {/foreach}
                            </select>
                        </td>
                    </tr>
                    <tr class="grey hide">
                        <td class="name">
                            <div>SEO Title</div>
                        </td>
                        <td><textarea name="seo_title" class="text min">{if $smarty.post}{$smarty.post.seo_title}{else}{$good.seo_title}{/if}</textarea></td>
                    </tr>
                    <tr class="hide">
                        <td class="name">
                            <div>SEO Description</div>
                        </td>
                        <td><textarea name="seo_description" class="text min">{if $smarty.post}{$smarty.post.seo_description}{else}{$good.seo_description}{/if}</textarea></td>
                    </tr>
                    <tr class="grey hide">
                        <td class="name">
                            <div>SEO Keywords</div>
                        </td>
                        <td><textarea name="seo_keywords" class="text min">{if $smarty.post}{$smarty.post.seo_keywords}{else}{$good.seo_keywords}{/if}</textarea></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>
                            <table cellpadding="0" cellspacing="0" border="0" style="width: 100%;">
                                <tr>
                                    <td style="width: 25%;" class="hide">
                                        <input id="field-new" type="checkbox" name="new" value="1"{if $smarty.post}{if $smarty.post.new} checked{/if}{else}{if $good.new} checked{/if}{/if} class="check" />
                                        <label for="field-new">Новинка</label>
                                    </td>
                                    <td style="width: 25%;" class="hide">
                                        <input id="field-sale" type="checkbox" name="sale" value="1"{if $smarty.post}{if $smarty.post.sale} checked{/if}{else}{if $good.sale} checked{/if}{/if} class="check" />
                                        <label for="field-sale">Распродажа</label>
                                    </td>
                                    <td style="width: 25%;" class="hide">
                                        <input id="field-bonus" type="checkbox" name="bonus" value="1"{if $smarty.post}{if $smarty.post.bonus} checked{/if}{else}{if $good.bonus} checked{/if}{/if} class="check" />
                                        <label for="field-bonus">Бонус</label>
                                    </td>
                                    <td style="width: 25%;" class="hide">
                                        <input id="field-discount" type="checkbox" name="discount" value="1"{if $smarty.post}{if $smarty.post.discount} checked{/if}{else}{if $good.discount} checked{/if}{/if} class="check" />
                                        <label for="field-discount">Промо-код</label>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr class="grey">
                        <td></td>
                        <td>
                            <input id="field-status" type="checkbox" value="1" name="status"{if $smarty.post}{if $smarty.post.status} checked{/if}{else}{if $good.status} checked{/if}{/if} class="check" />
                            <label for="field-status">Отображать на сайте</label>
                        </td>
                    </tr>

                    <tr class="hide">
                        <td></td>
                        <td>
                            <input id="field-confirm" type="checkbox" value="1" {if $good.confirm == 1} checked {/if} name="confirm" class="check" />
                            <label for="field-confirm">Промодерирован</label>
                        </td>
                    </tr>


                </table>
                {if $good.variations}
                <div class="group">Варианты</div>
                <table cellpadding="0" cellspacing="0" border="0" class="good-variations">
                    {foreach from=$good.variations item=variation key=key}
                    <tr>
                        <td class="i">{$key+1}.</td>
                        <td>
                            <span class="name{if !$variation.status} grey{/if}" onclick="variation('{$variation.id}');">{$variation.id}{if $variation.tags_name} - {$variation.tags_name}{/if}</span>
                        </td>
                        <td id="description-{$variation.id}" class="description">
                            {if $variation.description}Описание{/if}
                        </td>
                        <td id="images-{$variation.id}" class="images">
                            {if $variation.images}Фото ({$variation.images|@count}){/if}
                        </td>
                        <td id="price-{$variation.id}" class="price">
                            <div>{if $variation.price_out}{$variation.price_out|money}{/if}</div>
                        </td>
                    </tr>
                    <tr id="variation-{$variation.id}" variation="{$variation.id}" class="variation">
                        <td colspan="5">
                            <div class="variation">
                                <div class="i grey">
                                    <div class="name">Название поставщика</div>
                                    <div class="value"><input type="text" name="variations[{$variation.id}][full_name]" value="{if $smarty.post}{$smarty.post.variations[$variation.id].full_name}{else}{$variation.full_name}{/if}" maxlength="128" class="string max" /></div>
                                </div>
                                <div class="i">
                                    <div class="name">Артикул</div>
                                    <div class="value"><input type="text" name="variations[{$variation.id}][code]" value="{if $smarty.post}{$smarty.post.variations[$variation.id].code}{else}{$variation.code}{/if}" maxlength="64" class="string max" /></div>
                                </div>
                                <div class="i grey">
                                    <div class="name">Цена входная</div>
                                    <div class="value">
                                        <input type="text" name="variations[{$variation.id}][price_in]" value="{if $smarty.post}{$smarty.post.variations[$variation.id].price_in}{else}{$variation.price_in}{/if}" maxlength="8" class="price_in money{if $error.price_in[$variation.id]} error{/if}" />
                                        <span class="info"> руб.</span>
                                    </div>
                                </div>
                                <div class="i hide">
                                    <div class="name">Комиссия</div>
                                    <div class="value">
                                        <input type="text" name="variations[{$variation.id}][comission]" value="{if $smarty.post}{$smarty.post.variations[$variation.id].comission}{else}{$variation.comission|default:"20"}{/if}" maxlength="8" class="comission money{if $error.comission[$variation.id]} error{/if}" />
                                        <span class="info">%</span>
                                    </div>
                                </div>
                                <div class="i grey">
                                    <div class="name">Цена выходная</div>
                                    <div class="value">
                                        <input type="text" name="variations[{$variation.id}][price_out]" value="{if $smarty.post}{$smarty.post.variations[$variation.id].price_out}{else}{$variation.price_out}{/if}" maxlength="8" class="price_out money{if $error.price_out[$variation.id]} error{/if}" />
                                        <span class="info"> руб.</span>
                                    </div>
                                </div>
                                {if $variation.unknow_tag}
                                {foreach from=$variation.unknow_tag item=tag}
                                <div class="i grey">
                                    <div class="name">Неопознаный тег</div>
                                    <div class="value">
                                        <span class="unknowtag" >{$tag.value}  </span>
                                        {if $good.tag_group_list}
                                        <select class="choise_group_tag">
                                            <option value="">--</option>
                                            {foreach from=$good.tag_group_list item=groups}
                                            <option value="{$groups.id}">{$groups.name}</option>
                                            {/foreach}
                                        </select>
                                        {/if}
                                        <div class="button_action" data-action="delete" data-id="{$tag.id}">Удалить</div>
                                        <div class="button_action" data-action="add" data-id="{$tag.id}">Добавить</div>
                                    </div>
                                </div>
                                {/foreach}
                                <div class="hidden">
                                    {foreach from=$variation.unknow_tag item=tag}
                                    <input type="hidden" class="unknow_tag_id_{$tag.id}" name="variations[{$variation.id}][tags][{$tag.id}]" value="Черный">
                                    {/foreach}
                                </div>
                                {/if}
                                <div class="i grey">
                                    <div class="name">Остаток</div>
                                    <div class="value">
                                        <input type="text" name="variations[{$variation.id}][count]" value="{if $smarty.post}{$smarty.post.variations[$variation.id].count}{else}{$variation.count}{/if}" maxlength="8" class="" />
                                        <span class="info"> шт.</span>
                                    </div>
                                </div>
                                {foreach from=$goods_options item=option key=key}
                                <div class="i options{if $key % 2 == 1} grey{/if}">
                                    <div class="name">{$option.name}</div>
                                    <div class="value value-{$option.id}">
                                        <input type="text"  name="" value="" maxlength="64" group="{$option.id}" class="string" />
                                        <div class="load"></div>
                                        <div class="values"></div>
                                        {foreach from=$variation.tags[$option.id] item=tag}
                                        <span class="tag"><input type="hidden" name="variations[{$variation.id}][tags][{$tag.id}]" value="{$tag.value}" /> {$tag.value} <a href="/" title="Удалить тег" onclick="$(this).parent().remove(); return false;">X</a></span>
                                        {/foreach}
                                    </div>
                                </div>
                                {/foreach}
                                <div class="variations-status">
                                    <input id="variations-status-{$variation.id}" type="checkbox" name="variations[{$variation.id}][status]" value="1"{if $variation.status} checked{/if} class="check" />
                                    <label for="variations-status-{$variation.id}">Активность</label>
                                </div>
                            </div>
                            <div class="variation-description">
                                <div class="caption"><span onclick="variation_description('{$variation.id}');">Описание</span> – {if $variation.description}Есть{else}Нет{/if}</div>
                                <div class="text">
                                    <textarea name="variations[{$variation.id}][description]" class="text">{$variation.description}</textarea>
                                    <script>wysiwyg('variations[{$variation.id}][description]', '320')</script>
                                </div>
                            </div>
                            <div class="variation-images">
                                <div class="caption"><span onclick="variation_images('{$variation.id}');">Фото</span></div>
                                <div class="images">
                                    {foreach from=$variation.images item=image}
                                    <div id="image-{$image}" class="i">
                                        <img src="/files/goods/{$image|image_dir}/{$image}_min.jpg" alt="" />
                                        <div title="Удалить фотографию" class="image-delete" onclick="image_delete('{$image}');"></div>
                                    </div>
                                    {/foreach}
                                    {include file="systems/jquery/upload/upload.html" variation_id=$variation.id}
                                </div>
                            </div>
                        </td>
                    </tr>
                    {/foreach}
                </table>
                {/if}
                <div class="group">Добавить вариант</div>
                <table cellpadding="0" cellspacing="0" border="0" class="good-variations{if !$good} open{/if}">
                    <tr id="variation-add-0" class="variation add">
                        <td colspan="3">
                            <div class="variation">
                                <div class="i grey">
                                    <div class="name">Название поставщика</div>
                                    <div class="value"><input type="text" name="variations_add[0][full_name]" value="{$smarty.post.variations_add[0].full_name}" maxlength="128" class="string max" /></div>
                                </div>
                                <div class="i">
                                    <div class="name">Артикул</div>
                                    <div class="value"><input type="text" name="variations_add[0][code]" value="{$smarty.post.variations_add[0].name}" maxlength="64" class="string max" /></div>
                                </div>
                                <div class="i grey">
                                    <div class="name">Цена входная</div>
                                    <div class="value">
                                        <input type="text" name="variations_add[0][price_in]" value="{if $smarty.post}{$smarty.post.variations_add[0].price_in}{else}{$good.variations[0].price_in}{/if}" maxlength="8" class="price_in money{if $error.price_in[0]} error{/if}" />
                                        <span class="info"> руб.</span>
                                    </div>
                                </div>
                                <div class="i hide">
                                    <div class="name">Комиссия</div>
                                    <div class="value">
                                        <input type="text" name="variations_add[0][comission]" value="{if $smarty.post}{$smarty.post.variations_add[0].comission|default:"20"}{else}{$good.percent|default:"20"}{/if}" maxlength="8" class="comission money{if $error.comission[0]} error{/if}" />
                                        <span class="info">%</span>
                                    </div>
                                </div>
                                <div class="i grey">
                                    <div class="name">Цена выходная</div>
                                    <div class="value">
                                        <input type="text" name="variations_add[0][price_out]" value="{if $smarty.post}{$smarty.post.variations_add[0].price_in}{else}{$good.variations[0].price_out}{/if}" maxlength="8" class="price_out money{if $error.price_out[0]} error{/if}" />
                                        <span class="info"> руб.</span>
                                    </div>
                                </div>
                                <div class="i grey">
                                    <div class="name">Остаток</div>
                                    <div class="value">
                                        <input type="text" name="variations_add[0][count]" value="{if $smarty.post}{$smarty.post.variations_add[0].count}{else}{$good.variations[0].count}{/if}" maxlength="8" class="" />
                                        <span class="info"> шт.</span>
                                    </div>
                                </div>
                                {foreach from=$goods_options item=option key=key}
                                <div class="i options{if $key % 2 == 1} grey{/if}">
                                    <div class="name">{$option.name}</div>
                                    <div class="value value-{$option.id}">
                                        <input type="text" name="" value="" maxlength="64" group="{$option.id}" class="string" />
                                        <div class="load"></div>
                                        <div class="values"></div>
                                    </div>
                                </div>
                                {/foreach}
                                <div class="variations-status">
                                    {if $good}
                                    <input id="variations-status-0" type="checkbox" name="variations_add[0][status]"} value="1"{if $smarty.post.variations_add[0].status} checked{/if} class="check" />
                                    {else}
                                    <input type="hidden" name="variations_add[0][status]" value="1" />
                                    <input type="checkbox" checked disabled class="check" />
                                    {/if}
                                    <label for="variations-status-0">Активность</label>
                                </div>
                            </div>
                            <div class="variation-description">
                                <div class="caption"><span onclick="variation_description('add-0');">Описание</span></div>
                                <div class="text">
                                    <textarea name="variations_add[0][description]" class="text"></textarea>
                                    <script>wysiwyg('variations_add[0][description]', '320')</script>
                                </div>
                            </div>
                            <div class="variation-images">
                                <div class="caption"><span onclick="variation_images('add-0');">Фото</span></div>
                                <div class="images">
                                    {include file="systems/jquery/upload/upload.html" variation_id="0"}
                                </div>
                            </div>
                        </td>
                    </tr>
                </table>
                <div class="buttons">
                    <span class="addVariant buttonLong" data-good="{$good.id}" data-variant="1">Добавить вариант</span>
                </div>
                <div class="buttons">
                    <input type="hidden" name="id" value="{$good.id}" />
                    <input type="submit" name="save" value="Сохранить" class="button" />
                </div>
            </form>
        </div>
        {/if}
        <div class="clear"></div>
    </div>
</div>
{/strip}
