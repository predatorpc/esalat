<?php

namespace app\modules\common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use yii\helpers\ArrayHelper;
use app\modules\catalog\models\Codes;
use app\modules\shop\models\Orders;

/**
 *
 *
 * @property string $description
 * @property string $role
 *
 */

class UserSearch extends UserAdmin
{
    public $description;
    public $role_description;
    public $role;
    public $role_name;
//    public $auth_item;

    public function rules()
    {
        return [
            [['id', 'status','typeof'], 'integer'],
//            [['name', 'role_description', 'role', 'fullname', 'auth_key', 'password_hash', 'password_reset_token', 'email', 'phone', 'description'], 'safe'],
            [['name', 'role_description', 'role', 'role_name', 'auth_key', 'password_hash', 'password_reset_token', 'email', 'phone', 'description',
                'registration'], 'safe'],
            //          [['name', 'auth_key', 'password_hash', 'password_reset_token', 'email', 'phone', 'description'], 'safe'],
            [['created_at', 'updated_at'], 'date', 'format' => 'd.m.Y'],
        ];
    }

    public function relations()
    {
        return ['description' => array(self::HAS_ONE, 'id', 'user_id')];
//	    array('auth_item,'=>array(self::HAS_MANY, 'auth_item', 'name'),);
    }

    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }


    public function search($params)
    {
        $query = UserAdmin::find()
            ->select([
                'id',
                'registration',
                'users.created_at',
                'phone',
                'money',
                'bonus',
                'email',
                'users.name',
                'staff',
                'secret_word',
                'auth_assignment.item_name as role',
                'auth_assignment.description as description',
                'auth_item.name as role_name',
                'auth_item.description as role_description',
                'typeof'
            ])
            ->leftjoin('auth_assignment','auth_assignment.user_id = users.id')
            ->leftjoin('auth_item','auth_item.name = auth_assignment.item_name')
            //   ->joinWith('auth_item')
            ->where('status = 1')
//	->where('id = user.id')
            //->orderby('role_name')
            ->asarray();



        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $dataProvider->sort->attributes['users'] = [
            'asc' => ['role_name' => SORT_ASC],
            'desc' => ['role_name' => SORT_DESC],
        ];


        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        if(!empty(Yii::$app->request->get('start_date')) && !empty(Yii::$app->request->get('end_date')))
        {
            $query->where('users.created_at > '. strtotime(Yii::$app->request->get('start_date')));
            $query->andWhere('users.created_at < '. strtotime(Yii::$app->request->get('end_date')));
        }

        $query->orFilterWhere([
//            'id' => $this->id,
//            'username' => $this->username,
//            'fullname' => $this->fullname,
            'auth_assignment.item_name' => $this->role,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'registration' => $this->registration,
            //    'auth_assignment.description' => $this->description,
            //'auth_item.name' => $this->role_name,
        ]);

        $query->andFilterWhere(['like', 'users.name', $this->name])
            ->andFilterWhere(['like', 'id', $this->id])
            ->andFilterWhere(['like', 'auth_item.description', $this->description])
            ->andFilterWhere(['like', 'auth_key', $this->auth_key])
            ->andFilterWhere(['like', 'auth_assignment.item_name', $this->role])
            ->andFilterWhere(['like', 'password_hash', $this->password_hash])
            ->andFilterWhere(['like', 'password_reset_token', $this->password_reset_token])
            ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'phone', $this->phone])
            ->andFilterWhere(['like', 'auth_assignment.item_name', $this->role_name])
            //->all();
            ->limit(20)
            ->all();


        return $dataProvider;
    }

    public function StaffPromo($param){

        //print_r($param);
        if(empty($param['dateStart']) || !isset($param['dateStart'])){$dateStart = date("Y-m-d 00:00:00", strtotime("-1 month"));}
        if(empty($param['dateStop']) || !isset($param['dateStart'])){ $dateStop = date("Y-m-d 23:59:59", strtotime("now"));}

        if(!empty($param['dateStart'])) {
            $dateStart = date("Y-m-d 00:00:00", strtotime($param['dateStart']));
        }
        if(!empty($param['dateStop'])){
            $dateStop = date("Y-m-d 23:59:59", strtotime($param['dateStop']));
        }



        $arUsers = User::find()->select(['id','name','phone'])->where(['status'=>1])->andWhere(['NOT IN','id',[10004448, 10013181]])->andWhere(['IS NOT','staff',NULL])->andWhere(['IS NOT','typeof',NULL])->asArray();

        //Задаем Тип пользователя
        if(!empty($param['typeof'])){
            $typeof = $param['typeof'];
            $arUsers = $arUsers->andWhere(['typeof'=>$typeof]);
        }

        //Задаем клуб
        if($param['club']>0){
            $club = $param['club'];
            $arUsers = $arUsers->andWhere(['store_id'=>$club]);
        }

        //Задаем промокод
        if(isset($param['code']) && $param['code']>0){
            $code = $param['code'];
            $code = Codes::find()->where(['code'=> $code,'status'=>1])->One();
            if($code != NULL){
                $arUsers = $arUsers->andWhere(['id'=>$code->user_id]);
            }else{
                //Если такого промо нет, тут кастыль, что бы резульат был 0 в выводе
                $arUsers = $arUsers->andWhere(['id'=>101010101010101]);
            }

        }

        $arUsers = $arUsers->All();

        $arCodes = Codes::find()->where(['status'=>1])->andWhere(['IN','user_id',ArrayHelper::getColumn($arUsers,'id')])->asArray()->All();
        $code = ArrayHelper::getColumn($arCodes,'id');

        $arOrders = Orders::find()->where(['>=','date',$dateStart])->andWhere(['<=','date',$dateStop])->andWhere(['status'=>1]);

        //$arOrders = $arOrders->andWhere(['IN', 'code_id', $code]);

        //<!--Некий пиздец-->
        if(!isset($param['wocode']) || $param['wocode']!=1) {
            $arOrders = $arOrders->andWhere(['IN', 'code_id', $code]);
        }else if($param['wocode'] == 1){
            if(isset($param['code']) && $param['code']>0){
                $code = Codes::find()->where(['code'=> $param['code'],'status'=>1])->One();
                if($code != NULL){
                    $user = $code->user;
                    if(!empty($param['typeof']) && $user->typeof != $param['typeof']){
                        $arOrders = $arOrders->where(['code_id'=>101010101010101]);
                    }elseif(!empty($param['club']) && $user->store_id != $param['club']){
                        $arOrders = $arOrders->where(['code_id'=>101010101010101]);
                    }else{
                        $arOrders = Orders::find()->where(['>=','date',$dateStart])->andWhere(['<=','date',$dateStop])->andWhere(['status'=>1])
                            ->andWhere(['or','user_id = '. $code->user_id,'code_id = '.$code->id]);
                    }

                }else{
                    $arOrders = $arOrders->where(['code_id'=>101010101010101]);
                }
            }else{
                $arOrders = $arOrders->andWhere(['or','user_id IN('.implode(',',ArrayHelper::getColumn($arUsers,'id')).')','code_id IN('.implode(',',$code).')']);
            }
        }

        //<!--Некий пиздец-->
        $arOrders = $arOrders->orderBy('user_id')->All();




        //Да простим меня бог, за то что происходит ниже
        //Лучше сразу начать с начала
        $data = [];
        foreach ($arOrders as $order){
            if($order->code == NULL){
                $id_element = 'nocode_'.$order->user_id;
            }else{
                $id_element = $order->code->id.'_'.$order->code->user_id;
            }
            if(isset($data[$id_element])){
                $data[$id_element]['count']++;
                foreach ($order->ordersGroups as $orderGroup){
                    if($orderGroup->status != 1){
                        continue;
                    }
                    if( $order->code == NULL || $order->user_id == $order->code->user_id){
                        if($orderGroup->type_id == 1003 || $orderGroup->type_id == 1007){
                            foreach ($orderGroup->ordersItems as $ordersItem){
                                if($ordersItem->status !=1){
                                    continue;
                                }
                                $data[$id_element]['totalPurchase']['food'] = $data[$id_element]['totalPurchase']['food']  +  $ordersItem->price * $ordersItem->count;
                                $data[$id_element]['totalPurchase']['discount'] = $data[$id_element]['totalPurchase']['discount'] + $ordersItem->discount;
                                $data[$id_element]['totalPurchase']['total'] = $data[$id_element]['totalPurchase']['total'] + $ordersItem->price * $ordersItem->count;
                            }
                        }elseif($orderGroup->type_id == 1010 || $orderGroup->type_id == 1012 || $orderGroup->type_id == 1005){
                            foreach ($orderGroup->ordersItems as $ordersItem){
                                if($ordersItem->status !=1){
                                    continue;
                                }
                                $data[$id_element]['totalPurchase']['sport'] = $data[$id_element]['totalPurchase']['sport'] + $ordersItem->price * $ordersItem->count;
                                $data[$id_element]['totalPurchase']['discount'] = $data[$id_element]['totalPurchase']['discount'] + $ordersItem->discount;
                                $data[$id_element]['totalPurchase']['total'] = $data[$id_element]['totalPurchase']['total'] + $ordersItem->price * $ordersItem->count;
                            }
                        }
                        $data[$id_element]['totalPurchase']['delivery'] = $data[$id_element]['totalPurchase']['delivery'] + $orderGroup->delivery_price;
                        $data[$id_element]['totalPurchase']['total'] = $data[$id_element]['totalPurchase']['total'] + $orderGroup->delivery_price;
                    }else{
                        if($orderGroup->type_id == 1003 || $orderGroup->type_id == 1007){
                            foreach ($orderGroup->ordersItems as $ordersItem){
                                if($ordersItem->status !=1){
                                    continue;
                                }
                                $data[$id_element]['totalSale']['food'] = $data[$id_element]['totalSale']['food']  + + $ordersItem->price * $ordersItem->count;
                                $data[$id_element]['totalSale']['discount'] = $data[$id_element]['totalSale']['discount'] + $ordersItem->discount;
                                $data[$id_element]['totalSale']['total'] = $data[$id_element]['totalSale']['total'] + $ordersItem->price * $ordersItem->count;
                            }
                        }elseif($orderGroup->type_id == 1010 || $orderGroup->type_id == 1012 || $orderGroup->type_id == 1005){
                            foreach ($orderGroup->ordersItems as $ordersItem){
                                if($ordersItem->status !=1){
                                    continue;
                                }
                                $data[$id_element]['totalSale']['sport'] = $data[$id_element]['totalSale']['sport'] + $ordersItem->price * $ordersItem->count;
                                $data[$id_element]['totalSale']['discount'] = $data[$id_element]['totalSale']['discount'] + $ordersItem->discount;
                                $data[$id_element]['totalSale']['total'] = $data[$id_element]['totalSale']['total'] + $ordersItem->price * $ordersItem->count;
                            }
                        }
                        $data[$id_element]['totalSale']['delivery'] = $data[$id_element]['totalSale']['delivery'] + $orderGroup->delivery_price;
                    }
                }
                continue;
            }
            if($order->code == NULL){
                $data[$id_element]['user'] = $order->user->name;
            }else{
                $user = User::find()->where(['id'=>$order->code->user_id])->One();
                if($user == NULL){
                    $data[$id_element]['user'] = $order->code->user_id;
                }else {
                    $data[$id_element]['user'] = $user->name;
                }

            }

            if($order->code == NULL){
                $data[$id_element]['code'] = 'БЕЗ ПРОМОКОДА';
                $data[$id_element]['code_type'] = 'БЕЗ ПРОМОКОДА';
            }else{
                $data[$id_element]['code'] = $order->code->code;
                $data[$id_element]['code_type'] =$order->code->type->name;
            }
            $data[$id_element]['count']  = 1;
            $data[$id_element]['countSale'] = 0;
            $data[$id_element]['countPurchase'] = 0;
            $data[$id_element]['totalSale']['total'] = 0;
            $data[$id_element]['totalSale']['sport'] =0;
            $data[$id_element]['totalSale']['food'] = 0;
            $data[$id_element]['totalSale']['delivery'] = 0;
            $data[$id_element]['totalSale']['discount'] = 0;
            $data[$id_element]['totalPurchase']['total'] = 0;
            $data[$id_element]['totalPurchase']['sport'] = 0;
            $data[$id_element]['totalPurchase']['food'] = 0;
            $data[$id_element]['totalPurchase']['delivery'] = 0;
            $data[$id_element]['totalPurchase']['discount'] = 0;
            foreach ($order->ordersGroups as $orderGroup){
                if($orderGroup->status != 1){
                    continue;
                }
                if( $order->code == NULL || $order->user_id == $order->code->user_id){
                    if($orderGroup->type_id == 1003 || $orderGroup->type_id == 1007){
                        foreach ($orderGroup->ordersItems as $ordersItem){
                            if($ordersItem->status !=1){
                                continue;
                            }
                            $data[$id_element]['totalPurchase']['food'] = $data[$id_element]['totalPurchase']['food']  +  $ordersItem->price * $ordersItem->count - $ordersItem->discount;
                            $data[$id_element]['totalPurchase']['discount'] = $data[$id_element]['totalPurchase']['discount'] + $ordersItem->discount;
                            $data[$id_element]['totalPurchase']['total'] = $data[$id_element]['totalPurchase']['total'] + $ordersItem->price * $ordersItem->count  - $ordersItem->discount;
                        }
                    }elseif($orderGroup->type_id == 1010 || $orderGroup->type_id == 1012 || $orderGroup->type_id == 1005){
                        foreach ($orderGroup->ordersItems as $ordersItem){
                            if($ordersItem->status !=1){
                                continue;
                            }
                            $data[$id_element]['totalPurchase']['sport'] = $data[$id_element]['totalPurchase']['sport'] + $ordersItem->price * $ordersItem->count - $ordersItem->discount;
                            $data[$id_element]['totalPurchase']['discount'] = $data[$id_element]['totalPurchase']['discount'] + $ordersItem->discount;
                            $data[$id_element]['totalPurchase']['total'] = $data[$id_element]['totalPurchase']['total'] + $ordersItem->price * $ordersItem->count  - $ordersItem->discount;
                        }
                    }
                    $data[$id_element]['totalPurchase']['delivery'] = $data[$id_element]['totalPurchase']['delivery'] + $orderGroup->delivery_price;
                    $data[$id_element]['totalPurchase']['total'] = $data[$id_element]['totalPurchase']['total'] + $orderGroup->delivery_price;
                }else{
                    if($orderGroup->type_id == 1003 || $orderGroup->type_id == 1007){
                        foreach ($orderGroup->ordersItems as $ordersItem){
                            if($ordersItem->status !=1){
                                continue;
                            }
                            $data[$id_element]['totalSale']['food'] = $data[$id_element]['totalSale']['food']  + $ordersItem->price * $ordersItem->count - $ordersItem->discount;;
                            $data[$id_element]['totalSale']['discount'] = $data[$id_element]['totalSale']['discount'] + $ordersItem->discount;
                            $data[$id_element]['totalSale']['total'] = $data[$id_element]['totalSale']['total'] + $ordersItem->price * $ordersItem->count  - $ordersItem->discount;
                        }
                    }elseif($orderGroup->type_id == 1010 || $orderGroup->type_id == 1012 || $orderGroup->type_id == 1005){
                        foreach ($orderGroup->ordersItems as $ordersItem){
                            if($ordersItem->status !=1){
                                continue;
                            }
                            $data[$id_element]['totalSale']['sport'] = $data[$id_element]['totalSale']['sport'] + $ordersItem->price * $ordersItem->count - $ordersItem->discount;;
                            $data[$id_element]['totalSale']['discount'] = $data[$id_element]['totalSale']['discount'] + $ordersItem->discount;
                            $data[$id_element]['totalSale']['total'] = $data[$id_element]['totalSale']['total'] + $ordersItem->price * $ordersItem->count  - $ordersItem->discount;
                        }
                    }
                    $data[$id_element]['totalSale']['delivery'] = $data[$id_element]['totalSale']['delivery'] + $orderGroup->delivery_price;
                }
            }
            if($order->code == NULL){
                $data[$id_element]['club']  = $order->user->store_id;
                $data[$id_element]['phone']  = $order->user->phone;
            }else{
                $user = User::find()->where(['id'=>$order->code->user_id])->One();
                if($user !== NULL){
                    $data[$id_element]['club']  = $order->code->user->store_id;
                    $data[$id_element]['phone']  = $order->code->user->phone;
                }

            }

        }

        $dataProvider = new ArrayDataProvider([
            'allModels' => $data,
            'pagination' => [
                'pageSize' => 20,
            ],
            'sort' => [
                'attributes' => [ 'user',
                    'code',
                    'code_type',
                    'count',
                    'totalSale',
                    'totalPurchase',
                    'club',
                    'phone',
                ],
            ],
        ]);
        return $dataProvider;

    }
}
