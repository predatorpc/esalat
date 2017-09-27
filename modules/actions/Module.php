<?php
namespace app\modules\actions;

class Module extends \yii\base\Module
{
    public function init()
    {
        parent::init();

        $this->setAliases([
            '@actions-assets' => __DIR__ . '/assets'
        ]);
    }
}