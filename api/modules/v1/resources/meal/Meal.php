<?php
/**
 * Created by PhpStorm.
 * User: Ayham
 * Date: 2/7/2021
 * Time: 8:59 PM
 */

namespace api\modules\v1\resources\meal;


use api\modules\v1\resources\meal\form\MealForm;
use yii\helpers\ArrayHelper;

class Meal extends  MealForm
{
    public function fields(): array
    {
        $fields = parent::fields();

        $unsetFields = ['created_by', 'created_at'];
        foreach ($unsetFields as $key)
            unset($fields[$key]);

        switch ($this->scenario) {
            default:
                // All info
                return ArrayHelper::merge($fields, [
                ]);
        }
    }
}