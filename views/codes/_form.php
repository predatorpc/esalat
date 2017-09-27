<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\bootstrap\Modal;
use yii\widgets\Pjax;
use app\modules\common\models\User;
use app\modules\common\models\UserRoles;
/* @var $this yii\web\View */
/* @var $model app\modules\catalog\models\codes */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="codes-form">

    <?php

    if(empty($model->id)) {

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
            ->where(['users.status' => 1])
            ->orderBy('name')
            ->asArray()
            ->All();
        $userRolesModel = new UserRoles();

        Pjax::begin(['id' => 'search_user1']);
        echo Html::beginForm(['getusername'], 'post', ['data-pjax' => '', 'class' => 'form-inline']);
        echo Html::hiddenInput('user_id', $id);
        echo '<b>Введите Номер телефона без +7</b><br>';
        echo Html::input('text', 'string', Yii::$app->request->post('string'), ['class' => 'form-control', 'minlength' => 4, 'maxlength' => 10]);
        echo Html::submitButton('Найти', ['class' => 'btn btn-primary', 'name' => 'hash-button']);
        echo Html::endForm();
        echo "<br>";
        //print_r($stringHash);
        if (!empty($stringHash)) {
            echo '<b>'.Yii::t('admin', 'Выберите нужного пользователя из списка').':</b><br>';
            foreach ($stringHash as $item) {
                echo Html::a($item['id'] . " " . $item['name'],
                        'create?user_id=' . $item['id']) . "<br>";
            }
        } else {
            echo "<br>";
        }
        Pjax::end();
        Modal::end();

    }

    $form = ActiveForm::begin();

    if(empty($model->id)) {
        $newCode = new \app\modules\basket\models\PromoCode();
        $code =$newCode->generatePromocode();
        $param = ['options' =>[ $id => ['selected' => true]]];
        //TODO: uncomment when model will be update with unique row code and status
        //$hr = Yii::$app->authManager->getRolesByUser(Yii::$app->user->id);
        //if(!empty($hr['GodMode'])){
        //  echo $form->field($model, 'code')->textInput(['readonly' => false, 'value' => ''])->label(Yii::t('admin', 'Промо код'));
        //}
        //else{
            echo $form->field($model, 'code')->textInput(['readonly' => true, 'value' => $code])->label(Yii::t('admin', 'Промо код'));
        //}

    }
    else
        echo  $form->field($model, 'code')->textInput(['readonly' => true])->label(Yii::t('admin', 'Промо код'));
            $param = ['options' =>[ $model->user_id => ['selected' => true]]];
    ?>

    <?php // = $form->field($model, 'user_id')->textInput() ?>
    <?php // = $form->field($model, 'user_id')->DropDownList(ArrayHelper::map(array_merge([['id' => '--','name' => '--']],$users),'id','name'))->label('Пользователь'); ?>
    <?php
    //$user_id = '';
    if(isset($_GET['user_id']) && $_GET['user_id']>0){
    //Господа, я незнаю кто это поправил, НО, пожалуйста сначала разберитесь в логике сего мероприятия predator_pc@extremefitness 14/03/2017 
    if(empty($model->user_id)){
        $user_id = $_GET['user_id'];
        $model->user_id = $user_id;
        $user = User::find()->select(['id','name'])->where(['id'=>$model->user_id])->asArray()->all();
        //$users = [['id' => $user['id'], 'name' => $user['name']]];
    }
    }
    
    ?>
    <?=$form->field($model, 'user_id')->DropDownList(ArrayHelper::map(array_merge([['id' => '--','name' => '--']],$users),'id','name'))->label(Yii::t('admin', 'Клиент')); ?>

    <?= $form->field($model, 'type_id')->DropDownList(ArrayHelper::map(array_merge([['id' => '--','name' => '--']],$types),'id','name'))->label(Yii::t('admin', 'Выберите тип кода')); ?>

    <?php // = $form->field($model, 'type_id')->textInput() ?>

    <?php // = $form->field($model, 'key')->textInput(['maxlength' => true]) ?>

    <?php
    if(empty($model->id)) {
        echo $form->field($model, 'count')->textInput(['value' => 1000])->hint(Yii::t('admin', 'Максимальное кол-во покупок'));
    }
    else {
        echo $form->field($model, 'count')->textInput()->hint(Yii::t('admin', 'Максимальное кол-во покупок'));
    }

    ?>

    <?php // = $form->field($model, 'date_begin')->textInput()->hint('Дата начала действия') ?>

    <?php // = $form->field($model, 'date_end')->textInput()->hint('Дата конца действия')

    $model->status=1;

    ?>


    <?= $form->field($model, 'status')->checkbox() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('admin', 'Создать') : Yii::t('admin', 'Обновить'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('admin', 'Назад'),'/codes/index?sort=-code', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
