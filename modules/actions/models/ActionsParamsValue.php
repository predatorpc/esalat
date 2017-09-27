<?php

namespace app\modules\actions\models;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "actions_params_value".
 *
 * @property integer $id
 * @property integer $action_id
 * @property integer $param_id
 * @property string $object
 * @property string $value
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $created_user
 * @property integer $updated_user
 */
class ActionsParamsValue extends \yii\db\ActiveRecord
{
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at',
            ],
            [
                'class' => BlameableBehavior::className(),
                'createdByAttribute' => 'created_user',
                'updatedByAttribute' => 'updated_user',
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'actions_params_value';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        /*
        return [
            [['action_id', 'param_id', 'value'], 'required'],
            [['action_id', 'param_id', 'created_at', 'updated_at', 'created_user', 'updated_user'], 'integer'],
            [['value','object'], 'string', 'max' => 200],
        ];*/
        return [
            [['action_id', 'param_id', 'discont_value', ], 'required'],
            [['action_id', 'param_id', 'condition_value', 'basket_price',  'created_at', 'updated_at', 'created_user', 'updated_user','status'], 'integer'],
            ['discont_value', 'number'],
            [['action_id', 'param_id', 'condition_value'], 'unique', 'targetAttribute' => ['action_id', 'param_id', 'condition_value'], 'message' => 'Акция с таими параметрами уже существует'],
            [['action_id'], 'exist', 'skipOnError' => true, 'targetClass' => Actions::className(), 'targetAttribute' => ['action_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'action_id' => 'Action ID',
            'param_id' => 'Param ID',
            'condition_value' => 'Condition Value',
            'basket_price' => 'Basket Price',
            'discont_value' => 'Discont Value',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'created_user' => 'Created User',
            'updated_user' => 'Updated User',
        ];
    }

    public function getAction()
    {
        return $this->hasOne(Actions::className(), ['id' => 'action_id']);
    }

    public function getParam(){
        return $this->hasOne(ActionsParams::className(), ['id'=>'param_id']);
    }
    public function getParamAccum(){
        return $this->hasOne(ActionsParams::className(), ['id'=>'action_param_value_id']);
    }

}
