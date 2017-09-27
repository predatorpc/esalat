<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\modules\common\models\User;
use yii\bootstrap\Modal;
use app\modules\common\models\UserRoles;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model app\modules\common\models\UsersPays */
/* @var $form yii\widgets\ActiveForm */

$type = [
    [ 'id' => '',
      'name' => '--',
    ],
    [ 'id' => 0,
      'name' => Yii::t('admin', 'Расход бонусов (покупка товаров)'),
    ],
    [ 'id' => 1,
      'name' => Yii::t('admin', 'Начисление бонусов за пополнение счет в магазине'),
    ],
    [ 'id' => 2,
      'name' => Yii::t('admin', 'Начисление бонусов за оплату абонемента ExtremeFitness'),
    ],
    [ 'id' => 4,
      'name' => Yii::t('admin', 'Начисление бонусов за покупку более 1000 рублей'),
    ],
    [ 'id' => 5,
      'name' => Yii::t('admin', 'Начисление бонусов за покупку более 3000 рублей'),
    ],
];

?>

<div class="users-pays-form">

    <?php



    ///////////////////////////////////////////////////////////
    //        MODAL USERS BEGIN
    //////////////////////////////////////////////////////////

    Modal::begin([
        'header' => '<h3><b>'.Yii::t('admin', 'Найти пользователя').':</b></h3>',
        'id' => 'search_user',
        'toggleButton' => [
            'tag' => 'button',
            'class' => 'btn btn-primary',
            'label' => Yii::t('admin', 'Найти пользователя'),
        ]
    ]);
    $userRolesArray = User::find()
        ->select('users.id, users.name, users.phone, users.email')
        //->select('users.id, users_roles.user_id, users.name')
        // ->joinWith(['roles'])
        ->where(['users.status'=>1])
        ->orderBy('name')
        ->asArray()
        ->All();
    $userRolesModel = new UserRoles();

    Pjax::begin(['id' => 'search_user1']);
    echo Html::beginForm(['getusernamebonus'], 'post', ['data-pjax' => '', 'class' => 'form-inline']);
    echo Html::hiddenInput('user_id', $id);
    echo '<b>'.Yii::t('admin', 'Введите Номер телефона без +7').'</b><br>';
    echo Html::input('text', 'string', Yii::$app->request->post('string'), ['class' => 'form-control', 'minlength' => 4, 'maxlength' => 10]);
    echo Html::submitButton('Найти', ['class' => 'btn btn-primary', 'name' => 'hash-button']);
    echo Html::endForm();
    echo "<br>";
    //print_r($stringHash);
    if(!empty($stringHash)) {
        echo '<b>'.Yii::t('admin', 'Выберите нужного пользователя из списка').':</b><br>';
        foreach ($stringHash as $item) {
            echo Html::a($item['id'] . " " . $item['name'],
                    'create-bonus?id=' . $item['id']) . "<br>";
        }
    }else {
        echo "<br>";
    }
    Pjax::end();
    Modal::end();

    $param = ['options' =>[ $id => ['selected' => true]]];
    // echo "!!!".$id."!!!";

    ?>


    <?php $form = ActiveForm::begin(); ?>
    <?= $form->field($model, 'user_id')->DropDownList(ArrayHelper::map(array_merge([['id' => '--','name' => '--']],$users),'id','name'), $param)->label(Yii::t('admin', 'Пользователь')); ?>

    <?= $form->field($model, 'created_user_id')->hiddenInput(['value' => Yii::$app->user->getId()]); ?>

    <?php // = $form->field($model, 'user_id')->DropDownList(ArrayHelper::map(array_merge([['id' => '--','name' => '--']],$users),'id','name'))->label('Пользователь'); ?>

    <?= $form->field($model, 'order_id')->textInput()->label(Yii::t('admin', 'ID Заказа'))->hint(Yii::t('admin', 'Оставьте это поле пустым если Вы незнаете ID Заказа или его нет.'));; ?>

    <?= $form->field($model, 'type')->DropDownList(ArrayHelper::map($type,'id','name'))->label(Yii::t('admin', 'Тип')); ?>

    <?= $form->field($model, 'bonus')->textInput(['maxlength' => true])->label(Yii::t('admin', 'Бонусы')); ?>

    <?= $form->field($model, 'type_id')->hiddenInput(['value' => 1]) ?>

    <?php //= $form->field($model, 'date')->textInput()->label(''); ?>

    <?= $form->field($model, 'status')->checkbox()->label(Yii::t('admin', 'Статус транзакции'))->hint(Yii::t('admin', 'Если не поставить сию галочку, то транзакцию не будет видно, в общей таблице')); ?>

    <?= $form->field($model, 'comments')->textarea()->label(Yii::t('admin', 'Комментарий')); ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('admin', 'Создать') : Yii::t('admin', 'Обновить'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('admin', 'Назад'), '/transactions', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
