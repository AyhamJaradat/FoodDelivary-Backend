<?php
/**
 * Created by PhpStorm.
 * User: Ayham
 * Date: 2/9/2021
 * Time: 1:11 AM
 */

namespace api\modules\v1\resources\order\form;


use api\modules\v1\resources\meal\Meal;
use api\modules\v1\resources\order\behavior\OrderFormBehavior;
use api\modules\v1\resources\order\OrderLog;
use api\modules\v1\resources\order\OrderMeal;
use common\models\Order;
use function foo\func;
use yii\helpers\ArrayHelper;

class OrderForm extends Order
{

    /**
     * @var string
     */
    const SCENARIO_UPDATE = 'update';
    /**
     * @var string
     */
    const SCENARIO_CREATE = 'create';


    /**
     * @var array
     */
    public $meal_ids;
    public $meal_counts;

    /**
     * @inheritdoc
     */
    public function scenarios(): array
    {

        $commonAttributes = ['status', 'restaurant_id'];
        return ArrayHelper::merge(parent::scenarios(), [
            self::SCENARIO_CREATE => ArrayHelper::merge(
                $commonAttributes,
                [
                    'meal_ids', 'meal_counts', 'user_id'
                ]
            ),
            self::SCENARIO_UPDATE => ArrayHelper::merge(
                $commonAttributes,
                [

                ]
            ),
        ]);
    }

    public function fields(): array
    {
        $fields = parent::fields();

        $unset = ['created_by'];
        foreach ($unset as $key) {
            unset($fields[$key]);
        }
        return ArrayHelper::merge($fields, [


            'restaurant_name' => function ($model) {
                $restaurant = $model->restaurant;
                if ($restaurant) {
                    return $restaurant->name;
                } else {
                    return '';
                }
            },
            'status_name'=>function ($model) {
                $status_id = $model->status;
                return Order::getStatusName($status_id);
            },
            'order_meals' => function ($model) {
                $meals = $model->orderMeals;
                return $meals;
            },
            'total_price'=> function ($model) {
                $meals = $model->orderMeals;
                $totalPrice =0;
                foreach ($meals as $meal){
                    $totalPrice+= ($meal->price * $meal->amount);
                }
                return $totalPrice;
            },
            'num_of_meals'=> function ($model) {
                $meals = $model->orderMeals;
                $numOfMeals =0;
                foreach ($meals as $meal){
                    $numOfMeals+= ( $meal->amount);
                }
                return $numOfMeals;
            },
            'order_logs'=>function($model){

            $logs = $model->orderLogs;
            return $logs;
            },
            'user_name'=>function($model){
                return $model->user->publicIdentity;
            }




        ]);

    }

    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        return ArrayHelper::merge(parent::rules(),
            [
                // meal_ids
                [['meal_ids', 'meal_counts'], 'each', 'rule' => ['integer']],
                [['meal_ids', 'meal_counts'], 'required'],
                [['meal_ids'], 'exist', 'skipOnError' => true, 'targetClass' => Meal::className(), 'targetAttribute' => 'id', 'allowArray' => true],
            ]
        );
    }

    /**
     * @inheritdoc
     */
    public function behaviors(): array
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'orderFormBehavior' => OrderFormBehavior::className()
        ]);
    }

    /**
     * Gets query for [[OrderMeals]].
     *
     * @return \yii\db\ActiveQuery|\common\models\query\OrderMealQuery
     */
    public function getOrderMeals()
    {
        return $this->hasMany(OrderMeal::className(), ['order_id' => 'id']);
    }

    /**
     * Gets query for [[OrderLogs]].
     *
     * @return \yii\db\ActiveQuery|\common\models\query\OrderLogQuery
     */
    public function getOrderLogs()
    {
        return $this->hasMany(OrderLog::className(), ['order_id' => 'id'])->orderBy(['created_at' => SORT_DESC]);
    }

}