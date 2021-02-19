<?php
/**
 * Created by PhpStorm.
 * User: Ayham
 * Date: 2/9/2021
 * Time: 1:10 AM
 */

namespace api\modules\v1\controllers;


use api\modules\v1\common\Controller;
use api\modules\v1\resources\order\form\OrderForm;
use api\modules\v1\resources\order\Order;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;
use yii\rest\IndexAction;
use Yii;
use yii\web\ForbiddenHttpException;

class OrderController extends Controller
{

    /**
     * @SWG\Options(path="/v1/order/options",
     *     tags={"Order"},
     *     summary="Displays the options for the Order resource.",
     *     @SWG\Response(
     *         response = 200,
     *         description = "Displays the options available for the Order resource",
     *         @SWG\Schema(ref = "#/definitions/Order")
     *     ),
     * )
     */
    /**
     * @inheritdoc
     */
    public $modelClass = Order::class;

    /**
     * @inheritdoc
     */
    public $createScenario = OrderForm::SCENARIO_CREATE;
    /**
     * @inheritdoc
     */
    public $updateScenario = OrderForm::SCENARIO_UPDATE;

    //https://swagger.io/docs/specification/data-models/data-types/#array

    /**
     * create will create an Order, orderMeals, and OrderLog
     * @SWG\Post(path="/v1/order",
     *     tags={"Order"},
     *     summary="to create an Order",
     *     description="to create an Order",
     *     security={
     *         {"Bearer":{}}
     *     },
     *     @SWG\Parameter(
     *         name="status",
     *         in="formData",
     *         type="integer",
     *         description="status id, default to 1 Placed",
     *         required=false,
     *     ),
     *     @SWG\Parameter(
     *         name="restaurant_id",
     *         in="formData",
     *         type="integer",
     *         description="restaurant_id",
     *         required=true,
     *     ),
     *     @SWG\Parameter(
     *         name="meal_ids",
     *         in="formData",
     *         type="array",
     *         @SWG\Items(
     *          type="integer"
     *         ),
     *         description="list of meal_ids",
     *         required=true,
     *     ),
     *     @SWG\Parameter(
     *         name="meal_counts",
     *         in="formData",
     *         type="array",
     *         @SWG\Items(
     *          type="integer"
     *         ),
     *         description="list of meal_counts",
     *         required=true,
     *     ),
     *     @SWG\Response(
     *         response = 200,
     *         description = "return the created Order with its details",
     *         @SWG\Schema(ref = "#/definitions/Order")
     *     ),
     * )
     */

    /**
     * @SWG\Put(path="/v1/order/{order_id}",
     *     tags={"Order"},
     *     summary="to update an Order Status By Owner or User",
     *     description="to update an Order Status By Owner or User",
     *     security={
     *         {"Bearer":{}}
     *     },
     *      @SWG\Parameter(
     *          name="order_id",
     *          in="path",
     *          required=true,
     *          type="integer"
     *      ),
     *     @SWG\Parameter(
     *         name="status",
     *         in="formData",
     *         type="integer",
     *         description="the new status",
     *         required=true,
     *     ),
     *     @SWG\Response(
     *         response = 200,
     *         description = "return the edited Order",
     *         @SWG\Schema(ref = "#/definitions/Order")
     *     ),
     * )
     */

    /**
     * @SWG\Get(path="/v1/order/{order_id}",
     *     tags={"Order"},
     *     summary="get an Order data",
     *     description="get an Order data",
     *     security={
     *         {"Bearer":{}}
     *     },
     *     @SWG\Parameter(
     *         name="order_id",
     *         in="path",
     *         type="integer",
     *         required=true,
     *     ),
     *     @SWG\Response(
     *         response = 200,
     *         description = "return an Order details",
     *         @SWG\Schema(ref = "#/definitions/Order")
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


        // get all orders I created as User .. Or available for me as Owner
        $actions['orders-list'] = $actions['index'];
        $actions['orders-list']['prepareDataProvider'] = [$this, 'ordersListDataProvider'];


        $actions['pending-action-list'] = $actions['index'];
        $actions['pending-action-list']['prepareDataProvider'] = [$this, 'pendingActionListDataProvider'];





        // let create action use the form model
        $actions['create']['modelClass'] = OrderForm::className();
        $actions['update']['modelClass'] = OrderForm::className();


        // Remove unused actions
        $delete = ['delete'];
        foreach ($delete as $key)
            unset($actions[$key]);

        return $actions;
    }

    /**
     * Check Access to create an Order
     * @param array $params
     * @throws ForbiddenHttpException
     * @throws \Throwable
     */
    public function checkAccessCreate(array $params = []):void
    {
        $currentUser = \Yii::$app->user->getIdentity();
        $userRole = $currentUser->userRole; //1, 2, 3
        if($userRole!= 1){
            throw new ForbiddenHttpException('Only Regular User Can Create');
        }

    }

    /**
     * Check Access view of an Order
     * @param array $params
     * @throws ForbiddenHttpException
     * @throws \Throwable
     */
    public function checkAccessView(array $params = [],$model)
    {
        return $this->checkAccessIsMyOrder($model);
    }

    /**
     * @param array $params
     * @param $model
     * @throws ForbiddenHttpException
     * @throws \Throwable
     */
    public function checkAccessUpdate(array $params = [],$model)
    {
        // Only status field can be updated for an order
        $requestParams = Yii::$app->getRequest()->getBodyParams();
        if (empty($requestParams)) {
            $requestParams = Yii::$app->getRequest()->getQueryParams();
        }

        if(sizeof($requestParams) == 1){
            //status , order_id
            if(!isset($requestParams['status']) ){
                throw new ForbiddenHttpException('Only Status can be updated for an order');
            }
        }else{
            throw new ForbiddenHttpException('Only Status can be updated for an order');
        }

        // check if My Order
        return $this->checkAccessIsMyOrder($model);


    }

    /**
     * Create Accesss Control
     * @param $model
     * @throws ForbiddenHttpException
     * @throws \Throwable
     */
    private function checkAccessIsMyOrder($model)
    {
        // based on Role
        // Owner =>
        // Regular => order->user_id
        $currentUser = \Yii::$app->user->getIdentity();
        $user_id = $currentUser->id;
        $userRole = $currentUser->userRole;

        if($userRole == 1){
            if ($model->user_id != $user_id)
                throw new ForbiddenHttpException('This Order does not belong to you!');
        }else if($userRole ==2){
            // Owner
            $orderRestaurant = $model->restaurant;
            $restaurantOwner = $orderRestaurant->owner;

            if($restaurantOwner->id != $user_id )
                throw new ForbiddenHttpException('This Order does not belong to you!');
        }


    }


    /**
     * @SWG\Get(path="/v1/order/orders-list",
     *     tags={"Order"},
     *     summary="get all orders for Owner ,, or for User based on user Role",
     *     description="get all orders for Owner ,, or for User based on user Role",
     *     security={
     *         {"Bearer":{}}
     *     },
     *     @SWG\Response(
     *         response = 200,
     *         description = "return list of Orders",
     *         @SWG\Schema(ref = "#/definitions/Order")
     *     ),
     * )
     * Orders List Data Provider
     * @param IndexAction $action
     * @param array|null $filter
     * @return object
     * @throws \Throwable
     * @throws \yii\base\InvalidConfigException
     */
    public function ordersListDataProvider(IndexAction $action, array $filter = null)
    {
        $requestParams = Yii::$app->getRequest()->getBodyParams();
        if (empty($requestParams)) {
            $requestParams = Yii::$app->getRequest()->getQueryParams();
        }

        $query = Order::find()->myOrdersList();
        return $this->baseDataProvider($query, $action, $filter, true);
    }


    /**
     * @SWG\Get(path="/v1/order/pending-action-list",
     *     tags={"Order"},
     *     summary="get all orders that needs action from Owner ,, or from User based on user Role",
     *     description="get all orders that needs action from Owner ,, or from User based on user Role",
     *     security={
     *         {"Bearer":{}}
     *     },
     *     @SWG\Response(
     *         response = 200,
     *         description = "return list of Orders",
     *         @SWG\Schema(ref = "#/definitions/Order")
     *     ),
     * )
     * Orders List Data Provider
     * @param IndexAction $action
     * @param array|null $filter
     * @return object
     * @throws \Throwable
     * @throws \yii\base\InvalidConfigException
     */
    public function  pendingActionListDataProvider(IndexAction $action, array $filter = null)
    {
        $requestParams = Yii::$app->getRequest()->getBodyParams();
        if (empty($requestParams)) {
            $requestParams = Yii::$app->getRequest()->getQueryParams();
        }

        $query = Order::find()->myPendingOrdersList();
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