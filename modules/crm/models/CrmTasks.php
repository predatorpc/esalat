<?php

namespace app\modules\crm\models;

use Yii;

/**
 * This is the model class for table "crm_tasks".
 *
 * @property int $id
 * @property string $date_create
 * @property string $department
 * @property int $creator
 * @property int $slave
 * @property string $name
 * @property string $description
 * @property int $progress
 * @property string $start
 * @property string $deadline
 */
class CrmTasks extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'crm_tasks';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'],'required'],
            [['date_create'], 'safe'],
            [['department'], 'required'],
            [['creator', 'slave', 'progress','priority'], 'integer'],
            [['description'], 'string'],
            [['department', 'name'], 'string', 'max' => 255],
            ['start', 'validateDateStart'],
            [['start', 'deadline'],'validateDate' ],
        ];
    }

    public function validateDate(){
        if(strtotime($this->start) > strtotime($this->deadline)){
            $this->addError('deadline', 'Дата окончания должна быть позже даты начала');
            //$this->deadline = Date('Y-m-d', $this->deadline);
            //$this->start = Date('Y-m-d', $this->start);
        }
    }
    public function validateDateStart(){
        if($this->isNewRecord){
            if(strtotime($this->start) < strtotime(Date('Y-m-d', time()))) {
                $this->addError('start', 'Дата начала должна быть не раньше чем сегодня');
                //$this->deadline = Date('Y-m-d', $this->deadline);
                //$this->start = Date('Y-m-d', $this->start);
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'date_create' => 'Дата создания',
            'creator' => 'Создал',
            'slave' => 'Назначено',
            'name' => 'Название',
            'description' => 'Описание',
            'progress' => 'Прогресс',
            'start' => 'Дата начала',
            'deadline' => 'Дата окончания',
            'department' => 'Отдел',
            'priority' => 'Приоритет'
        ];
    }
}
