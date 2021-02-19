<?php
/**
 * Created by PhpStorm.
 * User: Ayham
 * Date: 2/4/2021
 * Time: 4:26 PM
 */

namespace api\modules\v1\resources\auth\form;


use api\modules\v1\resources\auth\User;
use common\commands\SendEmailCommand;
use common\models\UserToken;
use yii\base\Model;
use cheatsheet\Time;
use Yii;
use yii\helpers\Url;

class PasswordResetRequestForm extends Model
{
    /**
     * @var User email
     */
    public $email;
    public $baseUrl;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['email', 'filter', 'filter' => 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'exist',
                'targetClass' => '\common\models\User',
//                'filter' => ['status' => User::STATUS_ACTIVE],
                'message' => 'There is no user with such email.'
            ],
            ['baseUrl', 'string'],
            ['baseUrl', 'default', 'value' => 'http://fooddilevery/app/reset-password'],
        ];
    }


    /**
     * Sends an email with a link, for resetting the password.
     * @return bool|mixed whether the email was send
     * @throws \trntv\bus\exceptions\MissingHandlerException
     * @throws \yii\base\Exception
     * @throws \yii\base\InvalidConfigException
     */
    public function sendEmail()
    {
        /* @var $user User */
        $user = User::findOne([
//            'status' => User::STATUS_ACTIVE,
            'email' => $this->email,
        ]);

        if ($user) {
            $token = UserToken::create($user->id, UserToken::TYPE_PASSWORD_RESET, Time::SECONDS_IN_A_DAY);
            if ($user->save()) {
                $resetUrl = $this->baseUrl.'?token='.$token->token;
                return Yii::$app->commandBus->handle(new SendEmailCommand([
                    'to' => $this->email,
                    'subject' => Yii::t('frontend', 'Password reset for {name}', ['name' => Yii::$app->name]),
                    'view' => '@common/mail/passwordResetToken',
                    'params' => [
                        'resetLink'=> $resetUrl,
//                        'resetLink'=> Url::to('@frontendUrl/user/sign-in/reset-password?token='.$token->token, true),
                        'user' => $user->userProfile->getFullName() ?? $user->email

                    ]
                ]));
            }
        }

        return false;
    }


}