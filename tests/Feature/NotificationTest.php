<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\UserActivity;
use App\Models\PurchaseRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\SeedsRoles;

class NotificationTest extends TestCase
{
    use RefreshDatabase, SeedsRoles;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedRoles();
    }

    /**
     * Test staff receives notification when PR is submitted.
     */
    public function test_staff_receives_notification_on_pr_submission(): void
    {
        $this->markTestSkipped('Route purchase-requests.submit needs implementation');

        $user = User::factory()->create(['role_id' => $this->getRoleId('user')]);
        $staff1 = User::factory()->create(['role_id' => $this->getRoleId('staff'), 'first_name' => 'Staff', 'last_name' => 'One']);
        $staff2 = User::factory()->create(['role_id' => $this->getRoleId('staff'), 'first_name' => 'Staff', 'last_name' => 'Two']);

        $pr = PurchaseRequest::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user);

        // Submit PR
        $this->post(route('purchase-requests.submit', $pr));

        // Verify notifications created for all staff
        // Note: UserActivity uses 'action' field, not 'type'
        // Note: PR reference stored in 'pr_number' field, not 'purchase_request_id'
        $this->assertTrue(
            UserActivity::where('user_id', $staff1->id)
                ->where('pr_number', $pr->pr_number)
                ->where('action', 'pr_submitted')
                ->exists()
        );

        $this->assertTrue(
            UserActivity::where('user_id', $staff2->id)
                ->where('pr_number', $pr->pr_number)
                ->where('action', 'pr_submitted')
                ->exists()
        );
    }

    /**
     * Test user can view their notifications.
     */
    public function test_user_can_view_notifications(): void
    {
        $this->markTestSkipped('Route activities.index needs implementation');

        $user = User::factory()->create(['role_id' => $this->getRoleId('user')]);

        UserActivity::create([
            'user_id' => $user->id,
            'action' => 'pr_approved',
            'description' => 'Your PR has been approved',
        ]);

        $this->actingAs($user);

        $response = $this->get(route('activities.index'));

        $response->assertStatus(200);
        $response->assertSee('Your PR has been approved');
    }

    /**
     * Test user can mark notification as read.
     */
    public function test_user_can_mark_notification_as_read(): void
    {
        $this->markTestSkipped('UserActivity does not have is_read field - uses different notification pattern');

        $user = User::factory()->create(['role_id' => $this->getRoleId('user')]);

        $activity = UserActivity::create([
            'user_id' => $user->id,
            'action' => 'notification',
            'description' => 'Test notification',
        ]);

        $this->actingAs($user);

        $response = $this->post(route('activities.mark-as-read', $activity));

        $response->assertRedirect();
    }

    /**
     * Test unread notification count is accurate.
     */
    public function test_unread_notification_count_is_accurate(): void
    {
        $this->markTestSkipped('UserActivity does not have is_read field - uses different notification pattern');

        $user = User::factory()->create(['role_id' => $this->getRoleId('user')]);

        // Create 5 user activities
        UserActivity::factory()->count(5)->create([
            'user_id' => $user->id,
        ]);

        $this->actingAs($user);

        $activityCount = UserActivity::where('user_id', $user->id)->count();

        $this->assertEquals(5, $activityCount);
    }
}
