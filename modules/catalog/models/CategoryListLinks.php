<?php

namespace app\modules\catalog\models;

use Yii;

/**
 * This is the model class for table "category_list_links".
 *
 * @property integer $id
 * @property integer $category_id
 * @property integer $list_id
 */
class CategoryListLinks extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'category_list_links';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['category_id', 'list_id'], 'required'],
            [['category_id', 'list_id'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'category_id' => 'Category ID',
            'list_id' => 'List ID',
        ];
    }
}
