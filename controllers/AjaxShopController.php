<?php

namespace app\controllers;

use app\modules\coders\models\ClientLog;
use app\modules\common\controllers\FrontController;

use app\components\WProductItemOne;
use app\modules\catalog\models\Category;
use app\modules\catalog\models\Goods;
use Yii;

class AjaxShopController extends FrontController
{
    public function actionSetMasterStatus(){
        $post = \Yii::$app->request->post();
        $category = \Yii::$app->request->post('category');

        $timeStatus = $post['status'] == 1 ? 'startMaster' : 'stopMaster';

        if($post['status'] == 0){
            $startMasterTime = \Yii::$app->session->get('startMaster',time());
            $fullMasterTime = \Yii::$app->session->get('fullMasterTime',0);
            $stopMasterTime = time();
            \Yii::$app->session->set('fullMasterTime',$fullMasterTime + ($stopMasterTime - $startMasterTime));
        }else{

        }

        \Yii::$app->session->set('shopMaster',$post['status']);
        \Yii::$app->session->set($timeStatus,time());

    }
}
