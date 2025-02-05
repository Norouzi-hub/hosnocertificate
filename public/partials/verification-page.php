<div class="wrap">
    <h1>درخواست‌های اعتبارسنجی</h1>
    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th>نام کسب و کار</th>
                <th>سطح</th>
                <th>مبلغ</th>
                <th>وضعیت</th>
                <th>عملیات</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($verified_businesses as $business): 
                $verification = get_post_meta($business->ID, 'hc_verification_request', true);
            ?>
                <tr>
                    <td><?php echo $business->post_title; ?></td>
                    <td><?php echo $verification['level']; ?></td>
                    <td><?php echo number_format($verification['price']); ?> تومان</td>
                    <td><?php echo $verification['status']; ?></td>
                    <td>
                        <button class="button approve-verification" 
                                data-business-id="<?php echo $business->ID; ?>">
                            تأیید
                        </button>
                        <button class="button reject-verification" 
                                data-business-id="<?php echo $business->ID; ?>">
                            رد
                        </button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script>
jQuery(document).ready(function($) {
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
                nonce: '<?php echo wp_create_nonce('hc_handle_verification_nonce'); ?>'
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
});
</script>
