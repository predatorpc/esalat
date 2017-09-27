<?php

namespace app\modules\basket\models;

use Yii;

/**
 * This is the model class for table "codes_types".
 *
 * @property integer $id
 * @property string $name
 * @property string $discount
 * @property string $fee
 * @property integer $show
 * @property integer $status
 *
 * @property Codes[] $codes
 */
class PromoCodeType extends \app\modules\common\models\ActiveRecordRelation
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'codes_types';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name','shop_id','money_discount'],'required'],
            [['discount', 'fee'], 'number'],
            [['show', 'status','shop_id','money_discount'], 'integer'],
            [['name'], 'string', 'max' => 64],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'discount' => 'Discount',
            'fee' => 'Fee',
            'show' => 'Show',
            'status' => 'Status',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCodes()
    {
        return $this->hasMany(Codes::className(), ['type_id' => 'id']);
    }
}
