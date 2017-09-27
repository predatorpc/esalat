<?php

namespace app\modules\common\models;

use app\modules\catalog\models\Category;
use Yii;

/**
 * This is the model class for table "alert_msg_tpl".
 *
 * @property integer $id
 * @property integer $category_id
 * @property string $text
 * @property string $created_at
 * @property string $updated_at
 * @property integer $status
 *
 * @property Category $category
 */
class AlertMsgTpl extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'alert_msg_tpl';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['category_id', 'status'], 'integer'],
            [['text'], 'required'],
            [['created_at', 'updated_at'], 'safe'],
            [['text'], 'string', 'max' => 255],
            [['category_id'], 'exist', 'skipOnError' => true, 'targetClass' => Category::className(), 'targetAttribute' => ['category_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('admin', 'ID'),
            'category_id' => Yii::t('admin', 'Category ID'),
            'text' => Yii::t('admin', 'Text'),
            'created_at' => Yii::t('admin', 'Created At'),
            'updated_at' => Yii::t('admin', 'Updated At'),
            'status' => Yii::t('admin', 'Status'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(Category::className(), ['id' => 'category_id']);
    }
}
