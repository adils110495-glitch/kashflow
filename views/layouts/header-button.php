<?php
use yii\helpers\Html;

/**
 * Header with buttons partial
 * @var string $title
 * @var string $subtitle
 * @var array $buttons
 */
?>
<div class="card-header">
    <div class="d-flex align-items-center">
        <div class="me-3">
            <h4 class="card-title mb-0"><?= Html::encode($title) ?></h4>
            <?php if (!empty($subtitle)): ?>
                <small class="text-muted"><?= Html::encode($subtitle) ?></small>
            <?php endif; ?>
        </div>
        <div class="btn-group" role="group">
            <?php if (!empty($buttons)): ?>
                <?php foreach ($buttons as $button): ?>
                    <?= $button ?>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>