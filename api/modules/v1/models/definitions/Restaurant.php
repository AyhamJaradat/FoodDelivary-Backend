<?php
/**
 * Created by PhpStorm.
 * User: Ayham
 * Date: 2/6/2021
 * Time: 3:10 PM
 */

namespace api\modules\v1\models\definitions;


/**
 * @SWG\Definition()
 *
 * @SWG\Property(property="id", type="integer")
 * @SWG\Property(property="name", type="string")
 * @SWG\Property(property="description", type="string")
 * @SWG\Property(property="owner_id", type="integer")
 * @SWG\Property(property="meals", type="array",@SWG\Items(
 *          type="object"
 *         ))
 * @SWG\Property(property="is_deleted", type="integer")
 * @SWG\Property(property="num_of_orders", type="integer")
 * @SWG\Property(property="updated_at", type="integer")
 * @SWG\Property(property="updated_by", type="integer")
 *
 */
class Restaurant
{
//"id": 1,
//  "name": "edo1",
//  "description": "des1",
//  "owner_id": 3,
//  "is_deleted": 0,
//  "updated_at": 1612613296,
//  "updated_by": 9

//meals: [{id: 8, name: "Special Meal", description: "two in one meal2", price: 4.5, restaurant_id: 8,…},…]

//num_of_orders: 5

}