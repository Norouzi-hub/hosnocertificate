<div class="wrap">
    <h1>داشبورد کسب و کارها</h1>
    
    <div class="hc-admin-stats">
        <div class="stat-box">
            <h3>تعداد کل کسب و کارها</h3>
            <p class="stat-number"><?php echo $this->get_total_businesses(); ?></p>
        </div>
        <div class="stat-box">
            <h3>کسب و کارهای تأیید شده</h3>
            <p class="stat-number"><?php echo $this->get_verified_businesses(); ?></p>
        </div>
        <div class="stat-box">
            <h3>میانگین امتیاز کلی</h3>
            <p class="stat-number"><?php echo $this->get_average_rating(); ?></p>
        </div>
    </div>

    <h2>کسب و کارهای اخیر</h2>
    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th>نام کسب و کار</th>
                <th>صاحب کسب و کار</th>
                <th>تاریخ ثبت</th>
                <th>وضعیت</th>
                <th>امتیاز</th>
                <th>عملیات</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $recent_businesses = $this->get_recent_businesses();
            foreach ($recent_businesses as $business) :
                $owner = get_user_by('id', $business->post_author);
                $status = get_post_status($business->ID);
                $rating = get_post_meta($business->ID, 'hc_average_rating', true);
            ?>
                <tr>
                    <td><?php echo $business->post_title; ?></td>
                    <td><?php echo $owner->display_name; ?></td>
                    <td><?php echo get_the_date('Y-m-d', $business->ID); ?></td>
                    <td><?php echo $status; ?></td>
                    <td><?php echo $rating ? number_format($rating, 2) : 'N/A'; ?></td>
                    <td>
                        <a href="<?php echo get_edit_post_link($business->ID); ?>" class="button">ویرایش</a>
                        <a href="<?php echo get_permalink($business->ID); ?>" class="button" target="_blank">مشاهده</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
