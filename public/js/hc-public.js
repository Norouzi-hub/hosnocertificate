jQuery(document).ready(function($) {
    // مقایسه کسب و کارها
    $('#compare-businesses-btn').on('click', function() {
        var business1 = $('#business-compare-1').val();
        var business2 = $('#business-compare-2').val();

        if (!business1 || !business2) {
            alert('لطفاً دو کسب و کار را انتخاب کنید');
            return;
        }

        $.ajax({
            url: hc_ajax_object.ajax_url,
            type: 'POST',
            data: {
                action: 'hc_compare_businesses',
                business1: business1,
                business2: business2,
                nonce: hc_ajax_object.nonce
            },
            success: function(response) {
                if (response.success) {
                    $('#comparison-results').html(response.data);
                } else {
                    alert('خطا در دریافت اطلاعات. لطفاً دوباره تلاش کنید.');
                }
            }
        });
    });

    // ارسال فرم امتیازدهی
    $('#hc-rating-form').on('submit', function(e) {
        e.preventDefault();
        var form = $(this);
        var businessId = form.data('business-id');

        $.ajax({
            url: hc_ajax_object.ajax_url,
            type: 'POST',
            data: form.serialize() + '&action=hc_submit_rating&business_id=' + businessId + '&nonce=' + hc_ajax_object.nonce,
            success: function(response) {
                if (response.success) {
                    alert('امتیاز شما با موفقیت ثبت شد.');
                    form[0].reset();
                } else {
                    alert('خطا در ثبت امتیاز. لطفاً دوباره تلاش کنید.');
                }
            }
        });
    });

    // به‌روزرسانی آمار داشبورد کاربر
    function updateDashboardStats() {
        $.ajax({
            url: hc_ajax_object.ajax_url,
            type: 'POST',
            data: {
                action: 'hc_get_user_dashboard_stats',
                nonce: hc_ajax_object.nonce
            },
            success: function(response) {
                if (response.success) {
                    $('#total-businesses .stat-number').text(response.data.total_businesses);
                    $('#total-ratings .stat-number').text(response.data.total_ratings);
                    $('#total-bookings .stat-number').text(response.data.total_bookings);
                    // به‌روزرسانی فعالیت‌های اخیر
                    updateRecentActivities(response.data.recent_activities);
                }
            }
        });
    }

    function updateRecentActivities(activities) {
        var activityList = $('#activities-list');
        activityList.empty();
        activities.forEach(function(activity) {
            var listItem = $('<li>').text(activity.description);
            activityList.append(listItem);
        });
    }

    // اگر در صفحه داشبورد کاربر هستیم، آمار را به‌روز کنیم
    if ($('.hc-user-dashboard').length) {
        updateDashboardStats();
        // هر 5 دقیقه یکبار آمار را به‌روز کنیم
        setInterval(updateDashboardStats, 300000);
    }
});
