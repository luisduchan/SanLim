<?php

namespace common\modules\sanlim\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\modules\sanlim\models\Component;

/**
 * ComponentSearch represents the model behind the search form about `common\modules\sanlim\models\Component`.
 */
class ComponentSearch extends Component
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['item_no'], 'safe'],
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
        $query = Component::find();

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
        $query->andFilterWhere(['like', 'item_no', $this->item_no]);

        return $dataProvider;
    }
}
