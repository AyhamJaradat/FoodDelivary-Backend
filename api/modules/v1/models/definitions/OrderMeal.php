<?php
/**
 * Created by PhpStorm.
 * User: Ayham
 * Date: 2/13/2021
 * Time: 11:18 AM
 */

namespace api\modules\v1\models\definitions;


/**
 * @SWG\Definition()
 *
 * @SWG\Property(property="id", type="integer")
 * @SWG\Property(property="meal_id", type="integer")
 * @SWG\Property(property="name", type="string")
 * @SWG\Property(property="description", type="string")
 * @SWG\Property(property="order_id", type="integer")
 * @SWG\Property(property="amount", type="integer")
 * @SWG\Property(property="price", type="double")
 *
 */
class OrderMeal
{

        //amount: 2
        //description: "here"
        //id: 11
        //meal_id: 6
        //name: "Last meal"
        //order_id: 6
        //price: 5
}