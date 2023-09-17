<?php

namespace App\Imports;

use App\Models\Section;
use App\Models\ClassType;

class ImportHandler
{
    protected $message;

    public function setMessage($message)
    {
        $this->message = $this->message . '<li>'. $message . '</li>';
    }

    public function getMessage()
    {

        return  '<ul>'.$this->message.'</ul>';
    }

    public function getClassTypeIdByName($class){
        return ClassType::where('name', trim($class))->value('id');
    }

    public function getSectionIdByName($section,$classID){
        return Section::where('my_class_id', $classID)->where('name', trim($section))
                    ->value('id');
    }
}
