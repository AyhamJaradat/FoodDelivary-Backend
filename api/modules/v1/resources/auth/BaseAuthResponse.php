<?php
/**
 * Created by PhpStorm.
 * User: Ayham
 * Date: 2/4/2021
 * Time: 4:19 PM
 */

namespace api\modules\v1\resources\auth;


use yii\base\Model;

class BaseAuthResponse  extends Model
{
    public $errors;
    public $user;
    public $auth_key;
}