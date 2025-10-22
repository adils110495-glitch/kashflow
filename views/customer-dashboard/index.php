<?php

use yii\helpers\Html;
use yii\helpers\Url;
use app\models\Customer;

/* @var $this yii\web\View */
/* @var $customer app\models\Customer */

$this->title = 'Dashboard';
$this->params['breadcrumbs'][] = $this->title;

// Get customer's currency information
$customerCurrency = $customer->getCurrencyForDisplay();
?>

<div class="customer-dashboard-index">
    <div class="row">
        <div class="col-md-12">
            <div class="page-header">
                <h1>Good to have you back, <?= Html::encode($customer->name) ?>!</h1>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- table card-1 start -->
        <div class="col-md-12 col-xl-6">
            <div class="card flat-card">
                <div class="row-table">
                    <div class="col-sm-6 card-body br">
                        <div class="row">
                            <div class="col-sm-4">
                                <i class="fas fa-briefcase text-info mb-1 d-block"></i>
                            </div>
                            <div class="col-sm-8 text-md-center">
                                <h5><?= $customer->formatCurrencyAmount($customer->convertFromInr($additionalMetrics['investment'])) ?></h5>
                                <span>Investment</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 card-body br">
                        <div class="row">
                            <div class="col-sm-4">
                                <i class="fas fa-dollar-sign text-success mb-1 d-block"></i>
                            </div>
                            <div class="col-sm-8 text-md-center p-0">
                                <h5><?= $customer->formatCurrencyAmount($customer->convertFromInr($financialData['currentMonthIncome'])) ?></h5>
                                <span><b><?= date('M')?></b> Earnings</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row-table">
                    <div class="col-sm-6 card-body br">
                        <div class="row">
                            <div class="col-sm-4">
                                <i class="fas fa-credit-card text-danger mb-1 d-block"></i>
                            </div>
                            <div class="col-sm-8 text-md-center p-0">
                                <h5><?= $customer->formatCurrencyAmount($customer->convertFromInr($financialData['totalWithdrawal'])) ?></h5>
                                <span>Earning Released</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 card-body">
                        <div class="row">
                            <div class="col-sm-4">
                                <i class="fas fa-wallet text-warning mb-1 d-block"></i>
                            </div>
                            <div class="col-sm-8 text-md-center p-0">
                                <h5><?= $customer->formatCurrencyAmount($customer->convertFromInr($financialData['currentBalance'])) ?></h5>
                                <span>Earning Balance</span>
                            </div>
                        </div>
                    </div>
                    
                </div>
            </div>
            <!-- widget-success-card start -->
            <div class="card flat-card widget-purple-card">
                <div class="row-table">
                    <div class="col-sm-3 card-body">
                        <i class="fas fa-trophy"></i>
                    </div>
                    <div class="col-sm-9">
                        <h4>17</h4>
                        <h6>Achievements</h6>
                    </div>
                </div>
            </div>
            <!-- widget-success-card end -->
        </div>
        <!-- table card-1 end -->

        <!-- table card-2 start -->
        <div class="col-md-12 col-xl-6">
            <div class="card flat-card">
                <div class="row-table">
                    <div class="col-sm-6 card-body br">
                        <div class="row">
                            <div class="col-sm-4">
                                <i class="fas fa-chart-line text-primary mb-1 d-block"></i>
                            </div>
                            <div class="col-sm-8 text-md-center p-0">
                                <h5><?= $customer->formatCurrencyAmount($customer->convertFromInr($financialData['totalIncome'])) ?></h5>
                                <span>Total Earnings</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 card-body">
                        <div class="row">
                            <div class="col-sm-4">
                                <i class="fas fa-users text-success mb-1 d-block"></i>
                            </div>
                            <div class="col-sm-8 text-md-center p-0">
                                <h5><?= $additionalMetrics['referrals'] ?></h5>
                                <span>Referrals</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row-table">
                    <div class="col-sm-6 card-body br">
                        <div class="row">
                            <div class="col-sm-4">
                                <i class="fas fa-network-wired text-primary mb-1 d-block"></i>
                            </div>
                            <div class="col-sm-8 text-md-center">
                                <h5><?= $additionalMetrics['network'] ?></h5>
                                <span>Network</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 card-body">
                        <div class="row">
                            <div class="col-sm-4">
                                <i class="fas fa-chart-line text-warning mb-1 d-block"></i>
                            </div>
                            <div class="col-sm-8 text-md-center">
                                <h5><?= $customer->formatCurrencyAmount($customer->convertFromInr($additionalMetrics['profit'])) ?></h5>
                                <span>Total Profit</span>
                            </div>
                        </div>
                    </div>
                    
                </div>
            </div>
            <!-- widget primary card start -->
            <div class="card flat-card widget-primary-card">
                <div class="row-table">
                    <div class="col-sm-3 card-body">
                        <i class="feather icon-star-on"></i>
                    </div>
                    <div class="col-sm-9">
                        <h4>4000 +</h4>
                        <h6>Ratings Received</h6>
                    </div>
                </div>
            </div>
            <!-- widget primary card end -->
        </div>
        <!-- table card-2 end -->

        

        <!-- Customer Info Card -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Your Profile</h5>
                </div>
                <div class="card-body">
                    <p><strong>Username:</strong> <?= Html::encode($customer->user->username) ?></p>
                    <p><strong>Name:</strong> <?= Html::encode($customer->name) ?></p>
                    <p><strong>Email:</strong> <?= Html::encode($customer->email) ?></p>
                    <p><strong>Mobile:</strong> <?= Html::encode($customer->mobile_no) ?></p>
                    <p><strong>Country:</strong> <?= Html::encode($customer->country->name ?? 'N/A') ?></p>
                    <p><strong>Status:</strong>
                        <span class="badge <?= $customer->status == 1 ? 'badge-success' : 'badge-danger' ?>">
                            <?= $customer->getStatusText() ?>
                        </span>
                    </p>
                    <div class="mt-3">
                        <?= Html::a('Edit Profile', ['profile'], ['class' => 'btn btn-primary btn-sm']) ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Current Package Card -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Current Package</h5>
                </div>
                <div class="card-body">
                    <?php if ($customer->currentPackage): ?>
                        <h4 class="text-primary"><?= Html::encode($customer->currentPackage->name) ?></h4>
                        <p><strong>Amount:</strong> <?= $customer->formatCurrencyAmount($customer->convertFromInr($customer->currentPackage->amount)) ?></p>
                        <p><strong>Fee:</strong> <?= $customer->formatCurrencyAmount($customer->convertFromInr($customer->currentPackage->fee)) ?></p>
                        <p><strong>Status:</strong>
                            <span class="badge <?= $customer->currentPackage->status == 1 ? 'badge-success' : 'badge-warning' ?>">
                                <?= $customer->currentPackage->getStatusText() ?>
                            </span>
                        </p>
                    <?php else: ?>
                        <p class="text-muted">No package assigned</p>
                    <?php endif; ?>
                    <div class="mt-3">
                        <?php if (Customer::canCustomerUpgrade($customer->id, null)): ?>
                            <?= Html::button('<i class="fas fa-arrow-up"></i> Upgrade Package', ['class' => 'btn btn-success btn-sm upgrade-package-btn']) ?>
                        <?php else: ?>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i>
                                <small>You have already upgraded your package.</small>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <!-- Information Sliders Card -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Important Information</h5>
                </div>
                <div class="card-body">
                    <div class="info-slider-container">
                        <div class="info-slider" id="infoSlider">
                            <!-- Slider 1 -->
                            <div class="slider-item active">
                                <div class="slider-content">
                                    <div class="slider-icon">
                                        <i class="fas fa-chart-line text-success"></i>
                                    </div>
                                    <div class="slider-text">
                                        <h6>Double Income Opportunity</h6>
                                        <p>You may earn lot of earning on your capital in a single day.</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Slider 2 -->
                            <div class="slider-item">
                                <div class="slider-content">
                                    <div class="slider-icon">
                                        <i class="fas fa-money-bill-wave text-primary"></i>
                                    </div>
                                    <div class="slider-text">
                                        <h6>Flexible Withdrawals</h6>
                                        <p>Withdrawal mode cash, online, Dollars/ Crypto.</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Slider 3 -->
                            <div class="slider-item">
                                <div class="slider-content">
                                    <div class="slider-icon">
                                        <i class="fas fa-sync-alt text-warning"></i>
                                    </div>
                                    <div class="slider-text">
                                        <h6>Re-Investment Required</h6>
                                        <p>Re-Investment is mandatory after earnings 2X for non working users. Again start your earnings with 50% re-investment </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Slider Navigation Dots -->
                        <div class="slider-dots">
                            <span class="dot active" data-slide="0"></span>
                            <span class="dot" data-slide="1"></span>
                            <span class="dot" data-slide="2"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    

    <div class="row mt-4">

    <!-- Recent Activity or Statistics -->
    <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Account Overview</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-3">
                            <div class="stat-item">
                                <h3 class="text-primary"><?= Html::encode($customer->currentPackage->name ?? 'Free') ?></h3>
                                <p class="text-muted">Current Plan</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stat-item">
                                <h3 class="text-success"><?= $customer->status == 1 ? 'Active' : 'Inactive' ?></h3>
                                <p class="text-muted">Account Status</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stat-item">
                                <h3 class="text-info"><?= date('M Y', $customer->created_at) ?></h3>
                                <p class="text-muted">Member Since</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stat-item">
                                <h3 class="text-warning"><?= Html::encode($customer->country->name ?? 'N/A') ?></h3>
                                <p class="text-muted">Location</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity Section -->
        <div class="col-md-12 mt-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Recent Activity</h5>
                </div>
                <div class="card-body">
                    <div class="activity-list">
                        <div class="activity-item">
                            <i class="fas fa-user-plus text-success"></i>
                            <span>Account created successfully</span>
                            <small class="text-muted"><?= date('M d, Y', $customer->created_at) ?></small>
                        </div>
                        <div class="activity-item">
                            <i class="fas fa-box text-info"></i>
                            <span>Subscribed to <?= $customer->currentPackage ? Html::encode($customer->currentPackage->name) : 'Free' ?> package</span>
                            <small class="text-muted"><?= date('M d, Y', $customer->updated_at) ?></small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<script>
    document.addEventListener('DOMContentLoaded', function() {
        const slider = document.getElementById('infoSlider');
        const dots = document.querySelectorAll('.dot');
        const slides = document.querySelectorAll('.slider-item');

        let currentSlide = 0;
        let slideInterval;

        // Function to show specific slide
        function showSlide(index) {
            // Remove active class from all slides and dots
            slides.forEach(slide => slide.classList.remove('active'));
            dots.forEach(dot => dot.classList.remove('active'));

            // Add active class to current slide and dot
            slides[index].classList.add('active');
            dots[index].classList.add('active');

            currentSlide = index;
        }

        // Function to go to next slide
        function nextSlide() {
            currentSlide = (currentSlide + 1) % slides.length;
            showSlide(currentSlide);
        }

        // Function to start auto-slide
        function startAutoSlide() {
            slideInterval = setInterval(nextSlide, 4000); // Change slide every 4 seconds
        }

        // Function to stop auto-slide
        function stopAutoSlide() {
            clearInterval(slideInterval);
        }

        // Add click event listeners to dots
        dots.forEach((dot, index) => {
            dot.addEventListener('click', function() {
                showSlide(index);
                stopAutoSlide();
                startAutoSlide(); // Restart auto-slide after manual navigation
            });
        });

        // Pause auto-slide on hover
        slider.addEventListener('mouseenter', stopAutoSlide);
        slider.addEventListener('mouseleave', startAutoSlide);

        // Start auto-slide
        startAutoSlide();
    });
</script>

<?= $this->render('_upgrade_modal') ?>