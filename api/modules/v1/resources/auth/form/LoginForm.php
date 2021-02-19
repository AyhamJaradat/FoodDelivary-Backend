<?php
/**
 * Created by PhpStorm.
 * User: Ayham
 * Date: 2/4/2021
 * Time: 4:22 PM
 */

namespace api\modules\v1\resources\auth\form;


use api\modules\v1\resources\auth\User;
use yii\base\Model;
use Yii;

class LoginForm extends Model
{
    public $identity;
    public $password;

    private $user = false;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // usercode and password are both required
            [['identity', 'password'], 'required'],
            // password is validated by validatePassword()
            ['password', 'validatePassword'],
        ];
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     */
    public function validatePassword()
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError('password', Yii::t('frontend', 'Incorrect email or password.'));
            }
        }
    }

    /**
     * Finds user by [[identity]]
     * @return array|bool|\common\models\User|null
     */
    public function getUser()
    {
        if ($this->user === false) {
            $this->user = User::find()
                ->andWhere(['email' => $this->identity])
                ->one();
        }

        return $this->user;
    }

    /**
     * Logs in a user using the provided identity (email or code) and password.
     *
     * @return boolean whether the user is logged in successfully
     */
    public function login()
    {
        if ($this->validate()) {
            return true;
        }
        return false;
    }

}