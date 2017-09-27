<?php

namespace app\modules\catalog\models;

use app\modules\catalog\models\GoodsVariations;
use app\modules\catalog\models\Goods;
use Yii;

/**
 * This is the model class for table "lists_goods".
 *
 * @property integer $id
 * @property integer $list_id
 * @property integer $good_id
 * @property integer $amount
 * @property string $date_create
 * @property integer $sort
 * @property integer $status
 * @property integer $variation_id
 *
 * @property Goods $good
 * @property Lists $list
 */
class ListsGoods extends \yii\db\ActiveRecord
{
    public $variation;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'lists_goods';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['list_id', 'variation_id'], 'required'],
            [['list_id', 'variation_id', 'amount', 'sort', 'status'], 'integer'],
            [['date_create', 'good_id'], 'safe'],
            //[['variation_id'], 'exist', 'skipOnError' => true, 'targetClass' => GoodsVariations::className(), 'targetAttribute' => ['variation_id' => 'id']],
            //[['list_id'], 'exist', 'skipOnError' => true, 'targetClass' => Lists::className(), 'targetAttribute' => ['list_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'list_id' => Yii::t('admin','ID Списка'),
            'good_id' => Yii::t('admin','ID Товара'),
            'variation_id' => Yii::t('admin','ID Вариации'),
            'amount' => Yii::t('admin','Кол-во'),
            'date_create' => Yii::t('admin','Дата создания'),
            'sort' => Yii::t('admin','Сортировка'),
            'status' => Yii::t('admin','Статус'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGood()
    {
        return $this->hasOne(Goods::className(), ['id' => 'good_id']);
    }

    public function getVariant()
    {
        return $this->hasOne(GoodsVariations::className(), ['id' => 'variation_id']);
    }

    public function getProduct()
    {
        if (empty($this->variant)) return false;
        return Goods::findOne($this->variant->good_id);
    }


//    public function getVariation()
//    {
//        return $this->hasMany(GoodsVariations::className(), ['id' => 'variation_id']);
//    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getList()
    {
        return $this->hasOne(Lists::className(), ['id' => 'list_id']);
    }
}
