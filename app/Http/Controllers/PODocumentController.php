<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PODocument;
use App\Services\ActivityService;
use Illuminate\Support\Facades\Storage;

class PODocumentController extends Controller
{
    public function upload()
    {
        return view('staff.upload_po_document');
    }

    public function store(Request $request)
    {
        $request->validate([
            'po_number' => 'required|string|max:255',
            'po_document' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240', // 10MB max
            'notes' => 'nullable|string|max:1000',
        ]);

        try {
            $file = $request->file('po_document');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->store('po_documents', 'public');
            $fileSize = $this->formatFileSize($file->getSize());
            $fileType = strtoupper($file->getClientOriginalExtension());

            PODocument::create([
                'user_id' => auth()->id(),
                'po_number' => $request->po_number,
                'file_name' => $fileName,
                'file_path' => $filePath,
                'file_type' => $fileType,
                'file_size' => $fileSize,
                'notes' => $request->notes,
            ]);

            // Add this line:
            ActivityService::logPoDocumentUploaded($request->po_number, $fileName);

            return redirect()->route('staff.po_generation')->with('success', 'PO Document uploaded successfully!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error uploading document: ' . $e->getMessage()]);
        }
    }

    public function download(PODocument $poDocument)
    {
        try {
            if (!Storage::disk('public')->exists($poDocument->file_path)) {
                abort(404, 'File not found');
            }

            return Storage::disk('public')->download($poDocument->file_path, $poDocument->file_name);
        } catch (\Exception $e) {
            abort(404, 'Error downloading file: ' . $e->getMessage());
        }
    }

    public function destroy(PODocument $poDocument)
    {
        try {
            // Store document info before deletion
            $poNumber = $poDocument->po_number;
            $fileName = $poDocument->file_name;

            // Delete file from storage
            if (Storage::disk('public')->exists($poDocument->file_path)) {
                Storage::disk('public')->delete($poDocument->file_path);
            }

            // Delete record from database
            $poDocument->delete();

            // Add this line:
            ActivityService::logPoDocumentDeleted($poNumber, $fileName);

            return response()->json(['success' => true, 'message' => 'PO Document deleted successfully!']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error deleting document: ' . $e->getMessage()], 500);
        }
    }

    private function formatFileSize($bytes)
    {
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' bytes';
        }
    }
}
