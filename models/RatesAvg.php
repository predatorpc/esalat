<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "rates_avg".
 *
 * @property integer $id
 * @property string $name
 * @property integer $rate
 * @property integer $date
 */
class RatesAvg extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'rates_avg';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['rate'], 'number'],
            [['date'], 'integer'],
            [['name'], 'string', 'max' => 255],
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
            'rate' => 'Rate',
            'date' => 'Date',
        ];
    }
}
