<?php
class BusinessManagerTest extends WP_UnitTestCase {
    public function setUp(): void {
        parent::setUp();
        $this->business_manager = new HC_Business_Manager();
    }

    public function test_create_business() {
        $user_id = $this->factory->user->create();
        wp_set_current_user($user_id);

        $business_data = [
            'title' => 'Test Business',
            'description' => 'A test business description',
            'category' => 'services'
        ];

        $business_id = $this->business_manager->create_business($business_data);

        $this->assertNotFalse($business_id);
        $this->assertEquals('Test Business', get_the_title($business_id));
    }

    public function test_business_validation() {
        $invalid_data = [
            'title' => '',
            'description' => ''
        ];

        $result = $this->business_manager->validate_business($invalid_data);
        $this->assertFalse($result);
    }
}
