<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Meal */

$this->title = Yii::t('backend', 'Create {modelClass}', [
    'modelClass' => 'Meal',
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Meals'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="meal-create">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
