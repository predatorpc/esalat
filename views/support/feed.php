<?php
use app\modules\common\models\ModFunctions;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Breadcrumbs;
use app\modules\my\models\MessagesImages;
use app\modules\my\models\Feedback;
use app\modules\common\models\User;

$this->title = Yii::t('admin', 'Отзывы');
$this->params['breadcrumbs'][] = ['label' => Yii::t('admin', 'Управление магазином'), 'url' => ['shop-management'], 'template' => "/ {link} /\n",];
$this->params['breadcrumbs'][] = ['label' => Yii::t('admin', 'Техподдержка'), 'url' => [' '], 'template' => "{link} /\n",];
$this->params['breadcrumbs'][] = ['label' => $this->title, 'template' => "{link} \n",];
?>
<style>
    ul li {
        margin: 0px;
        padding: 0px;
    }
</style>
<script type="application/javascript">


</script>
<h1><?= $this->title ?></h1>
<div id="cms-feedback">
    <!--Хлебная крошка-->
    <?= Breadcrumbs::widget(['links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],]); ?>
    <div style="margin:0 0 10px 0">
        <b><?= Yii::t('admin', 'Количество отзывов всего') ?>:</b> <span
            class="badge"><?= Feedback::find()->where(['type_id' => 1003])->count() ?></span><br>
        <b><?= Yii::t('admin', 'Обработанные') ?>:</b><span
            class="badge"><?= Feedback::find()->where(['type_id' => 1003, 'status' => 1])->count() ?></span><br>
        <b><?= Yii::t('admin', 'Не обработанные') ?>:</b><span
            class="badge danger-com"><?= Feedback::find()->where(['type_id' => 1003, 'status' => 0])->count() ?></span><br>
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
                'attribute' => 'rating',
                'label'     => Yii::t('admin', 'Рейтинг'),
                'content'   => function ($data) {
                    $html = '';
                    if($data->rating == 1 || $data->rating == 2) {
                        $html .='danger-com';
                    }elseif($data->rating == 3){
                        $html .='warning-com';
                    }elseif($data->rating == 4 || $data->rating == 5) {
                        $html .='success-com';
                    }else{
                        $html = 'default-com';
                    }
                    return '<span class="badge '.$html.'">'.$data->rating.'</span>';
                }

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
            // 'show',
        ],

    ]); ?>
</div>


