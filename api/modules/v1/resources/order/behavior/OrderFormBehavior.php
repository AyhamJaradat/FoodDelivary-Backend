<?php
/**
 * Created by PhpStorm.
 * User: Ayham
 * Date: 2/9/2021
 * Time: 1:15 AM
 */

namespace api\modules\v1\resources\order\behavior;


use common\models\Meal;
use common\models\Order;
use common\models\OrderLog;
use common\models\OrderMeal;
use yii\base\Behavior;
use yii\base\ModelEvent;
use yii\db\ActiveRecord;
use yii\db\AfterSaveEvent;
use yii\web\ForbiddenHttpException;

class OrderFormBehavior extends Behavior
{

    private $isUpdatingStatus = false;

    /**
     * @inheritdoc
     */
    public function events(): array
    {
        return [
            ActiveRecord::EVENT_BEFORE_VALIDATE => 'beforeValidate',
//            ActiveRecord::EVENT_AFTER_VALIDATE => 'afterValidate',
            ActiveRecord::EVENT_AFTER_INSERT => 'afterInsert',
            ActiveRecord::EVENT_AFTER_UPDATE => 'afterUpdate',
//            ActiveRecord::EVENT_BEFORE_INSERT => 'beforeInsert',
            ActiveRecord::EVENT_BEFORE_UPDATE => 'beforeUpdate',
        ];
    }

    /**
     * Before Validate
     * - set Owner_id
     * @param ModelEvent $event
     * @throws \Throwable
     */
    public function beforeValidate(ModelEvent $event): void
    {
        $currentUser = \Yii::$app->user->getIdentity();
        $user_id = $currentUser->id;
        // update it only on create
        if ($this->owner->user_id == null)
            $this->owner->user_id = $user_id;

        // Just for Swagger UI to works
        // if the meal_ids and meal_counts is given as string ex = 6,5,4
        // convert them to array of int
        if (is_string($this->owner->meal_ids)) {
            $this->owner->meal_ids = array_map('intval', explode(',', $this->owner->meal_ids));
        }
        if (is_string($this->owner->meal_counts)) {
            $this->owner->meal_counts = array_map('intval', explode(',', $this->owner->meal_counts));
        }
    }


    public function afterInsert(AfterSaveEvent $event): void
    {
        // When new Order is created .. Create OrderMeals and OrderLog

        // for each meal, create an OrderMeal record
        if ($this->owner->meal_ids != null) {

            foreach ($this->owner->meal_ids as $index => $meal_id) {

// * @property int $id
// * @property int $order_id
// * @property int $meal_id
// * @property int $amount
// * @property float $price
                $theMeal = Meal::find()->where(['id' => $meal_id])->one();
                $counter = OrderMeal::find()
                    ->where([
                        'order_id' => $this->owner->id,
                        'meal_id' => $meal_id,
                    ])
                    ->count();
                // Make Sure meal is not already added
                if ($counter == 0) {
                    $orderMeal = new OrderMeal();
                    $orderMeal->setAttributes([
                        'order_id' => $this->owner->id,
                        'meal_id' => $meal_id,
                        'amount' => $this->owner->meal_counts[$index],
                        'price' => $theMeal->price
                    ]);
                    $orderMeal->save();
                }
            }
        }

        // Create the first OrderLog
        // * @property int $id
        // * @property int $order_id
        // * @property int $status

        $orderLog = new OrderLog();
        $orderLog->setAttributes([
            'order_id' => $this->owner->id,
            'status' => Order::ORDER_STATUS_PLACED,//$this->owner->status,
        ]);
        $orderLog->save();


    }

    /**
     * @param ModelEvent $event
     * @throws ForbiddenHttpException
     * @throws \Throwable
     */
    public function beforeUpdate(ModelEvent $event): void
    {
        $oldStatus = $this->owner->oldAttributes['status'];
        $newStatus = $this->owner->status;
        if ($newStatus != $oldStatus) {
            // Check if valid change
            $isRegularUser = false;
            $currentUser = \Yii::$app->user->getIdentity();
            $user_id = $currentUser->id;
            // update it only on create
            if ($this->owner->user_id == $user_id) {
                $isRegularUser = true;
            }

            if ($isRegularUser) {

                if (
                    ($oldStatus == Order::ORDER_STATUS_PLACED && $newStatus == Order::ORDER_STATUS_CANCELED)
                    ||
                    ($oldStatus == Order::ORDER_STATUS_DELIVERED && $newStatus == Order::ORDER_STATUS_RECEIVED)

                ) {
                    // valid status change
                } else {
                    // not valid status change
                    throw new ForbiddenHttpException('Invalid Status Change');
                    return;
                }

            } else {
                // For Owner
                if (
                    ($oldStatus == Order::ORDER_STATUS_PLACED && $newStatus == Order::ORDER_STATUS_PROCESSING)
                    ||
                    ($oldStatus == Order::ORDER_STATUS_PROCESSING && $newStatus == Order::ORDER_STATUS_IN_ROUTE)
                    ||
                    ($oldStatus == Order::ORDER_STATUS_IN_ROUTE && $newStatus == Order::ORDER_STATUS_DELIVERED)

                ) {
                    // valid status change
                } else {
                    // not valid status change
                    throw new ForbiddenHttpException('Invalid Status Change');
                    return;
                }

            }

            $this->isUpdatingStatus = true;
        }


    }

    /**
     * @param AfterSaveEvent $event
     */
    public function afterUpdate(AfterSaveEvent $event): void
    {
        // if we are updating the status .. make order log for it
        if ($this->isUpdatingStatus) {
            // Create  OrderLog
            // * @property int $id
            // * @property int $order_id
            // * @property int $status

            $orderLog = new OrderLog();
            $orderLog->setAttributes([
                'order_id' => $this->owner->id,
                'status' => $this->owner->status
            ]);
            $orderLog->save();
        }

    }

}