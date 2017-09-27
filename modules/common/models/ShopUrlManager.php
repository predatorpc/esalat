<?php
namespace app\modules\common\models;

class ShopUrlManager extends \yii\web\UrlManager
{
    public function createUrl($route, $params=array(), $ampersand='&')
    {
        return $this->fixPathSlashes(parent::createUrl($route, $params, $ampersand));
    }

    protected  function fixPathSlashes($url)
    {
        return preg_replace('|\%2F|i', '/', $url);
    }
}