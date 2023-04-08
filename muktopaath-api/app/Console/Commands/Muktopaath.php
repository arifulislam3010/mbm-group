<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class Muktopaath extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Muktopaath:Api';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'generate necessary codes';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }
 
    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $nameArr = [];
        $name = $this->ask('What is the Api name?');

        //$field = $this->ask('field');

        // while($field==!''){
        //     $field = $this->ask('field');
        //     if($field!==''){
        //        $nameArr[] = $field; 
        //     }
        // }
        for($i=0; $i<5; $i++){
            $field = $this->ask('field');
            $nameArr[] = $field;
        }

        $prefix = $name;
        $preelements = 'protected $fillable = [';
        $elements = '';
    foreach ($nameArr as $key => $value) {
        $elements="".$elements."'".$value."'".",\n ";
    }
    $main = $preelements."\n\t\t".$elements."\n"."];";
        $contents = "
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Storage;

class ".$prefix."Controller extends Controller{
    public function index(){
        ".$main."
    }

}
?>";

         app('filesystem')->disk('orange')->put('App/Http/Controllers/'.$prefix.'Controller.php', $contents);

         // echo "<l>".$prefix."Controller created</l>"."\n";
        $this->line('<fg=red;bg=yellow>'.$prefix.'Controller created Successfully.</>');

        $this->line($nameArr);
    }
}
