<?php

use yii\bootstrap\Modal;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model app\models\Lists */
/* @var $form yii\widgets\ActiveForm */





if(!empty($model->id)){
    $category = \app\modules\catalog\models\CategoryListLinks::find()->where(['list_id' => $model->id])->one();
    if(!$category){
        $category = new \app\modules\catalog\models\CategoryListLinks();
    }
}else{
    $category = new \app\modules\catalog\models\CategoryListLinks();
}
?>

<div class="lists-form">

    <?php $form = ActiveForm::begin([
        'options' => ['enctype'=>'multipart/form-data']
    ]); ?>

    <?= $form->field($model, 'user_id')->textInput(['readonly' => true]) ?>

    <?php //= $form->field($category, 'category_id')->dropDownList([1=>'q',2=>'w']) ?>
    <?= $form->field($category, 'category_id')->dropDownList(\yii\helpers\ArrayHelper::map(\app\modules\catalog\models\Category::find()->where(['active' => 1])->where(['<','level',2])->orderBy('sort,title')->all(),'id','title')) ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>

    <?php //= $form->field($model, 'image')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'position')->textInput() ?>

    <?php //= $form->field($model, 'change')->textInput() ?>

    <?php //= $form->field($model, 'list_type')->textInput() ?>

    <?php
    if(!empty($model->image) && file_exists($_SERVER['DOCUMENT_ROOT'].$model->image)){
        $path = $model->image;
        print '<img src="'.$path.'" style="width:300px;">';
    }
    print $form->field($model, 'image')->fileInput()->label(Yii::t('admin','Загрузите картинку')) ?>

    <?php //= $form->field($model, 'level')->textInput() ?>

    <?php //= $form->field($model, 'date_create')->textInput() ?>

    <?php //= $form->field($model, 'date_update')->textInput() ?>

    <?= $form->field($model, 'show_banners')->checkbox() ?>

    <?= $form->field($model, 'status')->checkbox() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('admin','Добавить') : Yii::t('admin','Обновить'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('admin','Назад'), '/list', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>


<div class="lists-index">

    <h1><?=Yii::t('admin','Список относящихся товаров')?></h1>
    <?php

    ///////////////////////////////////////////////////////////
    //        MODAL USERS BEGIN
    //////////////////////////////////////////////////////////

    Modal::begin([
    'header' => '<h3><b>'.Yii::t('admin','Добавить вариацию к списку:').'</b><br>'.$model->title.'</h3>',
    'toggleButton' => [
    'tag' => 'button',
    'class' => 'btn btn-primary',
    'label' => Yii::t('admin','Добавить вариацию в список').' +',
    ]
    ]);

    Pjax::begin();
    echo Html::beginForm(['/list/getvariationname'], 'post', ['data-pjax' => '', 'class' => 'form-inline form___gl']);
    echo Html::hiddenInput('list_id', $model->id);
    echo "<div style='margin: 0px 0px 10px;'>". Html::textInput('amount', '', ['label' => 'Count','class'=>'form-control placeholder','placeholder'=>Yii::t('admin',"Количество"),'data-text'=>Yii::t('admin',"Количество")]). "</div>";
    echo "<div style='margin: 0px 0px 10px;'>".Html::textInput('sort', '', ['label' => 'Sort','class'=>'form-control placeholder','placeholder'=>Yii::t('admin',"Сортировка"),'data-text'=>Yii::t('admin',"Сортировка")]). "</div>";

    echo '<b style="display: block; margin-bottom: 5px;">'.Yii::t('admin','Введите слова для поиска вариации').'</b>';
    echo Html::input('text', 'string', Yii::$app->request->post('string'), ['class' => 'form-control', 'minlength' => 2, 'maxlength' => 50]);
    //echo Html::input('text', 'string', Yii::$app->request->post('string'), ['class' => 'form-control', 'minlength' => 4, 'maxlength' => 10]);
    echo Html::submitButton(Yii::t('admin','Найти'), ['class' => 'btn btn-primary', 'name' => 'hash-button']);
    echo Html::endForm();
    echo "<br>";

   // $userRolesModel = new UserRoles();

    //print_r($stringHash);
    if(!empty($stringHash)) {
    echo '<b>Выберите нужную вариацию из списка:</b><br>';

    foreach($stringHash as $item)
    {

        echo Html::a($item->id." ".$item->getTitleWithProperties(), '/list/variationadd?id='
                . $item->id
                . '&list_id='
                . $model->id
                . '&amount='.$item->amount
                . '&sort='.$item->sort
            ) . "<br>";

    }

//    foreach ($stringHash as $item) {
//        echo Html::a($item['id'] . " " . $item['full_name'], '/list/variationadd?id='
//                . $item['id']
//                . '&list_id='
//                . $model->id
//                . '&amount='.$item['amount']
//                . '&sort='.$item['sort'] ) . "<br>";
//    }
//    }else {
//            echo "<br>";
    }
    Pjax::end();


    Modal::end();

    ///////////////////////////////////////////////////////////
    //        MODAL USERS BEGIN
    //////////////////////////////////////////////////////////

    $counter = \app\modules\catalog\models\ListsGoods::find()->where('list_id')->asArray()->all();

    if(count($counter)>0)
    {




    echo GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            //'id',
            'variation_id',
            [
                    'attribute' => 'variation',
                    'label' => 'Название вариации',
                    'content' => function($data){

                        $query = \app\modules\catalog\models\GoodsVariations::find()
                               // ->select('full_name')
                                ->where('id = '.$data['variation_id'])
                                ->one();

                        //var_dump($query);die();

                        if(!empty($query))
                            return $query->getTitleWithProperties();
                        else
                            return '';

                    }
            ],
      //      'good_id',
            'amount',
            'sort',
            [
                'attribute' => 'status',
                'label' => 'Статус',
                'content' => function($data){
                    if($data['status'] == 1)
                        return 'Активный';
                    else
                        return 'Не активный';

                }
            ],

            //  ['class' => 'yii\grid\ActionColumn'],
            [
                'class' => 'yii\grid\ActionColumn',
                'header'=>'Действия',
                'headerOptions' => ['width' => '80'],
                'template' => '{update} {delete}',
                'buttons' => [
//                    'view' => function ($url,$model) {
//                        return Html::a('<span class="glyphicon glyphicon-share"></span>',
//                            '/shops/view?id='.$model->id);
//                    },
                    'update' => function ($url,$model) use ($id) {
                        return Html::a('<span class="glyphicon glyphicon-edit"></span>',
                            '/list/updatelist?id='.$model->id.'&modelId='.$id);
                    },
                    'delete' => function ($url,$model) use ($id) {
                        return Html::a('<span class="glyphicon glyphicon-trash"></span>',
                            '/list/deletelist?id='.$model->id.'&model='.$id);
                    },

                ],

            ],
        ],
    ]);


    }

    ?>


</div>
