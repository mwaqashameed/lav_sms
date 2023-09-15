<?php 

namespace App\Imports;

use League\Csv\Reader;
use App\User;

class UserImport implements ImportInterface
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

        foreach ($csv as $record) {
            // Assuming your CSV file has columns like 'name', 'email', 'password', etc.
            $userData = [
                'name' => $record['name'],
                'email' => $record['email'],
                'dob' => $record['dob'],
                'gender' => $record['gender'],
                'password' => bcrypt($record['password']), // Hash the password
                'code'=>rand(111111,9999999),
                'user_type'=>'student'
                // Add more fields as needed
            ];

            // Add the user data to the array
            $users[] = $userData;
        }

        // Now, you have an array of user data that you can use to insert into your database.
        // You can use Eloquent or another method to insert the data.

        // Example using Eloquent to insert the data
        User::insert($users);
    }
}
