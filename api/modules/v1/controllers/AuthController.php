<?php
/**
 * Created by PhpStorm.
 * User: Ayham
 * Date: 2/4/2021
 * Time: 1:58 PM
 */

namespace api\modules\v1\controllers;

use yii\rest\Controller;
use Yii;
use api\modules\v1\resources\auth\BaseAuthResponse;
use yii\base\Exception;
use yii\filters\Cors;
use yii\helpers\ArrayHelper;
use yii\rest\OptionsAction;
use yii\base\Model;

use api\modules\v1\resources\auth\form\LoginForm;
use api\modules\v1\resources\auth\form\PasswordResetRequestForm;
use api\modules\v1\resources\auth\form\ResetPasswordForm;
use api\modules\v1\resources\auth\form\SignupForm;
use api\modules\v1\resources\GeneralResponse;

class AuthController extends Controller
{
    /**
     * @var bool See details {@link \yii\web\Controller::$enableCsrfValidation}.
     */
    public $enableCsrfValidation = false;

    /**
     * @SWG\Options(path="/v1/auth/options",
     *     tags={"Auth"},
     *     summary="Displays the options for the Auth resource.",
     *     @SWG\Response(
     *         response = 200,
     *         description = "Displays the options available for the Auth resource",
     *         @SWG\Schema(ref = "#/definitions/AuthResponse")
     *     ),
     * )
     */

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();

        // add CORS filter
        $behaviors = ArrayHelper::merge($behaviors, [
            'corsFilter' => [
                'class' => Cors::class,
            ]
        ]);

        return $behaviors;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['delete'], $actions['create'], $actions['index'], $actions['view']);
        $actions['options'] = [
            'class' => OptionsAction::className(),
            'collectionOptions' => ['POST']
        ];
        return $actions;
    }


    /**
     * @SWG\Post(path="/v1/auth/sign-up",
     *     tags={"Auth"},
     *     summary="Sign up new User",
     *     description="create new account by filling the sign up form",
     *     @SWG\Parameter(
     *         name="firstname",
     *         in="formData",
     *         type="string",
     *         description="Your first name",
     *         required=true,
     *     ),
     *      @SWG\Parameter(
     *         name="lastname",
     *         in="formData",
     *         type="string",
     *         description="Your last name name",
     *         required=true,
     *     ),
     *      @SWG\Parameter(
     *         name="email",
     *         in="formData",
     *         type="string",
     *         description="email",
     *         required=true,
     *     ),
     *      @SWG\Parameter(
     *         name="password",
     *         in="formData",
     *         type="string",
     *         description="Your password",
     *         required=true,
     *     ),
     *      @SWG\Parameter(
     *         name="role",
     *         in="formData",
     *         type="integer",
     *         description="User Role (1 for regular User, 2 for Restaurant Owner)",
     *         required=true,
     *     ),
     *     @SWG\Response(
     *         response = 200,
     *         description = "return the created user object and an auth_key, Or return errors if something went wrong",
     *         @SWG\Schema(ref = "#/definitions/AuthResponse")
     *     ),
     * )
     *
     *
     * @return BaseAuthResponse
     * @throws Exception
     * @throws \yii\base\InvalidConfigException
     */
    public function actionSignUp()
    {
        if(!\Yii::$app->request->isPost)
        {
            throw new Exception("Only Post request is allowed");
        }
        // Set vars
        $model = new SignupForm;
        $response = new BaseAuthResponse;
        $request = Yii::$app->getRequest();
        // Load model
        $model->setAttributes($request->getBodyParams());
        if ($model->validate() ) {
            $user = $model->signup();
            if($user->hasErrors()){
                return $this->populateResponseWithErrors($response, $model);
            }else{
                return $this->populateResponseWithLinkedUser($response, $model);
            }
        }else{
            return $this->populateResponseWithErrors($response, $model);
        }
    }

    /**
     * @SWG\Post(path="/v1/auth/login",
     *     tags={"Auth"},
     *     summary="Login a user by email and password",
     *     description="login by filling the login form",
     *     @SWG\Parameter(
     *         name="identity",
     *         in="formData",
     *         type="string",
     *         description="email",
     *         required=true,
     *     ),
     *      @SWG\Parameter(
     *         name="password",
     *         in="formData",
     *         type="string",
     *         description="password",
     *         required=true,
     *     ),
     *     @SWG\Response(
     *         response = 200,
     *         description = "return the user object and an auth_key, Or return errors if something went wrong",
     *         @SWG\Schema(ref = "#/definitions/AuthResponse")
     *     ),
     * )
     *
     *
     * @return BaseAuthResponse
     * @throws Exception
     * @throws \yii\base\InvalidConfigException
     */
    public function actionLogin()
    {
        if(!\Yii::$app->request->isPost)
        {
            throw new Exception("Only Post request is allowed");
        }
        // Set vars
        $model = new LoginForm();
        $response = new BaseAuthResponse();
        $request = Yii::$app->getRequest();

        // Load model
        $model->setAttributes($request->getBodyParams());

        // Model errors
        if ($model->validate() &&  $model->login()) {
            // return
            return $this->populateResponseWithLinkedUser($response, $model);
        }
        return $this->populateResponseWithErrors($response, $model);


    }

    protected function populateResponseWithErrors(BaseAuthResponse &$response, Model &$model): BaseAuthResponse
    {
        $response->errors = $model->errors;
        return $response;
    }



    /**
     *
     * @SWG\Post(path="/v1/auth/request-password-reset",
     *     tags={"Auth"},
     *     summary="Request Password Reset",
     *     description="to send an email with link and token for resetting password",
     *     @SWG\Parameter(
     *         name="email",
     *         in="formData",
     *         type="string",
     *         description="email",
     *         required=true,
     *     ),
     *     @SWG\Response(
     *         response = 200,
     *         description = "return status true or false, with data or errors if needed",
     *         @SWG\Schema(ref = "#/definitions/GeneralResponse")
     *     ),
     * )
     *
     * @return GeneralResponse
     * @throws Exception
     * @throws \yii\base\InvalidConfigException
     */
    public function actionRequestPasswordReset()
    {

        if(!\Yii::$app->request->isPost)
        {
            throw new Exception("Only Post request is allowed");
        }

        $request = Yii::$app->getRequest();
        $model = new PasswordResetRequestForm();
        $response = new GeneralResponse();

        $model->setAttributes($request->getBodyParams());
        if (!$model->validate()) {
            $response->errors = $model->errors;
            $response->status = false;
            return $response;
        }

        if (!$model->sendEmail()) {
            $response->errors = "Can't send email";
            $response->status = false;
            return $response;
        }

        $response->status = true;
        $response->data = "email was sent successfully";
        return $response;
    }

    /**
     * @SWG\Post(path="/v1/auth/reset-password",
     *     tags={"Auth"},
     *     summary="Reset Password",
     *     description="Reset password by providing valid token and new password",
     *     @SWG\Parameter(
     *         name="token",
     *         in="formData",
     *         type="string",
     *         description="token",
     *         required=true,
     *     ),
     *     @SWG\Parameter(
     *         name="password",
     *         in="formData",
     *         type="string",
     *         description="new password",
     *         required=true,
     *     ),
     *     @SWG\Response(
     *         response = 200,
     *         description = "return the user object and an auth_key, Or return errors if something went wrong",
     *         @SWG\Schema(ref = "#/definitions/AuthResponse")
     *     ),
     * )
     *
     * @return BaseAuthResponse
     * @throws \Throwable
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\StaleObjectException
     */
    public function actionResetPassword()
    {
        $request = Yii::$app->getRequest();
        $response = new BaseAuthResponse;
        $model = new ResetPasswordForm();

        $model->setAttributes($request->getBodyParams());
        if ($model->validate() && $model->resetPassword() ) {
            return $this->populateResponseWithLinkedUser($response, $model->userToken);
        }
        return $this->populateResponseWithErrors($response, $model);
    }


    protected function populateResponseWithLinkedUser(BaseAuthResponse &$response, Model &$model): BaseAuthResponse
    {
        // Once user is ready to retrieved ,, let us set the language based on his profile
        if ($model->user->userProfile->locale) {
            $locale = $model->user->userProfile->locale;
            \Yii::$app->language = $locale;
        }

        $response->user = $model->user;
        if ($model->user)
            $response->setAttributes([
                'user' => $model->user,
                'auth_key' => $model->user->access_token
            ], false);

        return $response;
    }

}