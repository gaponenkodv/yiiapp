<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
?>
<?php $form = ActiveForm::begin()
/** @var $form_model \app\models\ChangeBalance */
?>

<?= $form->field($form_model, 'balanceFrom')
    ->dropDownList($form_model->getBalances()) ?>
<?= $form->field($form_model, 'balanceTo')
    ->dropDownList($form_model->getBalances())?>
<?= $form->field($form_model, 'amount') ?>
<?= Html::activeHiddenInput($form_model, 'hash', ['value'=> uniqid()]) ?>
<?= Html::submitButton('Отправить', ['class' => 'btn btn-success']) ?>
<?php ActiveForm::end() ?>