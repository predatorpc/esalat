<?php

namespace app\modules\pages\models;

use Yii;

/**
 * This is the model class for table "pages_menus".
 *
 * @property integer $id
 * @property string $key
 * @property integer $page_id
 * @property string $name
 * @property string $anchor
 * @property integer $position
 * @property integer $status
 */
class PagesMenus extends \app\modules\common\models\ActiveRecordRelation
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'pages_menus';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['page_id', 'position', 'status'], 'integer'],
            [['key'], 'string', 'max' => 32],
            [['name', 'anchor'], 'string', 'max' => 64]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'key' => 'Key',
            'page_id' => 'Page ID',
            'name' => 'Name',
            'anchor' => 'Anchor',
            'position' => 'Position',
            'status' => 'Status',
        ];
    }

    public function getPages(){
	return $this->hasMany(Pages::classname(),['id' => 'page_id']);
    }
    //Меню ЛК;
    public static function getPagesMyMenu() {
        /*Меню ЛК*/
        $subItems = [];
        if(!Yii::$app->user->isGuest) {
            $subItems[] = [
                'label' => Yii::t('app','Мои адреса'),
                'url'   => '/my/'
            ];
            $subItems[] = [
                'label' => Yii::t('app','Операции с балансом'),
                'url'   => '/my/balance-operation/'
            ];
        }
        if(!Yii::$app->user->isGuest) {
            $subItems[] =   [
                'label' => Yii::t('app','История заказов'),
                'url' => '/my/orders-history/'
            ];
            $subItems[] =   [
                'label' => Yii::t('app','Мои списки'),
                'url' => '/my/product-list/'
            ];
            $subItems[] =   [
                'label' => Yii::t('app','Список желаний'),
                'url' => '/my/wish-list/'
            ];
            $subItems[] =   [
                'label' => Yii::t('app','Промо код'),
                'url' => '/my/promo/'
            ];
            $subItems[] =   [
                'label' => Yii::t('app','Обратная связь'),
                'url' => '/my/feedback/'
            ];
        }
       if(Yii::$app->user->can('GodMode') || Yii::$app->user->can('categoryManager') || Yii::$app->user->can('callcenterOperator')  || Yii::$app->user->can('clubAdmin')  || Yii::$app->user->can('HR') || Yii::$app->user->can('conflictManager') || Yii::$app->user->can('shopOwner')){
/*        if(Yii::$app->user->can('GodMode') || Yii::$app->user->can('categoryManager') || Yii::$app->user->can('callcenterOperator')  || Yii::$app->user->can('clubAdmin')
            || Yii::$app->user->can('conflictManager') || Yii::$app->user->can('shopOwner')){*/
            $subItems[] =   [
                'label' => Yii::t('app','Управление магазином'),
                'url' => '/shop-management/'
            ];
            $subItems[] =   [
                'label' => Yii::t('app','Личный кабинет поставщика'),
                'url' => '/shop/'
            ];
        }
        return $subItems;
    }


}
