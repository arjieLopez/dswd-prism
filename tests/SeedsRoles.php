<?php

namespace Tests;

use App\Models\Role;
use App\Models\Status;
use App\Models\Unit;

trait SeedsRoles
{
    /**
     * Seed roles in the database for testing
     */
    protected function seedRoles(): void
    {
        // Create roles if they don't exist
        Role::firstOrCreate(['name' => 'admin']);
        Role::firstOrCreate(['name' => 'staff']);
        Role::firstOrCreate(['name' => 'user']);
    }

    /**
     * Seed common statuses in the database for testing
     */
    protected function seedStatuses(): void
    {
        // Procurement context statuses (used by Purchase Requests and Purchase Orders - matches actual StatusSeeder)
        Status::firstOrCreate(
            ['context' => 'procurement', 'name' => 'draft'],
            ['display_name' => 'Draft', 'color' => 'bg-gray-100 text-gray-800']
        );
        Status::firstOrCreate(
            ['context' => 'procurement', 'name' => 'pending'],
            ['display_name' => 'Pending Review', 'color' => 'bg-yellow-100 text-yellow-800']
        );
        Status::firstOrCreate(
            ['context' => 'procurement', 'name' => 'approved'],
            ['display_name' => 'Approved', 'color' => 'bg-green-100 text-green-800']
        );
        Status::firstOrCreate(
            ['context' => 'procurement', 'name' => 'rejected'],
            ['display_name' => 'Rejected', 'color' => 'bg-red-100 text-red-800']
        );
        Status::firstOrCreate(
            ['context' => 'procurement', 'name' => 'po_generated'],
            ['display_name' => 'PO Generated', 'color' => 'bg-purple-100 text-purple-800']
        );
        Status::firstOrCreate(
            ['context' => 'procurement', 'name' => 'completed'],
            ['display_name' => 'Completed', 'color' => 'bg-indigo-100 text-indigo-800']
        );

        // Supplier statuses
        Status::firstOrCreate(
            ['context' => 'supplier', 'name' => 'active'],
            ['display_name' => 'Active', 'color' => 'bg-green-100 text-green-800']
        );
        Status::firstOrCreate(
            ['context' => 'supplier', 'name' => 'inactive'],
            ['display_name' => 'Inactive', 'color' => 'bg-gray-100 text-gray-800']
        );
    }

    /**
     * Seed common units in the database for testing
     */
    protected function seedUnits(): void
    {
        Unit::firstOrCreate(['name' => 'piece'], ['abbreviation' => 'pc']);
        Unit::firstOrCreate(['name' => 'box'], ['abbreviation' => 'box']);
        Unit::firstOrCreate(['name' => 'ream'], ['abbreviation' => 'rm']);
        Unit::firstOrCreate(['name' => 'set'], ['abbreviation' => 'set']);
        Unit::firstOrCreate(['name' => 'pack'], ['abbreviation' => 'pack']);
        Unit::firstOrCreate(['name' => 'bottle'], ['abbreviation' => 'btl']);
    }

    /**
     * Get role ID by name
     */
    protected function getRoleId(string $roleName): int
    {
        return Role::where('name', $roleName)->firstOrFail()->id;
    }
}
