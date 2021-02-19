<?php
/**
 * Created by PhpStorm.
 * User: Ayham
 * Date: 2/6/2021
 * Time: 12:29 AM
 */

namespace api\modules\v1\resources\restaurant;


use api\modules\v1\resources\restaurant\form\RestaurantForm;
use yii\helpers\ArrayHelper;

class Restaurant extends RestaurantForm
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