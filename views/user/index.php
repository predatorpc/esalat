<?php

use yii\widgets\Pjax;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;
use yii\widgets\Breadcrumbs;
use app\modules\pages\models\SignupForm;
//use app\modules\common\models\UserTableForm;
use app\modules\common\models\UserAdmin;
use yii\data\ActiveDataProvider;
use yii\rbac\Rule;
//use app\modules\common\models\AuthItem;
use yii\db\ActiveRecord;
use yii\db\Query;

/* @var $this yii\web\View */
/* @var $searchModel app\models\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('admin', 'Управление пользователями');
$this->params['breadcrumbs'][] = ['label' => Yii::t('admin', 'Домой'), 'url' => ['/'], ['id' => '1']];
$this->params['breadcrumbs'][] = $this->title;

if(!Yii::$app->user->identity)
{
	return Yii::$app->controller->Gohome();
}

if(1)
{

Yii::$app->session['filter']=$_SERVER['REQUEST_URI'];

?>
<div class="useradmin-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?=Html::a(Yii::t('admin', 'Добавить Пользователя'). ' +', ['create'], ['class' => 'btn btn-success white no-border']);?>
    </p>


 <?php 

    $auth = Yii::$app->authManager;
    $authors = $auth->getRoles();
//
////var_dump($model);
////	where(['id' => $model->id])->all();
//
// формируем массив, с ключем равным полю 'id' и значением равным полю 'name'
    $items = ArrayHelper::map($authors,'name','description');
    $params = [
        'prompt' => Yii::t('admin', 'Выбрать категорию')
    ];


echo	GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
		'layout' =>"{pager}\n{summary}\n{items}\n{pager}\n",

        'columns' => [
	        ['class' => 'yii\grid\SerialColumn'],
//         	'id', 'name','fullname',
		 	[
			'class' => 'yii\grid\DataColumn', // this line is optional
		    'attribute' => 'id',
			'label' => 'ID',
			//'value' => 'ID Пользователя',
//			'format' => 'raw',
			'contentOptions' => ['style' => 'width: 10px;'],

			],
				[
						'attribute' => 'phone',
						'label' => Yii::t('admin', 'Телефон'),
				],

			[
					'attribute' => 'secret_word',
					'label' => Yii::t('admin', 'Секретное слово'),
			],

			[
//			'header' => 'Login',
			'attribute' => 'name',
//			'value' => 'name',
			'label' => Yii::t('admin', 'ФИО'),
			'format'=>'raw',
			'value' => function ($data, $url, $model){
				    
//        		            return Html::a($data['name'],"index.php?r=user/update&id=".$url);
							$code = \app\modules\catalog\models\Codes::find()->where('user_id = '.$url)->one();
							if(empty($code->code)) $promocode = "";
							else $promocode = $code->code;
        		            return Html::a($data['name']."<br>".Yii::t('admin', 'Промо-код')." (".$promocode.")","/user/update?id=".$url);
        		            
			},
//			'contentOptions' => ['style' => 'width: 10px;'],
		],
			'money',
			'bonus',
	  	  [
            'attribute'=>'role_name',
            'label'=> Yii::t('admin', 'Первичная роль'),
            'format'=>'text', // Возможные варианты: raw, html
            'filter' => $items,
			'content' => function($data, $model){
				$sql = Useradmin::find()
						->select([
								'id',
								'auth_assignment.user_id as user_id',
								'auth_item.name as role_name',
								'auth_item.description as role_description'
						])
					->leftjoin('auth_assignment','auth_assignment.user_id = users.id')
					->leftjoin('auth_item','auth_item.name = auth_assignment.item_name')
					->where(['auth_assignment.user_id' => $model])
//					->orderby('id')
					->asarray()
					->one();

					if(empty($sql))
						return Yii::t('admin', 'Пользователь');

//					if($sql['role_name']=='GodMode')
//						$response = 'Superadmin';
//					else
						$response = $sql['role_description'];

				//if($sql['role_name']!='user')
	                return $response;
	                
				},
        ],

            [
                'attribute' => 'typeof',
                'value' => function ($data){
                    if($data['typeof']>0) {
                        $typeof = [1 => 'Администратор клуба',
                            2 => 'Персональный тренер',
                            3 => 'Тренер групповых тренировок',
                            4 => 'Управление',
                            5 => 'Фитнес консультант'
                        ];
                        return $typeof[$data['typeof']];
                    }else{
                        return '-';
                    }
                },
            ],
//			'registration',
			[
					'attribute' => Yii::t('admin', 'Регистрация'),
					'label' => Yii::t('admin', 'Регистрация (OLD)'),
					'value' => function ($data, $url, $model){
						return $data['registration'];
					},
			],
//			'created_at',
			[
					'attribute' => 'created_at',
					'label' => Yii::t('admin', 'Регистрация'),
					'value' => function ($data, $url, $model){
						return date("Y-m-d H:i:s",$data['created_at']);
					},
			],
				[
						'attribute' => 'staff',
						'label' => Yii::t('admin', 'Сотрудник?'),
						'content' => function($data) {
							return ($data['staff']!=NULL || $data['staff']!=0) ? Yii::t('admin', 'Да'): Yii::t('admin', 'Нет');
						},
				],
			[
				'attribute' => 'sms',
				'label' => Yii::t('admin', 'СМС'),
				'content' => function($data,$model) {
					return '<a href="/support/sms-send?user_id='.$data['id'].'">Отправлять СМС</a>';
				},
			],
           ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); 


}
else {
?>

<h1><?= Yii::t('admin', 'Недостаточно привелегий!') ?></h1>

<?php

}
?>
</div>
