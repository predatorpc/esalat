<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\modules\common\models\UserAdmin;


/*
if(Yii::$app->user->can('GodMode')){
	$flash = Yii::$app->session->getAllFlashes();
	foreach ($flash as $item) {
		\app\modules\common\models\Zloradnij::print_arr($item);
	}
}

*/

$typeof = [
		[ 'id' => 0,
		  'name' => '--',
		],
		[ 'id' => 1,
		  'name' => Yii::t('admin', 'Администратор клуба'),
		],
		[ 'id' => 2,
		  'name' => Yii::t('admin', 'Персональный тренер'),
		],
		[ 'id' => 3,
				'name' => Yii::t('admin', 'Тренер групповых тренеровок'),
		],
		[ 'id' => 4,
				'name' => Yii::t('admin', 'Управление'),
		],
		[ 'id' => 5,
				'name' => Yii::t('admin', 'Фитнес консультант'),
		],
];

$stores = \app\modules\managment\models\ShopsStores::find()
		->select('id, name')
		->where('shop_id = 10000001')
		->andWhere('status = 1')
		->all();

//var_dump($model);die();
/* @var $this yii\web\View */
/* @var $model app\modules\common\models\Useradmin */
/* @var $form yii\widgets\ActiveForm */
?>



<div class="useradmin-form">

	<?php $form = ActiveForm::begin(); ?>

	<?php $params = ['prompt' => Yii::t('admin', 'Выбрать категорию')]; ?>

	<?php //$params1 = [ '0' => 'Не активен', '1' => 'Активен'] ?>

	<br><h3>Общая информация:</h3><br>

	<?php if(Yii::$app->user->getId() == 10015520 || Yii::$app->user->getId() == 10013181){ ?>
	<?= $form->field($model, 'phone')->textInput(['maxlength' => 12,])->hint(Yii::t('admin', 'Пожалуйста внимательно введите номер телефона в формате ХХХ-ХХХ-ХХ-ХХ (10 цифр), ибо если неверен он будет - пользователь не сможет залогиниться аки раб.')); ?>
	<?php } else {?>
	<?= $form->field($model, 'phone')->textInput(['maxlength' => 10, 'readonly' => false])->hint(Yii::t('admin', 'Пожалуйста внимательно введите номер телефона в формате ХХХ-ХХХ-ХХ-ХХ (10 цифр), ибо если неверен он будет - пользователь не сможет залогиниться аки раб.')); ?>
	<?php } ?>
	
	<?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
	<?= $form->field($model, 'password')->passwordInput()->label(Yii::t('admin', 'Новый пароль'))->hint(Yii::t('admin', 'Оставьте без изменений, если не нужно менять')) ?>
	<?= $form->field($model, 'bonus')->textInput(['readonly' => true])->hint('Bonus') ?>
	<?= $form->field($model, 'money')->textInput(['readonly' => true])->hint('Money') ?>
	<?= $form->field($model, 'email')->textInput()->hint(Yii::t('admin', 'Электронная почта')) ?>
	<?= $form->field($model, 'secret_word')->textInput(['readonly' => true])->hint(Yii::t('admin', 'Секретное слово')) ?>
	<?= $form->field($model, 'outsourcing')->CheckBox()->label(false) ?>
	<?= $form->field($model, 'status')->CheckBox()->label(false) ?>

	<?= $form->field($model, 'sms')->CheckBox()->label(Yii::t('admin', 'Отправлять СМСки?')) ?>

	<br><h3><?= Yii::t('admin', 'Если пользователь сотрудник') ?>:</h3><br>


	<?= $form->field($model, 'staff')->CheckBox()->label(Yii::t('admin', 'Сотрудник?')) ?>
	<?= $form->field($model, 'role')->DropDownList(ArrayHelper::map(Yii::$app->authManager->getRoles(),'name','description'), $params)  ?>
	<?= $form->field($model, 'typeof')->DropDownList(ArrayHelper::map($typeof, 'id', 'name'))->label(Yii::t('admin', 'Тип сотрудника'));  ?>
	<?= $form->field($model, 'store_id')->DropDownList(ArrayHelper::map(array_merge([['id' => '0','name' => '--']],$stores), 'id', 'name'))->label(Yii::t('admin', 'Если пользователь сотрудник'));  ?>

	<?php //= $form->field($model, 'email')->CheckBox() ?>


	<div class="form-group">
		<?= Html::submitButton($model->isNewRecord ? Yii::t('admin', 'Добавить') : Yii::t('admin', 'Обновить'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>
