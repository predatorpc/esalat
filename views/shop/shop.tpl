{strip}
<div class="container">
    {include file="templates/html/_shop_menu.html"}
    <div id="shop-statistics">
        <span id="current-graph" data-value="getNewValue"></span>

        <span class="getNewValue active" id="yearPrice" data-variant="Price" data-period="year">
            Доход / Год
            <span class="monthVariantSimbil">$</span>
        </span>
        <span class="getNewValue" id="yearCount" data-variant="Count" data-period="year">
            Количество / Год
            <span class="monthVariantSimbil">C</span>
        </span>
        <hr />
        <div>
            <span class="arrowMonthLeft">&nbsp;&nbsp;&nbsp;<<&nbsp;&nbsp;&nbsp;</span>
            {foreach  from=$monthLine item=month}
            <span class="monthVariant">
                <span class="monthTitle">
                    {$monthLanguage[$month]}
                </span>
                <span class="monthStatisticValue">
                    <span class="getNewValue" id="{$month}Count" data-variant="Count" data-period="{$month}">
                        Количество / {$monthLanguage[$month]}
                    </span>
                    <span class="getNewValue" id="{$month}Count" data-variant="Price" data-period="{$month}">
                        Доход / {$monthLanguage[$month]}
                    </span>
                </span>
            </span>
            {/foreach}
            <span class="arrowMonthRight">&nbsp;&nbsp;&nbsp;>>&nbsp;&nbsp;&nbsp;</span>
        </div>
        <hr />
        
        <span id="shopStatisticData" data-value='{$visibleParams}'></span>
        <div id="shopStatisticCanvas" style="padding:30px 50px;background:#FFF;"></div>
    </div>
</div>
{/strip}
