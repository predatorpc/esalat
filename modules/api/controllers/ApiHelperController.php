<?php

namespace app\modules\api\controllers;

use app\modules\common\models\User;
use app\modules\shop\models\Orders;
use app\modules\shop\models\OrdersGroups;
use app\modules\shop\models\OrdersItems;
use app\modules\actions\models\ActionsPresentSave;
use Yii;
use yii\web\Controller;

/**
 * ActionsController implements the CRUD actions for Actions model.
 */
class ApiHelperController extends Controller
{
    public $enableCsrfValidation = false;

    /**
     * Lists all Actions models.
     * @return mixed
     */
    public function actionSuccess()
    {
        //file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/111.txt',var_export($_POST,true));
        $response = [];

        if(!empty(Yii::$app->request->post())){
            $params = Yii::$app->request->post();

            if(!empty($params['result'])){
                foreach ($params['result'] as $orderGroupId => $param) {
                    $orderGroup = OrdersGroups::findOne($orderGroupId);
                    if(!empty($orderGroup)){
                        $ordersItems = OrdersItems::find()->where(['order_group_id' => $orderGroupId])->all();
                        if(!empty($ordersItems)){
                            foreach ($ordersItems as $ordersItem) {
                                if(!in_array($ordersItem->variation_id, [1000066096,1000066336])){
                                    $ordersItem->status_id = $param['status_kod'];
                                    if($ordersItem->save()){
                                        $response['status'] = 'success';
                                        $response['message'] = 'Всё правой';
                                    }else{
                                        $response['status'] = 'error';
                                        $response['message'] = 'not save status';
                                    }
                                }
                            }
                        }else{
                            $response['status'] = 'error';
                            $response['message'] = 'orders items not found';
                        }
                    }else{
                        $response['status'] = 'error';
                        $response['message'] = 'order group not found';
                    }
                }
            }else{
                $response['status'] = 'error';
                $response['message'] = 'empty result';
            }

        }else{
            $response['status'] = 'error';
            $response['message'] = 'empty POST';
        }

        return json_encode($response);
    }
    public function actionCard()
    {
        $file = "----------------------------------------------------\n------------------------START-----------------------\n";
        $fileName =  'ef_card_'.time().'_'.rand(0, 1000).'.txt';
        $file.=  time(). '--'.Date('Y.m.d H:i:s'."\n", time()). "\n";
        $file.= var_export(Yii::$app->request->post(), true);
        $dirName =$_SERVER['DOCUMENT_ROOT'] . '/logs/api/'.Date('Y-m-d', time());
        if(!file_exists($dirName)){
            mkdir($dirName);
        }
        file_put_contents($dirName.'/'.$fileName, $file."\n");

        $response = [];

        if(!empty(Yii::$app->request->post())){
            $params = Yii::$app->request->post();
            //test params
            /*$params = [
                'result'=>[
                    'card'=>10137211,
                    'status'=>1,
                ]
            ];*/
            if(!empty($params['result'])){
                $present = ActionsPresentSave::find()->where(['card_number'=>$params['result']['card'], 'status'=>1])->asArray()->one();
                if(!empty($present)){
                    $order = Orders::find()->where(['basket_id'=>$present['basket_id'], 'user_id'=>$present['user_id'], 'status'=>1])->one();
                    if(!empty($order)){
                        foreach ($order->ordersGroups as $group){
                            //print_r($group);
                            $ordersItem = OrdersItems::find()->where(['order_group_id' => $group->id, 'variation_id'=>$present['present'], 'status'=>1])->one();
                            if(!empty($ordersItem)){
                                $ordersItem->status_id = 1007;
                                if($ordersItem->save(true)){
                                    $response['status'] = 'success';
                                    $response['message'] = 'ok';
                                }
                            }
                        }
                    }
                    else{
                        $response['status'] = 'error';
                        $response['message'] = 'empty order';
                    }
                }

            }
            else{
                $response['status'] = 'error';
                $response['message'] = 'empty result';
            }

        }
        else{
            $response['status'] = 'error';
            $response['message'] = 'empty POST';
        }

        return json_encode($response);
    }
}
