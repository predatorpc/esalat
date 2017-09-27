<?php

namespace app\modules\actions\models;

use app\modules\actions\models\ActionsAccumulation;
use app\modules\common\models\User;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\web\UploadedFile;

/**
 * This is the model class for table "actions".
 *
 * @property integer $id
 * @property string $title
 * @property string $description
 * @property string $file_type
 * @property integer $periodic
 * @property integer $date_start
 * @property integer $date_end
 * @property integer $priority
 * @property integer $block
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $created_user
 * @property integer $updated_user
 * @property integer $status
 */
class Actions extends \yii\db\ActiveRecord
{
    public $image;


    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at',
            ],
            [
                'class' => BlameableBehavior::className(),
                'createdByAttribute' => 'created_user',
                'updatedByAttribute' => 'updated_user',
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'actions';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title', 'status', 'priority', 'block', 'date_start', 'date_end'], 'required'],
            [['description'], 'string'],
            [['periodic', 'created_at', 'updated_at', 'created_user', 'updated_user', 'status', 'block', 'accumulation', 'accum_value', 'count_purchase', 'count_for_user', 'for_user_id'], 'integer'],
            ['priority', 'integer', 'min'=>1, 'max'=>999999, 'message'=>'Должен быть числом'],
            ['type_promo_code','integer'],
            //[['title', 'file_type'], 'string', 'max' => 250],
            ['title', 'string', 'max' => 250],
            ['count_for_user', 'default', 'value' => 0],
            ['file_type', 'string', 'max' => 250],
            ['date_start', 'validateDateStart'],
            [['date_start', 'date_end'],'validateDate' ],
            //['date_start','safe', 'when' => function($model) { return strtotime($model->date_start);}],
            //['date_end','safe', 'when' => function($model) { return strtotime($model->date_end);}]
            [['image'], 'image'],
            ['accum_value','default', 'value' => 0]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('actions', 'ID'),
            'title' => Yii::t('actions', 'Title'),
            'description' => Yii::t('actions', 'Description'),
            'file_type' => Yii::t('actions', 'file_type'),
            'periodic' => Yii::t('actions', 'Periodic'),
            'date_start' => Yii::t('actions', 'Date Start'),
            'date_end' => Yii::t('actions', 'Date End'),
            'priority' => Yii::t('actions', 'Priority'),
            'block' => Yii::t('actions', 'Block'),
            'accumulation' => Yii::t('actions', 'Accumulation'),
            'accum_value' => Yii::t('actions', 'Accum_value'),
            'count_purchase' => Yii::t('actions', 'Count_purchase'),
            'created_at' => Yii::t('actions', 'Created At'),
            'updated_at' => Yii::t('actions', 'Updated At'),
            'created_user' => Yii::t('actions', 'Created User'),
            'updated_user' => Yii::t('actions', 'Updated User'),
            'status' => Yii::t('actions', 'Status'),
            'type_promo_code' => 'Тип промокода',
        ];
    }
    public function validateDate(){
         if($this->date_start> $this->date_end){
            $this->addError('date_end', 'Дата окончания акции должна быть позже даты начала');
            $this->date_end = Date('d.m.Y', $this->date_end);
            $this->date_start = Date('d.m.Y', $this->date_start);
        }
    }
    public function validateDateStart(){
        if($this->isNewRecord){
            if($this->date_start < strtotime(Date('Y-m-d', time()))) {
                $this->addError('date_start', 'Дата начала должна быть не раньше чем сегодня');
                $this->date_end = Date('d.m.Y', $this->date_end);
                $this->date_start = Date('d.m.Y', $this->date_start);
            }
        }
    }
    public function getCreatedUser(){
        return User::findOne($this->created_user);
    }

    public function getUpdatedUser(){
        return User::findOne($this->updated_user);
    }

    public function getParamsValue(){
        return ActionsParamsValue::findAll(['action_id' => $this->id]);
    }

    public function getActionvalues(){
        return  $this->hasMany(ActionsParamsValue::className(),['action_id'=>'id'])->with('param')->where(['status'=>1]);
    }

    public function getParams(){
        return ActionsParams::find()->all();
    }

    public function getActionParamsValue(){
        return $this->hasMany(ActionsParamsValue::className(), ['action_id'=>'id'])->with('param');
    }

    public function getAccum(){
        return $this->hasMany(ActionsAccumulation::className(), ['action_id'=>'id'])
            ->select('action_id, action_param_value_id, SUM(current_value) as `current_value`, count(id) as `count_row`, sum(active) as `active_row`')
            ->where(['user_id'=>Yii::$app->user->id, 'status'=>1,])
            ->groupBy(['action_id', 'action_param_value_id'])
            ->with('actionParamValue');
    }

    public function uploadImage(){

        if ($this->validate()) {
            if(!empty($this->image)){
                $this->image->saveAs('../web'.Yii::$app->params['actionsImagePath'] . $this->id . '.' . $this->image->getExtension());
            }
            return true;
        }
        else {
            return false;
        }

        /*$imageName = !empty($this->file_type) ? $this->file_type : '';
        if(!empty(Yii::$app->request->post())){
            $image = UploadedFile::getInstance($this,'image');
            if(!empty($image)){
                $imageName = explode('.',$image->name);
                if(count($imageName) > 0){
                    $imageName = Yii::$app->params['actionsImagePath'].$this->id .'.'.$imageName[count($imageName)-1];
                    $image->saveAs($_SERVER['DOCUMENT_ROOT'].$imageName);
                }
            }
        }

        return $imageName;*/
    }



    public function afterSave($insert, $changedAttributes)
    {
        if(!empty(Yii::$app->request->post()['ActionsParamsValue'])){
            $actionsParamsValue = Yii::$app->request->post()['ActionsParamsValue'];
            $notArr = false;
            foreach ($actionsParamsValue as $item) {
                if(is_array($item)){
                    if(!empty($item['id'])){
                        $modelValues = ActionsParamsValue::findOne($item['id']);
                    }else{
                        $modelValues = new ActionsParamsValue();
                    }
                    if($modelValues){
                        $modelValues->action_id = $this->id;
                        $modelValues->param_id = $item['param_id'];
                        $modelValues->condition_value = $item['condition_value'];
                        $modelValues->discont_value = $item['discont_value'];
                        $modelValues->basket_price = empty($item['basket_price'])?0:$item['basket_price'];
                        $modelValues->save(true);
                    }
                }
                else{
                    $notArr = true;
                    break;
                }
            }

            if($notArr){
                if(!empty($actionsParamsValue['id'])){
                    $modelValues = ActionsParamsValue::findOne($actionsParamsValue['id']);
                }
                else{
                    $modelValues = new ActionsParamsValue();
                }
                if($modelValues){
                    $modelValues->action_id = $this->id;
                    $modelValues->param_id = $actionsParamsValue['param_id'];
                    $modelValues->condition_value = $actionsParamsValue['condition_value'];
                    $modelValues->discont_value = $actionsParamsValue['discont_value'];
                    $modelValues->basket_price = empty($item['basket_price'])?0:$actionsParamsValue['basket_price'];
                    $modelValues->save(true);
                }
            }
        }

        parent::afterSave($insert, $changedAttributes); // TODO: Change the autogenerated stub
    }
}
