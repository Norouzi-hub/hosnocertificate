jQuery(document).ready(function($) {
    // مدیریت درخواست‌های اعتبارسنجی
    $('.approve-verification, .reject-verification').on('click', function() {
        var businessId = $(this).data('business-id');
        var action = $(this).hasClass('approve-verification') ? 'approve' : 'reject';

        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'hc_handle_verification',
                business_id: businessId,
                handle_action: action,
                nonce: hc_admin_ajax.nonce
            },
            success: function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert('خطا در پردازش درخواست. لطفاً دوباره تلاش کنید.');
                }
            }
        });
    });

    // مدیریت نظرات و امتیازها
    $('.approve-rating, .reject-rating').on('click', function() {
        var ratingId = $(this).data('rating-id');
        var action = $(this).hasClass('approve-rating') ? 'approve' : 'reject';

        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'hc_handle_rating',
                rating_id: ratingId,
                handle_action: action,
                nonce: hc_admin_ajax.nonce
            },
            success: function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert('خطا در پردازش نظر. لطفاً دوباره تلاش کنید.');
                }
            }
        });
    });

    // به‌روزرسانی آمار داشبورد مدیریت
    function updateAdminDashboardStats() {
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'hc_get_admin_dashboard_stats',
                nonce: hc_admin_ajax.nonce
            },
            success: function(response) {
                if (response.success) {
                    $('#total-businesses .stat-number').text(response.data.total_businesses);
                    $('#verified-businesses .stat-number').text(response.data.verified_businesses);
                    $('#average-rating .stat-number').text(response.data.average_rating);
                    $('#total-ratings .stat-number').text(response.data.total_ratings);
                    $('#pending-ratings .stat-number').text(response.data.pending_ratings);
                }
            }
        });
    }

    // اگر در صفحه داشبورد مدیریت هستیم، آمار را به‌روز کنیم
    if ($('.hc-admin-dashboard').length) {
        updateAdminDashboardStats();
        // هر 5 دقیقه یکبار آمار را به‌روز کنیم
        setInterval(updateAdminDashboardStats, 300000);
    }

    // مدیریت تنظیمات پلاگین
    $('#hc-settings-form').on('submit', function(e) {
        e.preventDefault();
        var form = $(this);

        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: form.serialize() + '&action=hc_save_settings&nonce=' + hc_admin_ajax.nonce,
            success: function(response) {
                if (response.success) {
                    alert('تنظیمات با موفقیت ذخیره شد.');
                } else {
                    alert('خطا در ذخیره تنظیمات. لطفاً دوباره تلاش کنید.');
                }
            }
        });
    });
});
