<?php
/**
 * Created by PhpStorm.
 * User: Ayham
 * Date: 2/6/2021
 * Time: 12:26 AM
 */

namespace api\modules\v1\controllers;


use api\modules\v1\common\Controller;
use api\modules\v1\resources\GeneralResponse;
use api\modules\v1\resources\restaurant\form\RestaurantForm;

use api\modules\v1\resources\restaurant\Restaurant;
use yii\base\Exception;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;
use yii\rest\IndexAction;
use Yii;
use yii\web\ForbiddenHttpException;

class RestaurantController extends Controller
{

    /**
     * @SWG\Options(path="/v1/restaurant/options",
     *     tags={"Restaurant"},
     *     summary="Displays the options for the Restaurant resource.",
     *     @SWG\Response(
     *         response = 200,
     *         description = "Displays the options available for the Restaurant resource",
     *         @SWG\Schema(ref = "#/definitions/Restaurant")
     *     ),
     * )
     */
    /**
     * @inheritdoc
     */
    public $modelClass = Restaurant::class;

    /**
     * @inheritdoc
     */
    public $createScenario = RestaurantForm::SCENARIO_CREATE;
    /**
     * @inheritdoc
     */
    public $updateScenario = RestaurantForm::SCENARIO_UPDATE;

    // https://stackoverflow.com/questions/45839934/laravel-post-delete-put-routes-in-swagger
    // For documentation of how to use swg annotitions

    /**
     * create will create a restaurant
     * @SWG\Post(path="/v1/restaurant",
     *     tags={"Restaurant"},
     *     summary="to create a restaurant",
     *     description="to create a restaurant with name and description",
     *     security={
     *         {"Bearer":{}}
     *     },
     *     @SWG\Parameter(
     *         name="name",
     *         in="formData",
     *         type="string",
     *         description="name",
     *         required=true,
     *     ),
     *     @SWG\Parameter(
     *         name="description",
     *         in="formData",
     *         type="string",
     *         description="description",
     *         required=true,
     *     ),
     *     @SWG\Response(
     *         response = 200,
     *         description = "return the created restaurant with its details",
     *         @SWG\Schema(ref = "#/definitions/Restaurant")
     *     ),
     * )
     */

    /**
     * @SWG\Put(path="/v1/restaurant/{restaurant_id}",
     *     tags={"Restaurant"},
     *     summary="to edit restaurant I created",
     *     description="to edit name, description, is_deleted of restaurant I created",
     *     security={
     *         {"Bearer":{}}
     *     },
     *      @SWG\Parameter(
     *          name="restaurant_id",
     *          in="path",
     *          required=true,
     *          type="integer"
     *      ),
     *     @SWG\Parameter(
     *         name="name",
     *         in="formData",
     *         type="string",
     *         description="name",
     *         required=false,
     *     ),
     *      @SWG\Parameter(
     *         name="description",
     *         in="formData",
     *         type="string",
     *         description="description",
     *         required=false,
     *     ),
     *      @SWG\Parameter(
     *         name="is_deleted",
     *         in="formData",
     *         type="integer",
     *         description="is deleted",
     *         required=false,
     *     ),
     *     @SWG\Response(
     *         response = 200,
     *         description = "return the edited Restaurant Resource",
     *         @SWG\Schema(ref = "#/definitions/Restaurant")
     *     ),
     * )
     */

    /**
     * @SWG\Delete(path="/v1/restaurant/{restaurant_id}",
     *     tags={"Restaurant"},
     *     summary="to delete a restaurant I created",
     *     description="To delete a restaurant I created - Delete it permanently",
     *     security={
     *         {"Bearer":{}}
     *     },
     *      @SWG\Parameter(
     *          name="restaurant_id",
     *          in="path",
     *          required=true,
     *          type="integer"
     *      ),
     *     @SWG\Response(
     *         response = 200,
     *         description = "HTTP/1.1 204 No Content",
     *         @SWG\Schema(ref = "#/definitions/Restaurant")
     *     ),
     * )
     */

    /**
     * @inheritdoc
     */
    public function actions()
    {

        // get actions [index, create, view, update, delete]
        $actions = parent::actions();


        // get all restaurants I created as Owner .. Or available for me as User
        ///v1/rating/criterias-list  ,, optional for_user_id if not for me
        $actions['restaurant-list'] = $actions['index'];
        $actions['restaurant-list']['prepareDataProvider'] = [$this, 'restaurantListDataProvider'];



        // let create action use the form model
        $actions['create']['modelClass'] = RestaurantForm::className();
        $actions['update']['modelClass'] = RestaurantForm::className();


        // Remove unsued actions
        $delete = [];
        foreach ($delete as $key)
            unset($actions[$key]);

        return $actions;
    }


    /**
     * Check Access to create a Restaurant
     * @param array $params
     * @throws ForbiddenHttpException
     * @throws \Throwable
     */
    public function checkAccessCreate(array $params = []):void
    {
        $currentUser = \Yii::$app->user->getIdentity();
        $userRole = $currentUser->userRole; //1, 2, 3
        if($userRole!= 2){
            throw new ForbiddenHttpException('Only Owners Can Create');
        }

    }

    /**
     * @param array $params
     * @param $model
     * @throws ForbiddenHttpException
     */
    public function checkAccessDelete(array $params = [],$model):void
    {
        throw new ForbiddenHttpException('No one is allowed to delete a restaurant using APIs, Owners can update is_deleted');
    }

    /**
     * Create Accesss Control
     * @param array $params
     * @param RestaurantForm $model
     * @throws ForbiddenHttpException
     * @throws \Throwable
     */
    public function checkAccessUpdate(array $params = [], RestaurantForm $model)
    {

        $currentUser = \Yii::$app->user->getIdentity();
        $userRole = $currentUser->userRole; //1, 2, 3
        if($userRole != 2){
            throw new ForbiddenHttpException('Only Owners Can Update a Restaurant');
        }
        return $this->checkAccessIsMyRestaurant($model);
    }

    /**
     * Create Accesss Control
     * @param $model
     * @throws ForbiddenHttpException
     * @throws \Throwable
     */
    private function checkAccessIsMyRestaurant($model)
    {
        $currentUser = \Yii::$app->user->getIdentity();
        $user_id = $currentUser->id;

        if ($model->owner_id != $user_id)
            throw new ForbiddenHttpException('This Restaurant does not belong to you!');
    }


    /**
     * @SWG\Get(path="/v1/restaurant/restaurant-list",
     *     tags={"Restaurant"},
     *     summary="get all restaurants",
     *     description="get all restaurants that I created as Owner, or all available for me as User",
     *     security={
     *         {"Bearer":{}}
     *     },
     *     @SWG\Response(
     *         response = 200,
     *         description = "return list of Restaurants",
     *         @SWG\Schema(ref = "#/definitions/Restaurant")
     *     ),
     * )
     * Restaurants List Data Provider
     * @param IndexAction $action
     * @param array|null $filter
     * @return object
     * @throws \Throwable
     * @throws \yii\base\InvalidConfigException
     */
    public function restaurantListDataProvider(IndexAction $action, array $filter = null)
    {
        $requestParams = Yii::$app->getRequest()->getBodyParams();
        if (empty($requestParams)) {
            $requestParams = Yii::$app->getRequest()->getQueryParams();
        }

        $query = Restaurant::find()->myRestaurantsList();
        return $this->baseDataProvider($query, $action, $filter, true);
    }


    /**
     *  Base Data Provider
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
     * @SWG\Get(path="/v1/restaurant/{restaurant_id}",
     *     tags={"Restaurant"},
     *     summary="get a Restaurant data",
     *     description="get a Restaurant data",
     *     security={
     *         {"Bearer":{}}
     *     },
     *     @SWG\Parameter(
     *         name="restaurant_id",
     *         in="path",
     *         type="integer",
     *         required=true,
     *     ),
     *     @SWG\Response(
     *         response = 200,
     *         description = "return Restaurant details",
     *         @SWG\Schema(ref = "#/definitions/Restaurant")
     *     ),
     * )
     */

//    /**
//     * @return GeneralResponse
//     * @throws Exception
//     */
//    public function actionGetRestaurant()
//    {
//        if (!\Yii::$app->request->isGet) {
//            throw new Exception("Only Get request is allowed");
//        }
//        $response = new GeneralResponse();
//        $requestParams = Yii::$app->getRequest()->getQueryParams();
//        $restaurantId = null;
//        if (isset($requestParams['restaurant_id'])) {
//            $restaurantId = $requestParams['restaurant_id'];
//            $results = Restaurant::find()->where(['id' => $restaurantId])->one();
//            if ($results) {
//                $response->data = $results;
//                $response->status = true;
//            } else {
//                $response->errors = 'No Such Restaurant !';
//                $response->status = false;
//            }
//
//        }else{
//            $response->errors = 'restaurant_id is required';
//            $response->status = false;
//        }
//        return $response;
//
//    }
}