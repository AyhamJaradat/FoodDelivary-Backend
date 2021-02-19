<?php
/**
 * Created by PhpStorm.
 * User: Ayham
 * Date: 2/13/2021
 * Time: 11:20 AM
 */

namespace api\modules\v1\models\definitions;

/**
 * @SWG\Definition()
 *
 * @SWG\Property(property="id", type="integer")
 * @SWG\Property(property="status", type="integer")
 * @SWG\Property(property="status_name", type="string")
 * @SWG\Property(property="created_by_name", type="string")
 * @SWG\Property(property="order_id", type="integer")
 * @SWG\Property(property="created_by", type="integer")
 * @SWG\Property(property="created_at", type="integer")
 * @SWG\Property(property="created_by_role", type="integer")
 *
 */
class OrderLog
{
        //created_at: 1612888000
        //created_by: 11
        //created_by_name: "Regular  User"
        //created_by_role: 1
        //id: 4
        //order_id: 6
        //status: 1
        //status_name: "Placed"
}