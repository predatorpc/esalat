<?php

namespace app\modules\managment\models;

use app\modules\catalog\models\Goods;
use Yii;
use yii\data\ActiveDataProvider;
use yii\db\Query;

/**
 * This is the model class for table "category".
 *
 * @property integer $id
 * @property integer $parent_id
 * @property integer $level
 * @property string $title
 * @property string $alias
 * @property integer $sort
 * @property integer $active
 *
 * @property Category $parent
 * @property Category[] $categories
 */
class ShopGroupVariantLink extends \app\modules\common\models\ActiveRecordRelation
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'shop_group_variant_link';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['shop_group_id', 'product_id'], 'integer'],
            [['shop_group_id','product_id'], 'required'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'shop_group_id' => Yii::t('admin', 'Группа Магазинов'),
            'product_id' => Yii::t('admin', 'Вариант Id'),
        ];
    }

    public function getShopGroup(){
        return $this->hasOne(ShopGroup::className(), ['id' => 'shop_group_id']);
    }
    public function getProducts(){
        return $this->hasOne(Goods::className(), ['id' => 'product_id']);
    }

}