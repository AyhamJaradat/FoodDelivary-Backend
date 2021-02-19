<?php

namespace common\models\query;

use common\models\Order;
use common\models\Restaurant;
use common\models\User;
use common\models\UserBlock;
use yii\db\ActiveQuery;

/**
 * Class UserQuery
 * @package common\models\query
 * @author Eugene Terentev <eugene@terentev.net>
 */
class UserQuery extends ActiveQuery
{
    /**
     * @return $this
     */
    public function notDeleted()
    {
        $this->andWhere(['!=', 'status', User::STATUS_DELETED]);
        return $this;
    }

    /**
     * @return $this
     */
    public function active()
    {
        $this->andWhere(['status' => User::STATUS_ACTIVE]);
        return $this;
    }

    /**
     * @return $this
     */
    public function byEmail($email)
    {
        $this->andWhere(['email' => $email]);
        return $this;
    }

    /**
     * {@inheritdoc}
     * @return \common\models\User[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return \common\models\User|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }


    public function myBlockedCustomersList(): ActiveQuery
    {

        $currentUser = \Yii::$app->user->getIdentity();
        $user_id = $currentUser->id;

        $userRole = $currentUser->userRole; //1, 2, 3



        if($userRole == 2){
            //  For Owner : all users that have orders from my resturants
            // All Users that belong to resturant where resturant Owner is this user_id
            $resturants = Restaurant::find()->where([  'owner_id' => $user_id])->all();
//            $resturants = $currentUser->myRestaurants->all();
            $resturantIds = array_column($resturants, 'id');

            $blockedUsers = UserBlock::find()->where(['owner_id' => $user_id])->all();
            $blockedIds = array_column($blockedUsers, 'user_id');
            return $this
                ->joinWith(['myOrders'])
                ->andWhere([
                    Order::tableName().'.restaurant_id' => $resturantIds,
                    User::tableName().'.id'=>$blockedIds
//                    UserBlock::tableName().'.owner_id'=> $user_id,

                ])->orderBy([
//                    'status'=>SORT_ASC,
                    User::tableName().'.created_at' => SORT_DESC
                ])->groupBy([User::tableName().'.id']);
        }else{
            return $this;
        }

    }

    /**
     * @return ActiveQuery
     * @throws \Throwable
     */
    public function myCustomersList(): ActiveQuery
    {

        $currentUser = \Yii::$app->user->getIdentity();
        $user_id = $currentUser->id;

        $userRole = $currentUser->userRole; //1, 2, 3



        if($userRole == 2){
            //  For Owner : all users that have orders from my resturants
            // All Users that belong to resturant where resturant Owner is this user_id
            $resturants = Restaurant::find()->where([  'owner_id' => $user_id])->all();
//            $resturants = $currentUser->myRestaurants->all();
            $resturantIds = array_column($resturants, 'id');
            return $this
                ->joinWith('myOrders')
                ->andWhere([
                    Order::tableName().'.restaurant_id' => $resturantIds
                ])->orderBy([
//                    'status'=>SORT_ASC,
                    User::tableName().'.created_at' => SORT_DESC
                ]) ->groupBy([User::tableName().'.id']);
        }else{
            return $this;
        }

    }
}