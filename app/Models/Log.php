<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    public $timestamps = false;
    protected $table = 'logs';

    function saveDb($error,$file, $priority){
        return $this->insert(
            [
                'error' => $error,
                'file' => $file, 
                'priority' => $priority,
                'url'=> url()->full(),
                'created_at'=> date('Y-m-d H:i:s')
            ]
        );
    }
}
