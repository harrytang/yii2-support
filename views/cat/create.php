<?php
/**
 * @author Harry Tang <harry@powerkernel.com>
 * @link https://powerkernel.com
 * @copyright Copyright (c) 2017 Power Kernel
 */

/* @var $this yii\web\View */
/* @var $model powerkernel\support\models\Cat */


/* breadcrumbs */
$this->params['breadcrumbs'][] = ['label' => Yii::t('support', 'Categories'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

/* misc */
//$js=file_get_contents(__DIR__.'/index.min.js');
//$this->registerJs($js);
//$css=file_get_contents(__DIR__.'/index.css');
//$this->registerCss($css);
?>
<div class="cat-create">
    <div class="box box-success">
        <div class="box-body">
            <?= $this->render('_form', [
                'model' => $model,
            ]) ?>
        </div>
    </div>
</div>
