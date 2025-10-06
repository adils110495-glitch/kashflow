// Custom jQuery functions for the application

$(document).ready(function() {
    var base_url = 'http://localhost/Kashflow/web';
    // Initialize tooltips (Bootstrap 5 compatible)
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Fallback for Bootstrap 4 tooltips
    $('[data-toggle="tooltip"]').tooltip();
    
    
    
    // Update mobile code when country changes
    $('#country-select').on('change', function() {
        var countryId = $(this).val();
        if (countryId) {
            var countriesData = window.countriesData || [];
            var countryData = countriesData.find(function(c) { return c.id == countryId; });
            if (countryData && countryData.mobile_code) {
                // Display country code and mobile code
                var flagAndCode = '<span class="country-flag">' + countryData.flag + '</span> +' + countryData.mobile_code;
                $('#mobile-code-display').html(flagAndCode);
            } else {
                $('#mobile-code-display').text('+1');
            }
        } else {
            $('#mobile-code-display').text('+1');
        }
    });
    
    // Additional custom jQuery code can be added here
    
    // Upgrade Modal Functionality
    // Handle upgrade modal opening
    $(document).on('click', '.upgrade-package-btn', function(e) {
        e.preventDefault();
        
        // Show modal (Bootstrap 5 compatible)
        var modal = new bootstrap.Modal(document.getElementById('upgradePackageModal'));
        modal.show();
        
        // Reset modal content
        $('#upgradeModalLoading').show();
        $('#upgradeModalData').hide();
        //$('#upgradeModalFooter').hide();
        
        // Load upgrade view via AJAX
        $.ajax({
            url: base_url + '/customer-dashboard/upgrade',
            type: 'GET',
            success: function(response) {
                $('#upgradeModalLoading').hide();
                
                // Extract the package cards from the response
                const $response = $(response);
                const packageCards = $response.find('.available-packages');
                
                if (packageCards.length > 0) {
                    // Update modal content with the rendered view
                    $('#upgradeModalData').html(packageCards.parent().html()).show();
                    $('#upgradeModalFooter').show();
                    
                    // Convert any form submission to AJAX for modal
                    $('#upgradeModalData .upgrade-form').on('submit', function(e) {
                        e.preventDefault();
                        const formData = $(this).serialize();
                        
                        $.ajax({
                            url: base_url + '/customer-dashboard/upgrade',
                            type: 'POST',
                            data: formData,
                            success: function(result) {
                                location.reload(); // Reload to show success message
                            },
                            error: function() {
                                showErrorModal('Error!', 'An error occurred during upgrade.');
                            }
                        });
                    });
                    
                } else {
                    $('#upgradeModalData').html(
                        '<div class="alert alert-danger">' +
                        '<i class="fas fa-exclamation-triangle"></i> ' +
                        'Unable to load upgrade packages.' +
                        '</div>'
                    ).show();
                    $('#upgradeModalFooter').show();
                }
            },
            error: function(xhr, status, error) {
                $('#upgradeModalLoading').hide();
                
                // Check if response is HTML (login redirect)
                if (xhr.responseText && xhr.responseText.includes('<html>')) {
                    $('#upgradeModalData').html(
                        '<div class="alert alert-warning">' +
                        '<i class="fas fa-sign-in-alt"></i> ' +
                        'Please log in to access upgrade options. <a href="/user/login" class="alert-link">Login here</a>' +
                        '</div>'
                    ).show();
                } else {
                    $('#upgradeModalData').html(
                        '<div class="alert alert-danger">' +
                        '<i class="fas fa-exclamation-triangle"></i> ' +
                        'Failed to load upgrade options. Please try again.' +
                        '</div>'
                    ).show();
                }
                $('#upgradeModalFooter').show();
            }
        });
    });
    
    // Handle package selection for upgrade buttons
    $(document).on('click', '.upgrade-btn', function(e) {
        e.preventDefault();
        
        // Get package details from the button
        var packageId = $(this).val();
        var packageName = $(this).data('package-name') || $(this).text().replace('Upgrade to ', '').replace(/\s+/g, ' ').trim();
        var packagePrice = $(this).data('package-price') || $(this).closest('.package-card').find('.package-price h2').text();
        
        // Remove selected class from all cards
        $('.package-card').removeClass('selected');
        
        // Add selected class to clicked card
        $(this).closest('.package-card').addClass('selected');
        
        // Update footer with selected package info
        $('#selectedPackageName').text(packageName);
        $('#selectedPackagePrice').text(packagePrice);
        $('#selectedPackageInfo').show();
        
        // Store selected package ID for payment
        $('#upgradeModalFooter').data('package-id', packageId);
        
        // Enable the proceed to payment button
        $('.modal-upgrade-btn').prop('disabled', false);
        
        // Show footer
        $('#upgradeModalFooter').show();
    });
    
    // Handle upgrade form submission
    $(document).on('click', '.modal-upgrade-btn', function(e) {
        e.preventDefault();
        
        var packageId = $('#upgradeModalFooter').data('package-id');
        var packageName = $('#selectedPackageName').text();
        var packagePrice = $('#selectedPackagePrice').text();
        
        if (!packageId) {
            showWarningModal('Warning!', 'Please select a package to upgrade to.');
            return;
        }
        
        // Show payment confirmation modal
        showConfirmModal(
            'Confirm Package Upgrade',
            'Package: ' + packageName + '<br>Price: ' + packagePrice + '<br><br>This will initiate the payment process. Continue?',
            function() {
                // User confirmed, proceed with upgrade
                proceedWithUpgrade(packageId);
            }
        );
    });
    
    // Function to proceed with upgrade after confirmation
    function proceedWithUpgrade(packageId) {
        // Disable button and show loading
        $('.modal-upgrade-btn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Processing Payment...');
        
        // Submit upgrade request
        $.ajax({
            url: base_url + '/customer-dashboard/process-upgrade',
            type: 'POST',
            data: {
                package_id: packageId,
                _csrf: $('meta[name=csrf-token]').attr('content')
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    showSuccessModal('Success!', 'Package upgrade initiated successfully! Please complete the payment process.', response.redirect_url);
                    // Close modal
                    $('#upgradePackageModal').modal('hide');
                } else {
                    showErrorModal('Upgrade Failed!', 'Upgrade failed: ' + (response.message || 'Unknown error occurred.'));
                    $('.modal-upgrade-btn').prop('disabled', false).html('<i class="fas fa-credit-card"></i> Proceed to Payment');
                }
            },
            error: function(xhr, status, error) {
                // Check if response is HTML (login redirect)
                if (xhr.responseText && xhr.responseText.includes('<html>')) {
                    showWarningModal('Session Expired!', 'Session expired. Please log in again.');
                    setTimeout(function() {
                    window.location.href = '/user/login';
                    }, 2000);
                } else {
                    showErrorModal('Network Error!', 'Network error occurred. Please try again.');
                    $('.modal-upgrade-btn').prop('disabled', false).html('<i class="fas fa-credit-card"></i> Proceed to Payment');
                }
            }
        });
    }
    
    // Level Team Filter Auto-submit (Customer and Admin)
    // Auto-submit form when username filter changes (with debounce)
    let usernameTimeout;
    const usernameFilter = document.getElementById('username-filter');
    if (usernameFilter) {
        usernameFilter.addEventListener('input', function() {
            clearTimeout(usernameTimeout);
            usernameTimeout = setTimeout(() => {
                if (this.value.length >= 2 || this.value.length === 0) {
                    this.form.submit();
                }
            }, 500);
        });
    }
    
    // Auto-submit form when level filter changes
    const levelFilter = document.getElementById('level-filter');
    if (levelFilter) {
        levelFilter.addEventListener('change', function() {
            this.form.submit();
        });
    }
    
    // Auto-submit form when date filters change
    const fromDateFilter = document.getElementById('from-date');
    if (fromDateFilter) {
        fromDateFilter.addEventListener('change', function() {
            this.form.submit();
        });
    }
    
    const toDateFilter = document.getElementById('to-date');
    if (toDateFilter) {
        toDateFilter.addEventListener('change', function() {
            this.form.submit();
        });
    }
    
    // Full screen toggle function
    window.toggleFullScreen = function() {
        if (!document.fullscreenElement) {
            document.documentElement.requestFullscreen();
        } else {
            if (document.exitFullscreen) {
                document.exitFullscreen();
            }
        }
    };
    
    // Mobile menu toggle for customer dashboard
    const mobileToggle = document.getElementById('mobile-collapse1');
    const sidebar = document.querySelector('.pcoded-navbar');
    const mainContainer = document.querySelector('.pcoded-main-container');
    
    if (mobileToggle && sidebar) {
        mobileToggle.addEventListener('click', function(e) {
            e.preventDefault();
            sidebar.classList.toggle('mob-open');
        });
    }
    
    // Close mobile menu when clicking outside
    document.addEventListener('click', function(e) {
        if (sidebar && sidebar.classList.contains('mob-open')) {
            if (!sidebar.contains(e.target) && !mobileToggle.contains(e.target)) {
                sidebar.classList.remove('mob-open');
            }
        }
    });
    
    // Auto-hide alerts after 5 seconds
    setTimeout(function() {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(function(alert) {
            if (alert.classList.contains('alert-success') || alert.classList.contains('alert-info')) {
                alert.style.transition = 'opacity 0.5s';
                alert.style.opacity = '0';
                setTimeout(function() {
                    alert.remove();
                }, 500);
            }
        });
    }, 5000);
    
    // Auto-update username when email changes (Admin user form)
    const emailField = document.getElementById('user-email');
    const usernameField = document.getElementById('user-username');
    
    if (emailField && usernameField) {
        emailField.addEventListener('input', function() {
            usernameField.value = this.value;
        });
    }
    
    // Referral Code Validation
    let referralValidationTimeout;
    
    // Debug: Check if element exists
    console.log('Referral code input element exists:', $('#referral-code-input').length);
    
    // Use document ready and event delegation to ensure the element exists
    $(document).on('input keyup', '#referral-code-input', function() {
        console.log('Referral code event triggered:', $(this).val());
        const referralCode = $(this).val().trim();
        const validationMessage = $('#referral-validation-message');
        
        // Clear previous timeout
        clearTimeout(referralValidationTimeout);
        
        // Clear validation message
        validationMessage.removeClass('text-success text-danger').html('');
        
        // If empty, don't validate
        if (referralCode === '') {
            return;
        }
        
        // Debounce the validation request
        referralValidationTimeout = setTimeout(function() {
            validateReferralCode(referralCode);
        }, 500);
    });
    
    // Also try direct binding as fallback
    setTimeout(function() {
        if ($('#referral-code-input').length > 0) {
            console.log('Direct binding to referral code input');
            $('#referral-code-input').on('input keyup', function() {
                console.log('Direct binding event triggered:', $(this).val());
                const referralCode = $(this).val().trim();
                const validationMessage = $('#referral-validation-message');
                
                clearTimeout(referralValidationTimeout);
                validationMessage.removeClass('text-success text-danger').html('');
                
                if (referralCode === '') {
                    return;
                }
                
                referralValidationTimeout = setTimeout(function() {
                    validateReferralCode(referralCode);
                }, 500);
            });
        }
    }, 1000);
    
    function validateReferralCode(referralCode) {
        const validationMessage = $('#referral-validation-message');
        console.log(referralCode);
        // Show loading state
        validationMessage.html('<small class="text-info"><i class="fa fa-spinner fa-spin"></i> Validating...</small>');
        
        $.ajax({
            url: base_url + '/user/registration/validate-referral-code',
            type: 'POST',
            data: {
                referral_code: referralCode,
                _csrf: $('meta[name="csrf-token"]').attr('content') || $('input[name="_token"]').val() || $('input[name="csrf-token"]').val()
            },
            dataType: 'json',
            success: function(response) {
                if (response.valid) {
                    validationMessage.html('<small class="text-success"><i class="fa fa-check"></i> ' + response.message + '</small>');
                    $('#referral-code-input').removeClass('is-invalid').addClass('is-valid');
                } else {
                    validationMessage.html('<small class="text-danger"><i class="fa fa-times"></i> ' + response.message + '</small>');
                    $('#referral-code-input').removeClass('is-valid').addClass('is-invalid');
                }
            },
            error: function(xhr, status, error) {
                console.error('Referral validation error:', error);
                validationMessage.html('<small class="text-warning"><i class="fa fa-exclamation-triangle"></i> Validation failed. Please try again.</small>');
                $('#referral-code-input').removeClass('is-valid is-invalid');
            }
        });
    }
    
    // Form submission validation
    $('#registration-form').on('submit', function(e) {
        const referralCode = $('#referral-code-input').val().trim();
        const validationMessage = $('#referral-validation-message');
        
        // If referral code is provided, validate it before form submission
        if (referralCode !== '') {
            // Check if the field has validation classes
            if ($('#referral-code-input').hasClass('is-invalid')) {
                e.preventDefault();
                validationMessage.html('<small class="text-danger"><i class="fa fa-times"></i> Please fix the referral code before submitting.</small>');
                $('#referral-code-input').focus();
                return false;
            }
            
            // If no validation classes, perform quick validation
            if (!$('#referral-code-input').hasClass('is-valid')) {
                e.preventDefault();
                validationMessage.html('<small class="text-info"><i class="fa fa-spinner fa-spin"></i> Validating referral code...</small>');
                
                $.ajax({
                    url: base_url + '/user/registration/validate-referral-code',
                    type: 'POST',
                    data: {
                        referral_code: referralCode,
                        _csrf: $('meta[name="csrf-token"]').attr('content') || $('input[name="_token"]').val() || $('input[name="csrf-token"]').val()
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.valid) {
                            validationMessage.html('<small class="text-success"><i class="fa fa-check"></i> ' + response.message + '</small>');
                            $('#referral-code-input').removeClass('is-invalid').addClass('is-valid');
                            // Submit the form after successful validation
                            $('#registration-form')[0].submit();
                        } else {
                            validationMessage.html('<small class="text-danger"><i class="fa fa-times"></i> ' + response.message + '</small>');
                            $('#referral-code-input').removeClass('is-valid').addClass('is-invalid');
                            $('#referral-code-input').focus();
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Referral validation error:', error);
                        validationMessage.html('<small class="text-warning"><i class="fa fa-exclamation-triangle"></i> Validation failed. Please try again.</small>');
                        $('#referral-code-input').focus();
                    }
                });
                
                return false;
            }
        }
        
        // If no referral code or already validated, allow form submission
        return true;
    });

    // Modal Functions for Success and Error Messages
    function showSuccessModal(title, message, redirectUrl = null) {
        const modalId = 'successModal_' + Date.now();
        const modalHtml = `
            <div class="modal fade" id="${modalId}" tabindex="-1" role="dialog" aria-labelledby="${modalId}Label" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header bg-success text-white">
                            <h5 class="modal-title" id="${modalId}Label">
                                <i class="feather icon-check-circle mr-2"></i>${title}
                            </h5>
                            <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="text-center">
                                <i class="feather icon-check-circle text-success" style="font-size: 3rem;"></i>
                                <p class="mt-3 mb-0">${message}</p>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-success" data-dismiss="modal" id="${modalId}_ok">
                                <i class="feather icon-check mr-1"></i>OK
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        // Remove existing success modals if any
        $('[id^="successModal_"]').remove();
        
        // Add modal to body
        $('body').append(modalHtml);
        
        // Show modal
        $('#' + modalId).modal('show');
        
        // Handle OK button click
        $('#' + modalId + '_ok').on('click', function() {
            $('#' + modalId).modal('hide');
        });
        
        // Handle modal close events
        $('#' + modalId).on('hidden.bs.modal', function() {
            $(this).remove();
            // Auto redirect if URL provided
            if (redirectUrl) {
                window.location.href = redirectUrl;
            }
        });
    }

    function showErrorModal(title, message) {
        const modalId = 'errorModal_' + Date.now();
        const modalHtml = `
            <div class="modal fade" id="${modalId}" tabindex="-1" role="dialog" aria-labelledby="${modalId}Label" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header bg-danger text-white">
                            <h5 class="modal-title" id="${modalId}Label">
                                <i class="feather icon-x-circle mr-2"></i>${title}
                            </h5>
                            <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="text-center">
                                <i class="feather icon-x-circle text-danger" style="font-size: 3rem;"></i>
                                <p class="mt-3 mb-0">${message}</p>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-dismiss="modal" id="${modalId}_close">
                                <i class="feather icon-x mr-1"></i>Close
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        // Remove existing error modals if any
        $('[id^="errorModal_"]').remove();
        
        // Add modal to body
        $('body').append(modalHtml);
        
        // Show modal
        $('#' + modalId).modal('show');
        
        // Handle Close button click
        $('#' + modalId + '_close').on('click', function() {
            $('#' + modalId).modal('hide');
        });
        
        // Handle modal close events
        $('#' + modalId).on('hidden.bs.modal', function() {
            $(this).remove();
        });
    }

    function showWarningModal(title, message) {
        const modalId = 'warningModal_' + Date.now();
        const modalHtml = `
            <div class="modal fade" id="${modalId}" tabindex="-1" role="dialog" aria-labelledby="${modalId}Label" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header bg-warning text-dark">
                            <h5 class="modal-title" id="${modalId}Label">
                                <i class="feather icon-alert-triangle mr-2"></i>${title}
                            </h5>
                            <button type="button" class="close text-dark" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="text-center">
                                <i class="feather icon-alert-triangle text-warning" style="font-size: 3rem;"></i>
                                <p class="mt-3 mb-0">${message}</p>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-warning" data-dismiss="modal" id="${modalId}_ok">
                                <i class="feather icon-check mr-1"></i>OK
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        // Remove existing warning modals if any
        $('[id^="warningModal_"]').remove();
        
        // Add modal to body
        $('body').append(modalHtml);
        
        // Show modal
        $('#' + modalId).modal('show');
        
        // Handle OK button click
        $('#' + modalId + '_ok').on('click', function() {
            $('#' + modalId).modal('hide');
        });
        
        // Handle modal close events
        $('#' + modalId).on('hidden.bs.modal', function() {
            $(this).remove();
        });
    }

    function showInfoModal(title, message) {
        const modalId = 'infoModal_' + Date.now();
        const modalHtml = `
            <div class="modal fade" id="${modalId}" tabindex="-1" role="dialog" aria-labelledby="${modalId}Label" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header bg-info text-white">
                            <h5 class="modal-title" id="${modalId}Label">
                                <i class="feather icon-info mr-2"></i>${title}
                            </h5>
                            <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="text-center">
                                <i class="feather icon-info text-info" style="font-size: 3rem;"></i>
                                <p class="mt-3 mb-0">${message}</p>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-info" data-dismiss="modal" id="${modalId}_ok">
                                <i class="feather icon-check mr-1"></i>OK
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        // Remove existing info modals if any
        $('[id^="infoModal_"]').remove();
        
        // Add modal to body
        $('body').append(modalHtml);
        
        // Show modal
        $('#' + modalId).modal('show');
        
        // Handle OK button click
        $('#' + modalId + '_ok').on('click', function() {
            $('#' + modalId).modal('hide');
        });
        
        // Handle modal close events
        $('#' + modalId).on('hidden.bs.modal', function() {
            $(this).remove();
        });
    }

    function showConfirmModal(title, message, confirmCallback, cancelCallback = null) {
        const modalId = 'confirmModal_' + Date.now();
        const modalHtml = `
            <div class="modal fade" id="${modalId}" tabindex="-1" role="dialog" aria-labelledby="${modalId}Label" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header bg-warning text-dark">
                            <h5 class="modal-title" id="${modalId}Label">
                                <i class="feather icon-help-circle mr-2"></i>${title}
                            </h5>
                            <button type="button" class="close text-dark" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="text-center">
                                <i class="feather icon-help-circle text-warning" style="font-size: 3rem;"></i>
                                <p class="mt-3 mb-0">${message}</p>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal" id="${modalId}_cancel">
                                <i class="feather icon-x mr-1"></i>Cancel
                            </button>
                            <button type="button" class="btn btn-warning" id="${modalId}_confirm">
                                <i class="feather icon-check mr-1"></i>Confirm
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        // Remove existing confirm modals if any
        $('[id^="confirmModal_"]').remove();
        
        // Add modal to body
        $('body').append(modalHtml);
        
        // Show modal
        $('#' + modalId).modal('show');
        
        // Handle confirm button click
        $('#' + modalId + '_confirm').on('click', function() {
            $('#' + modalId).modal('hide');
            if (confirmCallback && typeof confirmCallback === 'function') {
                confirmCallback();
            }
        });
        
        // Handle cancel button click
        $('#' + modalId + '_cancel').on('click', function() {
            $('#' + modalId).modal('hide');
            if (cancelCallback && typeof cancelCallback === 'function') {
                cancelCallback();
            }
        });
        
        // Handle modal close (X button or backdrop click)
        $('#' + modalId).on('hidden.bs.modal', function() {
            $(this).remove();
        });
    }
});