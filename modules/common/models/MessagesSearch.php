<?php

namespace app\modules\common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\common\models\Messages;
use app\modules\common\models\MessagesImages;

/**
 * MessagesSearch represents the model behind the search form about `app\modules\common\models\Messages`.
 */
class MessagesSearch extends Messages
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'type_id', 'group_id', 'user_id', 'show', 'status'], 'integer'],
            [['name', 'topic', 'order', 'phone', 'text', 'answer', 'date'], 'safe'],
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

    public function search($params,$type_id)
    {
        $query = Messages::find()->where(['type_id'=> $type_id])->orderby(['id'=>SORT_DESC]);

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
            'type_id' => $this->type_id,
            'group_id' => $this->group_id,
            'user_id' => $this->user_id,
            'date' => $this->date,
            'show' => $this->show,
            'status' => $this->status,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'topic', $this->topic])
            ->andFilterWhere(['like', 'order', $this->order])
            ->andFilterWhere(['like', 'phone', $this->phone])
            ->andFilterWhere(['like', 'text', $this->text])
            ->andFilterWhere(['like', 'answer', $this->answer]);

        // Фильтры;
        if(isset($_POST['filters']) && $_POST['name'] == 'active' ) $query->andFilterWhere(['like', 'active', 1]);
        if(isset($_POST['filters']) && $_POST['name'] == 'status') $query->andFilterWhere(['like', 'status', $_POST['type']]);

        //$query->leftJoin(MessagesImages::tableName(),'messages.id = messages_images.message_id');
        //$query->select(['messages_images.id as message_id','messages.*']);
        return $dataProvider;
    }
}
