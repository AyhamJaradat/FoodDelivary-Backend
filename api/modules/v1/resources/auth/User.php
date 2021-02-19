<?php
/**
 * Created by PhpStorm.
 * User: Ayham
 * Date: 2/4/2021
 * Time: 4:20 PM
 */

namespace api\modules\v1\resources\auth;


use common\models\UserBlock;
use yii\helpers\ArrayHelper;

class User extends \common\models\User
{
    /**
     * @return array
     */
    public function fields()
    {
        $fields = parent::fields();

        $unsetFields = ['created_at','updated_at','auth_key', 'access_token', 'password_hash','oauth_client','oauth_client_user_id','updated_at','username'];
        foreach ($unsetFields as $key)
            unset($fields[$key]);

        switch ($this->scenario) {
            default:
                // All info
                return ArrayHelper::merge($fields, [
                    'full_name' => function ($model, $field) {
                        return $model->userProfile->fullName ?? $model->email;
                    },
//                    'picture_url' => function ($model, $field) {
//                        return $model->userProfile->avatar ?? null;
//                    },
                    'is_blocked'=>function($model, $field){
                        $currentUser = \Yii::$app->user->getIdentity();
                        if($currentUser){
                            $user_id = $currentUser->id; //Owner
                            $userBlocked = UserBlock::find()->where([
                                'owner_id'=>$user_id,
                                'user_id'=>$model->id
                            ])->one();
                            if($userBlocked){
                                return true;
                            }else{
                                return false;
                            }
                        }else{
                            // for not logged in User
                            return false;
                        }


                    }
                ]);
        }
    }


}