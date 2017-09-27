<?php

namespace app\modules\pages\models;

use Yii;

/**
 * This is the model class for table "pages_options".
 *
 * @property integer $id
 * @property integer $page_id
 * @property integer $type
 * @property string $name
 * @property string $key
 * @property string $value
 * @property integer $status
 */
class PagesOptions extends \app\modules\common\models\ActiveRecordRelation
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'pages_options';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['page_id', 'type', 'status'], 'integer'],
            [['value'], 'required'],
            [['value'], 'string'],
            [['name'], 'string', 'max' => 128],
            [['key'], 'string', 'max' => 64]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'page_id' => 'Page ID',
            'type' => 'Type',
            'name' => 'Name',
            'key' => 'Key',
            'value' => 'Value',
            'status' => 'Status',
        ];
    }
    // Загрузка параметров;
    public static function pagesOptions() {
        $pagesOptions = PagesOptions::find()->select('value')->from('pages_options')->where(['status' => 1])->indexBy('key')->column();
        return  $pagesOptions;
    }
}
