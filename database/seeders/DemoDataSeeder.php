<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Supplier;
use App\Models\PurchaseRequest;
use App\Models\PurchaseRequestItem;
use App\Models\PODocument;
use App\Models\User;
use Illuminate\Support\Str;
use Carbon\Carbon;

class DemoDataSeeder extends Seeder
{
    public function run()
    {
        // Seed 10 Suppliers
        for ($i = 0; $i < 10; $i++) {
            Supplier::create([
                'supplier_name' => 'Supplier ' . ($i + 1),
                'tin' => 'TIN-' . str_pad($i + 1, 3, '0', STR_PAD_LEFT),
                'address' => ($i % 2 == 0 ? 'Main St' : 'Market Ave') . ', City',
                'contact_person' => 'Contact ' . ($i + 1),
                'contact_number' => '0917' . str_pad($i + 1, 7, '0', STR_PAD_LEFT),
                'email' => 'supplier' . ($i + 1) . '@example.com',
                'status' => $i % 2 == 0 ? 'active' : 'inactive',
            ]);
        }

        $supplierIds = Supplier::pluck('id')->toArray();
        $userId = User::first()->id ?? 1;

        // Helper: Get random date between July 1 and September 30, 2025
        function randomDate()
        {
            $start = Carbon::create(2025, 7, 1);
            $end = Carbon::create(2025, 9, 30);
            return Carbon::createFromTimestamp(rand($start->timestamp, $end->timestamp));
        }

        // Seed 10 PRs for each status
        $statuses = ['draft', 'pending', 'approved', 'po_generated', 'completed'];
        foreach ($statuses as $statusIdx => $status) {
            for ($i = 0; $i < 10; $i++) {
                $dateCreated = randomDate();
                $total = (10 + $i) * (100 + ($i * 10));
                $pr = PurchaseRequest::create([
                    'user_id' => $userId,
                    'pr_number' => 'PR-2025-' . $status . '-' . ($i + 1),
                    'entity_name' => 'DSWD',
                    'fund_cluster' => '01',
                    'office_section' => 'GSO',
                    'responsibility_center_code' => 'RC-' . ($statusIdx + 1) . '-' . ($i + 1),
                    'date' => $dateCreated,
                    'total' => $total,
                    'delivery_period' => '7 days',
                    'delivery_address' => 'DSWD Main Office',
                    'purpose' => 'Demo seeding',
                    'requested_by_name' => 'Requestor Name',
                    'requested_by_designation' => 'Staff',
                    'requested_by_signature' => null,
                    'approved_by_name' => 'Approver Name',
                    'approved_by_designation' => 'GSO',
                    'approved_by_signature' => null,
                    'status' => $status,
                    'remarks' => $status === 'rejected' ? 'Not approved' : null,
                    'notes' => null,
                    'po_number' => ($status === 'po_generated' || $status === 'completed') ? 'PO-2025-' . $status . '-' . ($i + 1) : null,
                    'po_generated_at' => ($status === 'po_generated' || $status === 'completed') ? $dateCreated->copy()->addDays(1) : null,
                    'po_generated_by' => ($status === 'po_generated' || $status === 'completed') ? $userId : null,
                    'completed_at' => ($status === 'completed') ? $dateCreated->copy()->addDays(2) : null,
                    'supplier_id' => $supplierIds[array_rand($supplierIds)],
                    'mode_of_procurement' => 'Direct',
                    'delivery_term' => 'FOB',
                    'payment_term' => 'COD',
                    'date_of_delivery' => $dateCreated->copy()->addDays(7),
                ]);

                // Seed PR Items
                for ($j = 0; $j < 3; $j++) {
                    PurchaseRequestItem::create([
                        'purchase_request_id' => $pr->id,
                        'unit' => 'piece',
                        'quantity' => 5 + $j,
                        'unit_cost' => 20 + ($j * 5),
                        'item_description' => 'Item ' . ($j + 1),
                        'total_cost' => (5 + $j) * (20 + ($j * 5)),
                    ]);
                }

                // Seed PO Document for po_generated and completed
                if (in_array($status, ['po_generated', 'completed'])) {
                    PODocument::create([
                        'user_id' => $userId,
                        'po_number' => 'PO-2025-' . $status . '-' . ($i + 1),
                        'file_name' => 'po_demo_' . $status . '_' . ($i + 1) . '.pdf',
                        'file_path' => 'po_documents/po_demo_' . $status . '_' . ($i + 1) . '.pdf',
                        'file_type' => 'pdf',
                        'file_size' => 123456,
                        'notes' => 'Demo PO document',
                    ]);
                }
            }
        }
    }
}
