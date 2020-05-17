<?php
namespace Zph;
use Illuminate\Support\Facades\Schema;
class Zph
{
    public static function createBase($appDir)
    {
        $toBaseController = $appDir."/Http/Controllers/BaseController.php";
        if(!file_exists($toBaseController)){
            copy(dirname(__FILE__)."/base/BaseController.php",$toBaseController);
            printf("BaseController.php创建成功");
        }
        $toBaseService = $appDir."/Services/BaseService.php";

        if(!file_exists($toBaseService)){
            if(!is_dir($appDir."/Services")){
                mkdir($appDir."/Services");
            }
            copy(dirname(__FILE__)."/base/BaseService.php",$toBaseService);
            printf("BaseService.php创建成功");
        }
    }
    public static function createDemo($appDir,$name)
    {
        $columns = Schema::getColumnListing($name.'s');
//        dd($columns);
//        die;
        //DemoModel
        if(!is_dir($appDir."/Models")){
            mkdir($appDir."/Models");
        }
        if(!is_dir($appDir."/Services")){
            mkdir($appDir."/Services");
        }

        $toDemoModel = $appDir."/Models/".$name.".php";
        $codeModel = "<?php\n";
        $codeModel .= "namespace App\Models;\n";
        $codeModel .= "use Illuminate\Database\Eloquent\Model;\n";
//        $codeModel .= "use Illuminate\Database\Eloquent\SoftDeletes;\n";
        $codeModel .= 'class '.$name.' extends Model'."\n";
        $codeModel .= "{\n";
        $codeModel .= "\t".'protected $fillable = ['."\n";
        foreach ($columns as $v){
            $codeModel .= "\t\t'".$v."',\n";
        }
        $codeModel .= "\t".'];'."\n";
        $codeModel .= "}\n";
        file_put_contents($toDemoModel, $codeModel, LOCK_EX);

        //DemoController
        $toDemoController = $appDir."/Http/Controllers/".$name."Controller.php";

        $codeController = "<?php\n";
        $codeController .= "namespace App\Http\Controllers;\n";
        $codeController .= "use App\Http\Controllers\Controller;\n";
        $codeController .= "use App\Services\\".$name."Service;\n";
        $codeController .= 'class '.$name.'Controller extends BaseController'."\n";
        $codeController .= "{\n";
        $codeController .= "\t".'protected $service;'."\n";
        $codeController .= "\t".'public function __construct('.$name.'Service $service)'."\n";
        $codeController .= "\t".'{'."\n";
        $codeController .= "\t"."\t".'$this->service = $service;'."\n";
        $codeController .= "\t".'}'."\n";
        $codeController .= "}\n";
        file_put_contents($toDemoController, $codeController, LOCK_EX);

        //DemoService
        $toDemoService = $appDir."/Services/".$name."Service.php";

        $codeService = "<?php\n";
        $codeService .= "namespace App\Services;\n";
        $codeService .= "use App\Models\\".$name.";\n";
        $codeService .= "use App\Services\BaseService;\n";
        $codeService .= 'class '.$name.'Service extends BaseService'."\n";
        $codeService .= "{\n";
        $codeService .= "\t".'protected $model;'."\n";
        $codeService .= "\t".'public function __construct('.$name.' $app)'."\n";
        $codeService .= "\t".'{'."\n";
        $codeService .= "\t"."\t".' $this->model = $app;'."\n";
        $codeService .= "\t".'}'."\n";
        $codeService .= "}\n";
        file_put_contents($toDemoService, $codeService, LOCK_EX);

    }
}

