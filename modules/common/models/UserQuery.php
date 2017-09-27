<?php

namespace app\modules\common\models;

use Yii;
use yii\base\Model;
use yii\db\Query;
use yii\data\ActiveDataProvider;
use yii\db\ActiveRecord;
use yii\db\ActiveQuery;

/**
 *
 * @property string $description
 *
*/

class UserQuery extends \yii\db\ActiveQuery
{
    public function once($db = null){}
    public function all($db = null)
    {
        return parent::all($db);
    }

    public function one($db = null)
    {
        return parent::one($db);
    }
}