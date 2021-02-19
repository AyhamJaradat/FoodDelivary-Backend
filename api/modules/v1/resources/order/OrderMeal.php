<?php
/**
 * Created by PhpStorm.
 * User: Ayham
 * Date: 2/9/2021
 * Time: 7:06 PM
 */

namespace api\modules\v1\resources\order;


use yii\helpers\ArrayHelper;

class OrderMeal extends \common\models\OrderMeal
{
    public function fields(): array
    {
        $fields = parent::fields();

        $unsetFields = ['created_by', 'created_at','updated_at','updated_by'];
        foreach ($unsetFields as $key)
            unset($fields[$key]);

        switch ($this->scenario) {
            default:
                // All info
                return ArrayHelper::merge($fields, [
                    'name'=>function($model){
                        return $model->meal->name;
                    },
                    'description'=>function($model){
                        return $model->meal->description;
                    },
                ]);
        }
    }
}