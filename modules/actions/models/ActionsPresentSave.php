<?php

namespace app\modules\actions\models;

use Yii;

/**
 * This is the model class for table "actions_present_save".
 *
 * @property integer $id
 * @property integer $basket_id
 * @property integer $present
 * @property integer $status
 *
 * @property Basket $basket
 */
class ActionsPresentSave extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'actions_present_save';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['basket_id'], 'required'],
            [['user_id', 'basket_id', 'present', 'status'], 'integer'],
            [['card_number'], 'string'],
            [['create_date', 'update_date', 'bought_date'], 'safe'],
            //[['basket_id'], 'exist', 'skipOnError' => true, 'targetClass' => Basket::className(), 'targetAttribute' => ['basket_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'user_id' => Yii::t('app', 'User ID'),
            'basket_id' => Yii::t('app', 'Basket ID'),
            'present' => Yii::t('app', 'Present'),
            'status' => Yii::t('app', 'Status'),
            'create_date'=>Yii::t('app', 'Create date'),
            'update_date'=>Yii::t('app', 'Update date'),
            'bought_date'=>Yii::t('app', 'Bought date'),
            'card_number'=>Yii::t('app', 'Card Number'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    /*public function getBasket()
    {
        return $this->hasOne(Basket::className(), ['id' => 'basket_id']);
    }*/
}
