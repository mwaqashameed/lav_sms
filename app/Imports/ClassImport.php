<?php

namespace App\Imports;

use App\User;
use League\Csv\Reader;
use App\Models\MyClass;
use App\Models\Section;
use App\Models\Subject;
use Illuminate\Support\Str;
use App\Models\StudentRecord;
use App\Helpers\Qs;

class ClassImport extends ImportHandler implements ImportInterface
{
    protected $csvFile;
    public function __construct($csvFile)
    {
        $this->csvFile = $csvFile;
    }

    public function import()
    {
        $cnt = 0;
        try {
            $csv = Reader::createFromPath($this->csvFile, 'r');
            $csv->setHeaderOffset(0); // Assuming the first row contains headers

            $users = [];
            $classArr = MyClass::pluck('id', 'name')->toArray();
            $cnt = 0;
            foreach ($csv as $record) {
                $class = trim($record['class']);
                if (!isset($classArr[$class])) {

                    $userData = [
                        'name' => $class,
                        'class_type_id' => $this->getClassTypeIdByName($record['class type']),
                    ];

                    $id = MyClass::insertGetId($userData);
                    $sectionArr = explode(',', trim($record['section']));
                    foreach ($sectionArr as $kk => $val) {
                        Section::insertGetId([
                            'name' => $val,
                            'my_class_id' => $id,
                            'active' => '1'
                        ]);
                    }

                    $cnt++;
                } else {
                    $this->setMessage($class . ' - class already exists');
                }
            }

            $this->setMessage('Total ' . $cnt . ' records added successfully');
            $this->setMessage('Done');
            return true;
        } catch (\Exception $e) {
            $this->setMessage('<span style="color:red;">****** Error: ' . $e->getMessage().'</span>');
            $this->setMessage('Total ' . $cnt . ' records added');
            $this->setMessage('Done');
            Qs::insertLog($e, 'critical');
            return false;
        }
    }
}
