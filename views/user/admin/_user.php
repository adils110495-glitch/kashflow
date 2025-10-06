<?php

/*
 * This file is part of the Dektrium project.
 *
 * (c) Dektrium project <http://github.com/dektrium>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

/**
 * @var yii\widgets\ActiveForm    $form
 * @var dektrium\user\models\User $user
 */

?>

<?= $form->field($user, 'email')->textInput(['maxlength' => 255, 'id' => 'user-email']) ?>
<?= $form->field($user, 'username')->textInput(['maxlength' => 255, 'value' => $user->email ?: '', 'readonly' => true, 'id' => 'user-username'])->hint('Username will be set to email address automatically') ?>
<?= $form->field($user, 'password')->passwordInput() ?>

