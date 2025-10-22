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
                
                <li class="nav-item <?= Yii::$app->controller->id === 'customer-dashboard' && Yii::$app->controller->action->id === 'kyc' ? 'active' : '' ?>">
                    <a href="<?= Url::to(['/customer-dashboard/kyc']) ?>" class="nav-link">
                        <span class="pcoded-micon"><i class="feather icon-shield"></i></span>
                        <span class="pcoded-mtext">KYC Profile</span>
                    </a>
                </li>
                <!-- Team Section -->
                <li class="nav-item pcoded-menu-caption">
                    <label>Team & Income</label>
                </li>
                
                <li class="nav-item <?= Yii::$app->controller->id === 'customer-dashboard' && Yii::$app->controller->action->id === 'direct-team' ? 'active' : '' ?>">
                    <a href="<?= Url::to(['/customer-dashboard/direct-team']) ?>" class="nav-link">
                        <span class="pcoded-micon"><i class="feather icon-users"></i></span>
                        <span class="pcoded-mtext">Referrals</span>
                    </a>
                </li>
                
                <li class="nav-item <?= Yii::$app->controller->id === 'customer-dashboard' && Yii::$app->controller->action->id === 'level-team' ? 'active' : '' ?>">
                    <a href="<?= Url::to(['/customer-dashboard/level-team']) ?>" class="nav-link">
                        <span class="pcoded-micon"><i class="feather icon-layers"></i></span>
                        <span class="pcoded-mtext">Network</span>
                    </a>
                </li>
                
                <li class="nav-item <?= Yii::$app->controller->id === 'customer-dashboard' && Yii::$app->controller->action->id === 'income' ? 'active' : '' ?>">
                    <a href="<?= Url::to(['/customer-dashboard/income']) ?>" class="nav-link">
                        <span class="pcoded-micon"><i class="feather icon-credit-card"></i></span>
                        <span class="pcoded-mtext">Earnings</span>
                    </a>
                </li>
                
                <!-- Account Section -->
                <li class="nav-item pcoded-menu-caption">
                    <label>Account</label>
                </li>

                
                <!--<li class="nav-item">
                    <a href="#" class="nav-link">
                        <span class="pcoded-micon"><i class="feather icon-file-text"></i></span>
                        <span class="pcoded-mtext">Invoices</span>
                    </a>
                </li>-->
                
                
                <li class="nav-item <?= Yii::$app->controller->id === 'customer-dashboard' && Yii::$app->controller->action->id === 'withdrawal' ? 'active' : '' ?>">
                    <a href="<?= Url::to(['/customer-dashboard/withdrawal']) ?>" class="nav-link">
                        <span class="pcoded-micon"><i class="feather icon-minus-circle"></i></span>
                        <span class="pcoded-mtext">Withdrawal</span>
                    </a>
                </li>
                
                <li class="nav-item <?= Yii::$app->controller->id === 'customer-dashboard' && Yii::$app->controller->action->id === 'fund-request' ? 'active' : '' ?>">
                    <a href="<?= Url::to(['/customer-dashboard/fund-request']) ?>" class="nav-link">
                        <span class="pcoded-micon"><i class="feather icon-plus-circle"></i></span>
                        <span class="pcoded-mtext">Fund Request</span>
                    </a>
                </li>
                
                <li class="nav-item <?= Yii::$app->controller->id === 'customer-dashboard' && Yii::$app->controller->action->id === 'fund-transfer' ? 'active' : '' ?>">
                    <a href="<?= Url::to(['/customer-dashboard/fund-transfer']) ?>" class="nav-link">
                        <span class="pcoded-micon"><i class="feather icon-repeat"></i></span>
                        <span class="pcoded-mtext">Fund Transfer</span>
                    </a>
                </li>
                
                <!-- Support Section -->
                <li class="nav-item pcoded-menu-caption">
                    <label>Support & Complaints</label>
                </li>
                
                <!-- Ticket Section -->
                <li class="nav-item pcoded-hasmenu <?= Yii::$app->controller->id === 'customer-dashboard' && in_array(Yii::$app->controller->action->id, ['tickets', 'create-ticket', 'view-ticket']) ? 'pcoded-trigger' : '' ?>">
                    <a href="#" class="nav-link">
                        <span class="pcoded-micon"><i class="feather icon-help-circle"></i></span>
                        <span class="pcoded-mtext">Ticket</span>
                    </a>
                    <ul class="pcoded-submenu">
                        <li class="nav-item <?= Yii::$app->controller->id === 'customer-dashboard' && Yii::$app->controller->action->id === 'tickets' && empty(Yii::$app->request->get('status')) ? 'active' : '' ?>">
                            <a href="<?= Url::to(['/customer-dashboard/tickets']) ?>" class="nav-link">
                                <span class="pcoded-mtext">All Tickets</span>
                            </a>
                        </li>
                        <li class="nav-item <?= Yii::$app->controller->id === 'customer-dashboard' && Yii::$app->controller->action->id === 'tickets' && Yii::$app->request->get('status') == '1' ? 'active' : '' ?>">
                            <a href="<?= Url::to(['/customer-dashboard/tickets', 'status' => '1']) ?>" class="nav-link">
                                <span class="pcoded-mtext">Pending Tickets</span>
                            </a>
                        </li>
                        <li class="nav-item <?= Yii::$app->controller->id === 'customer-dashboard' && Yii::$app->controller->action->id === 'create-ticket' ? 'active' : '' ?>">
                            <a href="<?= Url::to(['/customer-dashboard/create-ticket']) ?>" class="nav-link">
                                <span class="pcoded-mtext">Create Ticket</span>
                            </a>
                        </li>
                    </ul>
                </li>
                
                <li class="nav-item">
					    <form class="form-inline" action="/Kashflow/web/site/logout" method="post">
<input type="hidden" name="_csrf" value="Qri5PVWHM4n0UJgKhHWE8b5YWeM7boQiIOlLdBTsp8ct0-9NZ80D05IhrHPxOMe6yzw6u1Qg3EBz3yYbd9v96g=="><button type="submit" class="btn btn-link logout nav-link"><span class="pcoded-micon"><i class="feather icon-log-out"></i></span><span class="pcoded-mtext">Logout</span></button></form>					</li>
            </ul>
        </div>
    </div>
</nav>