<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\common\models\Useradmin */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('admin', 'Администрирование пользователей'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="useradmin-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('admin', 'Редактировать'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary white no-border']) ?>
        <?= Html::a(Yii::t('admin', 'Назад к списку'), ['/user'], ['class' => 'btn btn-primary white no-border']) ?>


        <?php

        $options[0] = Yii::t('admin', 'Нет');
        $options[1] = Yii::t('admin', 'Администратор клуба');
        $options[2] = Yii::t('admin', 'Персональный тренер');
        $options[3] = Yii::t('admin', 'Тренер групповых тренеровок');
        $options[4] = Yii::t('admin', 'Управление');
        $options[5] = Yii::t('admin', 'Фитнес консультант');

        //$options[5] = 'Офис';

        $stores = \app\modules\managment\models\ShopsStores::find()
            ->select('id, name')
            ->where('shop_id = 10000001')
            ->andWhere('status = 1')
            ->asArray()
            ->all();


        //\app\modules\common\models\Zloradnij::print_arr($stores);die();

        $storeName = Yii::t('admin', 'Фитнес консультант');

        foreach ($stores as $store) {
            if ($store['id'] == $model->store_id) {
                $storeName = $store['name'];
                break;
            }
        }

        //Если что раскоментить эхо
        //echo
        Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>


    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'phone:ntext',
            'email:email',
            'name',
            'role',
            [
                'attribute' => 'sms',
                'value' => $model->sms ? Yii::t('admin', 'Отправлять SMS') : Yii::t('admin', 'Не Отправлять SMS'),
            ],

//            [ 'filter' => $params1,],
//            'fullname:ntext',
//            'auth_key',
//           'password_hash',
            [
                'attribute' => 'typeof',
                'label' => Yii::t('admin', 'Свойства пользователя'),
                'value' => !empty($model->typeof) ? $options[$model->typeof] : Yii::t('admin', 'Не задано'),
            ],
            [
                'attribute' => 'money',
                'label' => 'Money',
                'value' => $model->money,
            ],
            [
                'attribute' => 'bonus',
                'label' => 'Bonus',
                'value' => $model->bonus,
            ],
            [
                'attribute' => 'store_id',
                'label' => Yii::t('admin', 'Клуб пользователя'),
                'value' => $storeName,
            ],
            [
                'attribute' => 'secret_word',
                'label' => Yii::t('admin', 'Секретное слово'),

            ],
            [
                'attribute' => 'status',
                'value' => $model->status ? Yii::t('admin', 'Активный') : Yii::t('admin', 'Неактивный'),
            ],
            [
                'attribute' => 'registration',
                'label' => Yii::t('admin', 'Зарегистрирован (старый формат)'),
            ],
            [
                'attribute' => 'created_at',
                'label' => Yii::t('admin', 'Зарегистрирован'),
                'value' => date("Y-m-d H:i:s", $model->created_at),//,$data->created_at);
                //'format' => 'text',
            ],

            [
                'attribute' => 'updated_at',
                'label' => Yii::t('admin', 'Обновлен'),
                'value' => date("Y-m-d H:i:s", $model->updated_at),//,$data->created_at);
                //'format' => 'text',
            ],
        ],
    ]) ?>

</div>
