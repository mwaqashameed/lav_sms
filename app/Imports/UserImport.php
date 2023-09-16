<?php

namespace App\Imports;

use App\User;
use League\Csv\Reader;
use App\Models\MyClass;
use App\Models\Section;
use Illuminate\Support\Str;
use App\Models\StudentRecord;

class UserImport extends ImportHandler implements ImportInterface
{
    protected $csvFile;

    public function __construct($csvFile)
    {
        $this->csvFile = $csvFile;
    }

    public function import()
    {
        $csv = Reader::createFromPath($this->csvFile, 'r');
        $csv->setHeaderOffset(0); // Assuming the first row contains headers

        $users = [];

        $classArr = MyClass::pluck('id', 'name')->toArray();
        $cnt = 0;
        foreach ($csv as $record) {
            // Assuming your CSV file has columns like 'name', 'email', 'password', etc.
            $class = trim($record['class']);
            if (isset($classArr[$class])) {
                $classID =  (int)$classArr[$class];
                $session = $record['session'];
                $cnt = 0;
                $userData = [
                    'name' => $record['name'],
                    'email' => $record['email'] ?? 'user@user-' . ++$cnt . '-' . date('YmdHis') . '.com',
                    'username' => Str::lower($record['username']),
                    'dob' => $record['dob'],
                    'gender' => $record['gender'],
                    'photo' => url('/') . '/global_assets/images/user.png',
                    'password' => bcrypt($record['password']), // Hash the password
                    'code' => rand(111111, 9999999),
                    'user_type' => 'student',
                    'nal_id' => '136',
                    'address' => $record['address'] ?? null,
                    'phone' => $record['phone'] ?? null,
                    'phone2' => $record['telephone'] ?? null,
                    // Add more fields as needed
                ];
                $id = User::insertGetId($userData);
                $cnt++;

                // User::where('id',$id)->update(['username'=>date('Y').'-'.$id]);

                $sectionID = (int)Section::where('my_class_id', $classID)->where('name', trim($record['section']))
                    ->value('id');

                StudentRecord::insert(
                    [
                        'user_id' => $id, 'my_class_id' => $classArr[$class],
                        'session' => $session,
                        'adm_no' => date('Y') . '-' . $id,
                        'section_id' => $sectionID
                    ]
                );
                
            } else {
                $this->setMessage($class . ' - Class did not found');
            }
        }

        $this->setMessage('Total ' . $cnt . ' records added successfully');
        $this->setMessage('Done');
        return true;
    }
}
