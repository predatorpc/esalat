<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">
    google.charts.load('current', {packages: ['corechart', 'bar']});
</script>
<?php

// \app\modules\common\models\Zloradnij::print_arr($array);

use app\modules\common\models\User;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\grid\GridView;
use \app\modules\common\models\Profile;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ListsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */


$this->title = Yii::t('admin', 'Отчет по профайлам');
$this->params['breadcrumbs'][] = $this->title;
?>

<h1><?= Html::encode($this->title) ?></h1><br><br><br><br>

<div id="profile-report">

    <div class="row">
        <div class="col-md-12">

            <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'tableOptions' => [
                    'class' => 'table table-striped table-bordered'
                ],
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],

                    [
                        'attribute' => 'user_id',
                        'label' => Yii::t('admin', 'Имя'),
                        'format' => 'raw',
                        'filter' => ArrayHelper::map(User::find()->select('id, CONCAT_WS(" ", name, phone)  as `name`')->orderBy('name')->asArray()->all(), 'id', 'name'),
                        //'filter' = true,
                        'content' => function ($data) {
                            if (!empty($data['user_id'])) {
                                $user = \app\modules\common\models\User::find()->where(
                                    'id = ' . $data['user_id']
                                )->one();
                                if (!empty($user)) {
                                    return Html::a($user->name, '/user/view?id=' . $data['user_id'], ['target' => '_blank']);
                                } else {
                                    return '';
                                }
                            } else {
                                return '';
                            }
                        }

                    ],

                    [
                        'attribute' => 'gender',
                        'label' => Yii::t('admin', 'Пол'),
                        'filter' => array('1' => Yii::t('admin', 'Мужской'), '2' => Yii::t('admin', 'Женский')),
                        'content' => function ($data) {
                            if ($data['gender'] == 1)
                                return Yii::t('admin', 'Мужской');
                            else
                                return Yii::t('admin', 'Женский');

                        }
                    ],

                    [
                        'attribute' => 'profileLinks.rate',
                        'label' => Yii::t('admin', 'Индекс'),
                        'format' => 'raw',
                        'content' => function ($data) {
                            //print_r($data);
                            return '<b>' . $data['profileLinks']['rate'] . '</b>';

                        }
                    ],

                    [
                        'attribute' => 'pets',
                        'label' => Yii::t('admin', 'Животные'),
                        'filter' => array('1' => Yii::t('admin', 'Есть')),
                        'content' => function ($data) {
                            if ($data['pets'] == 1)
                                return '<div style="color: green;">'.Yii::t('admin', 'Есть').'</div>';
                            else
                                return '<div style="color: blue;">'.Yii::t('admin', 'Неизвестно').'</div>';

                        }
                    ],
                    [
                        'attribute' => 'children',
                        'label' => Yii::t('admin', 'Дети'),
                        'filter' => array('1' => Yii::t('admin', 'Есть')),
                        'content' => function ($data) {
                            if ($data['children'] == 1)
                                return '<div style="color: green;">'.Yii::t('admin', 'Есть').'</div>';
                            else
                                return '<div style="color: blue;">'.Yii::t('admin', 'Неизвестно').'</div>';

                        }
                    ],
                    [
                        'attribute' => 'car',
                        'label' => Yii::t('admin', 'Машина'),
                        'filter' => array('1' => Yii::t('admin', 'Есть')),
                        'content' => function ($data) {
                            if ($data['car'] == 1)
                                return '<div style="color: green;">'.Yii::t('admin', 'Есть').'</div>';
                            else
                                return '<div style="color: blue;">'.Yii::t('admin', 'Неизвестно').'</div>';
                        }
                    ],

                    [
                        'attribute' => 'status',
                        'label' => Yii::t('admin', 'Статус'),
                        'filter' => array('1' => Yii::t('admin', 'Активный'), '0' => Yii::t('admin', 'Неактивный')),
                        'content' => function ($data) {
                            if ($data['status'] == 1)
                                return '<div style="color: green;">'.Yii::t('admin', 'Активный').'</div>';
                            else
                                return '<div style="color: red;">'.Yii::t('admin', 'Неактивный').'</div>';

                        }
                    ],

                    [
                        'class' => 'yii\grid\ActionColumn',
                        'header' => Yii::t('admin', 'Профайл'),
                        'headerOptions' => ['width' => '30'],
                        'template' => '{view}',
                        'buttons' => [
                            'view' => function ($url,$model) {
                                return Html::a(Yii::t('admin', 'Открыть'), ['/reports/profile?id='.$model->user_id], ['target'=>'_blank']);
                            },
                        ],
                    ],

                ],
            ]); ?>


        </div>
    </div>

</div> <!--./report-sales-->

<?php // \app\modules\common\models\Zloradnij::print_arr($array); ?>

