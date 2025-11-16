<?php

namespace Tests\Unit;

use App\Models\PurchaseRequest;
use App\Models\User;
use App\Models\Office;
use App\Models\Status;
use App\Models\PurchaseRequestItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\SeedsRoles;

class PurchaseRequestTest extends TestCase
{
    use RefreshDatabase, SeedsRoles;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedRoles();
        $this->seedStatuses();
        $this->seedUnits();
    }

    /**
     * Test purchase request can be created with valid data.
     */
    public function test_purchase_request_can_be_created(): void
    {
        $user = User::factory()->create(['role_id' => $this->getRoleId('user')]);
        $office = Office::factory()->create();
        $status = Status::where('context', 'procurement')->where('name', 'draft')->first();

        $pr = PurchaseRequest::create([
            'user_id' => $user->id,
            'office_id' => $office->id,
            'pr_number' => 'PR-2025-001',
            'entity_name' => 'DSWD',
            'fund_cluster' => '01',
            'responsibility_center_code' => 'RC-001',
            'date' => now(),
            'total' => 10000.00,
            'delivery_period' => '30 days',
            'delivery_address' => '123 Main St',
            'purpose' => 'Office supplies',
            'status_id' => $status->id,
        ]);

        $this->assertDatabaseHas('purchase_requests', [
            'pr_number' => 'PR-2025-001',
            'user_id' => $user->id,
            'total' => 10000.00,
        ]);
    }

    /**
     * Test purchase request belongs to user.
     */
    public function test_purchase_request_belongs_to_user(): void
    {
        $user = User::factory()->create();
        $pr = PurchaseRequest::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(User::class, $pr->user);
        $this->assertEquals($user->id, $pr->user->id);
    }

    /**
     * Test purchase request belongs to office.
     */
    public function test_purchase_request_belongs_to_office(): void
    {
        $office = Office::factory()->create();
        $pr = PurchaseRequest::factory()->create(['office_id' => $office->id]);

        $this->assertInstanceOf(Office::class, $pr->office);
        $this->assertEquals($office->id, $pr->office->id);
    }

    /**
     * Test purchase request has many items.
     */
    public function test_purchase_request_has_many_items(): void
    {
        $pr = PurchaseRequest::factory()->create();
        $items = PurchaseRequestItem::factory()->count(3)->create([
            'purchase_request_id' => $pr->id
        ]);

        $this->assertCount(3, $pr->fresh()->items);
        $this->assertInstanceOf(PurchaseRequestItem::class, $pr->fresh()->items->first());
    }

    /**
     * Test purchase request total is calculated correctly.
     */
    public function test_purchase_request_total_calculation(): void
    {
        $pr = PurchaseRequest::factory()->create(['total' => 0]);

        PurchaseRequestItem::factory()->create([
            'purchase_request_id' => $pr->id,
            'quantity' => 10,
            'unit_cost' => 100.00,
            'total_cost' => 1000.00
        ]);

        PurchaseRequestItem::factory()->create([
            'purchase_request_id' => $pr->id,
            'quantity' => 5,
            'unit_cost' => 200.00,
            'total_cost' => 1000.00
        ]);

        $expectedTotal = $pr->fresh()->items->sum('total_cost');

        $this->assertEquals(2000.00, $expectedTotal);
    }

    /**
     * Test purchase request status attribute casting.
     */
    public function test_purchase_request_casts_dates_correctly(): void
    {
        $pr = PurchaseRequest::factory()->create([
            'date' => '2025-01-15',
            'submitted_at' => '2025-01-16 10:30:00'
        ]);

        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $pr->date);
        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $pr->submitted_at);
    }

    /**
     * Test purchase request can be submitted.
     */
    public function test_purchase_request_status_can_be_updated(): void
    {
        $draftStatus = Status::where('context', 'procurement')->where('name', 'draft')->first();
        $pendingStatus = Status::where('context', 'procurement')->where('name', 'pending')->first();

        $pr = PurchaseRequest::factory()->create(['status_id' => $draftStatus->id]);

        $pr->update([
            'status_id' => $pendingStatus->id,
            'submitted_at' => now()
        ]);

        $this->assertEquals($pendingStatus->id, $pr->status_id);
        $this->assertNotNull($pr->submitted_at);
    }

    /**
     * Test purchase request requires mandatory fields.
     */
    public function test_purchase_request_requires_mandatory_fields(): void
    {
        $this->expectException(\Illuminate\Database\QueryException::class);

        PurchaseRequest::create([
            'pr_number' => 'PR-2025-001',
            // Missing required fields like user_id, office_id
        ]);
    }
}
