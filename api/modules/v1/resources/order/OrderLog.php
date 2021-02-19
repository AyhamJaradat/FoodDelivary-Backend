<?php
/**
 * Created by PhpStorm.
 * User: Ayham
 * Date: 2/9/2021
 * Time: 7:19 PM
 */

namespace api\modules\v1\resources\order;


use yii\helpers\ArrayHelper;

class OrderLog extends \common\models\OrderLog
{
    public function fields(): array
    {
        $fields = parent::fields();

        $unsetFields = ['updated_at','updated_by'];
        foreach ($unsetFields as $key)
            unset($fields[$key]);

        switch ($this->scenario) {
            default:
                // All info
                return ArrayHelper::merge($fields, [
                    'status_name'=>function ($model) {
                        $status_id = $model->status;
                        return Order::getStatusName($status_id);
                    },
                    'created_by_name'=>function($model){
                        return $model->createdBy->publicIdentity;
                    },
                    'created_by_role'=>function($model){
                        return $model->createdBy->userRole; // 1 for Regular User , 2 For Restaurant Owner , 3 Admin
                    },
                ]);
        }
    }
}