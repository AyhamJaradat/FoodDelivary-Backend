<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\UserBlock */

$this->title = Yii::t('backend', 'Create {modelClass}', [
    'modelClass' => 'User Block',
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'User Blocks'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-block-create">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
