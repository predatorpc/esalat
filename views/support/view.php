<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\common\models\Messages */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('admin', 'Сообщения'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="messages-view">
    <h1><?= Html::encode($this->title) ?></h1>
    <div style="padding: 15px 0">
        <b><?=Yii::t('admin','Навигация')?></b><br>
        <?php foreach($menu['items'] as $key=>$item): ?>
              <a style="margin: 0 15px 0 0px;" href="<?=$item['link']?>"><?=Yii::t('admin',$item['title'])?></a>
         <?php endforeach; ?>
    </div>
    <p>
        <?= Html::a(Yii::t('admin', 'Редактировать'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
    </p>
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'type_id',
            'group_id',
            'user_id',
            'name',
            'topic',
            'order',
            'phone',
            'text:ntext',
            'answer:ntext',
            'date',
            'show',
            'status',
        ],
    ]) ?>

</div>
