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

    public static function logDocumentUploaded($prNumber, $documentName)
    {
        return self::log(
            'uploaded_document',
            "Uploaded scanned document",
            $prNumber,
            $documentName
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

    public static function logPrRejected($prNumber, $rejectedBy)
    {
        return self::log(
            'rejected_pr',
            "Purchase Request rejected",
            $prNumber,
            null,
            ['rejected_by' => $rejectedBy]
        );
    }
}
