<?php

namespace App\Http\Controllers;

use App\Helpers\Qs;
use App\Imports\UserImport;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Imports\SubjectImport;
use Illuminate\Support\Facades\Storage;

class ImportController extends Controller
{
    public function index(Request $request)
    {
        $data = [];
        return view('pages.support_team.import.index');
    }

    public function store(Request $request)
    {
        // $test = $request->file('csv_file');

        $request->validate([
            'csv_file' => 'required|mimes:csv',
        ]);

        // $csvFile = $request->file('csv_file');
        // $storagePath = 'uploads/import';
        // $fileName = time().'.'.$request->csv_file->extension();  
        // $csvFileName = $csvFile->store(storage_path('app/public/uploads/import/'.$fileName));
        
        $fileName = time().'.'.$request->csv_file->extension();  
        $request->csv_file->move(storage_path('app/public/uploads/import/'), $fileName);
        
        $msg = $this->processImportFile($fileName,$request->type);

        return redirect()->back()->with('success', $msg);
    }

    private function processImportFile($uploadedFile,$type)
    {
        $classTitle = Str::title($type);
        $className = $classTitle . 'Import';
        $className = 'App\\Imports\\' . $className;
        if (class_exists($className)) {
            $csvFile = Storage::path('uploads/import/' . $uploadedFile);
            $importObj = new $className($csvFile);
            $importObj->import();
            return $importObj->getMessage();
        } else {
            Qs::insertError($className . " - Class does not exist.");
            abort(403, 'invalid request');
        }
    }

    private function directRun($request) {
        $classTitle = Str::title($request->type);
        $className = $classTitle . 'Import';
        $className = 'App\\Imports\\' . $className;
        if (class_exists($className)) {
            $csvFile = Storage::path('sample/' . $request->type . '.csv');
            $importObj = new $className($csvFile);
            $importObj->import();
            echo $importObj->getMessage();
        } else {
            echo "Class does not exist.";
        }
    }
}
