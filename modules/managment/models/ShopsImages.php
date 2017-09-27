<?php

namespace app\modules\managment\models;

use Yii;

/**
 * This is the model class for table "shops_images".
 *
 * @property integer $id
 * @property integer $shop_id
 * @property string $hash
 * @property string $date
 * @property integer $status
 */
class ShopsImages extends \app\modules\common\models\ActiveRecordRelation
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'shops_images';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['shop_id', 'hash'], 'required'],
            [['shop_id', 'status'], 'integer'],
            [['date'], 'safe'],
            [['hash'], 'string', 'max' => 64]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'shop_id' => 'Shop ID',
            'hash' => 'Hash',
            'date' => 'Date',
            'status' => 'Status',
        ];
    }
}
