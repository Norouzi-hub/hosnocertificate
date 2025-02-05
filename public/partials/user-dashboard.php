<div class="hc-user-dashboard">
    <div class="dashboard-header">
        <h1>داشبورد کاربری</h1>
        <div class="user-info">
            <?php 
            $current_user = wp_get_current_user();
            echo get_avatar($current_user->ID, 100);
            echo '<h2>' . $current_user->display_name . '</h2>';
            ?>
        </div>
    </div>

    <div class="dashboard-stats">
        <div class="stat-card" id="total-businesses">
            <h3>کسب و کارها</h3>
            <p class="stat-number">0</p>
        </div>
        <div class="stat-card" id="total-ratings">
            <h3>امتیازها</h3>
            <p class="stat-number">0</p>
        </div>
        <div class="stat-card" id="total-bookings">
            <h3>رزروها</h3>
            <p class="stat-number">0</p>
        </div>
    </div>

    <div class="dashboard-sections">
        <div class="recent-activities">
            <h2>فعالیت‌های اخیر</h2>
            <ul id="activities-list"></ul>
        </div>

        <div class="quick-actions">
            <h2>اقدامات سریع</h2>
            <a href="<?php echo home_url('/add-business'); ?>" class="button">افزودن کسب و کار</a>
            <a href="<?php echo home_url('/profile-edit'); ?>" class="button">ویرایش پروفایل</a>
        </div>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    $.ajax({
        url: ajaxurl,
        type: 'POST',
        data: {
            action: 'hc_dashboard_stats'
        },
        success: function(response) {
            if (response.success) {
                $('#total-businesses .stat-number').text(response.data.total_businesses);
                $('#total-ratings .stat-number').text(response.data.total_ratings);
                $('#total-bookings .stat-number').text(response.data.total_bookings);

                var activitiesList = $('#activities-list');
                response.data.recent_activities.forEach(function(activity) {
                    var listItem = $('<li>');
                    if (activity.type === 'business_created') {
                        listItem.text('کسب و کار جدید: ' + activity.title);
                    } else if (activity.type === 'booking_created') {
                        listItem.text('رزرو جدید: ' + activity.title);
                    }
                    activitiesList.append(listItem);
                });
            }
        }
    });
});
</script>
