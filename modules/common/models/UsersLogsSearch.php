<?php

namespace app\modules\common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\common\models\UsersLogs;


/**
 * UsersLogsSearch represents the model behind the search form about `app\modules\common\models\UsersLogs`.
 */
class UsersLogsSearch extends UsersLogs
{

    public $user;
    public $good;
    public $variations;
    public $date_logs;

    public function rules()
    {
        return [
            [['id', 'user_id', 'good_id', 'variations_id', 'type', 'status'], 'integer'],
            [['user', 'good', 'variations', 'created_at', 'updated_at', 'date_logs'], 'safe'],
        ];
    }

    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = UsersLogs::find()->where('users_logs.type = 1');
            //->with('user')
            //->with('good')
            //->with('variations');
            //->select('users.*, goods.*, goods_variations.*, users_logs.*, users_logs.created_at as date_logs');

        $query->joinWith('user');
        $query->joinWith('good');
        $query->joinWith('variations');


        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $dataProvider->sort->attributes['user'] = [
            'asc' => ['users.name' => SORT_ASC],
            'desc' => ['users.name' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['good'] = [
            'asc' => ['goods.name' => SORT_ASC],
            'desc' => ['goods.name' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['variations'] = [
            'asc' => ['goods_variations.full_name' => SORT_ASC],
            'desc' => ['goods_variations.full_name' => SORT_DESC],
        ];

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'users_logs.id' => $this->id,
            'users_logs.user_id' => $this->user_id,
            'users_logs.good_id' => $this->good_id,
            'users_logs.variations_id' => $this->variations_id,
            'users_logs.type' => $this->type,
            //'users_logs.created_at' => $this->created_at,
            'users_logs.updated_at' => $this->updated_at,
            'users_logs.status' => $this->status,
        ])
            ->andFilterWhere(['like', 'users.name', $this->user])
            ->andFilterWhere(['like', 'goods.name', $this->good])
            ->andFilterWhere(['like', 'goods_variations.full_name', $this->variations]);

        if ((isset($params['UsersLogsSearch']['dateStart'])) && (strtotime($params['UsersLogsSearch']['dateStart']) > 0) && (isset($params['UsersLogsSearch']['dateEnd'])) && (strtotime($params['UsersLogsSearch']['dateEnd']) > 0)) {
            $query->andFilterWhere(['between', 'users_logs.created_at', date('Y-m-d 00:00:00', (strtotime($params['UsersLogsSearch']['dateStart']))), date('Y-m-d 23:59:59', strtotime($params['UsersLogsSearch']['dateEnd']))]);
        }

        return $dataProvider;
    }
}