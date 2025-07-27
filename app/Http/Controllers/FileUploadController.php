<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessCsvUpload;
use App\Models\FileUpload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class FileUploadController extends Controller
{
    public function index()
    {
        return view('welcome');
    }

    public function upload(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|mimes:csv,txt',
        ]);

        try {
            $file = $request->file('csv_file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('uploads', $fileName);

            Log::info('File stored successfully: ' . $fileName);

            $fileUpload = FileUpload::create([
                'file_name' => $fileName,
                'status' => 'pending',
            ]);
            Log::info('FileUpload record created: ' . $fileUpload->id);

            ProcessCsvUpload::dispatch($fileUpload);
            Log::info('ProcessCsvUpload job dispatched for FileUpload ID: ' . $fileUpload->id);

            return redirect('/')->with('success', 'File uploaded successfully.');
        } catch (\Exception $e) {
            Log::error('File upload failed: ' . $e->getMessage());
            return redirect('/')->with('error', 'File upload failed: ' . $e->getMessage());
        }
    }

    public function uploads()
    {
        $uploads = FileUpload::latest()->get();
        return response()->json($uploads);
    }
}
