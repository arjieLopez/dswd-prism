<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add office_id column
        Schema::table('purchase_requests', function (Blueprint $table) {
            $table->foreignId('office_id')->nullable()->after('user_id')->constrained()->onDelete('cascade');
        });

        // Migrate existing data from office_section to office_id
        DB::transaction(function () {
            $purchaseRequests = DB::table('purchase_requests')->get();

            /** @var object{id: int, office_section: string} $pr */
            foreach ($purchaseRequests as $pr) {
                // Try to find matching office by name
                /** @var object{id: int, name: string}|null $office */
                $office = DB::table('offices')
                    ->where('name', $pr->office_section)
                    ->first();

                if ($office) {
                    DB::table('purchase_requests')
                        ->where('id', $pr->id)
                        ->update(['office_id' => $office->id]);
                } else {
                    // If no matching office found, create a new one
                    $officeId = DB::table('offices')->insertGetId([
                        'name' => $pr->office_section,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);

                    DB::table('purchase_requests')
                        ->where('id', $pr->id)
                        ->update(['office_id' => $officeId]);
                }
            }
        });

        // Make office_id non-nullable after migration
        Schema::table('purchase_requests', function (Blueprint $table) {
            $table->foreignId('office_id')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_requests', function (Blueprint $table) {
            $table->dropForeign(['office_id']);
            $table->dropColumn('office_id');
        });
    }
};
