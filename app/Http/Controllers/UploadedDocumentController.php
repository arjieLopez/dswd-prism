<?php

namespace App\Http\Controllers;


use App\Models\UploadedDocument;
use App\Services\ActivityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;


// Use PhpSpreadsheet for XLSX export
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class UploadedDocumentController extends Controller
{
    // Export uploaded documents as XLSX using PhpSpreadsheet
    public function exportXLSX(Request $request)
    {
        $query = UploadedDocument::query()->where('user_id', auth()->id());
        $fileType = $request->input('file_type');
        if ($fileType && $fileType !== 'all') {
            $query->where('file_type', $fileType);
        }
        $documents = $query->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set header
        $headers = ['PR Number', 'File Name', 'File Type', 'File Size', 'Upload Date', 'Notes'];
        $sheet->fromArray($headers, NULL, 'A1');

        // Fill data
        $row = 2;
        foreach ($documents as $doc) {
            $sheet->fromArray([
                $doc->pr_number,
                $doc->original_filename,
                strtoupper($doc->file_type),
                $doc->file_size_formatted,
                $doc->created_at->format('M d, Y H:i'),
                $doc->notes,
            ], NULL, 'A' . $row);
            $row++;
        }

        // Output to browser
        $filename = 'uploaded_documents.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
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

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('exports.uploaded_documents_pdf', $data);
        return $pdf->download('uploaded_documents.pdf');
    }
    public function upload()
    {
        return view('user.upload_pr');
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
