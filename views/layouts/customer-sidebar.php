<?php
use yii\helpers\Html;
use yii\helpers\Url;
use app\models\Customer;

/* @var $this yii\web\View */

// Get current customer data
$customer = Customer::find()
    ->where(['user_id' => Yii::$app->user->id])
    ->with(['currentPackage'])
    ->one();
?>

<nav class="pcoded-navbar">
    <div class="navbar-wrapper">
        <div class="navbar-content scroll-div">
            <ul class="nav pcoded-inner-navbar">
                <?php if ($customer): ?>
                <li class="nav-item">
                    <div class="customer-profile-section" style="padding: 15px; margin-bottom: 10px; background: #f8f9fa; border-radius: 5px;">
                        <div class="text-center">
                            <div class="avatar-circle" style="width: 50px; height: 50px; background: #007bff; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 18px;">
                                <?= strtoupper(substr($customer->name, 0, 1)) ?>
                            </div>
                            <h6 class="mt-2 mb-1"><?= Html::encode($customer->name) ?></h6>
                            <small class="text-muted">
                                <?= $customer->currentPackage ? Html::encode($customer->currentPackage->name) . ' Plan' : 'No Package' ?>
                            </small>
                        </div>
                    </div>
                </li>
                <?php endif; ?>
                
                <!-- Navigation Menu -->
                <li class="nav-item <?= Yii::$app->controller->id === 'customer-dashboard' && Yii::$app->controller->action->id === 'index' ? 'active' : '' ?>">
                    <a href="<?= Url::to(['/customer-dashboard/index']) ?>" class="nav-link">
                        <span class="pcoded-micon"><i class="feather icon-home"></i></span>
                        <span class="pcoded-mtext">Dashboard</span>
                    </a>
                </li>
                
                <li class="nav-item <?= Yii::$app->controller->id === 'customer-dashboard' && Yii::$app->controller->action->id === 'profile' ? 'active' : '' ?>">
                    <a href="<?= Url::to(['/customer-dashboard/profile']) ?>" class="nav-link">
                        <span class="pcoded-micon"><i class="feather icon-user"></i></span>
                        <span class="pcoded-mtext">My Profile</span>
                    </a>
                </li>
                <!-- Team Section -->
                <li class="nav-item pcoded-menu-caption">
                    <label>Team & Income</label>
                </li>
                
                <li class="nav-item <?= Yii::$app->controller->id === 'customer-dashboard' && Yii::$app->controller->action->id === 'direct-team' ? 'active' : '' ?>">
                    <a href="<?= Url::to(['/customer-dashboard/direct-team']) ?>" class="nav-link">
                        <span class="pcoded-micon"><i class="feather icon-users"></i></span>
                        <span class="pcoded-mtext">Direct Team</span>
                    </a>
                </li>
                
                <li class="nav-item <?= Yii::$app->controller->id === 'customer-dashboard' && Yii::$app->controller->action->id === 'level-team' ? 'active' : '' ?>">
                    <a href="<?= Url::to(['/customer-dashboard/level-team']) ?>" class="nav-link">
                        <span class="pcoded-micon"><i class="feather icon-layers"></i></span>
                        <span class="pcoded-mtext">Level Team</span>
                    </a>
                </li>
                
                <li class="nav-item <?= Yii::$app->controller->id === 'customer-dashboard' && Yii::$app->controller->action->id === 'income' ? 'active' : '' ?>">
                    <a href="<?= Url::to(['/customer-dashboard/income']) ?>" class="nav-link">
                        <span class="pcoded-micon"><i class="feather icon-credit-card"></i></span>
                        <span class="pcoded-mtext">Income</span>
                    </a>
                </li>
                
                <!-- Account Section -->
                <li class="nav-item pcoded-menu-caption">
                    <label>Account</label>
                </li>
                
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <span class="pcoded-micon"><i class="feather icon-settings"></i></span>
                        <span class="pcoded-mtext">Settings</span>
                    </a>
                </li>
                 <li class="nav-item">
                    <a href="#" class="nav-link">
                        <span class="pcoded-micon"><i class="feather icon-credit-card"></i></span>
                        <span class="pcoded-mtext">Billing</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <span class="pcoded-micon"><i class="feather icon-file-text"></i></span>
                        <span class="pcoded-mtext">Invoices</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <span class="pcoded-micon"><i class="feather icon-help-circle"></i></span>
                        <span class="pcoded-mtext">Support</span>
                    </a>
                </li>
                
                <li class="nav-item">
					    <form class="form-inline" action="/Kashflow/web/site/logout" method="post">
<input type="hidden" name="_csrf" value="Qri5PVWHM4n0UJgKhHWE8b5YWeM7boQiIOlLdBTsp8ct0-9NZ80D05IhrHPxOMe6yzw6u1Qg3EBz3yYbd9v96g=="><button type="submit" class="btn btn-link logout nav-link"><span class="pcoded-micon"><i class="feather icon-log-out"></i></span><span class="pcoded-mtext">Logout</span></button></form>					</li>
            </ul>
        </div>
    </div>
</nav>