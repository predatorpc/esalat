<?php
namespace app\modules\pages\models;

use app\modules\common\models\User;
use yii\base\Model;
use yii\helpers\Html;
use Yii;

/**
 * Signup form
 */
class SignupForm extends Model
{
	public $phone;
//    public $username;
    public $name;
    public $email;
    public $password;
	public $verifyCode;
    public $registration;
    public $agreement;
//	public $reCaptcha;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        $rules = [
//            ['username', 'filter', 'filter' => 'trim'],
            ['phone', 'required', 'message' => Yii::t('app', 'Введите телефон!')],
            ['phone', 'unique', 'targetClass' => User::className(), 'message' => Yii::t('app', 'Такой номер телефона уже зарегистрирован') ],
            ['phone', 'string', 'min' => 7, 'max' => 12, 'tooShort' => Yii::t('app', 'Введите телефон корректно!')],
//            ['phone', 'required', 'message' => 'Такой телефон уже зарегистрирован', 'when' => function($model) {
//                return !(new User)->findByPhone($model->phone);
//            }],
            [['phone'], 'validateDouble'],

/*
* Родная валидация
*
            ['username', 'filter', 'filter' => 'trim'],
            ['username', 'required',  'message' => 'Введите имя пользователя!'],
//            ['username', 'unique', 'targetClass' => '\app\models\User', 'message' => 'This username has already been taken.'],
            ['username', 'string', 'min' => 2, 'max' => 255],
*/     

            ['agreement','required', 'message' => 'Agreement required'],
            ['agreement', 'validateAgree'],

            ['name', 'filter', 'filter' => 'trim'],
            ['name', 'required',  'message' => Yii::t('app', 'Введите имя пользователя!')],
//            ['name', 'unique', 'targetClass' => '\app\models\User', 'message' => 'This username has already been taken.'],
            ['name', 'string', 'min' => 2, 'max' => 50],
            ['name','validateUsername'],

            ['email', 'filter', 'filter' => 'trim'],
            ['email', 'required', 'message' => 'E-mail обязательное поле'],
            ['email', 'email', 'message' =>Yii::t('app', 'Неверный e-mail адресс')],
            ['email', 'string', 'max' => 255],
//            ['email', 'unique', 'targetClass' => '\common\models\User', 'message' => 'This email address has already been taken.'],
      
            ['password', 'required',  'message' => Yii::t('app', 'Введите пароль!')],
            ['password', 'string', 'min' => 6, 'tooShort' => Yii::t('app', 'Пароль минимум 6 символов!')],
            //['reCaptcha', ReCaptchaValidator::className(), 'secret' => '6LcgjCYTAAAAABUStOwdFFMIiW9-49qqT_VksU8B'],
            //[['reCaptcha'], \himiklab\yii2\recaptcha\ReCaptchaValidator::className(), 'secret' => '6LfliiYTAAAAAOZkHJwM0S8xzUlRRg0GwTj0d_SH', 'message'=>'111'],
            [['phone', 'password'], 'validateEmpty', 'skipOnEmpty'=> false],
            [['registration'], 'safe'],
        ];

//        if (!Yii::$app->request->isAjax) {
//            $rules[] = [['reCaptcha'], \himiklab\yii2\recaptcha\ReCaptchaValidator::className(), 'secret' => '6LcgjCYTAAAAABUStOwdFFMIiW9-49qqT_VksU8B'];
//        }
//
        return $rules;
    }
                                            
    /**
     * Signs user up.
     *
     * @return User|null the saved model or null if saving fails
     */

    public function validateDouble(){
        if((new User)->findByPhone($this->phone)){
            $errorMsg = Yii::t('app', 'Такой телефон уже зарегистрирован');
            $this->addError('phone',$errorMsg);
        }
    }

    public function validateAgree(){
        if(empty($this->agreement)){
            $errorMsg= Yii::t('app', 'Прочитайте пользовательское соглашение!');
            $this->addError('agreement',$errorMsg);
        }
    }

    public function validateUsername(){
        if(empty($this->name)){
            $errorMsg= Yii::t('app', 'Введите коректное имя пользователя!');
            $this->addError('name',$errorMsg);
        }
        if(!preg_match('/[\w]*/i', $this->name))
        {
            $errorMsg= Yii::t('app', 'Введите коректное имя пользователя!');
            $this->addError('name',$errorMsg);
        }
    }

    public function validateEmpty()
    {
		if(empty($this->phone))
		{
			$errorMsg= Yii::t('app', 'Введите телефон!');
			$this->addError('phone',$errorMsg);
		} 
		if(empty($this->password))
		{
			$errorMsg= Yii::t('app', 'Введите пароль!');
			$this->addError('password',$errorMsg);
		}
	}

    public function signup()
    {
        if (!$this->validate()) {
            return null;
        }

        $user = new User();

        if($user->findByPhone($this->phone)){
//            Yii::$app->session['signup-error'] = ['err' => 'phone'];
            return null;
        }

        $this->registration = date("Y-m-d H:i:s");

        $user->name = $this->name;
        $user->email = $this->email;
        $user->phone = $this->phone; //ubral +7 28.11.2016
//	$user->phone = "+7".$this->phone;
        $user->city_id = 1001;
        $user->store_id = 0;
        $user->registration = $this->registration;
        $user->created_at = strtotime($this->registration);
        $user->agree = $this->agreement;

        $user->setPassword($this->password);
        $user->generateAuthKey();

		if($signedUser = $user->save())
		{

//var_dump($user->id);
//die();
		//	return json_encode($signedUser);

			//Менеджер авторизации приложения(сам записывает/присваивает роль)
	        $auth = Yii::$app->authManager;
	        $role = $auth->getRole('user');
	        $auth->assign($role, $user->id);
            $_SESSION['basket-session-id'] = Yii::$app->session->id;
            Yii::$app->user->login($user, 3600*24*30);
			return $user;
		}
        return null;
    }
}
