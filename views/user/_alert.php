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
 * @var $module dektrium\user\Module
 */

?>

<?php if ($module->enableFlashMessages): ?>
    <div class="row">
        <div class="col-xs-12">
            <?php foreach (Yii::$app->session->getAllFlashes() as $type => $message): ?>
                <?php 
                // Map 'error' type to 'danger' for Bootstrap compatibility
                $alertType = ($type === 'error') ? 'danger' : $type;
                ?>
                <?php if (in_array($alertType, ['success', 'danger', 'warning', 'info'])): ?>
                    <div class="alert alert-<?= $alertType ?> alert-dismissible fade show" role="alert">
                        <?= $message ?>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                <?php endif ?>
            <?php endforeach ?>
        </div>
    </div>
<?php endif ?>