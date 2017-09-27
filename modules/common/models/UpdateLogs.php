<?php

namespace app\modules\common\models;

use Yii;

/**
 * This is the model class for table "goods_variations".
 *
 * @property integer $id
 * @property integer $good_id
 * @property string $code
 * @property string $full_name
 * @property string $name
 * @property string $description
 * @property string $price
 * @property string $comission
 * @property integer $status
 */
class UpdateLogs extends \yii\db\ActiveRecord
{
    public function beforeSave($insert)
    {
        if(parent::beforeSave($insert)){
            foreach ($this->activeAttributes() as $attr){
                if(isset($this->attributes[$attr], $this->oldAttributes[$attr])){
                    if($this->attributes[$attr] != $this->oldAttributes[$attr]){
                        $log = new LogsDouble();
                        $log->user_id = isset(Yii::$app->user->id)?Yii::$app->user->id:0;
                        $log->action = $insert ? 'create':'update';
                        $log->table_edit = $this::tableName();
                        $log->colum_edit = $attr;
                        $log->row_edit_id = $this->attributes['id'];
                        $log->new_val = strlen($this->attributes[$attr])>500?'over_size': strval($this->attributes[$attr]);
                        $log->old_val = strlen($this->oldAttributes[$attr])>500?'over_size':strval($this->oldAttributes[$attr]);
                        $log->save(true);
                    }
                }
            }
            return true;
        }
        return false;

    }
}
