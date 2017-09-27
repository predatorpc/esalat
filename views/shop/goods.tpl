{strip}
    {use class='yii\helpers\Html'}
    {use class='app\helpers\GridHelper'}
    {use class='yii\grid\GridView' type='function'}
    {use class='yii\widgets\LinkPager' type='function'}

    <script src="/systems/jquery/upload/jquery.fileupload.js"></script>
    <script type="text/javascript" src="/systems/jquery/crop/jquery.imgareaselect.js"></script>
{literal}
    <script>
    </script>
{/literal}
    <div class="container">
        {include file="templates/html/_shop_menu.html"}
        <div id="cms-goods">
            <div class="statisticBlock row small">
                {foreach from=$statistic.value key=key item=item}
                    <div class="row">
                        <div class="col-xs-8 col-sm-7 col-md-7 col-lg-6">
                            <div class="group small" style="text-align:right;margin-top: 1px;">{$statistic.title[$key]}</div>
                        </div>
                        <div class="col-xs-4 col-sm-5 col-md-5 col-lg-6">
                            <div class="visibleShopParams group small" data-param="{$key}" style="margin-top: 1px;">{$item}</div>
                        </div>
                    </div>
                {/foreach }
            </div>
            <div class="shop_goods_lists">
                <div class="button_href">
                    <a href="/shop/goods/add">Добавить товар</a>
                </div>

                {GridView::widget([
                    'dataProvider' => $dataProvider,
                    'filterModel' => $searchModel,
                    'columns' => [
                        'id',
                        'producer_name',
                        [
                            'attribute' => 'name',
                            'value' => ['app\helpers\GridHelper', 'getLinkValue'],
                            'format'=>'html'
                        ],
                        'price_out',
                        [
                            'attribute' => 'date_create',
                            'format' => ['date', 'd.M.Y H:m']
                        ],
                        [
                            'attribute'=>'status',
                            'filter' => ['Неактивные','Активные'],
                            'value' => ['app\helpers\GridHelper', 'columnStatusValue'],
                            'format'=>'html',
                            'contentOptions' => ['class' => 'text-center'],
                            'headerOptions' => ['class' => 'text-center']
                        ],
                        [
                            'attribute'=>'confirm',
                            'filter' => [-1 => 'Отклонён', 0 => 'На модерации', 1 => 'Одобрен'],
                            'value' => ['app\helpers\GridHelper', 'columnConfrmValue'],
                            'format'=>'html',
                            'contentOptions' => ['class' => 'text-center'],
                            'headerOptions' => ['class' => 'text-center']
                        ],
                        ['class' => 'yii\grid\ActionColumn']
                    ]
                ])}
            </div>
            <div class="clear"></div>
        </div>
    </div>
{/strip}
