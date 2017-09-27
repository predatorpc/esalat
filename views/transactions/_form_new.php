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

$type =
    [
        [
            'id' => '',
            'name' => "--",
        ],
        /*    [
               'id' => 0,
               'name' => "не указан",
            ],
            [
                'id' => 1,
                'name' => "Пополнение счета (VISA)",
            ],
            [
                'id' => 2,
                'name' => "Пополнение счета (ExtremeFitness)",
            ],
            [
                'id' => 3,
                'name' => "Старые продажи",
            ],
            [
                'id' => 4,
                'name' => "Оплата заказа на сайте",
            ],
            [
                'id' => 5,
                'name' => "Отмена заказа",
            ],
            [
                'id' => 6,
                'name' => "Комиссия за продажу товара",
            ],
            [
                'id' => 8,
                'name' => "Старые продажи",
            ],
            [
                'id' => 9,
                'name' => "Оплата доставки",
            ],
            [
                'id' => 10,
                'name' => "Перевод с клиента на клиента",
            ],
            [
                'id' => 13,
                'name' => "Оплата заказа через терминал",
            ],
            [
                'id' => 20,
                'name' => "Зачисление наличными",
            ],
            [
                'id' => 21,
                'name' => "Комиссия за доставку товара",
            ],*/
        [
            'id' => 22,
            'name' => Yii::t('admin', 'Вывод средств'),
        ],
        [
            'id' => 23,
            'name' => Yii::t('admin', 'Зачисление средств'),
        ],
    ];

?>

<div class="users-pays-form">

    <?php

    ///////////////////////////////////////////////////////////
    //        MODAL USERS BEGIN
    //////////////////////////////////////////////////////////

    Modal::begin([
        'header' => '<h3><b>Найти пользователя:</b></h3>',
        'id' => 'search_user',
        'toggleButton' => [
            'tag' => 'button',
            'class' => 'btn btn-primary',
            'label' => Yii::t('admin', 'Найти пользователя')
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
    echo Html::beginForm(['getusername'], 'post', ['data-pjax' => '', 'class' => 'form-inline']);
    echo Html::hiddenInput('user_id', $id);
    echo '<b>'.Yii::t('admin', 'Введите Номер телефона без +7').'</b><br>';
    echo Html::input('text', 'string', Yii::$app->request->post('string'), ['class' => 'form-control', 'minlength' => 4, 'maxlength' => 10]);
    echo Html::submitButton(Yii::t('admin', 'Найти'), ['class' => 'btn btn-primary', 'name' => 'hash-button']);
    echo Html::endForm();
    echo "<br>";
    //print_r($stringHash);
    if(!empty($stringHash)) {
        echo '<b>'.Yii::t('admin', 'Выберите нужного пользователя из списка').':</b><br>';
        foreach ($stringHash as $item) {
            echo Html::a($item['id'] . " " . $item['name'],
                    'create-new?id=' . $item['id']) . "<br>";
        }
    }else {
        echo "<br>";
    }
    Pjax::end();
    Modal::end();

    //  $user = User::find()->select('id, name')->where('id = '.$user_id)->one();
    $form = ActiveForm::begin();


    $param = ['options' =>[ $id => ['selected' => true]]];

   ?>

    <?php //= $form->field($model, 'user_id')->hiddenInput(['value' => $user->id])->label('') ?>

    <?php // = $form->field($user, 'name')->textInput(['value' => $user->name, 'readonly' => true])->label('Клиент'); ?>

    <?php // = $form->field($model, 'user_id')->DropDownList(ArrayHelper::map(array_merge([['id' => '--','name' => '--']],$users),'id','name'))->label('Клиент'); ?>

    <?=$form->field($model, 'user_id')->DropDownList(ArrayHelper::map(array_merge([['id' => '--','name' => '--']],$users),'id','name'), $param)->label('Клиент'); ?>

    <?= $form->field($model, 'created_user_id')->hiddenInput(['value' => Yii::$app->user->getId()]); ?>

    <?= $form->field($model, 'order_id')->textInput()->label(Yii::t('admin', '№ Заказа'))->hint(Yii::t('admin', 'Оставьте это поле пустым если Вы незнаете ID Заказа или его нет.'));; ?>

    <?= $form->field($model, 'type')->DropDownList(ArrayHelper::map($type,'id','name'))->label(Yii::t('admin', 'Тип транзакции')); ?>
    <?php //= $form->field($model, 'type')->textInput()->label('Тип'); ?>

    <?= $form->field($model, 'money')->textInput(['maxlength' => true])->label(Yii::t('admin', 'Сумма')); ?>

    <?= $form->field($model, 'comments')->textarea(['rows' => 6])->label(Yii::t('admin', 'Комментарий')); ?>

    <?= $form->field($model, 'type_id')->hiddenInput(['value' => 1])->label('') ?>

    <?php //= $form->field($model, 'status')->checkbox()->label('Статус транзакции'); ?>
    <?= $form->field($model, 'status')->checkbox()->label(Yii::t('admin', 'Статус транзакции'))->hint(Yii::t('admin', 'Если не поставить сию галочку, то транзакцию не будет видно, в общей таблице')); ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('admin', 'Создать') : Yii::t('admin', 'Обновить'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('admin', 'Назад'), '/transactions', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
