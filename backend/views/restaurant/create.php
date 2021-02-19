<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Restaurant */

$this->title = Yii::t('backend', 'Create {modelClass}', [
    'modelClass' => 'Restaurant',
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Restaurants'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="restaurant-create">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
