<?php
/**
 * Created by PhpStorm.
 * User: Ayham
 * Date: 2/13/2021
 * Time: 11:10 AM
 */

namespace api\modules\v1\models\definitions;


/**
 * @SWG\Definition()
 *
 * @SWG\Property(property="id", type="integer")
 * @SWG\Property(property="user_id", type="integer")
 * @SWG\Property(property="user_name", type="string")
 * @SWG\Property(property="status", type="integer")
 * @SWG\Property(property="status_name", type="string")
 * @SWG\Property(property="restaurant_id", type="integer")
 * @SWG\Property(property="restaurant_name", type="string")
 * @SWG\Property(property="total_price", type="double")
 * @SWG\Property(property="num_of_meals", type="integer")
 * @SWG\Property(property="order_meals", type="array",@SWG\Items(
 *          type="object"
 *         ))
 * @SWG\Property(property="order_logs", type="array",@SWG\Items(
 *          type="object"
 *         ))
 * @SWG\Property(property="updated_at", type="integer")
 * @SWG\Property(property="created_at", type="integer")
 * @SWG\Property(property="updated_by", type="integer")
 *
 */
class Order
{
        //created_at: 1612888000
        //id: 6
        //num_of_meals: 3
        //order_logs: [{id: 4, order_id: 6, status: 1, created_by: 11, created_at: 1612888000, status_name: "Placed",…}]
        //order_meals: [{id: 11, order_id: 6, meal_id: 6, amount: 2, price: 5, name: "Last meal", description: "here"},…]
        //restaurant_id: 9
        //restaurant_name: "Burger King"
        //status: 1
        //status_name: "Placed"
        //total_price: 14
        //updated_at: 1612888000
        //updated_by: 11
        //user_id: 11
        //user_name: "Regular  User"

}