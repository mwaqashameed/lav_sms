<?php

namespace App\Http\Controllers;

use App\Imports\UserImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ImportController extends Controller
{
    public function index(Request $request) {
        switch ($request->type) {
            case 'user':
                $csvFile = Storage::path('sample/users.csv');
                $userImport = new UserImport($csvFile);
                $userImport->import();
                break;
            case 'class':
               //Create Class Import
                break;
            case 'user':
                //Create Class Import
                break;
            
            default:
                # code...
                break;
        }
    }
}
