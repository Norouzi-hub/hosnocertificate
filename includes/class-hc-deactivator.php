<?php
class HC_Deactivator {
    public static function deactivate() {
        // حذف نقش‌های اضافه شده
        remove_role('business_manager');
        remove_role('expert');
        remove_role('inspector');

        // غیرفعال کردن قابلیت‌های اضافه شده
        $roles = ['administrator'];
        foreach ($roles as $role_name) {
            $role = get_role($role_name);
            if ($role) {
                $role->remove_cap('manage_businesses');
                $role->remove_cap('edit_business');
                $role->remove_cap('delete_business');
                $role->remove_cap('publish_businesses');
            }
        }

        // حذف تنظیمات
        delete_option('hc_max_businesses_per_user');
        delete_option('hc_enable_business_verification');
        delete_option('hc_rating_weight_settings');
    }
}
