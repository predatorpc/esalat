<?php
use app\modules\common\models\ModFunctions;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Breadcrumbs;
use app\modules\my\models\MessagesImages;
use app\modules\my\models\Feedback;
use app\modules\common\models\User;

$this->title = Yii::t('admin', 'Обратная связь');
$this->params['breadcrumbs'][] = ['label' => Yii::t('admin', 'Управление магазином'), 'url' => ['shop-management'], 'template' => "/ {link} /\n",];
$this->params['breadcrumbs'][] = ['label' => Yii::t('admin', 'Техподдержка'), 'url' => [' '], 'template' => "{link} /\n",];
$this->params['breadcrumbs'][] = ['label' => $this->title, 'template' => "{link} \n",];
?>
<script type="application/javascript">
    // Пометить статус;
    function status_negative(id) {
        loading('show');
        var active = $("#cms-feedback tr[data-key='" + id + "'] input").filter('input:checked').length;
        $.post(location.href, {'support': true, 'active': (active ? 1 : 0), 'id': id}, function (response) {
            $('.table', '#cms-feedback').html($(response).find('.table ', '#cms-feedback').html());
            loading('hide');
        });
    }
    function filters(name, type) {
        loading('show');
        $.post(location.href, {'filters': true, 'name': name, 'type': type}, function (response) {
            $('.table', '#cms-feedback').html($(response).find('.table ', '#cms-feedback').html());
            loading('hide');
        });
        return false;
    }
    function reset() {
        loading('show');
        $.post(location.href, {'reset': true}, function (response) {
            $('.table', '#cms-feedback').html($(response).find('.table ', '#cms-feedback').html());
            loading('hide');
        });
        return false;
    }


</script>
<h1><?= $this->title ?></h1>
<div id="cms-feedback">
    <!--Хлебная крошка-->
    <?= Breadcrumbs::widget(['links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],]); ?>
    <div class="filters text-right">
        <b style="margin: 0 10px 0 0;">
            <?= Yii::t('admin', 'Фильтр') ?>:
            <a href="#" onclick="return filters('active',1);"><?= Yii::t('admin', 'Негативный отзыв') ?></a>
            <a href="#" class="hidden" onclick="return filters('status',1);"><?= Yii::t('admin', 'Обработанные') ?></a>
            <a href="#" onclick="return filters('status',0);"><?= Yii::t('admin', 'Не обработанные') ?></a>
            <a href="#" onclick="return reset();"><?= Yii::t('admin', 'Сбросить') ?></a>
        </b>


    </div>
    <div style="margin: -35px 0 10px 0">
        <b><?= Yii::t('admin', 'Количество обращений всего') ?>:</b> <span
            class="badge"><?= Feedback::find()->where(['type_id' => 1002])->count() ?></span><br>
        <b><?= Yii::t('admin', 'Обработанные') ?>:</b><span
            class="badge"><?= Feedback::find()->where(['type_id' => 1002, 'status' => 1])->count() ?></span><br>
        <b><?= Yii::t('admin', 'Не обработанные') ?>:</b><span
            class="badge"><?= Feedback::find()->where(['type_id' => 1002, 'status' => 0])->count() ?></span><br>
        <b><?= Yii::t('admin', 'Количество негативных обращений') ?>:</b> <span
            class="badge danger-com"><?= Feedback::find()->where(['active' => 1, 'type_id' => 1002])->count() ?></span>
        <span
            class="text-danger bold">
            <?php $a = Feedback::find()->where(['type_id' => 1002])->count(); if ($a != 0): ?>
                <?= round((Feedback::find()->where(['active' => 1, 'type_id' => 1002])->count() / Feedback::find()->where(['type_id' => 1002])->count()  * 100)) ?>
            <?php else: ?>
                0
            <?php endif; ?>
            %</span>
    </div>
    <!--Хлебная крошка-->
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel'  => $searchModel,
        'rowOptions'   => function ($model, $key, $index, $grid) {
            $class = ($model->active == 1 ? ' danger-com' : ($model->status == 0 ? ' warning' : ''));

            return [
                'key'   => $key,
                'index' => $index,
                'class' => $class
            ];
        },
        'columns'      => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'user_id',
                'label'     => Yii::t('admin', 'Пользователь'),
            ], [
                'attribute' => 'date',
                'label'     => Yii::t('admin', 'Дата'),
                'format'    => ['date', 'php:d.m.Y'],
            ], [
                'attribute' => 'order',
                'label'     => Yii::t('admin', 'Заказ'),
            ],
            [
                'attribute' => 'name',
                'label'     => Yii::t('admin', 'Имя (ФИО)'),
                'content'   => function ($data) {

                    $html = '';
                    if (!empty($data->user_id)) {
                        $user = User::findOne($data->user_id);
                        $html .= '<div class="bold">' . $data->name . '</div>';
                        $html .= '<div style="margin-top:3px">' . (!empty($user->staff) ? '<span class="text-success">' . Yii::t('admin', 'Сотрудник') . '</span>' : '<span class="text-danger" >' . Yii::t('admin', 'Не сотрудник') . '</span>') . '</div>';
                        $html .= '<div style="margin-top:5px"><a target="_blank" href="/reports/profile?id=' . $user->id . '">' . Yii::t('admin', 'Профайл') . '</a></div>';
                        return $html;
                    } else {
                        return false;
                    }
                }
            ],
            [
                'attribute' => 'topic',
                'label'     => Yii::t('admin', 'Топик'),
            ],
            [
                'attribute' => 'answer',
                'label'     => Yii::t('admin', 'Ответ'),
            ],
            [
                'attribute' => 'text',
                'label'     => Yii::t('admin', 'Текст'),
            ],
//            'user_id',
//            'date',
//            'order',
//            'name',
//            'topic',
//            'answer:ntext',
//            'text:ntext',

            // 'answer:ntext',
            // 'topic',
            // 'order',
            // 'phone',
            // 'text:ntext',
            // 'answer:ntext',
            // 'date',
            // 'show',
            // 'status',
            [
                'attribute' => 'images',
                'label'     => Yii::t('admin', 'Изображения'),
                'content'   => function ($data) {
                    $images = MessagesImages::find()->where(['message_id' => $data->id])->all();
                    //print_arr($images);
                    if (!empty($images)) {
                        $html = '';
                        foreach ($images as $k => $image) {
                            $html .= '<a href="/files/images/' . $image->id . '.' . $image->exp . '" target="_blank"><img src="/files/images/' . $image->id . '.' . $image->exp . '" width="50px" height="50px" /></a> ';
                        }
                        return $html;
                    }

                }

            ],
            [
                'attribute' => 'status',
                'label'     => Yii::t('admin', 'Статус'),
                'content'   => function ($data) {
                    if ($data['status'] == 1)
                        return Yii::t('admin', 'Обработано');
                    else
                        return Yii::t('admin', 'Не обработано');

                }

            ],
            [
                'class'         => 'yii\grid\ActionColumn',
                'header'        => Yii::t('admin', 'Действия'),
                'headerOptions' => ['width' => '80'],
                'template'      => '{update}',
                'buttons'       => [
                    'update' => function ($url, $model) {
                        return Html::a(
                            '<span>' . Yii::t('admin', 'Ответить') . '</span>', $url);
                    },
                ],
            ],
            [
                'attribute' => 'active',
                'label'     => Yii::t('admin', 'Негативный отзыв'),
                'format'    => 'raw',
                'value'     => function ($model, $index, $widget) {
                    $checked = ($model->active == 1 ? 'checked' : '');
                    return Html::checkbox('active[]', $checked, ['onclick' => 'status_negative(' . $model->id . ');',]);
                },

            ],
            // 'show',
        ],

    ]); ?>
</div>


