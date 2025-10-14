<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserActivity extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'action',
        'description',
        'pr_number',
        'document_name',
        'details'
    ];

    protected $casts = [
        'details' => 'array'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getActionIconAttribute()
    {
        return match ($this->action) {
            // User Management
            'user_created' => 'mdi:account-plus',
            'user_updated' => 'mdi:account-edit',
            'user_deleted' => 'mdi:account-remove',
            'user_status_changed' => 'mdi:account-cog',
            'user_role_changed' => 'mdi:account-switch',

            // Authentication
            'user_login' => 'mdi:login',
            'user_logout' => 'mdi:logout',
            'login_failed' => 'mdi:login-variant',

            // Purchase Requests
            'created_pr' => 'mdi:file-plus',
            'updated_pr' => 'mdi:file-edit',
            'submitted_pr' => 'mdi:file-send',
            'approved_pr' => 'mdi:file-check',
            'rejected_pr' => 'mdi:file-remove',
            'deleted_pr' => 'mdi:file-delete',
            'pr_submitted_notification' => 'mdi:bell-alert',

            // Purchase Orders
            'generated_po' => 'mdi:file-document-plus',
            'uploaded_po_document' => 'mdi:upload',
            'deleted_po_document' => 'mdi:delete',

            // Documents
            'uploaded_document' => 'mdi:upload',
            'deleted_document' => 'mdi:delete',
            'downloaded_document' => 'mdi:download',

            // Suppliers
            'created_supplier' => 'mdi:store-plus',
            'updated_supplier' => 'mdi:store-edit',
            'deleted_supplier' => 'mdi:store-remove',
            'supplier_status_changed' => 'mdi:store-cog',

            // System
            'generated_report' => 'mdi:chart-line',
            'exported_data' => 'mdi:export',
            'system_error' => 'mdi:alert-circle',

            default => 'mdi:information'
        };
    }

    public function getActionColorAttribute()
    {
        return match ($this->action) {
            // User Management
            'user_created' => 'text-green-600 bg-green-100',
            'user_updated' => 'text-blue-600 bg-blue-100',
            'user_deleted' => 'text-red-600 bg-red-100',
            'user_status_changed' => 'text-orange-600 bg-orange-100',
            'user_role_changed' => 'text-purple-600 bg-purple-100',

            // Authentication
            'user_login' => 'text-green-600 bg-green-100',
            'user_logout' => 'text-gray-600 bg-gray-100',
            'login_failed' => 'text-red-600 bg-red-100',

            // Purchase Requests
            'created_pr' => 'text-green-600 bg-green-100',
            'updated_pr' => 'text-blue-600 bg-blue-100',
            'submitted_pr' => 'text-yellow-600 bg-yellow-100',
            'approved_pr' => 'text-green-600 bg-green-100',
            'rejected_pr' => 'text-red-600 bg-red-100',
            'deleted_pr' => 'text-red-600 bg-red-100',
            'pr_submitted_notification' => 'text-yellow-600 bg-yellow-100',

            // Purchase Orders
            'generated_po' => 'text-purple-600 bg-purple-100',
            'uploaded_po_document' => 'text-blue-600 bg-blue-100',
            'deleted_po_document' => 'text-red-600 bg-red-100',

            // Documents
            'uploaded_document' => 'text-blue-600 bg-blue-100',
            'deleted_document' => 'text-red-600 bg-red-100',
            'downloaded_document' => 'text-green-600 bg-green-100',

            // Suppliers
            'created_supplier' => 'text-green-600 bg-green-100',
            'updated_supplier' => 'text-blue-600 bg-blue-100',
            'deleted_supplier' => 'text-red-600 bg-red-100',
            'supplier_status_changed' => 'text-orange-600 bg-orange-100',

            // System
            'generated_report' => 'text-purple-600 bg-purple-100',
            'exported_data' => 'text-green-600 bg-green-100',
            'system_error' => 'text-red-600 bg-red-100',

            default => 'text-gray-600 bg-gray-100'
        };
    }
}
