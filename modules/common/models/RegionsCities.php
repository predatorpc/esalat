<?php

namespace app\modules\common\models;

use Yii;

/**
 * This is the model class for table "regions_cities".
 *
 * @property integer $id
 * @property integer $region_id
 * @property string $name
 * @property integer $position
 * @property integer $status
 *
 * @property Regions $region
 * @property RegionsDistricts[] $regionsDistricts
 * @property ShopsStores[] $shopsStores
 * @property Users[] $users
 */
class RegionsCities extends ActiveRecordRelation
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'regions_cities';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['region_id', 'position', 'status'], 'integer'],
            [['name'], 'string', 'max' => 64]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'region_id' => 'Region ID',
            'name' => 'Name',
            'position' => 'Position',
            'status' => 'Status',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRegion()
    {
        return $this->hasOne(Regions::className(), ['id' => 'region_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRegionsDistricts()
    {
        return $this->hasMany(RegionsDistricts::className(), ['city_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getShopsStores()
    {
        return $this->hasMany(ShopsStores::className(), ['city_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsers()
    {
        return $this->hasMany(Users::className(), ['city_id' => 'id']);
    }
}
