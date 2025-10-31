<?php
use yii\helpers\Html;
?>
<!-- [ navigation menu ] start -->
	<nav class="pcoded-navbar  ">
		<div class="navbar-wrapper  ">
			<div class="navbar-content scroll-div " >
				
				<div class="">
					<div class="main-menu-header">
						<img class="img-radius" src="<?= Yii::getAlias('@web/images/user/avatar-2.jpg') ?>" alt="User-Profile-Image">
						<div class="user-details">
							<span>John Doe</span>
							<div id="more-details">UX Designer<i class="fa fa-chevron-down m-l-5"></i></div>
						</div>
					</div>
					<div class="collapse" id="nav-user-link">
						<ul class="list-unstyled">
							<li class="list-group-item"><a href="user-profile.html"><i class="feather icon-user m-r-5"></i>View Profile</a></li>
							<li class="list-group-item"><a href="#!"><i class="feather icon-settings m-r-5"></i>Settings</a></li>
							<li class="list-group-item"><?= Html::beginForm(['/site/logout'], 'post', ['class' => 'form-inline']) . Html::submitButton('<i class="feather icon-log-out m-r-5"></i>Logout', ['class' => 'btn btn-link logout']) . Html::endForm() ?></li>
						</ul>
					</div>
				</div>
				
				<ul class="nav pcoded-inner-navbar ">
					<li class="nav-item pcoded-menu-caption">
						<label>Navigation</label>
					</li>
					<li class="nav-item">
					    <a href="<?= Yii::$app->homeUrl; ?>" class="nav-link "><span class="pcoded-micon"><i class="feather icon-home"></i></span><span class="pcoded-mtext">Dashboard</span></a>
					</li>
					<li class="nav-item pcoded-menu-caption">
						<label>Settings</label>
					</li>
					<li class="nav-item pcoded-hasmenu">
						<a href="#!" class="nav-link "><span class="pcoded-micon"><i class="feather icon-box"></i></span><span class="pcoded-mtext">Plan</span></a>
						<ul class="pcoded-submenu">
							<li><a href="<?= \yii\helpers\Url::to(['/package/index']) ?>">Package</a></li>
							<li><a href="<?= \yii\helpers\Url::to(['/roi-plan/configure']) ?>">ROI Plan</a></li>
							<li><a href="<?= \yii\helpers\Url::to(['/level-plan/index']) ?>">Level Plan</a></li>
							<li><a href="<?= \yii\helpers\Url::to(['/reward-plan/index']) ?>">Reward Plan</a></li>
						</ul>
					</li>
					<li class="nav-item pcoded-hasmenu">
						<a href="#!" class="nav-link "><span class="pcoded-micon"><i class="feather icon-box"></i></span><span class="pcoded-mtext">Basic</span></a>
						<ul class="pcoded-submenu">
							<li><a href="<?= \yii\helpers\Url::to(['/company/index']) ?>">Company</a></li>
							<li><a href="<?= \yii\helpers\Url::to(['/country/index']) ?>">Country</a></li>
							<li><a href="<?= \yii\helpers\Url::to(['/states/index']) ?>">States</a></li>
						</ul>
					</li>
					<li class="nav-item pcoded-menu-caption">
						<label>Customer & Income</label>
					</li>
					<li class="nav-item">
					    <a href="<?= \yii\helpers\Url::to(['/customer/index']) ?>" class="nav-link "><span class="pcoded-micon"><i class="feather icon-users"></i></span><span class="pcoded-mtext">Customer</span></a>
					</li>
					<li class="nav-item">
					    <a href="<?= \yii\helpers\Url::to(['/customer/referred-team']) ?>" class="nav-link "><span class="pcoded-micon"><i class="feather icon-user-plus"></i></span><span class="pcoded-mtext">Referred Team</span></a>
					</li>
					<li class="nav-item">
					    <a href="<?= \yii\helpers\Url::to(['/customer/level-team']) ?>" class="nav-link "><span class="pcoded-micon"><i class="feather icon-layers"></i></span><span class="pcoded-mtext">Level Team</span></a>
					</li>
					<li class="nav-item">
					    <a href="<?= \yii\helpers\Url::to(['/customer/income']) ?>" class="nav-link "><span class="pcoded-micon"><i class="feather icon-credit-card"></i></span><span class="pcoded-mtext">Income</span></a>
					</li>
					<li class="nav-item pcoded-menu-caption">
						<label>Payment</label>
					</li>
					<li class="nav-item pcoded-hasmenu <?= Yii::$app->controller->id === 'admin' && in_array(Yii::$app->controller->action->id, ['withdrawals', 'pending-withdrawals', 'all-withdrawals']) ? 'pcoded-trigger' : '' ?>">
						<a href="#!" class="nav-link "><span class="pcoded-micon"><i class="feather icon-minus-circle"></i></span><span class="pcoded-mtext">Withdrawal</span></a>
						<ul class="pcoded-submenu">
							<li><a href="<?= \yii\helpers\Url::to(['/admin/withdrawals']) ?>">All Withdrawals</a></li>
							<li><a href="<?= \yii\helpers\Url::to(['/admin/pending-withdrawals']) ?>">Pending Requests</a></li>
						</ul>
					</li>
					<li class="nav-item pcoded-hasmenu <?= Yii::$app->controller->id === 'fund-request' ? 'pcoded-trigger' : '' ?>">
						<a href="#!" class="nav-link "><span class="pcoded-micon"><i class="feather icon-plus-circle"></i></span><span class="pcoded-mtext">Fund Requests</span></a>
						<ul class="pcoded-submenu">
							<li><a href="<?= \yii\helpers\Url::to(['/fund-request/index']) ?>">All Fund Requests</a></li>
							<li><a href="<?= \yii\helpers\Url::to(['/fund-request/index', 'status' => '0']) ?>">Pending Requests</a></li>
							<li><a href="<?= \yii\helpers\Url::to(['/fund-request/index', 'status' => '1']) ?>">Approved Requests</a></li>
							<li><a href="<?= \yii\helpers\Url::to(['/fund-request/index', 'status' => '2']) ?>">Rejected Requests</a></li>
						</ul>
					</li>
					<li class="nav-item pcoded-hasmenu <?= Yii::$app->controller->id === 'fund-transfer' ? 'pcoded-trigger' : '' ?>">
						<a href="#!" class="nav-link "><span class="pcoded-micon"><i class="feather icon-repeat"></i></span><span class="pcoded-mtext">Fund Transfers</span></a>
						<ul class="pcoded-submenu">
							<li><a href="<?= \yii\helpers\Url::to(['/fund-transfer/index']) ?>">All Transfers</a></li>
							<li><a href="<?= \yii\helpers\Url::to(['/fund-transfer/index', 'status' => '0']) ?>">Pending Transfers</a></li>
							<li><a href="<?= \yii\helpers\Url::to(['/fund-transfer/index', 'status' => '1']) ?>">Approved Transfers</a></li>
							<li><a href="<?= \yii\helpers\Url::to(['/fund-transfer/index', 'status' => '2']) ?>">Rejected Transfers</a></li>
							<li><a href="<?= \yii\helpers\Url::to(['/fund-transfer/create']) ?>">Create Transfer</a></li>
						</ul>
					</li>
					<li class="nav-item pcoded-menu-caption">
						<label>Complain & Support</label>
					</li>
					<li class="nav-item pcoded-hasmenu <?= Yii::$app->controller->id === 'admin-ticket' && in_array(Yii::$app->controller->action->id, ['index', 'view']) ? 'pcoded-trigger' : '' ?>">
						<a href="#!" class="nav-link "><span class="pcoded-micon"><i class="feather icon-ticket"></i></span><span class="pcoded-mtext">Tickets</span></a>
						<ul class="pcoded-submenu">
							<li><a href="<?= \yii\helpers\Url::to(['/admin-ticket/index']) ?>">All Tickets</a></li>
							<li><a href="<?= \yii\helpers\Url::to(['/admin-ticket/index', 'status' => '1']) ?>">Pending Tickets</a></li>
						</ul>
					</li>
					<li class="nav-item">
					    <?= Html::beginForm(['/site/logout'], 'post', ['class' => 'form-inline']) . Html::submitButton('<span class="pcoded-micon"><i class="feather icon-log-out"></i></span><span class="pcoded-mtext">Logout</span>', ['class' => 'btn btn-link logout nav-link']) . Html::endForm() ?>
					</li>

				</ul>
				
			</div>
		</div>
	</nav>
	<!-- [ navigation menu ] end -->