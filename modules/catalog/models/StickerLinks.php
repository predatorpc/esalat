<?php

namespace app\modules\catalog\models;

use Yii;

/**
 * This is the model class for table "sticker_links".
 *
 * @property integer $id
 * @property integer $sticker_id
 * @property integer $good_id
 * @property integer $status
 *
 * @property Sticker $sticker
 * @property Goods $good
 */
class StickerLinks extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'sticker_links';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['sticker_id', 'good_id'], 'required'],
            [['sticker_id', 'good_id', 'status'], 'integer'],
            [['sticker_id'], 'exist', 'skipOnError' => true, 'targetClass' => Sticker::className(), 'targetAttribute' => ['sticker_id' => 'id']],
            [['good_id'], 'exist', 'skipOnError' => true, 'targetClass' => Goods::className(), 'targetAttribute' => ['good_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'sticker_id' => 'Sticker ID',
            'good_id' => 'Good ID',
            'status' => 'Status',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSticker()
    {
        return $this->hasOne(Sticker::className(), ['id' => 'sticker_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGood()
    {
        return $this->hasOne(Goods::className(), ['id' => 'good_id']);
    }
}
