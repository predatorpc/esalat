<?php
namespace app\modules\catalog;

class Module extends \yii\base\Module
{
    public function init()
    {
        parent::init();

        $this->params['category'] = 0;
        $this->params['cacheDuration'] = 86400;
        $this->params['cacheTemplate'] = 'ListView-#MODEL_ID#-page-#PAGE_ID#';
        $this->params['cacheKey'] = '';
    }
}