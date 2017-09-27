<?php

namespace app\modules\common\models;

use app\modules\shop\models\Orders;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\common\models\User;

/**
 * UserSearchDefault represents the model behind the search form about `app\modules\common\models\User`.
 */
class UserSearchDefault extends User
{
    public $orderDate;

    public function rules()
    {
        return [
            [['id', 'city_id', 'extremefitness', 's', 'updated_at', 'created_at', 'staff', 'driver', 'manager', 'level', 'call', 'store_id', 'sms', 'confirm', 'agree', 'typeof', 'compliment', 'status'], 'integer'],
            [['name', 'birthday', 'phone', 'secret_word', 'email', 'password_reset_token', 'password_hash', 'auth_key', 'password', 'hash', 'enter', 'registration', 'orderDate'], 'safe'],
            [['money', 'bonus'], 'number'],
        ];
    }

    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function search($params)
    {
        /*$query = User::find()
                    ->select('users.*, max(orders.date) as `orderDate`')
                    ->leftJoin('orders', 'orders.user_id = users.id')
                    ->leftJoin('orders_groups', 'orders_groups.order_id = orders.id')
                    ->leftJoin('orders_items', 'orders_items.order_group_id = orders_groups.id')
                    ->leftJoin('goods', 'goods.id = orders_items.good_id')
                    ->where(['users.status' => 1])
                    ->andWhere(['orders.status' => 1])
                    ->andWhere('goods.type_id != 1005')
                    ->andWhere('(users.staff IS NULL or users.staff = 0)')
                    ->andWhere('orders.code_id IS NULL')
                    ->groupBy('users.id')
                    ->having('COUNT(orders.user_id) > 0');*/

        $query = User::find()
            ->select('users.*, orders.date as `orderDate`')
            ->leftJoin('orders', 'orders.user_id = users.id')
            ->where('orders.id IN (
                SELECT`orders`.`id` AS `order_id` FROM `orders`
                LEFT JOIN `codes` ON `codes`.`id` = `orders`.`code_id`
                LEFT JOIN `users` ON `users`.`id` = `codes`.`user_id`
                LEFT JOIN `orders_groups` ON `orders`.`id` = `orders_groups`.`order_id`
                LEFT JOIN  `orders_items` ON  `orders_groups`.`id` = `orders_items`.`order_group_id`
                WHERE (SELECT IFNULL(`users`.`staff`, 0) FROM `users`
                WHERE `id` = `orders`.`user_id` LIMIT 1) = 0
                AND `orders`.`type` = 1
                AND (`orders`.`status` = 1)
                AND (`orders_items`.`status` = 1)
                AND (`orders_items`.`store_id` NOT IN (10000191, 10000001, 10000002, 10000003, 10000004, 10000005, 10000006, 10000007, 10000108))
                AND ( (`orders`.`code_id` IS NULL))
                GROUP BY `order_id`, users.id
            )');

/*       $orders = Orders::find()
            ->select('`orders`.`id` AS `order_id`, max(orders.date) as `orderDate`')
            ->leftJoin('codes', '`codes`.`id` = `orders`.`code_id`')
            ->leftJoin('users', '`users`.`id` = `codes`.`user_id`')
            ->leftJoin('orders_groups', '`orders`.`id` = `orders_groups`.`order_id`')
            ->leftJoin('orders_items', '`orders_groups`.`id` = `orders_items`.`order_group_id`')
            ->where('(SELECT IFNULL(`users`.`staff`, 0) FROM `users`
      WHERE `id` = `orders`.`user_id` LIMIT 1) = 0')
            ->andWhere('orders.type = 1')
            ->andWhere('orders.status = 1')
            ->andWhere('orders_items.status = 1')
            ->andWhere('orders_items.status = 1')
            ->andWhere('orders_items.store_id NOT IN (10000191, 10000001, 10000002, 10000003, 10000004, 10000005, 10000006, 10000007, 10000108)')
            ->andWhere('orders.code_id IS NULL')
            ->groupBy('order_id, users.id')->asArray()->all();*/

/*

       echo '<pre>';
       print_r($query);
       echo '</pre>';
       die();*/

        /*$a = SELECT`orders`.`id` AS `order_id`, max(orders.date) as `orderDate` FROM `orders`
LEFT JOIN `codes` ON `codes`.`id` = `orders`.`code_id`
LEFT JOIN `users` ON `users`.`id` = `codes`.`user_id`
LEFT JOIN `orders_groups` ON `orders`.`id` = `orders_groups`.`order_id`
LEFT JOIN  `orders_items` ON  `orders_groups`.`id` = `orders_items`.`order_group_id`
WHERE DATE(`orders`.`date`) >= '2017-01-31' AND DATE(`orders`.`date`) <= '2017-02-09'
    AND  (SELECT IFNULL(`users`.`staff`, 0) FROM `users`
      WHERE `id` = `orders`.`user_id` LIMIT 1) = 0
    AND `orders`.`type` = '1'
    AND (`orders`.`status` = 1)
    AND (`orders_items`.`status` = 1)
    AND (`orders_items`.`store_id` NOT IN (10000191, 10000001, 10000002, 10000003, 10000004, 10000005, 10000006, 10000007, 10000108))
      AND ( (`orders`.`code_id` IS NULL))
      GROUP BY `order_id`, users.id;*/

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id'         => $this->id,
            'birthday'   => $this->birthday,
            /*'registration'   => $this->registration,*/
            'confirm'    => $this->confirm,
            'agree'      => $this->agree,
            'typeof'     => $this->typeof,
            'compliment' => $this->compliment,
            'status'     => $this->status
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'phone', $this->phone])
            ->andFilterWhere(['like', 'secret_word', $this->secret_word])
            ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'password_reset_token', $this->password_reset_token])
            ->andFilterWhere(['like', 'password_hash', $this->password_hash])
            ->andFilterWhere(['like', 'auth_key', $this->auth_key])
            ->andFilterWhere(['like', 'password', $this->password])
            ->andFilterWhere(['like', 'hash', $this->hash]);

        if ((isset($params['UserSearchDefault']['dateStart'])) && (strtotime($params['UserSearchDefault']['dateStart']) > 0) && (isset($params['UserSearchDefault']['dateEnd'])) && (strtotime($params['UserSearchDefault']['dateEnd']) > 0)) {
        $query->andFilterWhere(['between', 'orders.date', date('Y-m-d 00:00:00', (strtotime($params['UserSearchDefault']['dateStart']))), date('Y-m-d 23:59:59', strtotime($params['UserSearchDefault']['dateEnd']))]);
    }

        return $dataProvider;
    }
}