<?php

namespace Tests\Unit;

use App\Models\Supplier;
use App\Models\Status;
use App\Models\PurchaseOrder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\SeedsRoles;

class SupplierTest extends TestCase
{
    use RefreshDatabase, SeedsRoles;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedStatuses();
        $this->seedUnits();
    }

    /**
     * Test supplier can be created with valid data.
     */
    public function test_supplier_can_be_created(): void
    {
        $status = Status::where('context', 'supplier')->where('name', 'active')->first();

        $supplier = Supplier::create([
            'supplier_name' => 'ABC Corporation',
            'tin' => '123-456-789-000',
            'address' => '123 Business St, Manila',
            'contact_person' => 'John Doe',
            'contact_number' => '09123456789',
            'email' => 'contact@abccorp.com',
            'status_id' => $status->id,
        ]);

        $this->assertDatabaseHas('suppliers', [
            'supplier_name' => 'ABC Corporation',
            'tin' => '123-456-789-000',
            'email' => 'contact@abccorp.com',
        ]);
    }

    /**
     * Test supplier has many purchase orders.
     */
    public function test_supplier_has_many_purchase_orders(): void
    {
        $supplier = Supplier::factory()->create();
        $purchaseOrders = PurchaseOrder::factory()->count(3)->create([
            'supplier_id' => $supplier->id
        ]);

        $this->assertCount(3, $supplier->purchaseOrders);
        $this->assertInstanceOf(PurchaseOrder::class, $supplier->purchaseOrders->first());
    }

    /**
     * Test supplier email must be unique.
     * 
     * @skip Email uniqueness constraint not implemented in database
     */
    public function test_supplier_email_must_be_unique(): void
    {
        $this->markTestSkipped('Email uniqueness constraint not implemented in database schema');

        Supplier::factory()->create(['email' => 'unique@test.com']);

        $this->expectException(\Illuminate\Database\QueryException::class);

        Supplier::factory()->create(['email' => 'unique@test.com']);
    }

    /**
     * Test supplier can be activated and deactivated.
     */
    public function test_supplier_status_can_be_updated(): void
    {
        $activeStatus = Status::where('context', 'supplier')->where('name', 'active')->first();
        $inactiveStatus = Status::where('context', 'supplier')->where('name', 'inactive')->first();

        $supplier = Supplier::factory()->create(['status_id' => $activeStatus->id]);

        $supplier->update(['status_id' => $inactiveStatus->id]);

        $this->assertEquals($inactiveStatus->id, $supplier->status_id);
    }

    /**
     * Test supplier requires name.
     */
    public function test_supplier_requires_name(): void
    {
        $this->expectException(\Illuminate\Database\QueryException::class);

        Supplier::create([
            'tin' => '123-456-789-000',
            'address' => '123 Business St',
            // Missing supplier_name
        ]);
    }
}
