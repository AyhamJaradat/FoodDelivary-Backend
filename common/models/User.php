<?php

namespace common\models;

use common\commands\AddToTimelineCommand;
use common\models\query\UserQuery;
use Yii;
use yii\behaviors\AttributeBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\web\IdentityInterface;

/**
 * User model
 * This is the model class for table "{{%user}}".
 *
 * @property integer $id
 * @property string $username
 * @property string $password_hash
 * @property string $email
 * @property string $auth_key
 * @property string $access_token
 * @property string $oauth_client
 * @property string $oauth_client_user_id
 * @property string $publicIdentity
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $logged_at
 * @property string $password write-only password
 *
 *
 * @property Meal[] $mealsCreatedByMe
 * @property Order[] $myOrders
 * @property Restaurant[] $myRestaurants
 * @property UserProfile $userProfile
 * @property UserBlock[] $userBlocks
 * @property UserBlock[] $userBlocks0
 */
class User extends ActiveRecord implements IdentityInterface
{
    const STATUS_NOT_ACTIVE = 1;
    const STATUS_ACTIVE = 2;
    const STATUS_DELETED = 3;

    const ROLE_USER = 'user';// Regular User
    const ROLE_MANAGER = 'manager'; // Restaurant Owner
    const ROLE_ADMINISTRATOR = 'administrator';// Project admin

    const EVENT_AFTER_SIGNUP = 'afterSignup';
    const EVENT_AFTER_LOGIN = 'afterLogin';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user}}';
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::find()
            ->active()
            ->andWhere(['id' => $id])
            ->one();
    }

    /**
     * @return UserQuery
     */
    public static function find()
    {
        return new UserQuery(get_called_class());
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::find()
            ->active()
            ->andWhere(['access_token' => $token])
            ->one();
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return User|array|null
     */
    public static function findByUsername($username)
    {
        return static::find()
            ->active()
            ->andWhere(['username' => $username])
            ->one();
    }

    /**
     * Finds user by username or email
     *
     * @param string $login
     * @return User|array|null
     */
    public static function findByLogin($login)
    {
        return static::find()
            ->active()
            ->andWhere(['or', ['username' => $login], ['email' => $login]])
            ->one();
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::class,
            'auth_key' => [
                'class' => AttributeBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'auth_key'
                ],
                'value' => Yii::$app->getSecurity()->generateRandomString()
            ],
            'access_token' => [
                'class' => AttributeBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'access_token'
                ],
                'value' => function () {
                    return Yii::$app->getSecurity()->generateRandomString(40);
                }
            ]
        ];
    }

    /**
     * @return array
     */
    public function scenarios()
    {
        return ArrayHelper::merge(
            parent::scenarios(),
            [
                'oauth_create' => [
                    'oauth_client', 'oauth_client_user_id', 'email', 'username', '!status'
                ]
            ]
        );
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['auth_key', 'access_token', 'password_hash', 'email'], 'required'],
            [['status', 'created_at', 'updated_at', 'logged_at'], 'integer'],
            [['username', 'auth_key'], 'string', 'max' => 32],
            [['access_token'], 'string', 'max' => 40],
            [['password_hash', 'oauth_client', 'oauth_client_user_id', 'email'], 'string', 'max' => 255],
            [['username', 'email'], 'unique'],
            ['status', 'default', 'value' => self::STATUS_NOT_ACTIVE],
            ['status', 'in', 'range' => array_keys(self::statuses())],
            [['username'], 'filter', 'filter' => '\yii\helpers\Html::encode']
        ];

    }

    /**
     * Returns user statuses list
     * @return array|mixed
     */
    public static function statuses()
    {
        return [
            self::STATUS_NOT_ACTIVE => Yii::t('common', 'Not Active'),
            self::STATUS_ACTIVE => Yii::t('common', 'Active'),
            self::STATUS_DELETED => Yii::t('common', 'Deleted')
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('common', 'ID'),
            'auth_key' => Yii::t('common', 'Auth Key'),
            'username' => Yii::t('common', 'Username'),
            'email' => Yii::t('common', 'E-mail'),
            'status' => Yii::t('common', 'Status'),
            'access_token' => Yii::t('common', 'API access token'),
            'password_hash' => Yii::t('common', 'Password Hash'),
            'oauth_client' => Yii::t('common', 'Oauth Client'),
            'oauth_client_user_id' => Yii::t('common', 'Oauth Client User ID'),
            'created_at' => Yii::t('common', 'Created at'),
            'updated_at' => Yii::t('common', 'Updated at'),
            'logged_at' => Yii::t('common', 'Last login'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserProfile()
    {
        return $this->hasOne(UserProfile::class, ['user_id' => 'id']);
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->getSecurity()->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->getSecurity()->generatePasswordHash($password);
    }


    /**
     * Creates user profile and application event
     * @param array $profileData
     * @param int $role
     * @throws \trntv\bus\exceptions\MissingHandlerException
     * @throws \yii\base\InvalidConfigException
     */
    public function afterSignup(array $profileData = [], $role = 1)
    {
        $this->refresh();
        Yii::$app->commandBus->handle(new AddToTimelineCommand([
            'category' => 'user',
            'event' => 'signup',
            'data' => [
                'public_identity' => $this->getPublicIdentity(),
                'user_id' => $this->getId(),
                'created_at' => $this->created_at
            ]
        ]));
        $profile = new UserProfile();
        $profile->locale = Yii::$app->language;
        $profile->load($profileData, '');
        $this->link('userProfile', $profile);
        $this->trigger(self::EVENT_AFTER_SIGNUP);
        // Default role
        $auth = Yii::$app->authManager;
        if($role == 1){
            $auth->assign($auth->getRole(User::ROLE_USER), $this->getId());
        }else if ($role ==2){
            $auth->assign($auth->getRole(User::ROLE_MANAGER), $this->getId());
        }else{
            $auth->assign($auth->getRole(User::ROLE_ADMINISTRATOR), $this->getId());
        }

    }

    /**
     * @return string
     */
    public function getPublicIdentity()
    {
        if ($this->userProfile && $this->userProfile->getFullname()) {
            return $this->userProfile->getFullname();
        }
        if ($this->username) {
            return $this->username;
        }
        return $this->email;
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }


    /**
     * Gets query for [[MealsCreatedByMe]].
     *
     * @return \yii\db\ActiveQuery|\common\models\query\MealQuery
     */
    public function getMealsCreatedByMe()
    {
        return $this->hasMany(Meal::className(), ['created_by' => 'id']);
    }

    /**
     * Gets query for [[MyOrders]].
     * Orders created by regular user
     *
     * @return \yii\db\ActiveQuery|\common\models\query\OrderQuery
     */
    public function getMyOrders()
    {
        return $this->hasMany(Order::className(), ['user_id' => 'id']);
    }

    /**
     * Gets query for [[MyRestaurants]].
     * restaurants I created ,, I am the owner of them
     * // for manager role
     *
     * @return \yii\db\ActiveQuery|\common\models\query\RestaurantQuery
     */
    public function getMyRestaurants()
    {
        return $this->hasMany(Restaurant::className(), ['owner_id' => 'id']);
    }

    /**
     * Gets query for [[UserBlocks]].
     * all Users I blocked
     *
     * @return \yii\db\ActiveQuery|\common\models\query\UserBlockQuery
     */
    public function getUserBlocked()
    {
        return $this->hasMany(UserBlock::className(), ['owner_id' => 'id']);
    }
    /**
     * Gets query for [[UserBlocks0]].
     * all owners that blocked me
     * @return \yii\db\ActiveQuery|\common\models\query\UserBlockQuery
     */
    public function getUserBlocking()
    {
        return $this->hasMany(UserBlock::className(), ['user_id' => 'id']);
    }

    public function fields()
    {
        $fields = parent::fields();

//        $unsetFields = ['auth_key', 'access_token', 'password_hash'];
//        foreach ($unsetFields as $key)
//            unset($fields[$key]);

        $fields = ArrayHelper::merge($fields, [
            'userRole',
        ]);

        return $fields;
    }

    /**
     * @return int
     * @throws \Exception
     */
    public function getUserRole(){
        // Default role
        $auth = Yii::$app->authManager;
        $roles = $auth->getRolesByUser($this->getId());


        if(isset($roles[User::ROLE_ADMINISTRATOR])){
            return 3;//administrator
        }else  if(isset($roles[User::ROLE_MANAGER])){
            return 2;//manager :: Restaurant Owner
        }else  if(isset($roles[User::ROLE_USER])){
            return 1;//user :: Regular User
        }else{
            return 1;// user
        }
    }

}
