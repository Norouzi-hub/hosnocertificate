<div class="wrap">
    <h1>داشبورد گزارش‌ها</h1>
    
    <div class="hc-admin-stats">
        <div class="stat-box">
            <h3>تعداد کل نظرات</h3>
            <p class="stat-number"><?php echo $this->get_total_ratings(); ?></p>
        </div>
        <div class="stat-box">
            <h3>نظرات در انتظار تأیید</h3>
            <p class="stat-number"><?php echo $this->get_pending_ratings(); ?></p>
        </div>
        <div class="stat-box">
            <h3>گزارش‌های تخلف</h3>
            <p class="stat-number"><?php echo $this->get_abuse_reports(); ?></p>
        </div>
    </div>

    <h2>نظرات اخیر</h2>
    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th>کسب و کار</th>
                <th>کاربر</th>
                <th>امتیاز</th>
                <th>نظر</th>
                <th>تاریخ</th>
                <th>وضعیت</th>
                <th>عملیات</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $recent_ratings = $this->get_recent_ratings();
            foreach ($recent_ratings as $rating) :
                $business = get_post($rating->business_id);
                $user = get_user_by('id', $rating->user_id);
            ?>
                <tr>
                    <td><?php echo $business->post_title; ?></td>
                    <td><?php echo $user->display_name; ?></td>
                    <td><?php echo number_format($rating->rating, 2); ?></td>
                    <td><?php echo wp_trim_words($rating->comment, 10); ?></td>
                    <td><?php echo date('Y-m-d', strtotime($rating->date_created)); ?></td>
                    <td><?php echo $rating->status; ?></td>
                    <td>
                        <a href="#" class="button approve-rating" data-rating-id="<?php echo $rating->id; ?>">تأیید</a>
                        <a href="#" class="button reject-rating" data-rating-id="<?php echo $rating->id; ?>">رد</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script>
jQuery(document).ready(function($) {
    $('.approve-rating, .reject-rating').on('click', function(e) {
        e.preventDefault();
        var ratingId = $(this).data('rating-id');
        var action = $(this).hasClass('approve-rating') ? 'approve' : 'reject';

        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'hc_handle_rating',
                rating_id: ratingId,
                handle_action: action,
                nonce: '<?php echo wp_create_nonce('hc_handle_rating_nonce'); ?>'
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
});
</script>
