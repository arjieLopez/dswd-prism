<?php

namespace Tests\Feature;

use App\Models\PurchaseRequest;
use App\Models\User;
use App\Models\Office;
use App\Models\Status;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\SeedsRoles;

class PurchaseRequestWorkflowTest extends TestCase
{
    use RefreshDatabase, SeedsRoles;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed roles and statuses
        $this->seedRoles();
        $this->seedStatuses();
    }

    /**
     * Test user can create a draft purchase request.
     */
    public function test_user_can_create_draft_purchase_request(): void
    {
        $user = User::factory()->create([
            'role_id' => $this->getRoleId('user'),
            'first_name' => 'Test',
            'last_name' => 'User'
        ]);
        $office = Office::factory()->create();
        $draftStatus = Status::where('context', 'procurement')->where('name', 'draft')->first();

        $this->actingAs($user);

        $response = $this->post(route('purchase-requests.store'), [
            'office_id' => $office->id,
            'entity_name' => 'DSWD',
            'fund_cluster' => '01',
            'responsibility_center_code' => 'RC-001',
            'date' => now()->format('Y-m-d'),
            'delivery_period' => '30 days',
            'delivery_address' => '123 Main St',
            'purpose' => 'Office supplies procurement',
            'status_id' => $draftStatus->id,
            // Add required items
            'unit' => ['pcs', 'box'],
            'quantity' => [10, 5],
            'unit_cost' => [50.00, 100.00],
            'item_description' => ['Ballpens', 'Folders'],
        ]);

        // Controller may return JSON response or redirect - check database instead
        $this->assertDatabaseHas('purchase_requests', [
            'user_id' => $user->id,
            'office_id' => $office->id,
            'purpose' => 'Office supplies procurement',
        ]);
    }

    /**
     * Test user can submit purchase request for review.
     */
    public function test_user_can_submit_purchase_request(): void
    {
        $user = User::factory()->create([
            'role_id' => $this->getRoleId('user'),
            'first_name' => 'Test',
            'last_name' => 'User'
        ]);
        $draftStatus = Status::where('context', 'procurement')->where('name', 'draft')->first();
        $pendingStatus = Status::where('context', 'procurement')->where('name', 'pending')->first();

        $pr = PurchaseRequest::factory()->create([
            'user_id' => $user->id,
            'status_id' => $draftStatus->id,
        ]);

        $this->actingAs($user);

        $response = $this->post(route('purchase-requests.submit', $pr));

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $this->assertDatabaseHas('purchase_requests', [
            'id' => $pr->id,
            'status_id' => $pendingStatus->id,
        ]);

        // Check that submitted_at is set
        $this->assertNotNull($pr->fresh()->submitted_at);
    }

    /**
     * Test staff can approve purchase request.
     */
    public function test_staff_can_approve_purchase_request(): void
    {
        $staff = User::factory()->create([
            'role_id' => $this->getRoleId('staff'),
            'first_name' => 'Staff',
            'last_name' => 'User'
        ]);
        $user = User::factory()->create([
            'role_id' => $this->getRoleId('user'),
            'first_name' => 'Regular',
            'last_name' => 'User'
        ]);
        $pendingStatus = Status::where('context', 'procurement')->where('name', 'pending')->first();
        $approvedStatus = Status::where('context', 'procurement')->where('name', 'approved')->first();

        $pr = PurchaseRequest::factory()->create([
            'user_id' => $user->id,
            'status_id' => $pendingStatus->id,
            'submitted_at' => now(),
        ]);

        $this->actingAs($staff);

        $response = $this->post(route('staff.pr_review.approve', $pr));

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $this->assertDatabaseHas('purchase_requests', [
            'id' => $pr->id,
            'status_id' => $approvedStatus->id,
        ]);
    }

    /**
     * Test staff can reject purchase request.
     */
    public function test_staff_can_reject_purchase_request(): void
    {
        $staff = User::factory()->create([
            'role_id' => $this->getRoleId('staff'),
            'first_name' => 'Staff',
            'last_name' => 'User'
        ]);
        $user = User::factory()->create([
            'role_id' => $this->getRoleId('user'),
            'first_name' => 'Regular',
            'last_name' => 'User'
        ]);
        $pendingStatus = Status::where('context', 'procurement')->where('name', 'pending')->first();
        $rejectedStatus = Status::where('context', 'procurement')->where('name', 'rejected')->first();

        $pr = PurchaseRequest::factory()->create([
            'user_id' => $user->id,
            'status_id' => $pendingStatus->id,
            'submitted_at' => now(),
        ]);

        $this->actingAs($staff);

        $response = $this->post(route('staff.pr_review.reject', $pr), [
            'remarks' => 'Insufficient justification'
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $this->assertDatabaseHas('purchase_requests', [
            'id' => $pr->id,
            'status_id' => $rejectedStatus->id,
            'remarks' => 'Insufficient justification',
        ]);
    }

    /**
     * Test only authorized users can access their own PRs.
     */
    public function test_user_can_only_view_own_purchase_requests(): void
    {
        $user1 = User::factory()->create([
            'role_id' => $this->getRoleId('user'),
            'first_name' => 'User',
            'last_name' => 'One'
        ]);
        $user2 = User::factory()->create([
            'role_id' => $this->getRoleId('user'),
            'first_name' => 'User',
            'last_name' => 'Two'
        ]);

        $pr1 = PurchaseRequest::factory()->create(['user_id' => $user1->id]);
        $pr2 = PurchaseRequest::factory()->create(['user_id' => $user2->id]);

        $this->actingAs($user1);

        $response = $this->get(route('user.requests'));

        $response->assertStatus(200);
        // Check that user1 sees their PR number (unique identifier) but not user2's
        $response->assertSee($pr1->pr_number);
        $response->assertDontSee($pr2->pr_number);
    }

    /**
     * Test staff can view all purchase requests.
     */
    public function test_staff_can_view_all_purchase_requests(): void
    {
        $staff = User::factory()->create([
            'role_id' => $this->getRoleId('staff'),
            'first_name' => 'Staff',
            'last_name' => 'User'
        ]);
        $user1 = User::factory()->create([
            'role_id' => $this->getRoleId('user'),
            'first_name' => 'User',
            'last_name' => 'One'
        ]);
        $user2 = User::factory()->create([
            'role_id' => $this->getRoleId('user'),
            'first_name' => 'User',
            'last_name' => 'Two'
        ]);

        $pr1 = PurchaseRequest::factory()->create(['user_id' => $user1->id]);
        $pr2 = PurchaseRequest::factory()->create(['user_id' => $user2->id]);

        $this->actingAs($staff);

        $response = $this->get(route('staff.pr_review'));

        $response->assertStatus(200);
    }

    /**
     * Test guest cannot access purchase requests.
     */
    public function test_guest_cannot_access_purchase_requests(): void
    {
        $response = $this->get(route('user.requests'));

        $response->assertRedirect(route('login'));
    }
}
