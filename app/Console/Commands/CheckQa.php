<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CheckQa extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:qa {--check}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check QA Database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $args = $this->options();
        $excludeTables = ['qa'];
        $onlyCheckTables = [];
        $dbTables = [];

        $db = env('DB_DATABASE');
        $tables = DB::select('SHOW TABLES');

        foreach ($tables as $table) {
            $tableName = $table->{'Tables_in_' . $db};
            if (empty($onlyCheckTables) || in_array($tableName, $onlyCheckTables)) {
                $dbTables[] = $tableName;
            }
        }
        echo "to Check change run : php artisan check:qa --check" . "\n";
        echo "--------- DB: " . $db . " (" . count($tables) . ")----------" . "\n";



        if ($args['check'] == true) {
            $qaArr = DB::table('qa')->pluck('old', 'table')->toArray();
            $totalChangedTables = 0;
            foreach ($dbTables as $tableName) {
                if (!in_array($tableName, $excludeTables)) {
                    $tot = DB::table($tableName)->count();
                    DB::table('qa')->where('table', $tableName)->update(['new' => $tot]);
                    if ($tot != $qaArr[$tableName] ?? null) {
                        $totalChangedTables++;
                        echo $totalChangedTables . ': ' . $tableName . " " . $qaArr[$tableName] . '>>' . $tot . "\n";
                    }
                }
            }

            if ($totalChangedTables === 0) {
                echo
                "--- No Table changed ---" . "\n";
            }
        } else {
            DB::table('qa')->truncate();
            $resultArr = [];
            $massInsertArr = [];
            foreach ($dbTables as $tableName) {
                if (empty($onlyCheckTables) || in_array($tableName, $onlyCheckTables)) {
                    $tot = DB::table($tableName)->count();
                    $massInsertArr[] = ['table' => $tableName, 'old' => $tot, 'new' => '0'];
                    //echo $tableName . ":" . $tot . "\n";
                    $resultArr[]=[$tableName,$tot];
                }
            }

            $titleArr = ['Table', 'count'];
            $this->table(
                $titleArr,
                $resultArr
            );

            DB::table('qa')->insert($massInsertArr);
        }
    }
}
