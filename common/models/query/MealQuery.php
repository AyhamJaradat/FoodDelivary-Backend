<?php

namespace common\models\query;

use yii\db\ActiveQuery;

/**
 * This is the ActiveQuery class for [[\common\models\Meal]].
 *
 * @see \common\models\Meal
 */
class MealQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return \common\models\Meal[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return \common\models\Meal|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    /**
     * @return ActiveQuery
     * @throws \Throwable
     */
    public function myRestaurantMealsList($restaurant_id): ActiveQuery
    {
        return $this
            ->andWhere([
                'restaurant_id'=>$restaurant_id,
                'is_deleted'=>0
            ]);
    }
}
