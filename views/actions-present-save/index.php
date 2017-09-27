<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ActionsPresentSaveSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Actions Present Saves';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="actions-present-save-index">

    <h1>Сброс статуса отказа от акционных подарков</h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?php // = Html::a('Create Actions Present Save', ['create'], ['class' => 'btn btn-success'])
        ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel'  => $searchModel,
        'columns'      => [
//            ['class' => 'yii\grid\SerialColumn'],
//            'id',
            [
                'attribute' => 'user_id',
                'label'     => 'Пользователь',
                /*                'content'   => function ($model) {
                                    if (!empty($model->user_id)) {
                                        $user = app\modules\common\models\UserAdmin::find()->where('id = ' . $model->user_id)->one();
                                        if (!empty($user))
                                            return $user->name;
                                        else
                                            return '';
                                    }
                                }*/
                'content'   => function ($data) {
                    if (!empty($data['user_id'])) {
                        $user = \app\modules\common\models\User::find()->where(
                            'id = ' . $data['user_id']
                        )->one();
                        if (!empty($user)) {
                            return Html::a($user->name, '/user/view?user_id=' . $data['user_id'], ['target' => '_blank']);
                        } else {
                            return '';
                        }
                    } else {
                        return '';
                    }
                },
            ],
            [
                'attribute' => 'present',
                'label'     => 'Номер заказа',
                'content'   => function ($model) {
                    if (!empty($model->order['id'])) {
                        return '<p style="color: green;">' . $model->order['id'] . '</p>';
                    } else {
                        return '<p style="color: red;">Заказ не найден</p>';
                    }
                }
            ],
            [
                'attribute' => 'card_number',
                'label'     => 'Номер карты',
                'content'   => function ($model) {
                    if (!empty($model->card_number)) {
                        return '<p style="color: green;">' . $model->card_number . '</p>';
                    } else {
                        return '<p style="color: red;">Карты нет</p>';
                    }
                }
            ],
            [
                'attribute' => 'basket_id',
                'label'     => 'ID корзины',
            ],
            // 'create_date',
            // 'update_date',
            // 'bought_date',
//            'status',
            ['attribute' => 'status',
             'label'     => 'Отказ?',
             'content'   => function ($model) {
                 return $model->status ? "Отказ" : "Нет отказа";
             }
            ],
            [
                'class'    => 'yii\grid\ActionColumn',
                'template' => '{update}',
                'buttons'  => [
                    'update' => function ($url, $model, $key) {
                        return Html::a('Изм. статус', $url);
                    }
                ]
            ],
        ],
    ]); ?>
</div>
