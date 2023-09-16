<?php

namespace App\Http\Controllers;

use App\Imports\UserImport;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Imports\SubjectImport;
use Illuminate\Support\Facades\Storage;

class ImportController extends Controller
{
    public function index(Request $request)
    {
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
