<?php
namespace app\modules\control;

class Module extends \yii\base\Module
{
    public function init()
    {
        parent::init();

        $this->setAliases([
            '@control-assets' => __DIR__ . '/assets'
        ]);
    }
}