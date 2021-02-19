<?php

namespace common\models\query;

use common\models\Restaurant;
use common\models\UserBlock;
use yii\db\ActiveQuery;

/**
 * This is the ActiveQuery class for [[\common\models\Restaurant]].
 *
 * @see \common\models\Restaurant
 */
class RestaurantQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return \common\models\Restaurant[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return \common\models\Restaurant|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    /**
     * @return ActiveQuery
     * @throws \Throwable
     */
    public function myRestaurantsList(): ActiveQuery
    {

        $currentUser = \Yii::$app->user->getIdentity();
        $user_id = $currentUser->id;
        $userRole = $currentUser->userRole; //1, 2, 3


        // all restaurants that I have created as Owner
        // Or all restaurants that I am not blocked by their owners
        if ($userRole == 1) {
            //Join with block and get not blocked
            // All resturants .. restuaurant Owner did not block me
            // get ownersIds those blocked me
            // get all resturants where theire owner_id not in the list
            $ownersBlockedMe = UserBlock::find()->where(['user_id' => $user_id])->all();
            $ownersBlockedMeIds = array_column($ownersBlockedMe, 'owner_id');
            return $this
                ->andWhere([
                    'is_deleted' => 0
                ])->andWhere([
                    'not',
                    [Restaurant::tableName() . '.owner_id' => $ownersBlockedMeIds]
                ])->groupBy([Restaurant::tableName().'.id']);
        } else {
            return $this
                ->andWhere([
                    'owner_id' => $user_id,
                    'is_deleted' => 0
                ]);
        }

    }
}
