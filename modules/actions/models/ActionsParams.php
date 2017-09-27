<?php

namespace app\modules\actions\models;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "actions_params".
 *
 * @property integer $id
 * @property string $title
 * @property string $description
 * @property integer $type
 * @property integer $object
 * @property integer $one_or_many
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $created_user
 * @property integer $updated_user
 */
class ActionsParams extends \yii\db\ActiveRecord
{
    private $objectList = [
        'productObject',
        'productTypeObject',
        'variantObject',
        'categoryObject',
        'deliveryObject',
        'basketObject',
        'groupObject',
    ];
    private $typeList = [
        'priceType',
        'percentType',
        'bonusType',
    ];
    private $typeAction = [
        'discount',
        'cashback',
    ];

    public function getObjectList(){
        $objectList = [];
        foreach ($this->objectList as $item) {
            $objectList[] = Yii::t('actions', $item);
        }
        return $objectList;
    }

    public function getTypeList(){
        $typeList = [];
        foreach ($this->typeList as $item) {
            $typeList[] = Yii::t('actions', $item);
        }
        return $typeList;
    }

    public function getTypeAction(){
        $typeActionlist = [];
        foreach ($this->typeAction as $item) {
            $typeActionlist[] = Yii::t('actions', $item);
        }
        return $typeActionlist;
    }

    public function getPeriodic(){
        $periodicList = [];
        foreach ($this->periodicList as $item) {
            $periodicList[] = Yii::t('actions', $item);
        }
        return $periodicList;
    }

    public function getObjectValue(){
        return Yii::t('actions', $this->objectList[$this->object]);
    }

    public function getTypeValue(){
        return Yii::t('actions', $this->typeList[$this->type]);
    }

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

    public static function tableName()
    {
        return 'actions_params';
    }

    public function rules()
    {
        /*
        return [
            [['type', 'area', 'currency'], 'integer'],
            [['id'], 'string', 'max' => 44],
            [['title'], 'string', 'max' => 500],
        ];*/
        return [
            [['id', 'type', 'area', 'currency', 'title'], 'required'],
            [['id', 'type', 'area', 'currency', 'status'], 'integer'],
            [['title'], 'string', 'max' => 50],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'type' => 'Type',
            'area' => 'Area',
            'currency' => 'Currency',
            'title' => 'Title',
            'status' => 'Status',
        ];
    }
}
