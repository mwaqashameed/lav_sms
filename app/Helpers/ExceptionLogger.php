<?php

namespace App\Helpers;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Illuminate\Support\Facades\Log;

class ExceptionLogger
{
    public $logfile = '';
    public $logger = '';
    function __construct()
	{
	    $this->logfile = 'ExceptionLog';
        $duration = 'month';
		
        // Create the logger
        $this->logger = new Logger($this->logfile);
        $logfilename = sprintf("%s/logs/%s" . "-" . $this->logfile . ".txt", storage_path(), date('Y-m'));

        if ($duration != "month") {
            $logfilename = sprintf("%s/logs/%s" . "-" . $this->logfile . ".txt", storage_path(), date('Y-m-d'));
        }

        $this->logger->pushHandler(new StreamHandler($logfilename, Logger::DEBUG));
        
	}

    public function __destruct()
    {
    }

	function logException($className, $e)
	{
        $this->logToFile('Exception in ' . $className . ' message: ' . $e->getMessage());
	}

    private function logToFile($data)
	{
        $t = microtime(true);
        $micro = sprintf("%06d",($t - floor($t)) * 1000000);
        $now = new \DateTime( date('Y-m-d H:i:s.'.$micro, $t) );

        $formattedDate = $now->format('Y-m-d H:i:s.u');

		$this->logger->info('[' . $formattedDate . '] ' . $data);

		$date = $now->format("Y-m-d");
		$time = $now->format("H:i:s.u");
		$this->collectMessages($data, $date, $time);
	}

    private function collectMessages($data, $date, $time)
	{
		return array(
			'ID' => '1',
			'message' => $data,
			'date' => $date,
			'time' => $time
		);
    }
}
