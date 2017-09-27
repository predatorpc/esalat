<?php

namespace app\modules\catalog\models;

use Yii;

/**
 * This is the model class for table "category_links".
 *
 * @property integer $id
 * @property integer $category_id
 * @property integer $product_id
 */
class CategoryLinks extends \app\modules\common\models\ActiveRecordRelation
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'category_links';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['category_id', 'product_id'], 'required'],
            [['category_id', 'product_id'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'category_id' => 'Category ID',
            'product_id' => 'Product ID',
        ];
    }

    public function getCategory(){
        return $this->hasOne(Category::className(),['id' => 'category_id']);
    }

    public function getProduct(){
        return $this->hasOne(Goods::className(),['id' => 'product_id']);
    }

    public function getCategoryProduct(){
        return $this->hasMany(Goods::className(),['id' => 'product_id'])->where(['category_id' => $this->category_id]);
    }
}
