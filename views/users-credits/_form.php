<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\bootstrap\Modal;
use app\modules\common\models\User;
use app\modules\common\models\UserRoles;
use yii\widgets\Pjax;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model app\models\UsersCredits */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="users-credits-form">

    <?= Html::a(Yii::t('admin', 'Назад'), ['/users-credits', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
    <?php



    ///////////////////////////////////////////////////////////
    //        MODAL USERS BEGIN
    //////////////////////////////////////////////////////////

    Modal::begin([
        'header' => '<h3><b>'.Yii::t('admin','Найти пользователя:').'</b></h3>',
        'id' => 'search_user',
        'toggleButton' => [
            'tag' => 'button',
            'class' => 'btn btn-primary',
            'label' => Yii::t('admin','Поиск пользователей')
        ]
    ]);


    $userRolesArray = User::find()
        ->select('users.id, users.name, users.phone, users.email')
        //->select('users.id, users_roles.user_id, users.name')
        // ->joinWith(['roles'])
        ->where(['users.status' => 1])
        ->orderBy('name')
        ->asArray()
        ->All();
    $userRolesModel = new UserRoles();

    Pjax::begin(['id' => 'search_user1']);
    echo Html::beginForm(['getusername'], 'post', ['data-pjax' => '', 'class' => 'form-inline']);
    echo Html::hiddenInput('user_id', $id);
    echo '<b>'.Yii::t('admin','Введите номер телефона без +7').'</b><br>';
    echo Html::input('text', 'string', Yii::$app->request->post('string'), ['class' => 'form-control', 'minlength' => 4, 'maxlength' => 10]);
    echo Html::submitButton(Yii::t('admin','Найти'), ['class' => 'btn btn-primary', 'name' => 'hash-button']);
    echo Html::endForm();
    echo "<br>";
    //print_r($stringHash);
    if (!empty($stringHash)) {
        echo '<b>'.Yii::t('admin','Выберите пользоватля:').'</b><br>';
        foreach ($stringHash as $item) {
            echo Html::a($item['id'] . " " . $item['name'],
                    'create?id=' . $item['id']) . "<br>";
        }
    } else {
        echo "<br>";
    }
    Pjax::end();
    Modal::end();

    $form = ActiveForm::begin();

    if(empty($id)) {
        $param = ['options' => [$id => ['selected' => true]]];
    }
    else
        $param = ['options' =>[ $id => ['selected' => true]]];

    ?>

    <?= $form->field($model, 'user_id')->DropDownList(ArrayHelper::map(array_merge([['id' => '--','name' => '--']],$users),'id','name'), $param)->label(Yii::t('admin','Пользователь')); ?>

    <?= $form->field($model, 'amount')->textInput() ?>

    <?php $model->status = 1; ?>
    <?= $form->field($model, 'status')->checkbox() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('admin', 'Добавить') : Yii::t('admin', 'Обновить'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
