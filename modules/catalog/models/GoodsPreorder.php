<?php

namespace app\modules\catalog\models;

use Yii;

/**
 * This is the model class for table "goods_preorder".
 *
 * @property integer $id
 * @property integer $good_variant_id
 * @property integer $count
 * @property string $date
 * @property integer $status
 */
class GoodsPreorder extends \yii\db\ActiveRecord
{
    public $summ=0;
    public $addsumm=0;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'goods_preorder';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['good_variant_id', 'count'], 'required'],
            [['good_variant_id', 'count', 'status'], 'integer'],
            [['date'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'good_variant_id' => Yii::t('app', 'Good Variant ID'),
            'count' => Yii::t('app', 'Count'),
            'date' => Yii::t('app', 'Date'),
            'status' => Yii::t('app', 'Status'),
        ];
    }

    public function getVariation(){
        return $this->hasOne(GoodsVariations::className(), ['id'=>'good_variant_id'])->with('product');
    }
}
