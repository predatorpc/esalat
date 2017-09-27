<?php
namespace app\modules\pages\models;

use app\modules\common\models\Api;
use app\modules\common\models\User;
use app\modules\common\models\Zloradnij;
use yii\base\Model;
use Yii;

/**
 * Signup form
 */
class ForgotPasswordForm extends Model
{
    public $phone;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        $rules = [
            ['phone', 'required', 'message' => Yii::t('app', 'Введите телефон!')],
            ['phone', 'string', 'min' => 7, 'max' => 12, 'tooShort' => Yii::t('app', 'Введите телефон корректно!')],
            ['phone', 'validateFindByPhone', 'skipOnEmpty'=> false],
        ];

        return $rules;
    }

    public function validateFindByPhone(){
        if((new User())->findByPhone($this->phone)){

        }else{
            $this->addError('phone',Yii::t('app', 'Такой телефон не зарегистрирован в системе!'));
        }
    }

    public function setResetPasswordToken(){
        $findUser = User::findOne(['phone' => $this->phone]);
        if(!$findUser){
            $findUser = User::findOne(['phone' => '+7' . $this->phone]);
        }
        $code = rand(1000,9999);//uniqid();
        $findUser->password_reset_token = $code;//md5($code);
        $findUser->save();

        $message = Yii::t('app', 'Код для восстановления пароля').' - '  .$code;

        (new Api())->sms($this->phone,$message);

//        Zloradnij::print_arr($this->phone);
//        Zloradnij::print_arr($message);
//        Zloradnij::print_arr($findUser);

    }
}
