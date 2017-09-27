<?php

namespace app\models;

use app\modules\common\models\User;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\UsersCredits;

/**
 * UsersCreditsSearch represents the model behind the search form about `app\models\UsersCredits`.
 */
class UsersCreditsSearch extends UsersCredits
{
    public $userName='';
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'user_id', 'amount', 'status'], 'integer'],
            [['userName'], 'string'],
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
        $query = UsersCredits::find()->from('users, users_credits')->where('users.id = users_credits.user_id')->with('user')->orderBy(['users.name'=>SORT_ASC]);

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
        if(!empty($params['UsersCreditsSearch']['userName'])){
            $userId = array_column( User::find()->where(['like', 'name', $params['UsersCreditsSearch']['userName']])->asArray()->all(), 'id');
            $query->andFilterWhere(['IN', 'user_id', $userId]);
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'user_id' => $this->user_id,
            'amount' => $this->amount,
            'status' => $this->status,
        ]);

        return $dataProvider;
    }
}
