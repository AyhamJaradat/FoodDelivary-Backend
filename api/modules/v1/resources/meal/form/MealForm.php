<?php
/**
 * Created by PhpStorm.
 * User: Ayham
 * Date: 2/7/2021
 * Time: 9:00 PM
 */

namespace api\modules\v1\resources\meal\form;


use common\models\Meal;
use yii\helpers\ArrayHelper;

class MealForm extends Meal
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
     * @inheritdoc
     */
    public function scenarios(): array
    {
        $commonAttributes = ['name', 'description','price','is_deleted','restaurant_id'];
        return ArrayHelper::merge(parent::scenarios(), [
            self::SCENARIO_CREATE => ArrayHelper::merge(
                $commonAttributes,
                [

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

        $unset = ['created_by', 'created_at'];
        foreach ($unset as $key) {
            unset($fields[$key]);
        }
        return ArrayHelper::merge($fields, [


//            'meals'=>function($model){
//                $record = $model->meals;
//                return $record;
//            },
//            'num_of_orders'=>function($model){
//                // for Owner => get # of all orders from this restaurant
//                // For regular User => get # of all orders by me for from this Restaurant
//                $currentUser = \Yii::$app->user->getIdentity();
//                $user_id = $currentUser->id;
//                if($model->owner_id == $user_id){
//                    // Owner
//                    return sizeof($model->orders);
//                }else{
//                    // regular user
//                    return sizeof($model->getMyOrders($user_id)->all());
//
//                }
//
//
//            }

        ]);

    }

    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        return ArrayHelper::merge(parent::rules(),
            [

            ]
        );
    }
}