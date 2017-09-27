<?php

namespace app\modules\catalog\models;

use app\modules\common\models\User;
use app\modules\common\models\Zloradnij;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\catalog\models\Codes;
use app\modules\common\models\UsersPays;

/**
 * CodesSearch represents the model behind the search form about `app\models\Codes`.
 */
class CodesSearchIndex extends Codes
{
    public $summa;
    public $user_name;
    public $dateStart;
    public $dateStop;
    public $countSale;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['status'], 'integer'],
            [['code', 'key', 'date_begin', 'date_end',
                'summa','dateStart','dateStop', 'user_name', 'user_id',], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Codes::find();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        //var_dump($params);die();

//        $dataProvider->pagination = $params['pagination']; // отключаем пагинацию

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'type_id' => $this->type_id,
            'user_id' => $this->user_id,
            'count' => $this->count,
            'date_begin' => $this->date_begin,
            'date_end' => $this->date_end,
            'status' => $this->status,
        ]);

        $query->andFilterWhere(['like', 'code', $this->code])
            ->andFilterWhere(['like', 'key', $this->key]);

        return $dataProvider;
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function searchCodes($params)
    {
        $query = Codes::find()
            ->select('users.id, users.name, codes.*')
            ->leftJoin('users','users.id = codes.user_id');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        //var_dump($params);die();
//        $dataProvider->pagination = $params['pagination']; // отключаем пагинацию

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            //'id' => $this->id,
            // 'type_id' => $this->type_id,
            //'user_id' => $this->user_id,
            // 'count' => $this->count,
            'date_begin' => $this->date_begin,
            'date_end' => $this->date_end,
            'status' => $this->status,
        ]);

        $query->andFilterWhere(['like', 'code', $this->code])
            ->andFilterWhere(['like', 'key', $this->key])
            ->andFilterWhere(['like', 'users.name', $this->user_id]);

        return $dataProvider;
    }

    public function searchForStatistic($params)
    {
        /*
        SELECT users.id, users.name, codes.code, users_pays.money, users_pays.comments, orders.date, SUM(users_pays.money) AS summa
        FROM  `orders`
        LEFT JOIN  `users` ON ( orders.user_id = users.id )
        LEFT JOIN  `codes` ON ( codes.id = orders.code_id )
        LEFT JOIN  `users_pays` ON ( orders.id = users_pays.order_id )
        WHERE orders.code_id > 0
            AND codes.status =1
            AND users_pays.comments LIKE '%Оплата заказа%'
            AND codes.code = 115511
        ORDER BY  `orders`.`date` DESC

        SELECT codes.code, users_pays.money, users_pays.comments, orders.date, SUM(users_pays.money) AS summa
        FROM  `codes`
        LEFT JOIN  `orders` ON ( codes.id = orders.code_id )
        LEFT JOIN  `users_pays` ON ( orders.id = users_pays.order_id )
        WHERE orders.code_id > 0
            AND codes.status =1
            AND users_pays.comments LIKE '%Оплата заказа%'
            AND codes.code = 115511
        ORDER BY  `orders`.`date` DESC

        */
        $query = Codes::find()
            ->select([
                'codes.*',
                'users_pays.comments',
                'SUM(`users_pays`.`money` * (-1)) AS summa',
            ])
            ->leftJoin('orders','orders.code_id = codes.id')
            ->leftJoin('users','codes.user_id = users.id')
            ->leftJoin('users_pays','users_pays.order_id = orders.id')
            ->andWhere([
                'codes.status' => 1,
                'orders.status' => 1,
            ])
            ->andWhere(['LIKE','users_pays.comments','Оплата заказа'])
            ->orderBy('orders.date DESC')
            ->groupBy('codes.id')
            //->limit(100)
        ;

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);
//
//        var_dump($params);
//        var_dump($this);


        if($this->dateStart || $this->dateStop){
            $query
                ->andWhere(['>=','users_pays.date',$this->dateStart])
                ->andWhere(['<=','users_pays.date',$this->dateStop]);
        }

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'type_id' => $this->type_id,
            'count' => $this->count,
            'date_begin' => $this->date_begin,
            'date_end' => $this->date_end,
            //'status' => $this->status,
//            'users_pays.date' => $this->dateStart,
//            'dateStop' => $this->dateStop,
        ]);

        $query->andFilterWhere(['like', 'code', $this->code])
            ->andFilterWhere(['like', 'key', $this->key])
            ->andFilterWhere(['like', 'codes.user_id', $this->user_id])
            ->andFilterWhere(['like', 'users.name', $this->user_name]);

        return $dataProvider;
    }



    /*
     *
     *
     *  new function by predator_pc 02/08/2016
     *
     *
     *
     */




//    public function searchForGoodStatistic($params)
//    {
//
//        if (isset($params['CodesSearch']['usetype'])) {
//            //die('Shit');
//            //Если у нас есть тип использования, то будем играть по другому
//            if(intval($params['CodesSearch']['usetype'])==-1){
//
//                $query = Codes::find()
//                    ->select([
//                        'codes.*',
//                        'users_pays.comments',
//                        'SUM(`users_pays`.`money` * (-1)) AS summa',
//                    ])
//                    ->leftJoin('orders','orders.code_id = codes.id')
//                    ->leftJoin('users','codes.user_id = users.id')
//                    ->leftJoin('users_pays','users_pays.order_id = orders.id')
//                    ->andWhere([
//                        'codes.status' => 1,
//                        'orders.status' => 1,
//                    ])
//                    ->andWhere(['LIKE','users_pays.comments','Оплата заказа'])
//                    ->orderBy('orders.date DESC')
//                    ->groupBy('codes.id')
//                    //->limit(100)
//                ;
//
//                $dataProvider = new ActiveDataProvider(['query' => $query,]);
//
//
//                $this->load($params);
//                if($this->dateStart || $this->dateStop){
//                    $query ->andWhere(['>=','users_pays.date',$this->dateStart])->andWhere(['<=','users_pays.date',$this->dateStop]);
//                }
//                if (!$this->validate()) {return $dataProvider;}
//
//                $query->andFilterWhere([
//                    'id' => $this->id,
//                    'type_id' => $this->type_id,
//                    'count' => $this->count,
//                    'date_begin' => $this->date_begin,
//                    'date_end' => $this->date_end,
//                    //'status' => $this->status,
//                    //            'users_pays.date' => $this->dateStart,
//                    //            'dateStop' => $this->dateStop,
//                ]);
//
//                if(intval($params['CodesSearch']['club'])!=0)
//                    $query->andWhere('users.store_id = '.$params['CodesSearch']['club']);
//
//                $query->andFilterWhere(['like', 'code', $this->code])
//                    ->andFilterWhere(['like', 'key', $this->key])
//                    ->andFilterWhere(['like', 'codes.user_id', $this->user_id])
//                    ->andFilterWhere(['like', 'users.name', $this->user_name]);
//
//                return $dataProvider;
//            }
//
//            if(intval($params['CodesSearch']['usetype'])==0){
//
//                $query = Codes::find()
//                    ->select([
//                        'codes.id  as code_id',
//                        'users.id  as user_id',
//                        'users_pays.user_id   as pay_user_id',
//                        'orders.id as order_id',
//                        'users_pays.money as pay_money',
//                        'codes.user_id as code_owner',
//                        'codes.code as code_itself',
//                        'users.name as name',
//                        'users.phone as phone',
//                    ])
//                    ->leftJoin('orders', 'orders.code_id = codes.id')
//                    ->leftJoin('users_pays', 'users_pays.order_id = orders.id')
//                    ->leftJoin('users', 'users_pays.user_id = users.id')
//                    ->leftJoin('orders_groups','orders_groups.order_id = orders.id')
//                    ->leftJoin('orders_items','orders_items.order_group_id = orders_groups.id')
//                    ->where('orders.status = 1')
//                    ->andWhere('orders.code_id IS NOT NULL')
//                    ->andWhere('orders_groups.status = 1')
//                    ->andWhere('orders_items.status = 1')
//                    ->andWhere('codes.status = 1')
//                    ->andWhere('users_pays.type = 1')
//                    ->andWhere('codes.user_id <> users_pays.user_id ')
//                    //->andWhere('codes.code = '.$params['CodesSearch']['code'])
//                    //->andWhere('codes.code = 115511')
//                    //->orderBy('orders.date DESC')
//                    ->groupBy('users_pays.id');
//
////                die($query);
//
//
//                $dataProvider = new ActiveDataProvider(['query' => $query,]);
//
//                $this->load($params);
//                if($this->dateStart || $this->dateStop){
//                    $query ->andWhere(['>=','users_pays.date',$this->dateStart])->andWhere(['<=','users_pays.date',$this->dateStop]);
//                }
//                if (!$this->validate()) {return $dataProvider;}
//
//                if(intval($params['CodesSearch']['code'])!=0)
//                    $query->andWhere('codes.code = '.$params['CodesSearch']['code']);
//
//                if(intval($params['CodesSearch']['club'])!=0)
//                    $query->andWhere('users.store_id = '.$params['CodesSearch']['club']);
//
//
//                //$query->andFilterWhere(['like', 'code', $this->code])
//
//                    //->andFilterWhere(['like', 'codes.user_id', $this->user_id])
//                    //->andFilterWhere(['like', 'orders.user_id', $this->pay_user_id])
//                    //->andFilterWhere(['like', 'users.name', $this->name])
//                    //->andFilterWhere(['like', 'users.name', $this->phone]);
//
//                return $dataProvider;
//
//            }
//
//            if(intval($params['CodesSearch']['usetype'])==1){
//
//                $query = Codes::find()
//                    ->select([
//                        'codes.id  as code_id',
//                        'users.id  as user_id',
//                        'users_pays.user_id   as pay_user_id',
//                        'orders.id as order_id',
//                        'users_pays.money as pay_money',
//                        'codes.user_id as code_owner',
//                        'codes.code as code_itself',
//                        'users.name as name',
//                        'users.phone as phone',
//                    ])
//                    ->leftJoin('orders', 'orders.code_id = codes.id')
//                    ->leftJoin('users_pays', 'users_pays.order_id = orders.id')
//                    ->leftJoin('users', 'users_pays.user_id = users.id')
//                    ->leftJoin('orders_groups','orders_groups.order_id = orders.id')
//                    ->leftJoin('orders_items','orders_items.order_group_id = orders_groups.id')
//                    ->where('orders.status = 1')
//                    ->andWhere('orders.code_id IS NOT NULL')
//                    ->andWhere('orders_groups.status = 1')
//                    ->andWhere('orders_items.status = 1')
//                    ->andWhere('codes.status = 1')
//                    ->andWhere('users_pays.type = 1')
//                    ->andWhere('codes.user_id = users_pays.user_id')
//                    //->andWhere('codes.code = '.$params['CodesSearch']['code'])
//                    //->andWhere('codes.code = 115511')
//                    //->orderBy('orders.date DESC')
//                    ->groupBy('users_pays.id');
//
//                $dataProvider = new ActiveDataProvider(['query' => $query,]);
//
//                $this->load($params);
//                if($this->dateStart || $this->dateStop){
//                    $query ->andWhere(['>=','users_pays.date',$this->dateStart])->andWhere(['<=','users_pays.date',$this->dateStop]);
//                }
//                if (!$this->validate()) {return $dataProvider;}
//
//                if(intval($params['CodesSearch']['code'])!=0)
//                    $query->andWhere('codes.code = '.$params['CodesSearch']['code']);
//
//                if(intval($params['CodesSearch']['club'])!=0)
//                    $query->andWhere('users.store_id = '.$params['CodesSearch']['club']);
//
//                //$query->andFilterWhere(['like', 'code', $this->code])
//                //->andFilterWhere(['like', 'codes.user_id', $this->user_id])
//                //->andFilterWhere(['like', 'orders.user_id', $this->pay_user_id])
//                //->andFilterWhere(['like', 'users.name', $this->name])
//                //->andFilterWhere(['like', 'users.name', $this->phone]);
//              //Zloradnij::print_arr($query);die();
////
//                return $dataProvider;
//            }
//
//        }
//    }



    public function searchStatistic($params)
    {
        $this->dateStart = '2016-08-01';
        $this->dateStop = '2016-08-08';

        $params['CodesSearch']['usetype'] = -1;

        $this->load($params);
        if(intval($params['CodesSearch']['usetype'])==1){

        }elseif(intval($params['CodesSearch']['usetype'])==0){

        }else{

        }

        $codes = Codes::find()
            ->select([
                'codes.*',
                'users_pays.comments',
                'SUM(`users_pays`.`money` * (-1)) AS summa',
            ])
            ->leftJoin('orders','orders.code_id = codes.id')
            ->leftJoin('users_pays','users_pays.order_id = orders.id')
            ->andWhere(['>=','users_pays.date',$this->dateStart])
            ->andWhere(['<=','users_pays.date',$this->dateStop])
            ->andWhere([
                'codes.status' => 1,
                'orders.status' => 1,
            ])
            ->andWhere(['LIKE','users_pays.comments','Оплата заказа'])
            ->orderBy('orders.date DESC')
            ->groupBy('codes.id')

            ->all();

        return $codes;
    }


    // By Zloradnij
    public function searchForGoodStatistic($params)
    {
        $this->dateStart = $this->dateStop = date('Y-m-d');
        $query = Codes::find()
            ->select([
                'codes.user_id as codeUserId',
                'codes.*',
                'users_pays.comments',
                'SUM(`users_pays`.`money` * (-1)) AS summa',
                'SUM(`users_pays`.`type` = 4) AS countSale',
//              'users.store_id',
//              'SUM(`users_pays`.`money` * (-1)) AS summa',
            ])
            ->leftJoin('orders','orders.code_id = codes.id')
            ->leftJoin('users_pays','users_pays.order_id = orders.id')
            //->leftJoin('users','users.id = users_pays.user_id')
            ->leftJoin('users','users.id = codes.user_id')
            ->andWhere([
                'codes.status' => 1,
                'orders.status' => 1,
                'users_pays.status' => 1,
            ]);

        if(!empty($params['CodesSearch']['code']) && intval($params['CodesSearch']['code'])!=0)
            $query->andWhere('codes.code = '.$params['CodesSearch']['code']);

        if(!empty($params['CodesSearch']['typeof']) && intval($params['CodesSearch']['typeof'])!=0)
            $query->andWhere('users.typeof = '.$params['CodesSearch']['typeof']);

        if(!empty($params['CodesSearch']['club']) && intval($params['CodesSearch']['club'])!=0)
            $query->andWhere('users.store_id = '.$params['CodesSearch']['club']);

        $query->andWhere([
            'OR',
            ['users_pays.type' => 4],
            ['users_pays.type' => 9],
        ])
            ->orderBy('countSale DESC')
            ->groupBy('codes.id');

        if($params['CodesSearch']['usetype']!=null){

            //var_dump($params['CodesSearch']['usetype']);die();
            if(intval($params['CodesSearch']['usetype'])==1){
//                $query->andWhere('orders.user_id = codes.user_id');
                $query->andWhere('orders.user_id != codes.user_id');

            }
            if(intval($params['CodesSearch']['usetype'])==0){
//                $query->andWhere('orders.user_id != codes.user_id');
                $query->andWhere('orders.user_id = codes.user_id');
                //       Zloradnij::print_arr($query->createCommand()->getRawSql());die();
            }
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);
        if($this->dateStart || $this->dateStop){
            $query ->andWhere(['>=','users_pays.date',$this->dateStart])->andWhere(['<=','users_pays.date',$this->dateStop . ' 23:59:59']);
        }
        if (!$this->validate()) {
            return $dataProvider;
        }

        //Zloradnij::print_arr($query->createCommand()->getRawSql());die();

        return $dataProvider;
    }




}
