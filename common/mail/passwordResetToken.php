<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $user common\modules\user\models\User */
/* @var $token string */

//$resetLink = Yii::$app->urlManager->createAbsoluteUrl(['/user/sign-in/reset-password', 'token' => $token]);
?>
<!-- should be Hello full name -->

Hello <?php echo Html::encode($user) ?>,

Follow the link below to reset your password:

<?php echo Html::a(Html::encode($resetLink), $resetLink) ?>
