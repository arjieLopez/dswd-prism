<?php

namespace App\Http\Controllers;


use App\Models\UploadedDocument;
use App\Services\ActivityService;
use App\Services\ExportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;


// Use PhpSpreadsheet for XLSX export
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use App\Constants\PaginationConstants;
use App\Constants\ActivityConstants;

class UploadedDocumentController extends Controller
{
    // Export uploaded documents as CSV
    public function exportXLSX(Request $request)
    {
        $query = UploadedDocument::query()->where('user_id', auth()->id());
        $fileType = $request->input('file_type');
        if ($fileType && $fileType !== 'all') {
            $query->where('file_type', $fileType);
        }
        $documents = $query->get();

        // Prepare headers
        $headers = ['#', 'PR Number', 'File Name', 'File Type', 'File Size', 'Upload Date', 'Notes'];

        // Prepare rows
        $rows = [];
        $index = 1;
        foreach ($documents as $doc) {
            $rows[] = [
                $index++,
                $doc->pr_number,
                $doc->original_filename,
                strtoupper($doc->file_type),
                $doc->file_size_formatted,
                $doc->created_at->format('M d, Y'),
                $doc->notes,
            ];
        }

        // Use ExportService
        $exportService = new ExportService();
        $filename = $exportService->generateFilename('uploaded_documents', 'csv');

        return $exportService->exportToCSV($headers, $rows, $filename);
    }

    // Export uploaded documents as PDF
    public function exportPDF(Request $request)
    {
        $query = UploadedDocument::query()->where('user_id', auth()->id());
        $fileType = $request->input('file_type');
        if ($fileType && $fileType !== 'all') {
            $query->where('file_type', $fileType);
        }
        $documents = $query->get();

        $data = [
            'documents' => $documents
        ];

        // Use ExportService
        $exportService = new ExportService();
        $filename = $exportService->generateFilename('uploaded_documents', 'pdf');

        return $exportService->exportToPDF('exports.uploaded_documents_pdf', $data, $filename);
    }
    public function upload(Request $request)
    {
        $user = auth()->user();
        $recentActivities = $user->activities()
            ->orderBy('created_at', 'desc')
            ->limit(ActivityConstants::RECENT_ACTIVITY_LIMIT)
            ->get();

        $prNumber = $request->query('pr_number');

        $uploadedDocument = null;
        if ($prNumber) {
            $uploadedDocument = \App\Models\UploadedDocument::where('pr_number', $prNumber)->first();
        }

        return view('user.upload_pr', compact('recentActivities', 'prNumber', 'uploadedDocument'));
    }

    public function forPr($pr_number)
    {
        $doc = \App\Models\UploadedDocument::where('pr_number', $pr_number)->latest()->first();
        if ($doc) {
            return response()->json([
                'exists' => true,
                'download_url' => route('uploaded-documents.download', $doc->id),
            ]);
        }
        return response()->json(['exists' => false]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'pr_number' => 'required|string|max:255',
            'scanned_copy' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240', // 10MB max
            'notes' => 'nullable|string|max:1000',
        ]);

        $file = $request->file('scanned_copy');
        $filename = time() . '_' . $file->getClientOriginalName();

        // Store the file
        $file->storeAs('uploaded_documents', $filename, 'public');

        // Create the record
        $uploadedDocument = auth()->user()->uploadedDocuments()->create([
            'pr_number' => $request->pr_number,
            'file_path' => $filename,
            'original_filename' => $file->getClientOriginalName(),
            'file_type' => $file->getClientOriginalExtension(),
            'file_size' => $file->getSize(),
            'notes' => $request->notes,
        ]);

        ActivityService::logDocumentUploaded($request->pr_number, $filename);

        return redirect()->route('user.requests')
            ->with('success', 'Document uploaded successfully!');
    }

    public function download(UploadedDocument $uploadedDocument)
    {
        // Check if user owns this document
        if ($uploadedDocument->user_id !== auth()->id()) {
            abort(403, 'You can only download your own documents.');
        }

        $filePath = storage_path('app/public/uploaded_documents/' . $uploadedDocument->file_path);

        if (!file_exists($filePath)) {
            abort(404, 'File not found.');
        }

        return response()->download($filePath, $uploadedDocument->original_filename);
    }

    public function destroy(UploadedDocument $uploadedDocument)
    {
        // Check if user owns this document
        if ($uploadedDocument->user_id !== auth()->id()) {
            abort(403, 'You can only delete your own documents.');
        }

        // Delete the file
        Storage::disk('public')->delete('uploaded_documents/' . $uploadedDocument->file_path);

        // Delete the record
        $uploadedDocument->delete();

        return redirect()->route('user.requests')
            ->with('success', 'Document deleted successfully!');
    }
}
