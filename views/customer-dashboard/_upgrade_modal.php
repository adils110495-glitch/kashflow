<?php

use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
?>

<!-- Package Upgrade Modal -->
<div class="modal fade" id="upgradePackageModal" tabindex="-1" role="dialog" aria-labelledby="upgradePackageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="upgradePackageModalLabel">
                    <i class="fas fa-arrow-up"></i> Upgrade Package
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="upgradeModalContent">
                <!-- Loading spinner -->
                <div class="text-center py-5" id="upgradeModalLoading">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <p class="mt-2">Loading upgrade options...</p>
                </div>
                
                <!-- Content will be loaded here via AJAX -->
                <div id="upgradeModalData" style="display: none;"></div>
            </div>
            <div class="modal-footer" id="upgradeModalFooter">
                <div class="d-flex justify-content-between align-items-center w-100">
                    <div class="selected-package-info" id="selectedPackageInfo" style="display: none;">
                        <small class="text-muted">Selected:</small>
                        <strong id="selectedPackageName"></strong>
                        <span class="text-success" id="selectedPackagePrice"></span>
                    </div>
                    <div class="modal-buttons">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times"></i> Close
                        </button>
                        <button type="button" class="btn btn-success modal-upgrade-btn" disabled>
                            <i class="fas fa-credit-card"></i> Proceed to Payment
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>