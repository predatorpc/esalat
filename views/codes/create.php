<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\modules\catalog\models\codes */

$this->title = Yii::t('admin', 'Создать промо-код');
$this->params['breadcrumbs'][] = ['label' => Yii::t('admin', 'Промо - коды'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="codes-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
            'id' => $id,
            'model' => $model,
            'types' => $types,
            'users' => $users,
            'stringHash' => $users, //['name'],
        ]);
    ?>

</div>
