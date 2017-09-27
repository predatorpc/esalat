<?php

namespace app\modules\common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\common\models\UsersPays;

/**
 * UsersPaysSearch represents the model behind the search form about `app\modules\common\models\UsersPays`.
 */
class UsersPaysSearch extends UsersPays
{
    public $usersName;
    public $userPhone;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'order_id', 'type', 'status', 'type_id'], 'integer'],
            [['money'], 'number'],
            [['comments', 'usersName', 'user_id', 'created_user_id', 'userPhone',], 'string'],
            [['date',], 'safe'],
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
        $query = UsersPays::find();

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
           // 'type' => $this->type,
         //   'money' => $this->money,
          //  'date' => $this->date,
           // 'status' => $this->status,
        ]);

        $query->andFilterWhere(['like', 'comments', $this->comments]);

        return $dataProvider;
    }

    public function searchWithUsers($params)
    {
        $query = UsersPays::find()
            //->joinWith('users')
            //->joinWith('userPhone')
            ->where('comments <> \'WF\'')
            ->with('users')
            ->with('creatorUser')
            ->orderBy('date', SORT_DESC)
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

        /*$dataProvider->sort->attributes['users'] = [
            'asc' => ['users.name' => SORT_ASC],
            'desc' => ['users.name' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['userPhone'] = [
            'asc' => ['users.phone' => SORT_ASC],
            'desc' => ['users.phone' => SORT_DESC],
        ];*/

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        if(!empty($params['UsersPaysSearch']['comment'])){
            $query->andFilterWhere(['like', 'comments', $this->comments]);
        }

        if(!empty($params['UsersPaysSearch']['user_id'])){
            $userId = array_column(User::find()->where(['like', 'name', $params['UsersPaysSearch']['user_id']])->asArray()->all(), 'id');
            $query->andFilterWhere(['IN', 'user_id', $userId]);
        }
        if(!empty($params['UsersPaysSearch']['userPhone'])){
            $userId = array_column(User::find()->where(['like', 'phone', $params['UsersPaysSearch']['userPhone']])->asArray()->all(), 'id');
            $query->andFilterWhere(['IN', 'user_id', $userId]);
        }
        if(!empty($params['UsersPaysSearch']['created_user_id'])){
            $creatorId = array_column(User::find()->where(['like', 'name', $params['UsersPaysSearch']['created_user_id']])->asArray()->all(), 'id');
            $query->andFilterWhere(['IN', 'created_user_id', $creatorId]);
        }

        if((isset($params['UsersPaysSearch']['date'])) && strtotime($params['UsersPaysSearch']['date'])>0){
            $query ->andFilterWhere(['>=', 'users_pays.date', Date('Y-m-d 00:00:00', strtotime($params['UsersPaysSearch']['date']))]);
            $query ->andFilterWhere(['<', 'users_pays.date', Date('Y-m-d 00:00:00', (strtotime($params['UsersPaysSearch']['date'])+86400))]);
        }
        $query->andFilterWhere([
            'id' => $this->id,
            //'user_id' => $this->user_id,
            'order_id' => $this->order_id,
            'type' => $this->type,
//            'money' => $this->money,
//            'date' => $this->date,
            'status' => $this->status,
        ]);
//        Zloradnij::print_arr($this->users);
//        $query->andFilterWhere(['like', 'users.name', $this->users]);
//        $query->andFilterWhere(['like', 'users.phone', $this->userPhone]);
//        Zloradnij::print_arr($query->all());
//        die();


        return $dataProvider;
    }
}
