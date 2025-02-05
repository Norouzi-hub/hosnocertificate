<?php
$comparison = new HC_Comparison();
$businesses = $comparison->get_comparable_businesses();
?>

<div class="hc-business-comparison-container">
    <h2>مقایسه کسب و کارها</h2>
    
    <div class="hc-comparison-selector">
        <select id="business-compare-1" class="business-compare-select">
            <option value="">انتخاب کسب و کار اول</option>
            <?php foreach ($businesses as $business): ?>
                <option value="<?php echo $business->ID; ?>">
                    <?php echo $business->post_title; ?>
                </option>
            <?php endforeach; ?>
        </select>

        <select id="business-compare-2" class="business-compare-select">
            <option value="">انتخاب کسب و کار دوم</option>
            <?php foreach ($businesses as $business): ?>
                <option value="<?php echo $business->ID; ?>">
                    <?php echo $business->post_title; ?>
                </option>
            <?php endforeach; ?>
        </select>

        <button id="compare-businesses-btn">مقایسه</button>
    </div>

    <div id="comparison-results" class="comparison-results">
        <!-- نتایج مقایسه اینجا نمایش داده می‌شود -->
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    $('#compare-businesses-btn').on('click', function() {
        var business1 = $('#business-compare-1').val();
        var business2 = $('#business-compare-2').val();

        if (!business1 || !business2) {
            alert('لطفاً دو کسب و کار را انتخاب کنید');
            return;
        }

        $.ajax({
            url: '<?php echo admin_url('admin-ajax.php'); ?>',
            type: 'POST',
            data: {
                action: 'hc_get_business_details',
                business_id: business1
            },
            success: function(response1) {
                $.ajax({
                    url: '<?php echo admin_url('admin-ajax.php'); ?>',
                    type: 'POST',
                    data: {
                        action: 'hc_get_business_details',
                        business_id: business2
                    },
                    success: function(response2) {
                        displayComparison(response1.data, response2.data);
                    }
                });
            }
        });
    });

    function displayComparison(business1, business2) {
        var html = `
            <div class="comparison-table">
                <div class="comparison-header">
                    <div class="comparison-cell">${business1.title}</div>
                    <div class="comparison-cell">${business2.title}</div>
                </div>
                <div class="comparison-row">
                    <div class="comparison-label">امتیاز کلی</div>
                    <div class="comparison-cell">${business1.overall_rating}</div>
                    <div class="comparison-cell">${business2.overall_rating}</div>
                </div>
                <div class="comparison-row">
                    <div class="comparison-label">گواهینامه</div>
                    <div class="comparison-cell">${business1.certificate}</div>
                    <div class="comparison-cell">${business2.certificate}</div>
                </div>
                <div class="comparison-row">
                    <div class="comparison-label">کیفیت محصول</div>
                    <div class="comparison-cell">${business1.ratings.product_quality}</div>
                    <div class="comparison-cell">${business2.ratings.product_quality}</div>
                </div>
                <div class="comparison-row">
                    <div class="comparison-label">کیفیت خدمات</div>
                    <div class="comparison-cell">${business1.ratings.service_quality}</div>
                    <div class="comparison-cell">${business2.ratings.service_quality}</div>
                </div>
                <div class="comparison-row">
                    <div class="comparison-label">مشتری مداری</div>
                    <div class="comparison-cell">${business1.ratings.customer_service}</div>
                    <div class="comparison-cell">${business2.ratings.customer_service}</div>
                </div>
                <div class="comparison-row">
                    <div class="comparison-label">رضایت قیمت</div>
                    <div class="comparison-cell">${business1.ratings.price_satisfaction}</div>
                    <div class="comparison-cell">${business2.ratings.price_satisfaction}</div>
                </div>
                <div class="comparison-row">
                    <div class="comparison-label">سهولت دسترسی</div>
                    <div class="comparison-cell">${business1.ratings.accessibility}</div>
                    <div class="comparison-cell">${business2.ratings.accessibility}</div>
                </div>
            </div>
        `;
        $('#comparison-results').html(html);
    }
});
</script>
