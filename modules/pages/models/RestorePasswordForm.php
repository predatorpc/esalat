<?php
namespace app\modules\pages\models;

use app\modules\common\models\User;
use yii\base\Model;
use Yii;

/**
 * Signup form
 */
class RestorePasswordForm extends Model
{
    public $code;
    public $password;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        $rules = [
            ['code', 'required', 'message' => Yii::t('app', 'Обязательное поле!') ],
            ['password', 'required', 'message' => Yii::t('app', 'Обязательное поле!')],
            ['password', 'string', 'min' => 6, 'tooShort' => Yii::t('app', 'Слишком короткий пароль!')],
            ['code','validateCode', 'message' => Yii::t('app', 'Неверный код!')],
        ];

        return $rules;
    }

    public function validateCode(){
//        $findUser = User::find()->where(['password_reset_token' => md5($this->code)])->andWhere(['>=','updated_at',(time() - 60*7)])->one();
    $findUser = User::find()->where(['password_reset_token' => $this->code])->andWhere(['>=','updated_at',(time() - 60*7)])->one();
        if(!$findUser){
            $this->addError('code',Yii::t('app', 'Неверный код или истекло время ввода'));
        }else{
//            if($this->updatePassword($findUser,$this->password)){
//
//            }else{
//                $this->addError('password','что-то пошло не так...');
//            }
        }
    }

    public function updatePassword(User $user,$password){
        $user->setPassword($password);
        $user->generateAuthKey();
        $user->password_reset_token = NULL;
        if($user->save()){
            return true;
        }else{
            return false;//$user->errors;
        }
    }
}
