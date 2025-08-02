<?php

namespace App\Services;

use App\Models\UserActivity;
use Illuminate\Support\Facades\Auth;

class ActivityService
{
    public static function log($action, $description, $prNumber = null, $documentName = null, $details = [])
    {
        return UserActivity::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'description' => $description,
            'pr_number' => $prNumber,
            'document_name' => $documentName,
            'details' => $details
        ]);
    }

    // User Management Activities
    public static function logUserCreated($userId, $userName, $role)
    {
        return self::log(
            'user_created',
            "Created new user: {$userName}",
            null,
            null,
            ['user_id' => $userId, 'user_name' => $userName, 'role' => $role]
        );
    }

    public static function logUserUpdated($userId, $userName, $changes)
    {
        return self::log(
            'user_updated',
            "Updated user: {$userName}",
            null,
            null,
            ['user_id' => $userId, 'user_name' => $userName, 'changes' => $changes]
        );
    }

    public static function logUserDeleted($userId, $userName)
    {
        return self::log(
            'user_deleted',
            "Deleted user: {$userName}",
            null,
            null,
            ['user_id' => $userId, 'user_name' => $userName]
        );
    }

    public static function logUserStatusChanged($userId, $userName, $oldStatus, $newStatus)
    {
        return self::log(
            'user_status_changed',
            "Changed status for user: {$userName}",
            null,
            null,
            ['user_id' => $userId, 'user_name' => $userName, 'old_status' => $oldStatus, 'new_status' => $newStatus]
        );
    }

    public static function logUserRoleChanged($userId, $userName, $oldRole, $newRole)
    {
        return self::log(
            'user_role_changed',
            "Changed role for user: {$userName}",
            null,
            null,
            ['user_id' => $userId, 'user_name' => $userName, 'old_role' => $oldRole, 'new_role' => $newRole]
        );
    }

    // Authentication Activities
    public static function logUserLogin($userId, $userName)
    {
        return self::log(
            'user_login',
            "User logged in: {$userName}",
            null,
            null,
            ['user_id' => $userId, 'user_name' => $userName]
        );
    }

    public static function logUserLogout($userId, $userName)
    {
        return self::log(
            'user_logout',
            "User logged out: {$userName}",
            null,
            null,
            ['user_id' => $userId, 'user_name' => $userName]
        );
    }

    public static function logLoginFailed($email)
    {
        return self::log(
            'login_failed',
            "Failed login attempt for: {$email}",
            null,
            null,
            ['email' => $email]
        );
    }

    // Purchase Request Activities
    public static function logPrCreated($prNumber, $entityName)
    {
        return self::log(
            'created_pr',
            "Created new Purchase Request",
            $prNumber,
            null,
            ['entity_name' => $entityName]
        );
    }

    public static function logPrUpdated($prNumber, $entityName)
    {
        return self::log(
            'updated_pr',
            "Updated Purchase Request",
            $prNumber,
            null,
            ['entity_name' => $entityName]
        );
    }

    public static function logPrApproved($prNumber, $approvedBy)
    {
        return self::log(
            'approved_pr',
            "Purchase Request approved",
            $prNumber,
            null,
            ['approved_by' => $approvedBy]
        );
    }

    public static function logPrRejected($prNumber, $rejectedBy, $reason = null)
    {
        return self::log(
            'rejected_pr',
            "Purchase Request rejected",
            $prNumber,
            null,
            ['rejected_by' => $rejectedBy, 'reason' => $reason]
        );
    }

    public static function logPrDeleted($prNumber, $entityName)
    {
        return self::log(
            'deleted_pr',
            "Deleted Purchase Request",
            $prNumber,
            null,
            ['entity_name' => $entityName]
        );
    }

    // Purchase Order Activities
    public static function logPoGenerated($prNumber, $poNumber)
    {
        return self::log(
            'generated_po',
            "Generated Purchase Order",
            $prNumber,
            null,
            ['po_number' => $poNumber]
        );
    }

    public static function logPoDocumentUploaded($poNumber, $documentName)
    {
        return self::log(
            'uploaded_po_document',
            "Uploaded PO Document",
            null,
            $documentName,
            ['po_number' => $poNumber]
        );
    }

    public static function logPoDocumentDeleted($poNumber, $documentName)
    {
        return self::log(
            'deleted_po_document',
            "Deleted PO Document",
            null,
            $documentName,
            ['po_number' => $poNumber]
        );
    }

    // Document Activities
    public static function logDocumentUploaded($prNumber, $documentName)
    {
        return self::log(
            'uploaded_document',
            "Uploaded scanned document",
            $prNumber,
            $documentName
        );
    }

    public static function logDocumentDeleted($prNumber, $documentName)
    {
        return self::log(
            'deleted_document',
            "Deleted document",
            $prNumber,
            $documentName
        );
    }

    public static function logDocumentDownloaded($prNumber, $documentName)
    {
        return self::log(
            'downloaded_document',
            "Downloaded document",
            $prNumber,
            $documentName
        );
    }

    // Supplier Activities
    public static function logSupplierCreated($supplierName)
    {
        return self::log(
            'created_supplier',
            "Created new supplier: {$supplierName}",
            null,
            null,
            ['supplier_name' => $supplierName]
        );
    }

    public static function logSupplierUpdated($supplierName, $changes)
    {
        return self::log(
            'updated_supplier',
            "Updated supplier: {$supplierName}",
            null,
            null,
            ['supplier_name' => $supplierName, 'changes' => $changes]
        );
    }

    public static function logSupplierDeleted($supplierName)
    {
        return self::log(
            'deleted_supplier',
            "Deleted supplier: {$supplierName}",
            null,
            null,
            ['supplier_name' => $supplierName]
        );
    }

    public static function logSupplierStatusChanged($supplierName, $oldStatus, $newStatus)
    {
        return self::log(
            'supplier_status_changed',
            "Changed status for supplier: {$supplierName}",
            null,
            null,
            ['supplier_name' => $supplierName, 'old_status' => $oldStatus, 'new_status' => $newStatus]
        );
    }

    // System Activities
    public static function logReportGenerated($reportType, $filters = [])
    {
        return self::log(
            'generated_report',
            "Generated {$reportType} report",
            null,
            null,
            ['report_type' => $reportType, 'filters' => $filters]
        );
    }

    public static function logDataExported($exportType, $recordCount)
    {
        return self::log(
            'exported_data',
            "Exported {$exportType} data",
            null,
            null,
            ['export_type' => $exportType, 'record_count' => $recordCount]
        );
    }

    public static function logSystemError($error, $context = [])
    {
        return self::log(
            'system_error',
            "System error occurred",
            null,
            null,
            ['error' => $error, 'context' => $context]
        );
    }
}
