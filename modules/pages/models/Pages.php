<?php

namespace app\modules\pages\models;

use Yii;

/**
 * This is the model class for table "pages".
 *
 * @property integer $id
 * @property integer $page_id
 * @property string $url
 * @property string $name
 * @property string $text
 * @property string $template
 * @property string $unit
 * @property integer $level
 * @property string $seo_title
 * @property string $seo_description
 * @property string $seo_keywords
 * @property integer $status
 */
class Pages extends \app\modules\common\models\ActiveRecordRelation
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'pages';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['page_id', 'level', 'status'], 'integer'],
            [['text'], 'required'],
            [['text'], 'string'],
            [['url', 'name'], 'string', 'max' => 64],
            [['template', 'unit'], 'string', 'max' => 32],
            [['seo_title', 'seo_description', 'seo_keywords'], 'string', 'max' => 128]
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
            'url' => 'Url',
            'name' => 'Name',
            'text' => 'Text',
            'template' => 'Template',
            'unit' => 'Unit',
            'level' => 'Level',
            'seo_title' => 'Seo Title',
            'seo_description' => 'Seo Description',
            'seo_keywords' => 'Seo Keywords',
            'status' => 'Status',
        ];
    }

    // Загрузка меню для сайта;
    public static function getPagesMenu()
    {
        $db = Yii::$app->getDb();
        $pagesMenus = Pages::find()->select('key','')->from('pages_menus')->where(['status' => 1])->orderBy(['key'=>SORT_ASC,])->column();
        $pagesMenus = array_flip($pagesMenus);
        // Формируем список страницы;
        foreach($pagesMenus as $key=>$value) {
            $pagesMenus[$key] = $db->createCommand("SELECT `pages`.`id`, `pages`.`url`, IF (`pages_menus`.`name` IS NOT NULL, `pages_menus`.`name`, `pages`.`name`) AS `name`, `pages_menus`.`anchor` FROM `pages_menus` LEFT JOIN `pages` ON `pages`.`id` = `pages_menus`.`page_id` WHERE `pages_menus`.`key` = '".$key."' AND  `pages_menus`.`status` = '1' AND `pages`.`status` = '1' ORDER BY `pages_menus`.`position` ASC")->queryAll();
        }
        return $pagesMenus;
    }
    // Загрузка страница;
    public static function getPageRow($url)
    {
        $pages = Pages::find()->select(['id','name','seo_title','seo_description','seo_keywords','url','text'])->where(['url' => $url])->asArray()->one();
        return $pages;
    }


/*regions_districts
    public function getpages_menus(){
        return $this->hasOne(PagesMenus::classname(),['id' => 'page_id']);
    }
*/
}
