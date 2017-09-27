<?php

namespace app\modules\pages\models;

use app\models\AuthAssignment;
use app\modules\common\models\BasketFakeUsers;
use app\modules\coders\models\ParentUser;
use app\modules\common\models\User;
use yii\base\Model;
use Yii;

/**
 * LoginForm is the model behind the login form.
 */
class LoginForm extends Model
{
    public $name;
	public $phone;
    public $password;
    public $rememberMe = true;

    private $_user = false;


    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            ['phone', 'required', 'message' => Yii::t('app', 'Введите телефон!')],
//            ['phone', 'unique', 'targetClass' => '\app\models\User', 'message' => 'Такой номер телефона уже зарегистрирован'],
            ['phone', 'string', 'min' => 7, 'max' => 12, 'tooShort' => 'Номер должен быть от 7 до 10 цифр!'],
            // username and password are both required
//            [['username', 'password'], 'required'],
            // rememberMe must be a boolean value
            ['rememberMe', 'boolean'],
            // password is validated by validatePassword()
            ['password', 'validatePassword'],
			[['phone','password'], 'validateEmpty', 'skipOnEmpty'=> false]
        ];
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */


	public function validateEmpty()
	{
		if(empty($this->phone))
		{
			$errorMsg= Yii::t('app', 'Введите телефон!');
			$this->addError('phone',$errorMsg);
		} 
		if(empty($this->password))
		{
			$errorMsg = Yii::t('app', 'Введите пароль!');
			$this->addError('password',$errorMsg);
		}
	}

    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();

            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError($attribute, Yii::t('app', 'Неверное имя пользователя или пароль!'));
            }
        }
    }

    /**
     * Logs in a user using the provided username and password.
     * @return boolean whether the user is logged in successfully
     */
    public function login()
    {
        if ($this->validate()) {
//            $parentUser = new ParentUser();
//            return $parentUser->login($this->getUser(), $this->rememberMe ? 3600*24*30 : 0);
            $_SESSION['basket-session-id'] = Yii::$app->session->id;
            return Yii::$app->user->login($this->getUser(), $this->rememberMe ? 3600*24*30 : 0);
        }
        return false;
    }

    public function loginByName()
    {
        if ($this->validate()) {
            return Yii::$app->user->login($this->getUserByName(), $this->rememberMe ? 3600*24*30 : 0);
        }
        return false;
    }


    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
    protected function getUserByName()
    {
        if ($this->_user === null) {
            $this->_user = User::findByUsername($this->name);
        }

        return $this->_user;
    }


    protected function getUser()
    {

        if ($this->_user === null || $this->_user==false) {
            $this->_user = User::findByPhone($this->phone);
		}
        return $this->_user;
    }

/*
 *
 * Old function when we login by username
 * predator_pc 18052016@0937
 *
   public function getUser()
    {
        if ($this->_user === false) {
            $this->_user = User::findByUsername($this->username);
        }

        return $this->_user;
    }
*/


    public function loginGod()
    {
        $user  =User::findByPhone($this->phone);
        if ($user && $user->staff == NULL){
            $role = Yii::$app->authManager->getRolesByUser($user->id);
            if(isset($role['user']) || $role == NULL){
                $fake_user = new BasketFakeUsers();
                $fake_user->fake_user = $user->id;
                $fake_user->user = Yii::$app->user->getId();
                $fake_user->date = date('Y-m-d H:i:s');
                $fake_user->status = 1;
                if(!$fake_user->save()){
                    print_r($fake_user->getErrors());
                    die;
                }

                $_SESSION['basket-session-id'] = Yii::$app->session->id;
                return Yii::$app->user->login($user, $this->rememberMe ? 3600*24*30 : 0);
            }

        }
        return false;
    }
}
