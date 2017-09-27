<?php

namespace app\modules\common\models;

use Yii;

/**
 * This is the model class for table "countries".
 *
 * @property integer $id
 * @property string $name
 * @property integer $position
 * @property integer $status
 */
class RegionDistrict extends ActiveRecordRelation
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'regions_districts';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['city_id', 'status'], 'integer'],
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
            'city_id' => 'city_id',
            'name' => 'name',
            'status' => 'Status',
        ];
    }

    public function getRegions_cities(){
        return $this->hasOne(RegionsCities::classname(),['id' => 'city_id']);
    }
}
