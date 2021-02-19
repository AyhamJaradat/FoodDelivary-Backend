<?php
return [
    'class' => 'yii\web\UrlManager',
    'enablePrettyUrl' => true,
    'showScriptName' => false,
    'rules' => [
//        // Article Api
//        [
//            'class' => 'yii\rest\UrlRule',
//            'controller' => 'api/v1/article',
////            'pluralize' => false,
//            'only' => ['index', 'view', 'options']
//        ],
        // Auth APIs
        [
            'class' => 'yii\rest\UrlRule',
            'controller' => 'v1/auth',
            'pluralize' => false,
            'extraPatterns' => [
                'OPTIONS email-and-password' => 'options',
                'POST email-and-password' => 'email-and-password',// takes email and password
                'OPTIONS sign-up' => 'options',
                'POST sign-up' => 'sign-up',//takes email , password and firstName and last name
                'OPTIONS request-password-reset' => 'options',
                'POST request-password-reset' => 'request-password-reset',
                'OPTIONS reset-password' => 'options',
                'POST reset-password' => 'reset-password',
            ]
        ],
        // Restaurant APIs
        [
            'class' => 'yii\rest\UrlRule',
            'controller' => 'v1/restaurant',
            'pluralize' => false,
        ],
        // Meal APIs
        [
            'class' => 'yii\rest\UrlRule',
            'controller' => 'v1/meal',
            'pluralize' => false,
        ],
        // Order APIs
        [
            'class' => 'yii\rest\UrlRule',
            'controller' => 'v1/order',
            'pluralize' => false,
        ],
        // User APIs
        [
            'class' => 'yii\rest\UrlRule',
            'controller' => 'v1/user',
            'pluralize' => false,
        ],
    ]
];
