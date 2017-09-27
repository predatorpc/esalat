<?php

namespace app\modules\actions\controllers;

use app\modules\actions\models\ActionsParams;
use app\modules\actions\models\ActionsParamsValue;
use app\modules\common\controllers\FrontController;
use Yii;
use yii\filters\AccessControl;


/**
 * BasketController implements the CRUD actions for Basket model.
 */
class AjaxActionsController extends FrontController
{
    public function actionAddActionParam(){
        $i = !empty($_POST['i']) ? intval($_POST['i']) : 0;
        $paramId = !empty($_POST['paramId']) ? intval($_POST['paramId']) : false;
        //print_r($paramId);die();

        if(!$paramId){
            return false;
        }

        $paramsValue = new ActionsParamsValue();
        $paramsValue->param_id = $paramId;
        $paramsValue->basket_price = 0;
        $paramsNamae = ActionsParams::find()->where(['id'=>$paramId])->asArray()->one();

        //print_r($paramsNamae);

        return $this->renderPartial('/actions/_param', [
            'paramsValue' => $paramsValue,
            'paramsName' => $paramsNamae,
            'i' => $i,
        ]);

    }
}
