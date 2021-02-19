<?php
/**
 * Created by PhpStorm.
 * User: Ayham
 * Date: 2/4/2021
 * Time: 1:54 PM
 */

namespace api\modules\v1\common;
use Yii;
use yii\filters\Cors;

class NoAuthController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $auth = $behaviors['authenticator'];
        unset($behaviors['authenticator']);

        // add CORS filter
        $behaviors['corsFilter'] = [
            'class' => Cors::className(),
            'cors' => [
                'Origin' => ['*'],
                'Access-Control-Request-Method' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'HEAD', 'OPTIONS'],
                'Access-Control-Request-Headers' => ['*'],
                // Allow the X-Pagination-Current-Page header to be exposed to the browser.
                'Access-Control-Expose-Headers' => ['X-Pagination-Current-Page', 'X-Pagination-Page-Count', 'X-Pagination-Per-Page', 'X-Pagination-Total-Count'],
            ],

        ];

        // re-add authentication filter
        $behaviors['authenticator'] = $auth;
        // Bearer Authenticator
        $behaviors['authenticator']['optional'] = ['*'];
        // unset($behaviors['authenticator']);


        return $behaviors;
    }
}