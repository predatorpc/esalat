<?php

namespace app\modules\common\models;

use Yii;

/**
 * This is the model class for table "user".
 *
 * @property integer $id
 * @property string $name
 * @property string $fullname // non active
 * @property string $auth_key
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $email
 * @property string $phone
 * @property integer $status
 * @property integer $role_description
 * @property string $description
 * @property integer $created_at
 * @property integer $updated_at
 * @property AuthAssignment $authAssignment
 */

class UserAdmin extends ActiveRecordRelation
{      	
//	public $id;
	public $role_description;
	public $role;
	public $role_name;
	public $password;
    public $auth_item;
    public $registration;

    public static function tableName(){  return 'users';    }

    public function rules()
    {
        return [
            [['id','phone'], 'unique', 'message' => 'Такой телефон уже зергистрирован'],
            [['name','phone'], 'required'],
            [['name', 'phone','role_description','role_name','role' ], 'string'],
            [['id','status','outsourcing', 'created_at', 'updated_at'], 'integer'],
            [['name', 'role_description', 'role', 'password', 'password_hash', 'password_reset_token',], 'string', 'max' => 255],
            [['auth_key', 'secret_word'], 'string', 'max' => 255],
			[['role_description', 'role', 'role_name', 'email', 'sms', 'registration', 'staff', 'typeof', 'store_id'], 'safe'],
            [['password'], 'string', 'min' => 6],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => Yii::t('admin', 'Пользователь'),
//            'fullname' => 'ФИО',
            'auth_key' => 'Auth Key',
            'password_hash' => 'Password Hash',
            'password_reset_token' => 'Password Reset Token',
            'email' => 'Email',
            'phone' => 'Phone',
            'outsourcing' => 'Outsourcing',
			'password' => 'Password',
            'status' => 'Status',
		    'role_description' => 'Role', 
			'role_name' => 'Rolename',
			'role' => Yii::t('admin', 'Роль'),
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'typeof' => 'Тип пользователя',
        ];
    }

    public function afterSave($insert, $model)
	{ if($this->password) {
        $user = User::findOne(['id' => $this->id]);
        $user->setPassword($this->password);
        $user->save();
    }
        if($this->role){
            $auth = Yii::$app->authManager;
            $auth->revokeAll($this->id);
            $role = $auth->getRole($this->role);
            $auth->assign($role, $this->id);
        }

		parent::afterSave($insert, $model);

	 	return true;
	}

    public function getAuth_Assignment()
    {
        return $this->hasOne(AuthAssignment::className(), ['user_id' => 'id']);
    }

    public function getAuth_item()
    {
        return $this->hasOne(AuthItem::className(), ['description' => 'role_description']);
    }

    public static function find()
    {
        return new UserQuery(get_called_class());
    }


}
