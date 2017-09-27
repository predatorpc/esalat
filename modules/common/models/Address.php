<?php

namespace app\modules\common\models;

use Yii;

/**
 * This is the model class for table "address".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $district_id
 * @property integer $delivery_id
 * @property string $street
 * @property string $house
 * @property string $room
 * @property string $map
 * @property string $comments
 * @property string $phone
 * @property string $date
 * @property integer $status
 *
 * @property RegionsDistricts $district
 * @property Users $user
 * @property OrdersGroups[] $ordersGroups
 */
class Address extends ActiveRecordRelation
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'address';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'district_id', 'status'], 'integer'],
            [['street'], 'required'],
            [['comments'], 'string'],
            //[['date'], 'safe'],
            //[['street'], 'string', 'max' => 64],
            [['street'], 'match', 'pattern' => '/(\D+)/i'],
            [['house', 'phone'], 'string', 'max' => 16],
            [['room'], 'string', 'max' => 8],
            [['map'], 'string', 'max' => 32],
            [['phone', 'delivery_id', 'date'],'safe'], // не убирать там валидируется на JS
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'district_id' => Yii::t('app', 'Район'),
            'delivery_id' => 'ID delivery',
            'district' => Yii::t('app', 'Район'),
            'city' => Yii::t('app', 'Город'),
            'street' => Yii::t('app', 'Улица'),
            'house' => Yii::t('app', 'Дом'),
            'room' => Yii::t('app', 'Квартира'),
            'map' => Yii::t('app', 'Карта'),
            'comments' => Yii::t('app', 'Комментарий'),
            'phone' => Yii::t('app', 'Телефон'),
            'date' => Yii::t('app', 'Дата'),
            'status' => Yii::t('app', 'Статус'),
        ];
    }



    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDistrict()
    {
        return $this->hasOne(RegionsDistricts::className(), ['id' => 'district_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(Users::className(), ['id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrdersGroups()
    {
        return $this->hasMany(OrdersGroups::className(), ['address_id' => 'id']);
    }

    public function getConcatAddress(){
        return $this->street . ", " . $this->house;
    }

    public function getConcatAddressFull(){
        $addressStrin = '';
        if(!empty($this->street)){
            $addressStrin .= $this->street;
        }
        if(!empty($this->house)){
            $addressStrin .= ", " . $this->house;
        }
        if(!empty($this->room)){
            $addressStrin .= ", " . $this->room;
        }
        return $addressStrin;
    }
}
