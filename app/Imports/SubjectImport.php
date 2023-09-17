<?php

namespace App\Imports;

use App\User;
use App\Helpers\Qs;
use League\Csv\Reader;
use App\Models\MyClass;
use App\Models\Section;
use App\Models\Subject;
use Illuminate\Support\Str;
use App\Models\StudentRecord;

class SubjectImport extends ImportHandler implements ImportInterface
{
    protected $csvFile;
    public function __construct($csvFile)
    {
        $this->csvFile = $csvFile;
    }

    public function import()
    {
        try {
            $csv = Reader::createFromPath($this->csvFile, 'r');
            $csv->setHeaderOffset(0); // Assuming the first row contains headers

            $users = [];
            $classArr = MyClass::pluck('id', 'name')->toArray();
            $cnt = 0;
            foreach ($csv as $record) {
                $class = trim($record['class']);
                if (isset($classArr[$class])) {
                    $classID =  (int)$classArr[$class];
                    if (Subject::where('name', trim($record['title']))->where('my_class_id', $classID)->doesntExist()) {

                        $userData = [
                            'name' => trim($record['title']),
                            'slug' => $record['short name'],
                            'my_class_id' => $classID,
                            'teacher_id' => null
                        ];

                        $id = Subject::insertGetId($userData);
                        $cnt++;
                    } else {
                        $this->setMessage(trim($record['title']) . ' - Subject already exists');
                    }
                } else {
                    $this->setMessage($class . ' - Class did not found');
                }
            }

            $this->setMessage('Total ' . $cnt . ' records added successfully');
            $this->setMessage('Done');
            return true;
        } catch (\Exception $e) {
            $this->setMessage('<span style="color:red;">****** Error: ' . $e->getMessage() . '</span>');
            $this->setMessage('Total ' . $cnt . ' records added');
            $this->setMessage('Done');
            Qs::insertLog($e, 'critical');
            return false;
        }
    }
}
