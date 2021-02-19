<?php
/**
 * Created by PhpStorm.
 * User: Ayham
 * Date: 2/6/2021
 * Time: 12:29 AM
 */

namespace api\modules\v1\resources\restaurant\form;


use api\modules\v1\resources\restaurant\behavior\RestaurantFormBehavior;
use common\models\Restaurant;
use yii\helpers\ArrayHelper;

class RestaurantForm extends  Restaurant
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
        $commonAttributes = ['name', 'description','is_deleted'];
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

            //get meals and Orders
//            'myRatingCriteriaMatch' => function ($model) {
//                $match = $model->myRatingCriteriaMatch;
////                if ($match != null)
////                    $match->scenario = EventMatch::SCENARIO_BASIC;
//                return $match;
//            },
            'meals'=>function($model){
                $record = $model->activeMeals;
                return $record;
            },
            'num_of_orders'=>function($model){
            // for Owner => get # of all orders from this restaurant
                // For regular User => get # of all orders by me for from this Restaurant
                $currentUser = \Yii::$app->user->getIdentity();
                $user_id = $currentUser->id;
                if($model->owner_id == $user_id){
                    // Owner
                    return sizeof($model->orders);
                }else{
                    // regular user
                    return sizeof($model->getMyOrders($user_id)->all());

                }


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
//                [['could_view_ratings', 'could_be_rated_by'], 'integer'],
//                [['could_view_ratings'], 'in',
//                    'range' => [Constants::VIEW_ANY_ONE, Constants::VIEW_PEOPLE_I_KNOW, Constants::VIEW_ONLY_ME],
//                    'message' => 'could_view_ratings must be integer: either 1 => AnyOne, 2 => PeopleIKnow, 3 => OnlyMe '],
//                [['could_be_rated_by'], 'in',
//                    'range' => [Constants::ACTION_ANY_ONE, Constants::ACTION_PEOPLE_I_KNOW],
//                    'message' => 'could_be_rated_by must be integer: either 1 => AnyOne, 2 => PeopleIKnow'],


            ]
        );
    }

    /**
     * @inheritdoc
     */
    public function behaviors(): array
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'restaurantFormBehavior' => RestaurantFormBehavior::className()
        ]);
    }
}