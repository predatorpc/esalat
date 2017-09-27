<?php

namespace app\modules\common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\common\models\UsersPays;

/**
 * UsersPaysSearch represents the model behind the search form about `app\modules\common\models\UsersPays`.
 */
class UsersBonusSearch extends UsersBonus
{
    public $users;
    public $userPhone;

    /**
     * @inheritdoc
     */
    public function rules()
    {
//        return [
//            [['id', 'user_id', 'order_id', 'type', 'status'], 'integer'],
//            [['money'], 'number'],
//            [['comments', 'date','users', 'userPhone'], 'safe'],
//        ];
        return [
            //[['user_id', 'type', 'status', 'bonus'], 'required'],
            [['id', 'user_id', 'type', 'status'], 'integer'],
            [['bonus'], 'number'],
            [['date', 'comments','order_id', 'type_id', 'users', 'userPhone'], 'safe']
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
        $query = UsersBonus::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'user_id' => $this->user_id,
            'order_id' => $this->order_id,
            'type' => $this->type,
            'bonus' => $this->bonus,
            'date' => $this->date,
            'status' => $this->status,
        ]);

        $query->andFilterWhere(['like', 'comments', $this->comments]);

        return $dataProvider;
    }

    public function searchWithUsers($params)
    {
        $query = UsersBonus::find()
            ->joinWith('users')
            ->joinWith('userPhone')
           // ->where('comments <> \'WF\'')
//            ->leftJoin('users','users_pays.user_id = users.id')
//            ->where('users.status = 1')
//            ->orderBy('users.id')
        ;

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);

        $dataProvider->sort->attributes['users'] = [
            'asc' => ['users.name' => SORT_ASC],
            'desc' => ['users.name' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['userPhone'] = [
            'asc' => ['users.phone' => SORT_ASC],
            'desc' => ['users.phone' => SORT_DESC],
        ];

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'users.id' => $this->id,
            'user_id' => $this->user_id,
            'order_id' => $this->order_id,
            'type' => $this->type,
            'bonus' => $this->bonus,
            'date' => $this->date,
            'type_id' => $this->type_id,
                        
         //   'status' => $this->status,
        ]);

//        Zloradnij::print_arr($this->users);
        $query->andFilterWhere(['like', 'users.name', $this->users]);
        $query->andFilterWhere(['like', 'users.phone', $this->userPhone]);

//        Zloradnij::print_arr($query->all());
//        die();


        return $dataProvider;
    }
}
