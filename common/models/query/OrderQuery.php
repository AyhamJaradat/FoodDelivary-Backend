<?php

namespace common\models\query;

use common\models\Restaurant;
use yii\db\ActiveQuery;

/**
 * This is the ActiveQuery class for [[\common\models\Order]].
 *
 * @see \common\models\Order
 */
class OrderQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return \common\models\Order[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return \common\models\Order|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    public  function myPendingOrdersList(): ActiveQuery
    {

        $currentUser = \Yii::$app->user->getIdentity();
        $user_id = $currentUser->id;

        $userRole = $currentUser->userRole; //1, 2, 3

        if($userRole == 1){
            //Regular User
            return $this
                ->andWhere([
                    'user_id'=>$user_id,
                    'status'=> [1,5]
                ])->orderBy([
                    // Regular User Orders order [1-Placed, 5-Delivered, 3-processing, 4-inRoute, 6-received, 2-canceld ]
                    new \yii\db\Expression('FIELD (status, 1,5,3,4,6,2)'),
//                    'status'=>SORT_ASC,
                    'created_at' => SORT_DESC
                ]);
        }else{
            // For Owner : all orders based on restaurants based on owner
            // All Orders that belong to resturant where resturant Owner is this user_id
            $resturants = Restaurant::find()->where([  'owner_id' => $user_id])->all();
            $resturantIds = array_column($resturants, 'id');
            return $this
                ->andWhere([
                    'restaurant_id' => $resturantIds,
                    'status'=> [1,3,4]
                ])->orderBy([
                    // Owner User Orders order [1-Placed, 3-processing, 4-inRoute, 5-Delivered, 6-received, 2-canceld ]
                    new \yii\db\Expression('FIELD (status, 1,3,4,5,6,2)'),
//                    'status'=>SORT_ASC,
                    'created_at' => SORT_DESC
                ]);
        }

    }
    public function myOrdersList(): ActiveQuery
    {

        $currentUser = \Yii::$app->user->getIdentity();
        $user_id = $currentUser->id;

        $userRole = $currentUser->userRole; //1, 2, 3


        // all restaurants that I have created as Owner
        // Or all restaurants that I am not blocked by their owners
        if($userRole == 1){
            //Regular User
            return $this
                ->andWhere([
                    'user_id'=>$user_id
                ])->orderBy([
                    // Regular User Orders order [1-Placed, 5-Delivered, 3-processing, 4-inRoute, 6-received, 2-canceld ]
                    new \yii\db\Expression('FIELD (status, 1,5,3,4,6,2)'),
//                    'status'=>SORT_ASC,
                    'created_at' => SORT_DESC
                ]);
        }else{
            //  For Owner : all orders based on restaurants based on owner
            // All Orders that belong to resturant where resturant Owner is this user_id
            $resturants = Restaurant::find()->where([  'owner_id' => $user_id])->all();
            $resturantIds = array_column($resturants, 'id');
            return $this
                ->andWhere([
                    'restaurant_id' => $resturantIds
                ])->orderBy([
                    // Owner User Orders order [1-Placed, 3-processing, 4-inRoute, 5-Delivered, 6-received, 2-canceld ]
                    new \yii\db\Expression('FIELD (status, 1,3,4,5,6,2)'),
//                    'status'=>SORT_ASC,
                    'created_at' => SORT_DESC
                ]);
        }

    }
}
