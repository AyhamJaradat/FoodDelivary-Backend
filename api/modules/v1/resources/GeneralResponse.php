<?php
/**
 * Created by PhpStorm.
 * User: Ayham
 * Date: 2/4/2021
 * Time: 4:46 PM
 */

namespace api\modules\v1\resources;


use yii\base\Model;

class GeneralResponse extends Model
{
    public $status;
    public $data;
    public $errors;
}