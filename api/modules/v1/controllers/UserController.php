<?php

namespace api\modules\v1\controllers;


use api\modules\v1\resources\auth\User;
use api\modules\v1\resources\BlockUserForm;
use api\modules\v1\resources\GeneralResponse;
use yii\base\Exception;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\HttpHeaderAuth;
use yii\filters\auth\QueryParamAuth;
use api\modules\v1\common\Controller;
use yii\rest\IndexAction;
use yii\rest\OptionsAction;
use Yii;
use yii\web\MethodNotAllowedHttpException;

/**
 * for hostinger server
 * basePath="/api/web/",
 * for local server:
 * basePath="/",
 */

/**
 * @SWG\Swagger(
 *     schemes={"http","https"},
 *     basePath="/",
 *     @SWG\SecurityScheme(
 *         securityDefinition="Bearer",
 *         type="apiKey",
 *         name="Authorization",
 *         in="header",
 *         description="API key, example: Bearer XERaexzXHjA-SLArj2X7sQjjdvNqyMY_YLuzN-8m"
 *     ),
 *     @SWG\Info(
 *         version="1.0.0",
 *         title="Food-Delivery API Documentation",
 *         description="API usage description and tests",
 *         termsOfService="",
 *         @SWG\License(
 *             name="BSD License",
 *             url="https://raw.githubusercontent.com/yii2-starter-kit/yii2-starter-kit/master/LICENSE.md"
 *         )
 *     ),
 * )
 *
 *
 * @author Eugene Terentev <eugene@terentev.net>
 */
class UserController extends Controller
{
    /**
     * @SWG\Options(path="/v1/user/options",
     *     tags={"User"},
     *     summary="Displays the options for the User resource.",
     *     @SWG\Response(
     *         response = 200,
     *         description = "Displays the options available for the User resource",
     *         @SWG\Schema(ref = "#/definitions/UserResponse")
     *     ),
     * )
     */
    /**
     * @inheritdoc
     */
    public $modelClass = User::class;



    /**
     * @inheritdoc
     */
//    public function actions()
//    {
//        return [
//            'options' => [
//                'class' => OptionsAction::class
//            ]
//        ];
//    }
    public function actions()
    {

        // get actions [index, create, view, update, delete]
        $actions = parent::actions();


        // get all orders I created as User .. Or available for me as Owner
        $actions['customers-list'] = $actions['index'];
        $actions['customers-list']['prepareDataProvider'] = [$this, 'customersListDataProvider'];


        $actions['blocked-customers-list'] = $actions['index'];
        $actions['blocked-customers-list']['prepareDataProvider'] = [$this, 'blockedCustomersListDataProvider'];




        // Remove unused actions
        $delete = ['index','delete','create','update'];
        foreach ($delete as $key)
            unset($actions[$key]);

        return $actions;
    }

    /**
     * @SWG\Get(path="/v1/user/identity",
     *     tags={"User"},
     *     summary="get Current User data",
     *     description="get Current User data",
     *     security={
     *         {"Bearer":{}}
     *     },
     *     @SWG\Response(
     *         response = 200,
     *         description = "return User details",
     *         @SWG\Schema(ref = "#/definitions/UserResponse")
     *     ),
     * )
     *
     * @return User
     * @throws \Throwable
     */
    public function actionIdentity()
    {
        $resource = new User();
        $resource->load(\Yii::$app->user->getIdentity()->attributes, '');
        return  $resource;
    }


    /**
     * @SWG\Get(path="/v1/user/blocked-customers-list",
     *     tags={"User"},
     *     summary="get all blocked customers of the Owner",
     *     description="get all blocked customers of the Owner",
     *     security={
     *         {"Bearer":{}}
     *     },
     *     @SWG\Response(
     *         response = 200,
     *         description = "return list of Users",
     *         @SWG\Schema(ref = "#/definitions/UserResponse")
     *     ),
     * )
     *
     * @param IndexAction $action
     * @param array|null $filter
     * @return object
     * @throws MethodNotAllowedHttpException
     * @throws \Throwable
     * @throws \yii\base\InvalidConfigException
     */
    public function blockedCustomersListDataProvider(IndexAction $action, array $filter = null)
    {
        $requestParams = Yii::$app->getRequest()->getBodyParams();
        if (empty($requestParams)) {
            $requestParams = Yii::$app->getRequest()->getQueryParams();
        }

        // Validate: this API is for Owners Only
        $currentUser = \Yii::$app->user->getIdentity();
        $userRole = $currentUser->userRole; //1, 2, 3
        if($userRole!= 2){
            throw new MethodNotAllowedHttpException("This API is allowed for Owners Only");
        }

        $query = User::find()->myBlockedCustomersList();
        return $this->baseDataProvider($query, $action, $filter, true);
    }

    /**
     * @SWG\Get(path="/v1/user/customers-list",
     *     tags={"User"},
     *     summary="get all customers of the Owner",
     *     description="get all customers of the Owner",
     *     security={
     *         {"Bearer":{}}
     *     },
     *     @SWG\Response(
     *         response = 200,
     *         description = "return list of Users",
     *         @SWG\Schema(ref = "#/definitions/UserResponse")
     *     ),
     * )
     *
     * @param IndexAction $action
     * @param array|null $filter
     * @return object
     * @throws MethodNotAllowedHttpException
     * @throws \Throwable
     * @throws \yii\base\InvalidConfigException
     */
    public function customersListDataProvider(IndexAction $action, array $filter = null)
    {
        $requestParams = Yii::$app->getRequest()->getBodyParams();
        if (empty($requestParams)) {
            $requestParams = Yii::$app->getRequest()->getQueryParams();
        }

        // Validate: this API is for Owners Only
        $currentUser = \Yii::$app->user->getIdentity();
        $userRole = $currentUser->userRole; //1, 2, 3
        if($userRole!= 2){
            throw new MethodNotAllowedHttpException("This API is allowed for Owners Only");
        }


        $query = User::find()->myCustomersList();
        return $this->baseDataProvider($query, $action, $filter, true);
    }


    /**
     * @param ActiveQuery $query
     * @param IndexAction $action
     * @param array|null $filter
     * @param bool $isPagination
     * @return object
     * @throws \yii\base\InvalidConfigException
     */
    protected function baseDataProvider(ActiveQuery $query, IndexAction $action, array $filter = null, $isPagination = false)
    {
        $requestParams = Yii::$app->getRequest()->getBodyParams();
        if (empty($requestParams)) {
            $requestParams = Yii::$app->getRequest()->getQueryParams();
        }

        if (!empty($filter)) {
            $query->andWhere($filter);
        }
        $perPage = Controller::perPage;

        if (isset($requestParams['per-page'])) {
            $perPage = $requestParams['per-page'];
        }
        $pagination = false;
        if ($isPagination) {
            $pagination = [
                'pageSize' => $perPage,
            ];
        }

        return Yii::createObject([
            'class' => ActiveDataProvider::className(),
            'query' => $query,
            'pagination' => $pagination,
            'sort' => [
                // return new restaurants first
                'defaultOrder' => [
                    'created_at' => SORT_DESC
                ],
                'params' => $requestParams,
            ],
        ]);
    }

    /**
     *   @SWG\Post(path="/v1/user/update-block-user",
     *     tags={"User"},
     *     summary="to block Or unblock a user",
     *     description="Owner to block or unblock a user requires customer_id, is_block",
     *     security={
     *         {"Bearer":{}}
     *     },
     *     @SWG\Parameter(
     *         name="customer_id",
     *         in="formData",
     *         type="integer",
     *         description="customer_id",
     *         required=true,
     *     ),
     *     @SWG\Parameter(
     *         name="is_block",
     *         in="formData",
     *         type="integer",
     *         description=" 1 for YES or 0 for NO",
     *         required=true,
     *     ),
     *     @SWG\Response(
     *         response = 200,
     *         description = "return status true or false, with data as (User) or errors if exists",
     *         @SWG\Schema(ref = "#/definitions/GeneralResponse")
     *     ),
     * )
     *
     * @return GeneralResponse
     * @throws Exception
     * @throws MethodNotAllowedHttpException
     * @throws \Throwable
     * @throws \yii\base\InvalidConfigException
     */
    public function actionUpdateBlockUser()
    {

        if (!\Yii::$app->request->isPost) {
            throw new Exception("Only Post request is allowed");
        }

        // Validate: this API is for Owners Only
        $currentUser = \Yii::$app->user->getIdentity();
        $userRole = $currentUser->userRole; //1, 2, 3
        if($userRole!= 2){
            throw new MethodNotAllowedHttpException("This API is allowed for Owners Only");
        }

        $response = new GeneralResponse();
        $request = \Yii::$app->getRequest();
        $model = new BlockUserForm();
        $model->setAttributes($request->getBodyParams());
        $results = $model->updateIsBlocked();
        if ($results) {
            $response->data = $results;
            $response->status = true;

        } else {
            $response->errors = $model->errors;
            $response->status = false;
        }
        return $response;

    }

}
