<?php

namespace app\modules\common\models;

use app\modules\catalog\models\Goods;
use app\modules\catalog\models\GoodsVariations;
use Yii;
use yii\db\Query;

/**
 * This is the model class for table "users_logs".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $good_id
 * @property integer $variations_id
 * @property integer $type
 * @property string $created_at
 * @property string $updated_at
 * @property integer $status
 *
 * @property Goods $good
 * @property User $user
 * @property GoodsVariations $variations
 */
class UsersLogs extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'users_logs';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id'], 'required'],
            [['user_id', 'good_id', 'variations_id', 'type', 'status'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['good_id'], 'exist', 'skipOnError' => true, 'targetClass' => Goods::className(), 'targetAttribute' => ['good_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
            [['variations_id'], 'exist', 'skipOnError' => true, 'targetClass' => GoodsVariations::className(), 'targetAttribute' => ['variations_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('admin', 'ID'),
            'user_id' => Yii::t('admin', 'User ID'),
            'good_id' => Yii::t('admin', 'Good ID'),
            'variations_id' => Yii::t('admin', 'Variations ID'),
            'type' => Yii::t('admin', 'Type'),
            'created_at' => Yii::t('admin', 'Created At'),
            'updated_at' => Yii::t('admin', 'Updated At'),
            'status' => Yii::t('admin', 'Status'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGood()
    {
        return $this->hasOne(Goods::className(), ['id' => 'good_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getVariations()
    {
        return $this->hasOne(GoodsVariations::className(), ['id' => 'variations_id']);
    }
}
