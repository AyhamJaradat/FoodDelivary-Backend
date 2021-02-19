<?php
/**
 * Created by PhpStorm.
 * User: Ayham
 * Date: 2/4/2021
 * Time: 4:31 PM
 */

namespace api\modules\v1\resources\auth\form;


use api\modules\v1\resources\auth\User;
use common\commands\SendEmailCommand;
use common\models\UserToken;
use yii\base\Exception;
use yii\base\Model;
use Yii;
use yii\helpers\Url;

class SignupForm extends Model
{
    /**
     * @var
     */
    public $email;
    /**
     * @var
     */
    public $password;

    /**
     * Profile fields
     * @var
     */
    public $firstname;
    public $lastname;

    public $role;

    /**
     * @var string
     */
    public $locale;

    /**
     * @var User
     */
    private $_user = false;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['firstname', 'lastname'], 'required'],
            [['firstname', 'lastname'], 'string', 'max' => 255],

            ['email', 'filter', 'filter' => 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'unique',
                'targetClass' => '\common\models\User',
                'message' => Yii::t('frontend', 'This email address has already been taken.')
            ],

            ['password', 'required'],
            ['password', 'string', 'min' => 6],

            ['role','integer'],
            //set role to be one of two values
            [['role'], 'in',
                'range' => [1, 2],
                'message' => 'role must be integer: either 1 for RegularUser or 2 for Owner '],
            // set role default value as 1
            [['role'], 'default', 'value' => 1],

            ['locale', 'default', 'value' => 'en-US'],
            ['locale', 'safe'],
        ];
    }

    /**
     * Finds user by [[email]]
     *
     * @return User|null
     */
    public function getUser(): ?User
    {
        if ($this->_user === false) {
            $this->_user = User::find()->byEmail($this->email)->one();
        }
        return $this->_user;
    }

    public function setUser($user)
    {
        $this->_user = $user;
    }


    /**
     * Signs user up.
     *
     * @return User|null the saved model or null if saving fails
     * @throws Exception
     */
    public function signup()
    {

        $shouldBeActivated = $this->shouldBeActivated();
        $user = new User;
//        $user->usercode = User::generateUserCode();
        $user->email = $this->email;
        $user->status = $shouldBeActivated ? User::STATUS_NOT_ACTIVE : User::STATUS_ACTIVE;
        $user->setPassword($this->password);
        // save without validation because auth_key is not set yet
        if (!$user->save(false)) {
            $this->setUser($user);
            return $user;
//                throw new Exception("User couldn't be saved, errors:".json_encode($user->errors));
        };
        // get first name and last name and send them to afterSignup
        $profileData = [];

        $profileData['firstname'] = $this->firstname;
        $profileData['lastname'] = $this->lastname;
        $profileData['locale'] = $this->locale;

        $user->afterSignup($profileData, $this->role);

        $this->setUser($user);

        if ($shouldBeActivated) {
            $token = UserToken::create(
                $user->id,
                UserToken::TYPE_ACTIVATION,
                null//   Time::SECONDS_IN_A_DAY
            );
            Yii::$app->commandBus->handle(new SendEmailCommand([
                'subject' => Yii::t('frontend', 'Activation email'),
                'view' => '@common/mail/activation',
                'to' => $this->email,
                'params' => [
                    'url'=> Url::to('@frontendUrl/frontend/web/user/sign-in/activation?token='.$token->token, true),
//                    'url'=> Url::to('@frontendUrl/user/sign-in/activation?token='.$token->token, true),
//                    'url' => Url::to(['/user/sign-in/activation', 'token' => $token->token], true)
                ]
            ]));
        }
        return $user;

    }

    /**
     * @return bool
     */
    public function shouldBeActivated()
    {
        /** @var  $userModule */
        $userModule = Yii::$app->getModule('v1');
        if (!$userModule) {
            return false;
        } elseif ($userModule->shouldBeActivated) {
            return true;
        } else {
            return false;
        }
    }

}