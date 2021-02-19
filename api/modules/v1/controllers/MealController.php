<?php
/**
 * Created by PhpStorm.
 * User: Ayham
 * Date: 2/7/2021
 * Time: 8:58 PM
 */

namespace api\modules\v1\controllers;


use api\modules\v1\common\Controller;
use api\modules\v1\resources\meal\form\MealForm;
use api\modules\v1\resources\meal\Meal;
use yii\base\Exception;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;
use yii\rest\IndexAction;
use Yii;
use yii\web\ForbiddenHttpException;

class MealController extends Controller
{

    /**
     * @SWG\Options(path="/v1/meal/options",
     *     tags={"Meal"},
     *     summary="Displays the options for the Meal resource.",
     *     @SWG\Response(
     *         response = 200,
     *         description = "Displays the options available for the Meal resource",
     *         @SWG\Schema(ref = "#/definitions/Meal")
     *     ),
     * )
     */
    /**
     * @inheritdoc
     */
    public $modelClass = Meal::class;

    /**
     * @inheritdoc
     */
    public $createScenario = MealForm::SCENARIO_CREATE;
    /**
     * @inheritdoc
     */
    public $updateScenario = MealForm::SCENARIO_UPDATE;

    /**
     * create will create a meal
     * @SWG\Post(path="/v1/meal",
     *     tags={"Meal"},
     *     summary="to create a meal",
     *     description="to create a meal with name and description,price for a restaurant",
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
     *     @SWG\Parameter(
     *         name="price",
     *         in="formData",
     *         type="number",
     *         description="price",
     *         required=true,
     *     ),
     *     @SWG\Parameter(
     *         name="restaurant_id",
     *         in="formData",
     *         type="integer",
     *         description="restaurant_id",
     *         required=true,
     *     ),
     *     @SWG\Response(
     *         response = 200,
     *         description = "return the created meal with its details",
     *         @SWG\Schema(ref = "#/definitions/Meal")
     *     ),
     * )
     */

    /**
     * @SWG\Put(path="/v1/meal/{meal_id}",
     *     tags={"Meal"},
     *     summary="to edit a meal I created",
     *     description="to edit name and description price, is_deleted, of meal I created",
     *     security={
     *         {"Bearer":{}}
     *     },
     *      @SWG\Parameter(
     *          name="meal_id",
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
     *     @SWG\Parameter(
     *         name="price",
     *         in="formData",
     *         type="number",
     *         description="price",
     *         required=false,
     *     ),
     *     @SWG\Response(
     *         response = 200,
     *         description = "return the edited Meal",
     *         @SWG\Schema(ref = "#/definitions/Meal")
     *     ),
     * )
     */

    /**
     * @SWG\Delete(path="/v1/meal/{meal_id}",
     *     tags={"Meal"},
     *     summary="to delete a meal I created",
     *     description="To delete a meal I created - Delete it permanently ",
     *     security={
     *         {"Bearer":{}}
     *     },
     *      @SWG\Parameter(
     *          name="meal_id",
     *          in="path",
     *          required=true,
     *          type="integer"
     *      ),
     *     @SWG\Response(
     *         response = 200,
     *         description = "HTTP/1.1 204 No Content on success",
     *         @SWG\Schema(ref = "#/definitions/Meal")
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


        // get all meals I created as Owner .. Or available for me as User .. for given restaurant
        $actions['meal-list'] = $actions['index'];
        $actions['meal-list']['prepareDataProvider'] = [$this, 'mealListDataProvider'];



        // let create action use the form model
        $actions['create']['modelClass'] = MealForm::className();
        $actions['update']['modelClass'] = MealForm::className();


        // Remove unsued actions
        $delete = [];
        foreach ($delete as $key)
            unset($actions[$key]);

        return $actions;
    }


    /**
     * Check Access to create meal
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
     * @throws \Throwable
     */
    public function checkAccessUpdate(array $params = [],$model):void
    {
        $currentUser = \Yii::$app->user->getIdentity();
        $userRole = $currentUser->userRole; //1, 2, 3
        if($userRole!= 2){
            throw new ForbiddenHttpException('Only Owners Can Update a Meal');
        }

    }

    /**
     * @param array $params
     * @param $model
     * @throws ForbiddenHttpException
     */
    public function checkAccessDelete(array $params = [],$model):void
    {
            throw new ForbiddenHttpException('No one is allowed to delete a meal using APIs, Owners can update is_deleted');
    }



    /**
     * @SWG\Get(path="/v1/meal/meal-list",
     *     tags={"Meal"},
     *     summary="get all meals of given restaurant",
     *     description="get all meals that I created as Owner, or all available for me as User",
     *     security={
     *         {"Bearer":{}}
     *     },
     *     @SWG\Parameter(
     *         name="restaurant_id",
     *         in="query",
     *         type="integer",
     *         description=" restaurant_id",
     *         required=true,
     *     ),
     *     @SWG\Response(
     *         response = 200,
     *         description = "return list of RestaurantsMeals",
     *         @SWG\Schema(ref = "#/definitions/Meal")
     *     ),
     * )
     * Restaurant-Meal List Data Provider
     * @param IndexAction $action
     * @param array|null $filter
     * @return object
     * @throws \Throwable
     * @throws \yii\base\InvalidConfigException
     */
    public function mealListDataProvider(IndexAction $action, array $filter = null)
    {
        $requestParams = Yii::$app->getRequest()->getBodyParams();
        if (empty($requestParams)) {
            $requestParams = Yii::$app->getRequest()->getQueryParams();
        }

        $restaurant_id = null;
        if (isset($requestParams['restaurant_id'])) {
            $restaurant_id = $requestParams['restaurant_id'];
        }else{
            // throw error
            throw new Exception("restaurant_id is required");
        }

        $query = Meal::find()->myRestaurantMealsList($restaurant_id);
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


}