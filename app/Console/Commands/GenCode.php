<?php

namespace App\Console\Commands;

use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class GenCode extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gen:code';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate Code .....';

    private $modalName='';
    private $webRoute='';
    private $controllerName='';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $option = $this->ask("Select Option \n 
        1- Controller \n 
        2- Model \n
        3- Model & Controller & Routes \n
        4- Build Queries \n
        5- Make ID as Primary key in Database \n
        6- Create Index for Table \n
        7- EXPLAIN Query for Table \n
        8- List Tables \n
        9- create API Controller \n
        10- Make Foreign Key \n
        
        
        ");

        switch ($option) {
            case '1':
                $this->generateSampleController();
                break;
            case '2':
                $this->generateModel();
                break;
            case '3':
                $this->info("------------Model------------");
                $this->generateModel();
                $this->info("------------Controller------------");
                $this->generateSampleController();
                $this->info("------------Routes------------");
                $this->printRoutesCode();
                

                break;
            case '4':
                $this->buildQueries();
                break;
            case '5':
                $this->makeIdAsPrimaryKey();
                break;
            case '6':
                $this->createDbIndex();
                break;
            case '7':
                $this->explainTableQuery();
                break;
            case '8':
                $this->listDbTables();
                break;
            case '9':
                $this->createAPIController();
                break;
            case '10':
                $this->makeForeignTable();
                break;

            default:
                $this->error("ERROR: Option not found");
                break;
        }
    }

    private function printRoutesCode(){
        $route = $this->webRoute;
        $controllerName = $this->controllerName;
    $routes = <<<EOD
    Route::get('/$route/set_order', [$controllerName::class, 'set_order']);
    Route::get('/$route/pop', [$controllerName::class, 'pop']);
    Route::get('/$route/unset_search', [$controllerName::class, 'unset_search']);
    Route::post('/$route/update_status', [$controllerName::class, 'update_status']);
    Route::post('/$route/update_order', [$controllerName::class, 'update_order']);
    Route::resource('/$route', $controllerName::class);
    EOD;
    echo $routes;
    }

    // Function to generate a sample Laravel controller file
    private function generateSampleController()
    {
        $className = trim($this->ask("Controller Name?"));
        $this->webRoute=$webRoute = Str::slug($className);
        $className = UcWords(Str::camel($className));
        $this->controllerName=$controllerName = $className . 'Controller';
        $fileName = $controllerName . '.php';
        $modalName = $this->modalName;
        if($modalName==''){
            $modalName = $className;
        }

        $controllerTitle = Str::plural(Str::headline($className));
        $controllerSingleTitle = Str::singular(Str::headline($className));

        // Prepare the controller content
        $content = file_get_contents($this->genAPI(734));

        $content = str_replace('{modalName}', $modalName, $content);
        $content = str_replace('{controllerName}', $controllerName, $content);

        // Write the content to the file
        file_put_contents(base_path() . '/app/Http/Controllers/Back/' . $fileName, $content);

        $this->info('Done: Controller file generated. : ' . $fileName);
    }

    private function generateModel()
    {
        $name = trim($this->ask("Modal File Name ? "));
        $name = ucwords(Str::camel(trim($name)));
        $this->modalName = $name;
        $fileName = $name . '.php';
        $db = $this->ask("Model DB Table Name.");

        $content = file_get_contents($this->genAPI(735));

        $content = str_replace('{db}', $db, $content);
        $content = str_replace('{name}', $name, $content);


        file_put_contents(base_path() . '/app/Models/' . $fileName, $content);
        $this->info("Model Created : " . $fileName);
    }

    private function buildQueries()
    {
        $dbTable = $this->ask("DB Table NAME ? ");
        $dbTable = trim($dbTable);


        $totalRecords = number_format(DB::table($dbTable)->count());


        $totRecords = <<<EOD
Total Records : {$totalRecords}
--------------------------------

EOD;

        $content = file_get_contents($this->genAPI(732));//Build Queries
        $content = $totRecords.str_replace('{dbTable}', $dbTable, $content);

        echo $content;
    }

    private function makeIdAsPrimaryKey()
    {
        $db = env('DB_DATABASE');
        $dbTable = $this->ask("DB Table NAME ? ");
        $dbTable = trim($dbTable);
        $query = "ALTER TABLE " . $db . "." . $dbTable . " MODIFY COLUMN id INT auto_increment PRIMARY KEY;";
        echo $query;
        echo "\n";
        DB::select($query);

        $this->info("DONE");
    }

    private function createDbIndex()
    {
        $db = env('DB_DATABASE');
        $dbTable = $this->ask("DB Table NAME ? ");
        $dbFieldName = $this->ask("DB Table --> Field NAME ? ");
        $dbTable = trim($dbTable);


        $query = <<<EOD
CREATE INDEX {$dbTable}_{$dbFieldName}_IDX USING BTREE ON {$db}.{$dbTable} ({$dbFieldName});

EOD;

        echo $query;
        echo "\n";
        if ($this->confirm('Do you wish to continue(Run Above Query)?')) {
            DB::select(trim($query));
            $this->explainTableQuery($dbTable);
            $this->info("Done: Query Executed...");
        } else {
            $this->info("ERROR: Query NOT Executed...");
        }
    }

    private function explainTableQuery($dbTable = '')
    {
        $db = env('DB_DATABASE');
        if ($dbTable == '') {
            $dbTable = $this->ask("DB Table NAME ? ");
            $dbTable = trim($dbTable);
        }

        if(is_numeric($dbTable)){
            $tables = DB::select('SHOW TABLES');
            $dbTable = $tables[$dbTable - 1]->{'Tables_in_' . $db};
        }
        
        $totalRecords = number_format(DB::table($dbTable)->count());




        $query = <<<EOD
EXPLAIN {$db}.{$dbTable};

EOD;

        echo $query;
        echo "\n";
        echo <<<EOD
Total Records : {$totalRecords}
--------------------------------
EOD;

        $result = DB::select(trim($query));
        $resultArr = [];
        foreach ($result as $key => $value) {
            $resultArr[] = [
                'Field' => $value->Field, 'Type' => $value->Type, 'Key' => $value->Key, 'Default' => $value->Default, 'Extra' => $value->Extra
            ];
        }

        $titleArr = ['Field', 'Type', 'Key', 'Default', 'Extra'];
        $this->table(
            $titleArr,
            $resultArr
        );

        $this->explainTableQuery();
    }

    private function listDbTables(){
        $dbTables = [];

        $db = env('DB_DATABASE');
        $tables = DB::select('SHOW TABLES');
        $cnt=0;

        $dbArr=[];
        foreach ($tables as $kk=>$table) {
            $cnt++;
            $tableName = $table->{'Tables_in_' . $db};
            $dbArr[]=['table'=> $tableName,'tot'=> DB::table($tableName)->count()];
            echo $cnt.'- '.$tableName."    ---------------(".DB::table($tableName)->count().")\n";
        }
        echo "--------------------------------------------------------------- \n";
        echo "--------- DB: " . $db . " (" . $cnt . ")----------" . "\n";
    }

    private function createAPIController()
    {
        $name = trim($this->ask("API Controller File Name ? "));//{FILE}
        $name = ucwords(Str::camel(trim($name)));
        $this->modalName = $name;
        $fileName = $name . 'Controller';

        $content = file_get_contents($this->genAPI(731));
        $content = str_replace('{FILE}', $fileName, $content);

        file_put_contents(base_path() . '/app/Http/Controllers/Api/' . $fileName. '.php', $content);
        $this->info("API File Created : " . $fileName);
    }

    private function makeForeignTable($dbTable = '')
    {
        $db = env('DB_DATABASE');
        if ($dbTable == '') {
            $dbTable = $this->ask("DB Table NAME ? ");
            $dbTable = trim($dbTable);
        }

        $dbFieldName = $this->ask("DB Field NAME ? ");
        $parentTable = $this->ask("DB Parent Table ? ");
        $parentTablePrimaryKey = $this->ask("Parent Table Primary Key ? ");

        $query = file_get_contents($this->genAPI(733));
        $query = str_replace('{db}', $db, $query);
        $query = str_replace('{dbTable}', $dbTable, $query);
        $query = str_replace('{dbFieldName}', $dbFieldName, $query);
        $query = str_replace('{parentTable}', $parentTable, $query);
        $query = str_replace('{parentTablePrimaryKey}', $parentTablePrimaryKey, $query);
        //Make Forgin Key
        
        echo $query;
        echo "\n";
    }

    private function genAPI($idd=731){
        return 'https://www.gen.fastcodings.com/gen_code/'. $idd.'?text=api';
    }
}