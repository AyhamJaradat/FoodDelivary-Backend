<?php
/**
 * Created by PhpStorm.
 * User: Ayham
 * Date: 2/4/2021
 * Time: 4:30 PM
 */

namespace api\modules\v1\resources\auth\form;


use common\models\UserToken;
use yii\base\Model;

class ResetPasswordForm extends Model
{
    /**
     * @var
     */
    public $password;
    /**
     * @var
     */
    public $token;
    /**
     * @var UserToken
     */
    public $userToken;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            'required' => [['password', 'token'], 'required', 'message' => 'required'],
            [['password','token'],'string', 'min' => 6],
            'token' => ['token', 'validateToken']
        ];
    }

    /**
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validateToken($attribute, $params, $validator): void
    {
        // Only if no other errors found
        if ($this->hasErrors())
            return;

        // Find the token
        $this->userToken = UserToken::find()
            ->notExpired()
            ->byType(UserToken::TYPE_PASSWORD_RESET)
            ->byToken($this->token)
            ->one();

        // If no token the validator fails
        if ( !$this->userToken )
            $this->addError($attribute, 'Invalid password reset token.');
    }

    /**
     * Resets password.
     * @return bool if password was reset.
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function resetPassword()
    {
        $user = $this->userToken->user;
        $user->password = $this->password;

        if ($user->save()) {
            $this->userToken->delete();
        }

        return true;
    }

}